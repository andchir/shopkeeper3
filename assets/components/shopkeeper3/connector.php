<?php

/**
 * shopkeeper3 Connector
 *
 * @package shopkeeper3
 */
 
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('core_path').'components/shopkeeper3/';

$modx->lexicon->load('shopkeeper3:default');

/* handle request */
$modx->request->handleRequest(array(
    'processors_path' => $modx->getOption( 'core_path' ) . 'components/shopkeeper3/processors/',
    'location' => ''
));

