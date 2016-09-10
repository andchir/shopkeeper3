<?php

/**
 * getFilterData
 * Уникальные значения для одного поля (или TV-параметра)
 * 
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class getFilterDataProcessor extends modProcessor {

    public function process() {
        
        $scriptProperties = $this->getProperties();
        
        $options = array(
            'parent_id' => $this->modx->getOption('parent_id', $scriptProperties, $this->modx->config['tag_mgr2.catalog_id']),
            'context_key' => $this->modx->config['tag_mgr2.catalog_context'],
            'tvname' => $this->modx->getOption('tvname', $scriptProperties, ''),
            'numeric' => $this->modx->getOption('tag_mgr2.numeric', null, ''),
            'guard_key' => $this->modx->getOption('tag_mgr2.guard_key', null, ''),
            'multitags' => $this->modx->getOption('tag_mgr2.multitags', null, ''),
            'prod_template' => $this->modx->getOption('tag_mgr2.prod_templates', null, 0),
            'get_child_parents' => $this->modx->getOption('get_child_parents', $scriptProperties, true),
            'outtype' => $this->modx->getOption('outtype', $scriptProperties, 'tv_data_values')
        );
        
        $options['tvname_arr'] = explode(',',$options['tvname']);
        $options['prod_template'] = $options['prod_template'] ? explode(',',$options['prod_template']) : array();
        $options['numeric'] = $options['numeric'] ? explode(',',str_replace(', ',',',$options['numeric'])) : array();
        $options['multitags'] = $options['multitags'] ? explode(',',str_replace(', ',',',$options['multitags'])) : array();
        
        $modelpath = $this->modx->config['core_path'].'components/tag_manager2/model/';
        $this->modx->addPackage('tag_manager2', $modelpath);
        
        if(empty($options['tvname'])){
            
            return $this->failure();
            
        }
        
        /**
         * $is_numeric
         *
         */
        $is_numeric = false;
        if(!empty($options['numeric']) && in_array($options['tvname'],$options['numeric'])){
            $is_numeric = true;
        }
        
        /**
         * $is_multitag
         *
         */
        $is_multitag = false;
        if(!empty($options['multitags']) && in_array($options['tvname'],$options['multitags'])){
            $is_multitag = true;
        }
        
        /**
         * $parents_data
         *
         *
         */
        $parents_data = array();
        
        if( $options['get_child_parents'] ){
            
            $response = $this->modx->runProcessor('getparentslist',
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
                $parents_data = $result['object'];
            }
            
        }else{
            $parents_data = array( array( $options['parent_id'] ), array() );
        }
        
        /* fields values */
        
        list( $className, $packageName ) = $this->getPackageName( $options['parent_id'] );
        
        $tv_data_values = array();
        $values_arr = array();
        
        if(!empty($parents_data[0])){
        
            /* modResource */
            if(!$packageName || $packageName == 'modResource'){
                
                $c = $this->modx->newQuery("modTemplateVarResource");
                $c->leftJoin( "modTemplateVar", "tv", "tv.id = modTemplateVarResource.tmplvarid" );
                $c->leftJoin( "modResource", "rc", "rc.id = modTemplateVarResource.contentid" );
                $c->where(
                    array(
                        "tv.name:IN" => $options['tvname_arr'],
                        "rc.parent:IN" => $parents_data[0]
                    )
                );
                if(!empty($options['prod_template'])){
                    $c->where( array("rc.template:IN" => $options['prod_template']) );
                }
                $c->select("modTemplateVarResource.tmplvarid, modTemplateVarResource.contentid, modTemplateVarResource.value");
                $c->sortby('modTemplateVarResource.id','asc');
            
            /* not resources */
            }else{
                
                $modelpath = $this->modx->getOption('core_path') . "components/{$packageName}/model/";
                $added = $this->modx->addPackage($packageName, $modelpath);
                
                $c = $this->modx->newQuery($className);
                $c->where(
                    array(
                        "resource_id:IN" => $parents_data[0]
                    )
                );
                if(!empty($options['prod_template'])){
                    $c->where(array( "template:IN" => $options['prod_template'] ));
                }
                $c->select($options['tvname']. " AS `value` ");
                
            }
            
            if ($c->prepare() && $c->stmt->execute()) {
                
                while($row = $c->stmt->fetch(PDO::FETCH_ASSOC)){
                    
                    //echo '<pre>' . print_r($row,true) . '</pre>';
                    
                    if($row['value'] && !in_array($row['value'],$values_arr)){
                        
                        if($is_multitag){
                            $row['value'] = $row['value'] ? explode('||',str_replace($options['guard_key'],'',$row['value'])) : array();
                            foreach($row['value'] as $val){
                                if(!in_array($val,$values_arr)){
                                    array_push($values_arr, $val);
                                }
                            }
                        }else if($is_numeric){
                            $val = floatval(str_replace(',','.',$row['value']));
                            if(!in_array($val,$values_arr)){
                                array_push($values_arr, $val);
                            }
                        }else{
                            if(!in_array($row['value'],$values_arr)){
                                array_push($values_arr, $row['value']);
                            }
                        }
                        
                    }
                    
                }
                $c->stmt->closeCursor();
                
            }
        
        }
        
        //Если числовое, то берём только минимальное и максимально значения
        if($is_numeric){
            
            if(!empty($values_arr)){
                
                $tv_data_values = array(
                    array(
                        "value" => min($values_arr),
                        "active" => true
                    ),
                    array(
                        "value" => max($values_arr),
                        "active" => true
                    )
                );
                
                $values_arr = array( min($values_arr), max($values_arr) );
                
            }
            
        }else{
            
            foreach($values_arr as $val){
                array_push($tv_data_values,
                    array(
                        "value" => $val,
                        "active" => true
                    )
                );
            }
            
        }
        
        //sorting
        if( !function_exists( 'cmp_a' ) ){
            function cmp_a($a, $b) {
                if( is_numeric($a["value"]) && is_numeric($b["value"]) ){
                    return $a["value"] == $b["value"] ? 0 : ( ($a["value"] < $b["value"]) ? -1 : 1 );
                }
                return strcmp($a["value"], $b["value"]);
            }
        }
        usort( $tv_data_values, "cmp_a" );
        
        $count = count($tv_data_values);
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => $options['outtype'] == 'tv_data_values' ? $tv_data_values : $values_arr
        );
        
        //return $options['outtype'] == 'json' ? $this->outputArray( $filters_data, $count ) : $this->success( $filters_data );
        
        return $output;
        
    }
    
    
    /**
     * getPackageName
     *
     */
    function getPackageName( $parent_id ){
        
        $className = '';
        $packageName = '';
        $response = $this->modx->runProcessor('getpackagename',
            array( 'parent_id' => $parent_id ),
            array( 'processors_path' => $this->modx->getOption( 'core_path' ) . 'components/tag_manager2/processors/mgr/' )
        );
        if(!$response->isError()){
            $response_data = $response->getResponse();
            if( is_array($response_data['object'] )){
                
                if(!empty($response_data['object']['className'])) $className = $response_data['object']['className'];
                if(!empty($response_data['object']['packageName'])) $packageName = $response_data['object']['packageName'];
                
            }
        }
        
        return array( $className, $packageName );
        
    }
    
}

return 'getFilterDataProcessor';
