<?php

/**
 * getPackageName
 * 
 */

class getPackageNameProcessor extends modProcessor {

    public function process() {
        
        $scriptProperties = $this->getProperties();
        
        $options = array(
            'parent_id' => $this->modx->getOption('parent_id', $scriptProperties, $this->modx->config['tag_mgr2.catalog_id']),
            'className' => $this->modx->getOption('tag_mgr2.className', null, 'modResource'),
            'packageName' => $this->modx->getOption('tag_mgr2.packageName', null, 'modResource')
        );
        
        $parent_resource = $this->modx->getObject('modResource', $options['parent_id']);
        
        if( $parent_resource ){
            
            $templateObj = $parent_resource->getOne('Template');
            $templateProps = $templateObj->getProperties();
            
            if(!empty($templateProps['prodPackageName'])) $options['packageName'] = $templateProps['prodPackageName'];
            if(!empty($templateProps['prodClassName'])) $options['className'] = $templateProps['prodClassName'];
            
        }
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => $options
        );
        
        return $output;
        
    }
    
}

return 'getPackageNameProcessor';
