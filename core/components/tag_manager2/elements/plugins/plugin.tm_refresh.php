<?php
/**
 * tmRefresh
 *
 * System event: OnDocFormSave, OnCacheUpdate
 *
 */

$eventName = $modx->event->name;

if( !function_exists( 'get_filter_categories' ) ){
    
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
    
}

if( $eventName == 'OnDocFormSave' ){

    $page_id = $resource->get('id');
    $page_parent = $resource->get('parent');
    $isfolder = $resource->get('isfolder');
    $catalog_root = $modx->getOption('tag_mgr2.catalog_id', null, 0);
    
    if( !$isfolder ){
        
        $f_categories = get_filter_categories();
        
        if( in_array( $page_parent, $f_categories ) ){
            
            $response = $modx->runProcessor('updatefilters',
                array(
                   'parent_id' => $page_parent,
                   'clear_cache' => false
                ),
                array('processors_path' => $modx->config['core_path'] . 'components/tag_manager2/processors/mgr/')
            );
            if ($response->isError()) {
                return $response->getMessage();
            }
            
        }
        
    }
    
}else if( $eventName == 'OnCacheUpdate' ){
    
    //run in background
    @exec( "php -f '" . $modx->config['assets_path'] . "components/tag_manager2/cron_updatefilters.php' > /dev/null 2>&1 &" );
    
}

//$modx->event->output('');