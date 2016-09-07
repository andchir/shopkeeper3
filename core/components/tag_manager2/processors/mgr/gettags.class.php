<?php

/**
 * getTags
 * 
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class getTagsProcessor extends modProcessor {

    public function process() {
        
        $scriptProperties = $this->getProperties();
        
        $options = array(
            'parent_id' => $this->modx->getOption('parent_id', $scriptProperties, $this->modx->config['tag_mgr2.catalog_id']),
            'context_key' => $this->modx->config['tag_mgr2.catalog_context'],
            'prod_template' => $this->modx->getOption('tag_mgr2.prod_templates',null,0),
            'get_unsaved' => $this->modx->getOption('get_unsaved', $scriptProperties, true)
        );
        
        $options['prod_template'] = $options['prod_template'] ? explode(',',$options['prod_template']) : array();
        
        $modelpath = $this->modx->config['core_path'].'components/tag_manager2/model/';
        $this->modx->addPackage('tag_manager2', $modelpath);
        
        $saved_tags = array();
        $tvs_data_all = array();
        $tvs_data_all_names = array();
        
        /**
         * $saved_tags
         *
         *
         */
        $c = $this->modx->newQuery('tagManager');
        $c->where(array( 'category'=> strval($options['parent_id']) ));
        $c->select(array('id','category','tvid','tvname','tvcaption','tags','index'));
        $c->sortby('`index`','asc');
        
        if ($c->prepare() && $c->stmt->execute()) {
            
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                
                $row['tags'] = json_decode($row['tags'],true);
                array_push($saved_tags, $row);
                
                array_push($tvs_data_all, array(
                    'id' => $row['tvid'],
                    'tvname' => $row['tvname'],
                    'tvcaption' => $row['tvcaption'],
                    'active' => true
                ));
                array_push($tvs_data_all_names, $row['tvname']);
                
            }
            $c->stmt->closeCursor();
            
        }
        
        /**
         * get_unsaved
         *
         */
        if($options['get_unsaved']){
            
            /* $parents_data */
            $parents_data = array();
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
            unset($response);
            
            list( $className, $packageName ) = $this->getPackageName( $options['parent_id'] );
            
            if(!empty($parents_data[0])){
            
                /* modResource */
                if(!$packageName || $packageName == 'modResource'){
                
                    /**
                    * $tvs_data_all
                    */
                    $c = $this->modx->newQuery("modTemplateVar");
                    $c->leftJoin( "modTemplateVarResource", "tvr", "tvr.tmplvarid = modTemplateVar.id" );
                    $c->leftJoin( "modResource", "rc", "tvr.contentid = rc.id" );
                    $c->where(array( "`rc`.`parent`:IN" => $parents_data[0] ));
                    if(!empty($options['prod_template'])){
                        $c->where(array( "`rc`.`template`:IN" => $options['prod_template'] ));
                    }
                    if(!empty($tvs_data_all_names)) $c->where(array( "modTemplateVar.name:NOT IN" => $tvs_data_all_names ));
                    $c->select("modTemplateVar.id, modTemplateVar.name AS `tvname`, modTemplateVar.caption AS `tvcaption`");
                    $c->groupby('modTemplateVar.name');
                    $c->sortby('modTemplateVar.rank','asc');
                    
                    if ($c->prepare() && $c->stmt->execute()) {
                        
                        while($row = $c->stmt->fetch(PDO::FETCH_ASSOC)){
                            
                            if(!empty($row['tvcaption'])){
                                array_push($tvs_data_all, $row);
                            }
                            
                        }
                        $c->stmt->closeCursor();
                        
                    }
                    
                /* not resources */
                }else{
                    
                    $this->modx->addPackage('migx', $this->modx->getOption('core_path') . 'components/migx/model/');
                    $migx_config = $this->modx->getObject('migxConfig',array( 'name' => $packageName, 'deleted' => 0 ));
                    
                    if(is_object($migx_config)){
                        
                        $migx_conf_str = $migx_config->get('formtabs');
                        $migx_conf_arr = $this->modx->fromJSON($migx_conf_str);
                        
                        foreach($migx_conf_arr as $migx_data){
                            
                            if(is_string($migx_data['fields'])) $migx_data['fields'] = $this->modx->fromJSON($migx_data['fields']);
                            
                            foreach($migx_data['fields'] as $fields){
                                
                                if( in_array( $fields['field'], $tvs_data_all_names ) ) continue;
                                
                                //if( !in_array( $fields['field'], array('pagetitle','alias','introtext','template','content') ) ){
                                    
                                    array_push(
                                        $tvs_data_all,
                                        array(
                                            'id' => (isset($fields['MIGX_id']) ? $fields['MIGX_id'] : ''),
                                            'tvname' => $fields['field'],
                                            'tvcaption' => $fields['caption']
                                        )
                                    );
                                    
                                //}
                                
                            }
                            
                        }
                        
                    }
                    
                }
                
            }
            
        }
        
        $count = count($saved_tags);
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => array($tvs_data_all, $saved_tags)
        );
        
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

return 'getTagsProcessor';
