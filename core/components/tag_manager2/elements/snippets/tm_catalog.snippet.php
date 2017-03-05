<?php
/**
 * snippet tmCatalog
 *
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

$config = array(
    'snippet' => $modx->getOption('tag_mgr2.propertySetSnippet',null,'getPage'),
    'elementClass' => 'modSnippet',
    'element' => 'getProducts'
);

$output = '';

require_once MODX_CORE_PATH."components/tag_manager2/model/tm_catalog.class.php";
$tm_catalog = new tmCatalog($modx, $scriptProperties);

//Приводим строку фильтрации к нормальному виду
list($flt_arr, $parents) = $tm_catalog->getRequestParams();

$snippetProperties = $tm_catalog->getSnippetProperties();

$show = isset($_GET['show']) && !is_array($_GET['show']) ? htmlspecialchars(urldecode(trim($_GET['show']))) : '';
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? $_GET['limit'] : $snippetProperties['limit'];
$debug = isset($snippetProperties['debug']) ? $snippetProperties['debug'] : false;
$sortby = isset($_GET['sortby']) && !is_array($_GET['sortby']) ? htmlspecialchars(trim($_GET['sortby'])) : $modx->getOption('sortby',$snippetProperties,'pagetitle');
$sortdir = isset($_GET['sortdir']) && !is_array($_GET['sortdir']) ? htmlspecialchars(trim($_GET['sortdir'])) : $modx->getOption('sortdir',$snippetProperties,'asc');
$orderby = isset($snippetProperties['orderby']) ? json_decode($snippetProperties['orderby'], true) : array(); //берем параметр orderby из настроек, где он хранится в виде {"stock":"DESC"} 
if(strtolower($sortdir)=='rand' || strtolower($sortby)=='rand') { $sortby = 'RAND()'; }

$sorting = $tm_catalog->getSorting($sortby, $sortdir);

/* объединяем параметр $orderby с сортировкой */
$orderby[$sorting['sortby']] = $sorting['sortdir'];
$sorting['orderby'] = json_encode($orderby);

$properties = array_merge($config, $snippetProperties, $sorting);

if(count($flt_arr) > 0){
    if(isset($properties['className']) && $properties['className'] != 'modResource'){
        $where_arr = !empty($properties['where']) ? $modx->fromJSON($properties['where']) : array();
        $where_arr = array_merge($where_arr, $flt_arr);
        $properties['where'] = json_encode($where_arr);
        $properties['tvFilters'] = '';
    }else{
        $properties['tvFilters'] = json_encode($flt_arr);
    }
}

if(count($parents)>0) $properties['parents'] = implode(',',$parents);

//if($debug) echo '<pre>'.print_r($properties,true).'</pre>';

$output .= $modx->runSnippet($config['snippet'], $properties);

if(isset($modx->sanitizePatterns['tags1'])) $output = preg_replace($modx->sanitizePatterns['tags1'], '', $output);
if(isset($modx->sanitizePatterns['tags2'])) $output = preg_replace($modx->sanitizePatterns['tags2'], '', $output);

return $output;
