<?php

/**
 * removeFilters
 * 
 */

class removeFiltersProcessor extends modProcessor {

    public function process() {
        
        $output = array(
            'success' => false,
            'message' => 'Item not find.',
            'object' => array()
        );
        
        //check permissions
        if( !$this->modx->hasPermission('remove') ){
            $output['success'] = false;
            $output['message'] = $this->modx->lexicon('permission_denied');
            return $output;
        }
        
        $scriptProperties = $this->getProperties();
        
        $options = array(
            'parent_id' => $this->modx->getOption('parent_id', $scriptProperties, false),
            'context_key' => $this->modx->config['tag_mgr2.catalog_context'],
            'outtype' => $this->modx->getOption('outtype', $scriptProperties, 'json')
        );
        
        if($options['parent_id'] !== false){
            
            $modelpath = $this->modx->config['core_path'].'components/tag_manager2/model/';
            $this->modx->addPackage('tag_manager2', $modelpath);
            
            $remove_where = $options['parent_id'] == 'all' ? null : array('category' => $options['parent_id']);
            
            $removed = $this->modx->removeCollection("tagManager", $remove_where);
            
            if($removed){
                
                $output['success'] = true;
                $output['message'] = '';
                
            }
            
        }
        
        return $output;
        
    }
    
}

return 'removeFiltersProcessor';
