<?php

//ini_set('display_errors',1);
//error_reporting(E_ALL);

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
//require_once MODX_CONNECTORS_PATH.'index.php';

if( !@require_once (MODX_CORE_PATH . "model/modx/modx.class.php")){
    exit;
}

$modx = new modX();
$modx->initialize('web');
$modx->getRequest();
$modx->request->loadErrorHandler();


function get_filter_categories(){
    
    global $modx;
    
    $modelpath = $modx->config['core_path'].'components/tag_manager2/model/';
    $modx->addPackage('tag_manager2', $modelpath);
    
    $f_categories = array();
    $c = $modx->newQuery("tagManager");
    $c->select(array('id','category'));
    $c->groupby('category');
    if ($c->prepare() && $c->stmt->execute()) {
        
        while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
            
            array_push( $f_categories, $row['category'] );
            
        }
        
    }
    
    return $f_categories;
    
}

$f_categories = get_filter_categories();

foreach( $f_categories as $f_category ){
    
    $response = $modx->runProcessor('updatefilters',
        array(
           'parent_id' => $f_category,
           'clear_cache' => false
        ),
        array('processors_path' => $modx->config['core_path'] . 'components/tag_manager2/processors/mgr/')
    );
    if ($response->isError()) {
        echo $response->getMessage();
        exit;
    }
    
}

echo 'OK';
