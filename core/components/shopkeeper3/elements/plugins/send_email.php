<?php

//OnSHKChangeStatus

$order_ids = $modx->getOption('order_ids', $scriptProperties, '' );
$order_status = $modx->getOption('status', $scriptProperties, '1' );

if(empty($order_ids) || !is_array($order_ids)){
    return '';
}

$output = '';

$response = $modx->runProcessor('updateorderstatus',
    array(
        'status' => $order_status,
        'order_id' => $order_ids
    ),
    array('processors_path' => $modx->getOption('core_path') . 'components/shopkeeper3/processors/mgr/')
);
if( $result = $response->getResponse() ){
    $output .= $response->getMessage();
}

$modx->event->output( strip_tags( $output ) );

return '';