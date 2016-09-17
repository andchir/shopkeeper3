<?php

/**
 * shkGetOrderProcessor
 *
 * @package shopkeeper3
 * @subpackage processors
 */

class shkGetOrderProcessor extends modProcessor {

    public function process() {
        
        $modelpath = $this->modx->getOption('core_path') . 'components/shopkeeper3/model/';
        $this->modx->addPackage( 'shopkeeper3', $modelpath );
        
        $contacts_fields = $this->getContactsFields();
        
        $scriptProperties = $this->getProperties();
        $order_id = $this->modx->getOption( 'order_id', $scriptProperties, 0 );
        $date_format = $this->modx->getOption( 'date_format', $scriptProperties, 'd.m.Y H:i:s' );
        $order_data = array();
        
        if( $order_id ){
            
            $order = $this->modx->getObject('shk_order',$order_id);
            
            if( $order ){
                
                $order_data = $order->toArray();
                
                //contacts
                if( !empty( $order_data['contacts'] ) ){
                    
                    $contacts = json_decode( $order_data['contacts'], true );
                    $tmp_arr = array();
                    foreach( $contacts as $contact ){
                        $tmp_arr['contacts.'.$contact['name']] = $contact['value'];
                    }
                    $order_data = array_merge( $tmp_arr, $order_data );
                    
                    $order_data['contacts'] = array();
                    foreach( $contacts as $index => $val ){
                        
                        $rank = !empty( $val['rank'] ) ? $val['rank'] : $index;
                        $name = !empty($val['name']) ? $val['name'] : '';
                        if( $name && !empty( $contacts_fields[$name] ) ){
                            //$rank = $contacts_fields[$name]['rank'];
                        }
                        
                        $temp_arr = array(
                            'name' => $name,
                            'value' => $val['value'],
                            'label' => $val['label'],
                            'rank' => $rank
                        );
                        
                        array_push( $order_data['contacts'], $temp_arr );
                        
                    }
                    
                    $this->array_sort( $order_data['contacts'] );
                    
                }
                
                if( $order_data['date'] ){
                    $order_data['date'] = date( $date_format, strtotime( $order_data['date'] ) );
                }
                if( $order_data['sentdate'] ){
                    $order_data['sentdate'] = date( $date_format, strtotime( $order_data['sentdate'] ) );
                }
                
                $order_data['purchases'] = $this->getPurchases( $order_id );
                
            }
            
        }
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => $order_data
        );
        
        return $output;
        
    }
    
    
    /**
     * getPurchases
     *
     */
    public function getPurchases( $order_id ){
        
        $output = array();
        
        $query = $this->modx->newQuery('shk_purchases');
        $query->where( array( 'order_id' => $order_id ) );
        $query->sortby( 'id', 'asc' );
        $purchases = $this->modx->getIterator( 'shk_purchases', $query );
        
        if( $purchases ){
            
            foreach( $purchases as $purchase ){
                
                $p_data = $purchase->toArray();
                
                if( !empty( $p_data['options'] ) ){
                    $p_data['options'] = json_decode( $p_data['options'], true );
                }
                
                $fields_data = array();
                if( !empty( $p_data['data'] ) ){
                    $fields_data = json_decode( $p_data['data'], true );
                    unset($p_data['data']);
                }
                
                $purchase_data = array_merge( $fields_data, $p_data );
                
                array_push( $output, $purchase_data );
                
            }
            
        }
        
        return $output;
        
    }
    
    
    /**
     * getContactsFields
     *
     */
    public function getContactsFields(){
        
        $output = array();
        
        //get settings
        $response = $this->modx->runProcessor('getsettings',
            array( 'settings' => array('contacts_fields') ),
            array('processors_path' => $this->modx->getOption( 'core_path' ) . 'components/shopkeeper3/processors/mgr/')
        );
        if ($response->isError()) {
            echo $response->getMessage();
        }
        if($result = $response->getResponse()){
            
            $settings = $result['object'];
            
            foreach( $settings['contacts_fields'] as $field ){
                
                $output[ $field['name'] ] = array(
                    'label' => $field['label'],
                    'rank' =>  $field['rank']
                );
                
            }
            
        }
        
        return $output;
        
    }
    
    
    /**
     * getTotalCount
     *
     */
    public function getTotalCount( $purchases = array() ){
        
        $count = 0;
        
        if( !empty($purchases) ){
            
            foreach( $purchases as $purchase ){
                
                $count += $purchase['count'];
                
            }
            
        }
        
        return $count;
        
    }
    
    
    /**
     * array_sort
     *
     */
    public function array_sort( &$array ){
        
        if( !function_exists('cmp') ){
            function cmp($a, $b){
                if ($a['rank'] == $b['rank']) {
                    return 0;
                }
                return ($a['rank'] < $b['rank']) ? -1 : 1;
            }
        }
        
        usort($array, "cmp");
        
    }
    
    
}

return 'shkGetOrderProcessor';
