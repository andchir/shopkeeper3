<?php

/**
 * @package modx
 * @var modX $modx
 */

header("Content-type: application/javascript");

//define('MODX_REQP',false);
define('MODX_CONNECTOR_INCLUDED',true);

require_once dirname(dirname(dirname(dirname(__FILE__)))) . "/config.core.php";
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . "index.php";

$tm_config = array();

$tm_config['auth_token'] = $modx->user->getUserToken($modx->context->get('key'));
$tm_config['assets_url'] = $modx->getOption('assets_url');
$tm_config['manager_url'] = $modx->getOption('manager_url');
$tm_config['manager_language'] = $modx->getOption('manager_language');

//lexicon
$modx->getService('lexicon','modLexicon');
$modx->lexicon->load($tm_config['manager_language'].':tag_manager2:manager');
$tm_config['lang'] = $modx->lexicon->fetch('tag_mgr2.');

foreach($modx->config as $key => $val){
    
    if(substr($key, 0, 8) == 'tag_mgr2'){
        $tm_config[$key] = $val;
    }
    
}

echo "
/* tagManager global config */
var tm_config = ".(defined('JSON_PRETTY_PRINT') ? json_encode($tm_config,JSON_PRETTY_PRINT) : json_encode($tm_config)).";
";
