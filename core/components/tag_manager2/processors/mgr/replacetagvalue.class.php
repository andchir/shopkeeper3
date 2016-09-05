<?php

/**
 * replaceTagValue
 * replace all values of tag
 * 
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class replaceTagValueProcessor extends modProcessor {

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
            'context_key' => $this->modx->config['tag_mgr2.catalog_context'],
            'outtype' => $this->modx->getOption('outtype', $scriptProperties, 'json'),
            'field_name' => $this->modx->getOption('field_name', $scriptProperties, ''),
            'field_value' => $this->modx->getOption('field_value', $scriptProperties, ''),
            'old_value' => $this->modx->getOption('old_value', $scriptProperties, ''),
            'prod_template' => $this->modx->getOption('tag_mgr2.prod_templates',null,0)
        );
        
        $options['prod_template'] = $options['prod_template'] ? explode(',',$options['prod_template']) : array();
        
        $modelpath = $this->modx->config['core_path'].'components/tag_manager2/model/';
        $this->modx->addPackage('tag_manager2', $modelpath);
        
        require_once $modelpath . "tm_base.class.php";
        $tag_manager = new tagManagerBase( $this->modx, $scriptProperties );
        
        /**
         * $parents_data
         *
         *
         */
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
        
        
        if($options['field_name'] && $options['field_value']){
            
            $properties = $tag_manager->getSnippetProperties( $options['parent_id'] );
            
            $className = $this->modx->getOption('className', $properties, 'modResource');
            $packageName = $this->modx->getOption('packageName', $properties, '');
            
            //modResource
            if($className == 'modResource'){
                
                $c = $this->modx->newQuery('modTemplateVarResource');
                $c->leftJoin('modTemplateVar','modTemplateVar',array("modTemplateVar.id = modTemplateVarResource.tmplvarid"));
                $c->leftJoin('modResource','modResource',array("`modResource`.`id` = `modTemplateVarResource`.`contentid`"));
                $c->where(
                    array(
                        '`modTemplateVar`.`name`:=' => $options['field_name'],
                        '`modTemplateVarResource`.`value`:=' => $options['old_value'],
                        '`modResource`.`parent`:IN' => $parents_data[0]
                    )
                );
                if(!empty($options['prod_template'])){
                    $c->where(array( "`modResource`.`template`:IN" => $options['prod_template'] ));
                }
                
                if( $this->modx->getCount('modTemplateVarResource',$c) > 0 ){
                    
                    $tags = $this->modx->getCollection('modTemplateVarResource',$c);
                    
                    foreach($tags as $tag){
                        
                        $tag->set('value', $options['field_value']);
                        $tag->save();
                        
                    }
                    
                }
                
            //migxdb
            }else{
                
                $added = $this->modx->addPackage( $packageName, $this->modx->getOption('core_path') . 'components/' . $packageName . '/model/' );
                
                $c = $this->modx->newQuery( $className );
                $c->where(
                    array(
                        $options['field_name'] => $options['old_value'],
                        '`resource_id`:IN' => $parents_data[0]
                    )
                );
                if(!empty($options['prod_template'])){
                    $c->where(array( "`template`:IN" => $options['prod_template'] ));
                }
                
                if( $this->modx->getCount( $className, $c ) > 0 ){
                    
                    $items = $this->modx->getCollection( $className, $c );
                    
                    foreach($items as $item){
                        
                        $item->set( $options['field_name'], $options['field_value'] );
                        $item->save();
                        
                    }
                    
                }
                
            }
            
            $output['success'] = true;
            
        }else{
            
            $output['success'] = false;
            $output['message'] = "Item not find.";
            
        }
        
        return $output;
        
    }
    
}

return 'replaceTagValueProcessor';
