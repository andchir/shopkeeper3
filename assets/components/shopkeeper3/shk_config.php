<?php

/**
 * @package shopkeeper3
 * @var modX $modx
 */

//ini_set('display_errors',1);
//error_reporting(E_ALL);

header("Content-type: application/javascript");

define('MODX_REQP',false);
define('MODX_CONNECTOR_INCLUDED',true);

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$modx->getService('error','error.modError');

$shk_config = array();

$shk_config['auth_token'] = $modx->user->getUserToken($modx->context->get('key'));
$shk_config['assets_url'] = $modx->getOption('assets_url');
$shk_config['manager_url'] = $modx->getOption('manager_url');
$shk_config['manager_language'] = $modx->getOption('manager_language');

//lexicon
$modx->getService('lexicon','modLexicon');
$modx->lexicon->load($shk_config['manager_language'].':shopkeeper3:manager');
$shk_config['lang'] = $modx->lexicon->fetch('shk3.');

foreach($modx->config as $key => $val){
    
    if(substr($key, 0, 4) == 'shk3'){
        $shk_config[$key] = $val;
    }
    
}

//shopkeeper settings
$response = $modx->runProcessor('getsettings',
    array(),
    array('processors_path' => $modx->getOption( 'core_path' ) . 'components/shopkeeper3/processors/mgr/')
);
if ($response->isError()) {
    echo $response->getMessage();
}
if($result = $response->getResponse()){
    
    $settings = $result['object'];
    
    //Если значение олаты пустое, подставляем название как значение
    if( !empty( $settings['payments'] ) && is_array( $settings['payments'] ) ){
        
        foreach( $settings['payments'] as &$setting ){
            if( empty( $setting['value'] ) ){
                $setting['value'] = $setting['label'];
            }
        }
        
    }
    
    $shk_config['settings'] = $settings;
    
}


echo "
/* Shopkeeper global config */
var shk_config = ".(defined('JSON_PRETTY_PRINT') ? json_encode($shk_config,JSON_PRETTY_PRINT) : json_encode($shk_config)).";
";
