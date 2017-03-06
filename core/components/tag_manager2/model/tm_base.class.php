<?php

/**
 * tagManager2
 *
 * tagManagerBase class
 *
 * @author Andchir <andchir@gmail.com>
 * @package tag_manager2
 * @version 2.3
 */

class tagManagerBase {
    
    public $modx = null;
    public $config = array();
    
    /**
     * 
     * @param object $this->modx
     * @param array $config
     */
    function __construct(modX &$modx, $config = array()){
    
        $this->modx =& $modx;
        
        $this->config = array_merge(array(
            
            "className" => $this->modx->getOption('tag_mgr2.className',null,'modResource'),
            "packageName" => $this->modx->getOption('tag_mgr2.packageName',null,'modResource'),
            
            "categoryId" => $this->modx->resource ? $this->modx->resource->get('id') : 0,
            "context" => $this->modx->getoption('tag_mgr2.catalog_context', null, ''),
            "numericFields" => explode(',', $this->modx->getOption('tag_mgr2.numeric',null,'')),
            "multitagsFields" => $this->modx->getOption('tag_mgr2.multitags',null,''),
            "priceName" => $this->modx->getoption('tag_mgr2.priceName', null, 'price'),
            "className" => $this->modx->getOption('tag_mgr2.className',null,'modResource'),
            "packageName" => $this->modx->getOption('tag_mgr2.packageName',null,'modResource'),
            "jsMap" => false,
            "guardKey" => false,
            
            "filterTpl" => "tm2_filterTpl",
            "filterNumericTpl" => "tm2_filterNumericTpl",
            "filterOuterTpl" => "tm2_filterOuterTpl",
            "filterNumericOuterTpl" => "tm2_filterOuterTpl"
            
        ),$config);
        
        $this->config[ 'multitagsFields' ] = explode( ',', $this->config[ 'multitagsFields' ] );
        
    }
    
    
    /**
     * getSnippetProperties
     *
     */
    public function getSnippetProperties( $resource_id = 0 ){
        
        $propertySetName = $this->modx->getOption('tag_mgr2.propertySetName',null,'filters');
        $propertySetSnippet = $this->modx->getOption('tag_mgr2.propertySetSnippet',null,'getPage');
        
        //Ищем имя набора параметров в параметрах шаблона
        if( $resource_id ){
            $resource = $this->modx->getObject('modResource',$resource_id);
            if( $resource ){
                $templateObj = $resource->getOne('Template');
            }
        }else{
            $templateObj = $this->modx->resource->getOne('Template');
        }
        
        $templateProps = $templateObj->getProperties();
        if(!empty($templateProps['prodPropertySetName'])) $propertySetName = $templateProps['prodPropertySetName'];
        
        $propSet = $this->modx->getObject('modPropertySet',array('name'=>$propertySetName));
        $propSetProperties = is_object($propSet) ? $propSet->getProperties() : array();
        
        //Смешиваем с параметрами по умолчанию сниппета
        $snippet = $this->modx->getObject('modSnippet',array('name'=>$propertySetSnippet));
        $defaultProperties = $snippet->getProperties();
        
        if( !empty($propSetProperties['className']) ) $this->config['className'] = $propSetProperties['className'];
        if( !empty($propSetProperties['packageName']) ) $this->config['packageName'] = $propSetProperties['packageName'];
        $properties = array_merge($defaultProperties, $propSetProperties, $this->config);
        
        //Определяем шаблон вывода. Если надо берём номер шаблона из кук
        $view_type = !empty($_COOKIE['tm_view']) ? (int) $_COOKIE['tm_view'] : 1;
        if($view_type && !empty($properties['tpl_list'])){
            $tpl_list = explode(',',$properties['tpl_list']);
            if(isset($tpl_list[$view_type-1])){
                $properties['tpl'] = trim($tpl_list[$view_type-1]);
            }
        }
        
        return $properties;
        
    }

    /**
     * Invokes a specified Event with combine plugins results by returnedValues
     * @param $eventName
     * @param array $params
     * @return array|bool
     */
    public function invokeEventCombine($eventName, array $params= array ()) {
        if (!$eventName)
            return false;
        if ($this->modx->eventMap === null && $this->modx->context instanceof modContext) {
            //$this->modx->_initEventMap($this->modx->context->get('key'));
        }
        if (!isset($this->modx->eventMap[$eventName])) {
            $this->modx->log(modX::LOG_LEVEL_DEBUG,'System event '.$eventName.' was executed but does not exist.');
            return false;
        }
        $this->modx->event->returnedValues = [];
        $results = array ();
        if (count($this->modx->eventMap[$eventName])) {
            $this->modx->event = new modSystemEvent();
            foreach ($this->modx->eventMap[$eventName] as $pluginId => $pluginPropset) {
                $plugin = null;
                $this->modx->Event = & $this->modx->event;

                $this->modx->event->name = '';
                $this->modx->event->_output = '';
                $this->modx->event->activated = false;

                $this->modx->event->name = $eventName;
                if (isset ($this->modx->pluginCache[$pluginId])) {
                    $plugin = $this->modx->newObject('modPlugin');
                    $plugin->fromArray($this->modx->pluginCache[$pluginId], '', true, true);
                    $plugin->_processed = false;
                    if ($plugin->get('disabled')) {
                        $plugin = null;
                    }
                } else {
                    $plugin= $this->modx->getObject('modPlugin', array ('id' => intval($pluginId), 'disabled' => '0'), true);
                }
                if ($plugin && !$plugin->get('disabled')) {
                    $this->modx->event->plugin =& $plugin;
                    $this->modx->event->activated = true;

                    /* merge in plugin properties */
                    $eventParams = array_merge($plugin->getProperties(),$params);

                    $msg = $plugin->process($eventParams);
                    $results[] = $this->modx->event->returnedValues[] = $this->modx->event->_output;
                    if ($msg && is_string($msg)) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[' . $this->modx->event->name . ']' . $msg);
                    } elseif ($msg === false) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[' . $this->modx->event->name . '] Plugin ' . $plugin->name . ' failed!');
                    }
                    $this->modx->event->plugin = null;
                    if (!$this->modx->event->isPropagatable()) {
                        break;
                    }
                }
            }
        }
        $this->modx->event->returnedValues = null;
        return $results;
    }
    
    
}
