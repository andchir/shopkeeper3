<?php

/**
 * shkSaveOrderProcessor
 *
 * @package shopkeeper3
 * @subpackage processors
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class shkSaveOrderProcessor extends modProcessor {

    public function process() {
        
        $output = array(
            'success' => true,
            'message' => ''
        );
        
        //check permissions
        if( !$this->modx->hasPermission('save') ){
            $output['success'] = false;
            $output['message'] = $this->modx->lexicon('permission_denied');
            return $output;
        }
        
        $modelpath = $this->modx->getOption('core_path') . 'components/shopkeeper3/model/';
        $this->modx->addPackage( 'shopkeeper3', $modelpath );
        
        $scriptProperties = $this->getProperties();
        
        $order_data = $this->modx->getOption( 'order', $scriptProperties, array() );
        
        if( !empty( $order_data ) ){
            
            $purchases_data = $order_data['purchases'];
            unset( $order_data['purchases'] );
            if( isset( $order_data['date'] ) ) unset( $order_data['date'] );
            
            //contacts
            $order_data['contacts'] = json_encode( $order_data['contacts'] );
            
            $order = $this->modx->getObject( 'shk_order', $order_data['id'] );
            if( $order ){
                
                $order->fromArray( $order_data );
                $order->price = $this->getPriceTotal( $purchases_data, $order->delivery_price );
                $order->save();
                
                $this->savePurchases( $order->id, $purchases_data );
                
            }
            
        }
        
        return $output;
        
    }
    
    
    /**
     * getTotal
     *
     */
    public function getPriceTotal( $purchases_data, $delivery_price = 0 ){
        
        $price_total = 0;
        
        if( !empty( $purchases_data ) ){
        
            foreach( $purchases_data as $parchase ){
                
                $price_total += $parchase['price'] * $parchase['count'];
                
                if( !empty( $parchase['options'] ) ){
                    
                    //доп параметры товара
                    foreach( $parchase['options'] as $opts ){
                        
                        if( !empty( $opts[1] ) ){
                            $price_total += $opts[1] * $parchase['count'];
                        }
                        
                    }
                    
                }
                
            }
            
        }
        
        $price_total += $delivery_price;
        
        return $price_total;
        
    }
    
    
    /**
     * savePurchases
     *
     */
    public function savePurchases( $order_id, $purchases_data ){
        
        $p_ids = array();
        $del_ids = array();
        $query = $this->modx->newQuery( 'shk_purchases' );
        $query->where( array( 'order_id' => $order_id ) );
        $purchases = $this->modx->getIterator( 'shk_purchases', $query );
        $className = 'modResource';
        $packageName = '';
        
        if( $purchases ){
            
            foreach( $purchases as $purchase ){
                
                $index = -1;
                foreach( $purchases_data as $k => $temp_arr ){
                    
                    if( !empty( $temp_arr['id'] ) && $temp_arr['id'] == $purchase->id ){
                        $index = $k;
                        break;
                    }
                }
                
                if( $index > -1 ){
                    
                    if( !empty( $purchases_data[$index]['options'] ) ){
                        $options = array();
                        foreach( $purchases_data[$index]['options'] as $k => $v ){
                            if( !empty( $v[0] ) ){
                                $options[$k] = $v;
                            }
                        }
                        $purchases_data[$index]['options'] = json_encode( $options );
                    }
                    
                    $purchase->fromArray( $purchases_data[$index] );
                    $purchase->save();
                    
                }else{
                    array_push( $del_ids, $purchase->id );
                }
                
                if( !empty( $purchase->class_name ) && !empty( $purchase->package_name ) ){
                    $className = $purchase->class_name;
                    $packageName = $purchase->package_name;
                }
                
            }
            
            //print_r($purchases_data);
            
            //Сохраняем новые товары
            if( $purchases_data ){
                
                //Параметры сниппета Shopkeeper3
                $snippet_properties = array();
                $response = $this->modx->runProcessor( 'getsnippetproperties',
                    array(),
                    array('processors_path' => $this->modx->getOption( 'core_path' ) . 'components/shopkeeper3/processors/mgr/')
                );
                if ( !$response->isError() ) {
                    $snippet_properties = $response->getObject();
                }
                
                require_once $this->modx->getOption('core_path') . "components/shopkeeper3/model/shopkeeper.class.php";
                $shopCart = new Shopkeeper( $this->modx, $snippet_properties );
                $shopCart->addPackage( $packageName );
                
                foreach( $purchases_data as $k => $temp_arr ){
                    if( empty( $temp_arr['id'] ) ){
                        
                        $new_p = array(
                            'p_id' => ( !empty( $temp_arr['p_id'] ) ? $temp_arr['p_id'] : 0 ),
                            'order_id' => $order_id,
                            'name' => $temp_arr['name'],
                            'count' => ( !empty( $temp_arr['count'] ) ? $temp_arr['count'] : 1 ),
                            'price' => ( !empty( $temp_arr['price'] ) ? $temp_arr['price'] : '' ),
                            'options' => ( !empty( $temp_arr['options'] ) ? $temp_arr['options'] : '' ),
                            'class_name' => $className,
                            'package_name' => $packageName
                        );
                        
                        if( is_array( $new_p['options'] ) ){
                            $new_p['options'] = json_encode( $new_p['options'] );
                        }
                        
                        //Если нет названия или цены
                        if( !empty( $new_p['p_id'] ) && empty( $new_p['name'] ) || empty( $new_p['price'] ) ){
                            
                            $shopCart->getPurchaseFromDB( $new_p['p_id'], $className );
                            if( is_object( $shopCart->purchase ) ){
                                
                                if( empty( $new_p['name'] ) ){ $new_p['name'] = $shopCart->purchase->get($shopCart->config['fieldName']); }
                                if( !isset( $new_p['price'] ) || $new_p['price'] == '' ){
                                    $new_p['price'] = $shopCart->getProductPrice();
                                }
                                
                                $fields_data = array( 'url' => $shopCart->getPurchaseUrl() );
                                $fields_data_str = json_encode( $fields_data );
                                $new_p['data'] = $fields_data_str;
                                
                            }else{
                                $new_p['price'] = 0;
                            }
                            
                        }
                        
                        $purchase = $this->modx->newObject( 'shk_purchases' );
                        $purchase->fromArray($new_p);
                        $purchase->save();
                        
                    }
                }
                
            }
            
            //Удаляем товары
            if( count($del_ids) > 0 ){
                
                $affected = $this->modx->removeCollection(
                    'shk_purchases',
                    array(
                        "order_id" => $order_id,
                        "id:IN" => $del_ids
                    )
                );
                
            }
            
            return true;
            
        }else{
            return false;
        }
        
    }
    
}

return 'shkSaveOrderProcessor';
