<?php

/**
 * tagManager Connector
 *
 * @package tag_manager2
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('core_path').'components/tag_manager2/';

$modx->lexicon->load('tag_manager2:default');

/* handle request */
$modx->request->handleRequest(array(
    'processors_path' => $modx->getOption( 'core_path' ) . 'components/tag_manager2/processors/',
    'location' => ''
));

