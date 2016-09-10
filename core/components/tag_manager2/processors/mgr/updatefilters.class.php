<?php

/**
 * updateFilters
 * 
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class updateFiltersProcessor extends modProcessor {

    public function process() {
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => array()
        );
        
        //check permissions
        if( !$this->modx->hasPermission('save') ){
            $output['success'] = false;
            $output['message'] = $this->modx->lexicon('permission_denied');
            return $output;
        }
        
        $scriptProperties = $this->getProperties();
        
        $options = array(
            'parent_id' => $this->modx->getOption('parent_id', $scriptProperties, $this->modx->config['tag_mgr2.catalog_id']),
            'clear_cache' => $this->modx->getOption('clear_cache', $scriptProperties, true),
            'context_key' => $this->modx->config['tag_mgr2.catalog_context'],
            'numeric' => $this->modx->getOption('tag_mgr2.numeric', null, ''),
            'guard_key' => $this->modx->getOption('tag_mgr2.guard_key', null, ''),
            'multitags' => $this->modx->getOption('tag_mgr2.multitags', null, ''),
            'prod_template' => $this->modx->getOption('tag_mgr2.prod_templates', null, 0)
        );
        
        $options['prod_template'] = $options['prod_template'] ? explode(',',$options['prod_template']) : array();
        $options['numeric'] = $options['numeric'] ? explode(',',str_replace(', ',',',$options['numeric'])) : array();
        $options['multitags'] = $options['multitags'] ? explode(',',str_replace(', ',',',$options['multitags'])) : array();
        
        $modelpath = $this->modx->config['core_path'].'components/tag_manager2/model/';
        $this->modx->addPackage('tag_manager2', $modelpath);
        
        /**
         * $filters_data
         *
         */
        $filters_data = array( array(), array() );
        
        $response = $this->modx->runProcessor('gettags',
            array(
               'parent_id' => $options['parent_id'],
               'outtype' => 'array'
            ),
            array('processors_path' => $this->modx->getOption( 'core_path' ) . 'components/tag_manager2/processors/mgr/')
        );
        if ($response->isError()) {
            return $response->getMessage();
        }
        if($result = $response->getResponse()){
            $filters_data = $result['object'];
        }
        unset( $response );
        
        
        /* update filters data */
        if( count($filters_data[1]) > 0 ){
            
            foreach($filters_data[1] as $key => &$f_data){
                
                $actual_data = array();
                
                $response = $this->modx->runProcessor('getfilterdata',
                    array(
                       'parent_id' => $options['parent_id'],
                       'tvname' => $f_data['tvname'],
                       'outtype' => 'array'
                    ),
                    array('processors_path' => $this->modx->getOption( 'core_path' ) . 'components/tag_manager2/processors/mgr/')
                );
                if( !$response->isError() && $result = $response->getResponse() ){
                    $actual_data = $result['object'];
                }
                
                $old_data = array();
                
                foreach($f_data['tags'] as $tags){
                    array_push($old_data, $tags['value']);
                }
                
                //if is numeric
                if( in_array( $f_data['tvname'], $options['numeric'] ) ){
                    
                    $new_values = array_diff( $actual_data, $old_data );
                    
                    if(!empty($new_values)){
                        $f_data['tags'] = array(
                            array(
                                "value" => $actual_data[0],
                                "active" => true
                            ),
                            array(
                                "value" => $actual_data[1],
                                "active" => true
                            )
                        );
                    }
                    
                }else{
                    
                    //remove old values
                    $old_values = array_diff( $old_data, $actual_data );
                    if( !empty( $old_values ) ){
                        foreach($old_values as $old_value){
                            
                            $index = array_search( $old_value, $old_data );
                            array_splice( $f_data['tags'], $index, 1 );
                            
                        }
                    }
                    
                    //add new values
                    $new_values = array_diff( $actual_data, $old_data );
                    foreach($new_values as $new_value){
                        array_push($f_data['tags'], array(
                            'value' => $new_value,
                            'active' => 1
                        ));
                    }
                    unset($new_value);
                    
                }
                
                //save data
                if(!empty($old_values) || !empty($new_values)){
                    
                    $item = $this->modx->getObject('tagManager', array(
                        "tvname" => $f_data['tvname'],
                        "category" => $options['parent_id']
                    ));
                    if( is_object($item) ){
                        
                        $item->set('tags', json_encode($f_data['tags']));
                        $item->save();
                        
                    }
                    
                }
                
            }
            
        }
        
        $count = count($filters_data[1]);
        
        //Очистка кэша сайта
        if( $options['parent_id'] && $options['clear_cache'] ){
            $top_parent = $this->modx->getObject('modResource',$options['parent_id']);
            $workingContext = $top_parent->get('context_key');
            $this->modx->cacheManager->refresh(array(
                'resource' => array('contexts' => array($workingContext)),
            ));
        }
        
        $output['object'] = $filters_data;
        
        return $output;
        
    }
    
}

return 'updateFiltersProcessor';
