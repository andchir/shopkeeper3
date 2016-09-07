<?php

/**
 * saveFilters
 * 
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class saveFiltersProcessor extends modProcessor {

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
            'data' => $this->modx->getOption('data', $scriptProperties, ''),
            'outtype' => $this->modx->getOption('outtype', $scriptProperties, 'json')
        );
        
        $modelpath = $this->modx->config['core_path'].'components/tag_manager2/model/';
        $this->modx->addPackage('tag_manager2', $modelpath);
        
        $saved_names = array();
        
        if(!empty($options['data'])){
            
            $options['data'] = json_decode($options['data'], true);
            
            foreach($options['data'] as $key => $filter){
                
                $item = $this->modx->getObject('tagManager', array(
                    "tvname" => $filter['tvname'],
                    "category" => $options['parent_id']
                ));
                
                //var_dump(empty($item),$filter['tvname'],$options['parent_id'],$filter['tags']); continue;
                
                if(!$item){
                    
                    $item = $this->modx->newObject('tagManager');
                    
                }
                
                $item->set('tvid', $filter['id']);
                $item->set('tvname', $filter['tvname']);
                $item->set('tvcaption', $filter['tvcaption']);
                $item->set('category', $options['parent_id']);
                $item->set('index', $key);
                $item->set('tags', json_encode($filter['tags']));
                $saved = $item->save();
                
                if($saved) array_push($saved_names, $filter['tvname']);
                
            }
            
            if( !empty( $saved_names ) ){
                
                $result = $this->modx->removeCollection('tagManager', array(
                    "tvname:NOT IN" => $saved_names,
                    "category" => $options['parent_id']
                ));
                
            }
            
            //Очистка кэша сайта
            if($options['parent_id']){
                $top_parent = $this->modx->getObject('modResource',$options['parent_id']);
                $workingContext = $top_parent->get('context_key');
                $this->modx->cacheManager->refresh(array(
                    'resource' => array('contexts' => array($workingContext)),
                ));
            }
            
        }else{
            
            $output['success'] = false;
            
        }
        
        return $output;
        
    }
    
}

return 'saveFiltersProcessor';
