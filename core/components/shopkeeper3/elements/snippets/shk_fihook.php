<?php
/**
 * FormIt hook for Shopkeeper 3.x
 */

//ini_set('display_errors',1);
//error_reporting(E_ALL);

$output = false;

if(!defined('SHOPKEEPER_PATH')){
    define('SHOPKEEPER_PATH', MODX_CORE_PATH."components/shopkeeper3/");
}

//Определяем параметры сниппета Shopkeeper
$sys_property_sets = $modx->getOption( 'shk3.property_sets', $modx->config, 'default' );
$sys_property_sets = explode( ',', $sys_property_sets );
$propertySetName = trim( current( $sys_property_sets ) );

$snippet = $modx->getObject('modSnippet',array('name'=>'Shopkeeper3'));
$properties = $snippet->getProperties();
if( $propertySetName != 'default' && $modx->getCount( 'modPropertySet', array( 'name' => $propertySetName ) ) > 0 ){
    $propSet = $modx->getObject( 'modPropertySet', array( 'name' => $propertySetName ) );
    $propSetProperties = $propSet->getProperties();
    if(is_array($propSetProperties)) $properties = array_merge($properties,$propSetProperties);
}

$lang = $modx->getOption( 'lang', $properties, 'ru' );
$modx->getService( 'lexicon', 'modLexicon' );
$modx->lexicon->load( $lang . ':shopkeeper3:default' );

if( !empty( $_SESSION['shk_order'] ) ){
    
    require_once SHOPKEEPER_PATH . "model/shopkeeper.class.php";
    $shopCart = new Shopkeeper( $modx, $properties );
    
    $modx->addPackage( 'shopkeeper3', SHOPKEEPER_PATH . 'model/' );
    
    //shopkeeper settings
    $contacts_fields = array();
    $response = $modx->runProcessor('getsettings',
        array( 'settings' => array('contacts_fields') ),
        array( 'processors_path' => $modx->getOption( 'core_path' ) . 'components/shopkeeper3/processors/mgr/' )
    );
    if ($response->isError()) {
        echo $response->getMessage();
    }
    if($result = $response->getResponse()){
        
        $temp_arr = !empty( $result['object']['contacts_fields'] ) ? $result['object']['contacts_fields'] : array();
        if( !empty( $temp_arr ) ){
            
            foreach( $temp_arr as $opt ){
                
                $contacts_fields[$opt['name']] = $opt;
                
            }
            
        }
        
    }
    
    $userId = $modx->getLoginUserID( $modx->context->key );
    if( !$userId ) $userId = 0;
    
    //Контактные данные
    $contacts = array();
    $allFormFields = $hook->getValues();
    foreach( $allFormFields as $key => $val ){
        
        if( in_array( $key, array_keys( $contacts_fields ) ) ){
            
            $temp_arr = array(
                'name' => $contacts_fields[$key]['name'],
                'value' => $val,
                'label' => $contacts_fields[$key]['label']
            );
            
            array_push( $contacts, $temp_arr );
            
        }
        
    }
    
    $contacts = json_encode( $contacts );
    
    $emailField = $modx->getOption( 'fiarToField', $hook->config, 'email' );
    $phoneField = $modx->getOption( 'phoneField', $hook->config, 'phone' );
    $deliveryField = $modx->getOption( 'deliveryField', $hook->config, 'shk_delivery' );
    $paymentField = $modx->getOption( 'paymentField', $hook->config, 'payment' );
    
    //Доставка
    $delivery_price = !empty( $shopCart->delivery['price'] ) ? $shopCart->delivery['price'] : 0;
    $delivery_name = !empty( $shopCart->delivery['label'] ) ? $shopCart->delivery['label'] : '';
    if( !$delivery_name ){
	$delivery_name = !empty( $allFormFields[$deliveryField] ) ? $allFormFields[$deliveryField] : '';
    }
    
    //Сохраняем данные заказа
    $order = $modx->newObject('shk_order');
    $insert_data = array(
        'contacts' => $contacts,
        'options' => '',
        'price' => Shopkeeper::$price_total,
        'currency' => $shopCart->config['currency'],
        'date' => strftime('%Y-%m-%d %H:%M:%S'),
        'sentdate' => strftime('%Y-%m-%d %H:%M:%S'),
        'note' => '',
        'email' => isset( $allFormFields[$emailField] ) ? $allFormFields[$emailField] : '',
        'delivery' => $delivery_name,
        'delivery_price' => $delivery_price,
        'payment' => isset( $allFormFields[$paymentField] ) ? $allFormFields[$paymentField] : '',
        'tracking_num' => '',
        'phone' => isset( $allFormFields[$phoneField] ) ? $allFormFields[$phoneField] : '',
        'status' => $modx->getOption( 'shk3.first_status', null, '1' )
    );
    if( $userId ){
        $insert_data['userid'] = $userId;
    }
    $order->fromArray($insert_data);
    $saved = $order->save();
    
    //Сохраняем товары заказа
    if( $saved ){

        $purchasesData = $shopCart->getProductsData( true );

        foreach( $shopCart->data as $key => $p_data ){

            $options = !empty( $p_data['options'] ) ? json_encode( $p_data['options'] ) : '';
            $fields_data = !empty( $purchasesData[ $key ] ) ? $purchasesData[ $key ] : array();
            $fields_data['url'] = !empty( $p_data['url'] ) ? $p_data['url'] : '';
            unset( $fields_data['id'] );
            $fields_data_str = json_encode( $fields_data );

            $insert_data = array(
                'p_id' => $p_data['id'],
                'order_id' => $order->id,
                'name' => $p_data['name'],
                'price' => $p_data['price'],
                'count' => $p_data['count'],
                'class_name' => $p_data['className'],
                'package_name' => $p_data['packageName'],
                'data' => $fields_data_str,
                'options' => $options
            );

            $purchase = $modx->newObject('shk_purchases');
            $purchase->fromArray( $insert_data );
            $purchase->save();

        }

        $shopCart->setOrderDataSession( $order->toArray() );

    }
    
    $modx->invokeEvent( 'OnSHKChangeStatus', array( 'order_ids' => array( $order->id ), 'status' => $order->status ) );
    
    $orderOutputData = $shopCart->getOrderData( $order->id );
    
    //OnSHKsaveOrder
    $evtOut = $modx->invokeEvent('OnSHKsaveOrder',array('order_id' => $order->get('id')));
    if(is_array($evtOut)) $orderOutputData .= implode('',$evtOut);
    
    $hook->setValues(array(
        'orderID' => $order->get('id'),
        'orderDate' => $order->get('date'),
        'orderPrice' => $order->get('price'),
        'orderCurrency' => $shopCart->config['currency'],
        'orderOutputData' => $orderOutputData
    ));
    
    $shopCart->request_empty( false );
    
    $output = true;
    
}else{
    
    $hook->addError( 'error_message', $modx->lexicon('shk.order_empty') );
    $output = false;
    
}

return $output;