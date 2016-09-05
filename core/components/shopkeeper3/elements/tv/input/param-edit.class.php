<?php
/**
 * @package modx
 * @subpackage processors.element.tv.renders.mgr.input
 */

class modTemplateVarInputRenderParamEdit extends modTemplateVarInputRender {
    
    public function process($value,array $params = array()) {
        
        $resource_id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : 0);
        if(!$resource_id && isset($_POST['ident'])) $resource_id = $_POST['ident'];
        $this->setPlaceholder('resource_tv_id',$this->tv->get('id').'_'.$resource_id);
        $this->setPlaceholder('tv_source',$this->tv->get('source'));
        
        //get mediaSource baseUrl
        $mediaSource = $this->modx->getObject('modMediaSource',$this->tv->get('source'));
        $source_baseUrl = '';
        if(is_object($mediaSource)){
            $properties = $mediaSource->get('properties');
            $source_baseUrl = isset($properties['baseUrl']) ? $properties['baseUrl']['value'] : '';
        }
        $this->setPlaceholder('tv_source_base_url',$source_baseUrl);
        
        //$this->modx->smarty->left_delimiter = '{{';
        //$this->modx->smarty->right_delimiter = '}}';
    }
    
    public function getTemplate() {
        return MODX_CORE_PATH.'components/shopkeeper3/elements/tv/tpl/input_param-edit.tpl';
    }
}
return 'modTemplateVarInputRenderParamEdit';