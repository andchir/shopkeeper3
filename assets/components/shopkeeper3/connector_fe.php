<?php

/**
 * Shopkeeper frontend connector
 *
 * @package shopkeeper
 */
 
//ini_set( 'display_errors', 1 );
//error_reporting( E_ALL );

//if( !isset( $_POST['shk_action'] ) ) exit;

require dirname(dirname(dirname(dirname(__FILE__))))."/config.core.php";

if(!defined('MODX_CORE_PATH')) require_once '../../../config.core.php';
require_once MODX_CORE_PATH . 'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();

$modx->initialize( 'web' );
$modx->invokeEvent("OnLoadWebDocument");

define('SHOPKEEPER_PATH', MODX_CORE_PATH . 'components/shopkeeper3/');
define('SHOPKEEPER_URL', MODX_CORE_PATH . 'components/shopkeeper3/');

$manager_language = $modx->config['manager_language'];
$charset = $modx->config['modx_charset'];

header('Content-Type: text/html; charset={$charset}');

require_once SHOPKEEPER_PATH . 'model/shopkeeper.class.php';

//Определяем параметры сниппета Shopkeeper
$sys_property_sets = $modx->getOption( 'shk3.property_sets', null, 'default' );
$sys_property_sets = explode( ',', $sys_property_sets );
$sys_property_sets = array_map( 'trim', $sys_property_sets );

$propertySerNum = isset( $_POST['psn'] ) && is_numeric( $_POST['psn'] ) ? intval($_POST['psn']) : 1;
$propertySetName = isset( $sys_property_sets[($propertySerNum - 1)] ) ? $sys_property_sets[($propertySerNum - 1)] : $sys_property_sets[0];

$snippet = $modx->getObject('modSnippet',array('name'=>'Shopkeeper3'));
$properties = $snippet->getProperties();
if( $propertySetName != 'default' && $modx->getCount( 'modPropertySet', array( 'name'=>$propertySetName ) ) > 0 ){
    $propSet = $modx->getObject( 'modPropertySet', array( 'name'=>$propertySetName ) );
    $propSetProperties = $propSet->getProperties();
    if( is_array( $propSetProperties ) ) $properties = array_merge( $properties, $propSetProperties );
}

$shopCart = new Shopkeeper( $modx, $properties, true );
$shopCart->config['charset'] = $charset;

$cart_html = $shopCart->getCartContent();
$cart_html = $shopCart->stripModxTags( $cart_html );

$output = array(
    'price_total' => Shopkeeper::$price_total,
    'items_total' => Shopkeeper::$items_total,
    'items_unique_total' => Shopkeeper::$items_unique_total,
    'delivery_price' => ( !empty( $shopCart->delivery['price'] ) ? $shopCart->delivery['price'] : 0 ),
    'delivery_name' => ( !empty( $shopCart->delivery['label'] ) ? $shopCart->delivery['label'] : '' ),
    'ids' => $shopCart->getProdIds(),
    'html' => $cart_html
);

echo json_encode( $output );
