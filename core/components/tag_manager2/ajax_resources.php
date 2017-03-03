<?php

/**
 * tagManager2
 *
 * @package tag_manager2
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'){
    exit;
}

if (!defined('MODX_API_MODE')) {
    define('MODX_API_MODE', true);
}

$resource_id = !empty($_GET['page_id']) && is_numeric($_GET['page_id']) ? $_GET['page_id'] : 1;
$output = array('prod_list'=>'','pages'=>'','total'=>0,'pageCount'=>1,'onPageLimit'=>1);

require_once '../../../config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();

//get resourse context_key
$context_key = 'web';
$query = $modx->newQuery('modResource', array('id' => $resource_id, 'published' => true, 'deleted' => false));
$query->select($modx->getSelectColumns('modResource', '', '', array('context_key')));
$stmt = $query->prepare();
if ($stmt) {
    if( $value = $modx->getValue($stmt)){
        $context_key = $value;
    }
}

$modx->initialize($context_key);

//get resource
$criteria = $modx->newQuery('modResource');
$criteria->select(array($modx->escape('modResource').'.*'));
$criteria->where(array('id' => $resource_id, 'deleted' => false, 'published' => true));
$modx->resource = $modx->getObject('modResource', $criteria);

if (!is_object($modx->resource) || !$modx->resource->checkPolicy('view')) {
    echo json_encode($output);
    exit;
}

$modx->resourceIdentifier = $modx->resource->get('id');
$modx->getService('error','error.modError');
$modx->getRequest();
$modx->getParser();
$modx->resourceMethod = 'id';
$modx->resource->_contextKey = $modx->context->get('key');

$modx->invokeEvent('OnLoadWebDocument');

require_once MODX_CORE_PATH . "components/tag_manager2/model/tm_base.class.php";
$tag_manager = new tagManagerBase($modx);

$properties = $tag_manager->getSnippetProperties();

$output['prod_list'] = $modx->runSnippet('tmCatalog', $properties);
$output['prod_list'] .= '<div class="clearfix"></div>';
$output['onPageLimit'] = $limit = isset($_GET['limit']) && is_numeric($_GET['limit'])
    ? intval( $_GET['limit'] )
    : intval( $properties['limit'] );

if(isset($modx->placeholders[$properties['pageNavVar']])){
    $output['pages'] = $modx->placeholders[$properties['pageNavVar']];
    if(isset($modx->sanitizePatterns['tags1'])) $output['pages'] = preg_replace($modx->sanitizePatterns['tags1'], '', $output['pages']);
    if(isset($modx->sanitizePatterns['tags2'])) $output['pages'] = preg_replace($modx->sanitizePatterns['tags2'], '',$output['pages']);
}
if(isset($modx->placeholders['pageCount'])) $output['pageCount'] = $modx->placeholders['pageCount'];
if(isset($modx->placeholders[$properties['totalVar']])) $output['total'] = $modx->placeholders[$properties['totalVar']];

echo json_encode($output);
