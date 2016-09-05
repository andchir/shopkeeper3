<?php
/*
 * shk_updateInventory
 * 
 * Плагин обновляет количество товара на складе при переводе статуса заказа в "Отправлен" (или др.)
 * 
 * OnSHKChangeStatus
 */

$eventName = $modx->event->name;

$inventory_fieldname = $modx->getOption( 'inventory_fieldname', $scriptProperties, 'inventory' );
$plugin_status = $modx->getOption( 'plugin_status', $scriptProperties, '2' );
$context = $modx->getOption( 'context', $scriptProperties, '' );
$order_ids = $modx->getOption( 'order_ids', $scriptProperties, array() );
if( !is_array( $order_ids ) ){
    $order_ids = $order_ids && is_numeric( $order_ids ) ? array( $order_ids ) : array();
}
$status = $modx->getOption( 'status', $scriptProperties, '' );

if( empty( $order_ids ) || empty( $status ) || $status != $plugin_status ) return '';

$modelpath = $modx->getOption('core_path') . 'components/shopkeeper3/model/';
$modx->addPackage( 'shopkeeper3', $modelpath );

foreach( $order_ids as $order_id ){
    
    $query = $modx->newQuery( 'shk_purchases' );
    $query->where( array( 'order_id' => $order_id ) );
    $purchases = $modx->getIterator( 'shk_purchases', $query );
    
    if( $purchases ){
        
        foreach( $purchases as $purchase ){
            
            if( $purchase->package_name && $purchase->package_name != 'modResource' && !in_array( $purchase->package_name, array_keys($modx->packages) ) ){
                $modelpath = $modx->getOption('core_path') . 'components/' . $purchase->package_name . '/model/';
                $modx->addPackage( $purchase->package_name, $modelpath );
            }
            
            $product = $modx->getObject( $purchase->class_name, $purchase->p_id );
            if( $product ){
                
                $p_data = $product->toArray();
                
                //Если это поле основной таблицы
                if( isset( $p_data[ $inventory_fieldname ] ) ){
                    
                    $current_inventory = $p_data[ $inventory_fieldname ];
                    $current_inventory = $current_inventory ? floatval( $current_inventory ) : 0;
                    if( !$current_inventory ) continue;
                    
                    $new_inventory = $current_inventory - $purchase->count;
                    if( $new_inventory < 0 ) $new_inventory = 0;
                    
                    $product->set( $inventory_fieldname, $new_inventory );
                    $product->save();
                    
                }
                //Если значение хранится в TV параметре
                else{
                    
                    $table_name = '';
                    if ( $className = $modx->loadClass( $purchase->class_name ) ) {
                        $table_name = isset( $modx->map[$className]['table'] ) ? $modx->map[$className]['table'] : '';
                    }
                    if( $table_name != 'site_content' ) continue;
                    
                    $context = $p_data['context_key'];
                    
                    $tv = $modx->getObject( 'modTemplateVar', array( 'name' => $inventory_fieldname ));
                    if( !$tv ) continue;
                    
                    $current_inventory = $tv->getValue( $p_data['id'] );
                    $current_inventory = $current_inventory ? floatval( $current_inventory ) : 0;
                    if( !$current_inventory ) continue;
                    
                    $new_inventory = $current_inventory - $purchase->count;
                    if( $new_inventory < 0 ) $new_inventory = 0;
                    
                    //$tv->setValue( $p_data['id'], $new_inventory );
                    
                    $templateVarResource = $modx->getObject('modTemplateVarResource',array(
                        'tmplvarid' => $tv->get('id'),
                        'contentid' => $p_data['id'],
                    ),true);
                    
                    if( $templateVarResource ){
                        $templateVarResource->set( 'value', $new_inventory );
                        $templateVarResource->save();
                    }
                    
                }
                
            }
            
        }
        
    }
    
}


//Очистка кэша сайта
if( $context ){
    $modx->cacheManager->refresh(array(
        'resource' => array( 'contexts' => array( $context ) ),
    ));
}

return '';
