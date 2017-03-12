<?php
/*

 plugin contextSwitch
 
 System event: OnHandleRequest, OnPageNotFound, OnWebPageComplete

*/

//ini_set( 'display_errors', 1 );
//error_reporting(E_ALL);

if($modx->context->get('key') == 'mgr') return '';

$prodPackageName = $modx->getOption('prodPackageName', $scriptProperties, '');//shop
$prodClassName = $modx->getOption('prodClassName', $scriptProperties, '');//ShopContent
$prodTemplateId = $modx->getOption('prodTemplateId', $scriptProperties, 1);
$debug = $modx->getOption('debug', $scriptProperties, 0);
$cacheOptions = array(
    xPDO::OPT_CACHE_KEY => $modx->getOption('cache_resource_key', $scriptProperties, 'resource'),
    xPDO::OPT_CACHE_HANDLER => $modx->getOption('cache_resource_handler', $scriptProperties, 'xPDOFileCache'),
    xPDO::OPT_CACHE_EXPIRES => (integer) $modx->getOption('cache_resource_expires', $scriptProperties, 0),
    xPDO::OPT_CACHE_FORMAT => (integer) $modx->getOption('cache_resource_format', $scriptProperties, xPDOCacheManager::CACHE_PHP),
    xPDO::OPT_CACHE_ATTEMPTS => (integer) $modx->cacheManager->getOption('cache_resource_attempts', null, 1),
    xPDO::OPT_CACHE_ATTEMPT_DELAY => (integer) $modx->cacheManager->getOption('cache_resource_attempt_delay', null, 1000)
);

switch($modx->event->name){
    
    case "OnHandleRequest":
        
        $cntxt_param = $modx->getOption('context_param_alias', null, 'c');
        $request_param_id = $modx->getOption('request_param_id', null, 'id');
        $friendly_urls = $modx->getOption('friendly_urls', null, true);
        $container_suffix = $modx->getOption('container_suffix', null, '');
        $catalog_id = $modx->getOption('catalog_id',$scriptProperties,0);
        $context_key = $modx->getOption('context_key',$scriptProperties,'');
        $site_start = $modx->getOption('site_start', null, 1);
        $request_uri = substr($_SERVER['REQUEST_URI'],0,1)=='/' ? substr($_SERVER['REQUEST_URI'],1) : $_SERVER['REQUEST_URI'];
        if( strpos( $request_uri, '/' ) !== false ){
            $lang_key = substr( $request_uri, 0, strpos( $request_uri, '/' ) );
        }else{
            $lang_key = $request_uri;
        }
        
        if( !$context_key ) return '';
        
        if( $modx->config['friendly_urls'] ){
            
            if( $request_uri ){
                
                $contentType = $modx->getObject('modContentType',array('name'=>'HTML'));
                $html_ext = $contentType->getExtension();
                
                $base_dir_alias = current( explode( '/', $request_uri ) );
                if( strlen( $container_suffix ) > 1 && strpos( $base_dir_alias, $container_suffix ) === false ){
                    $base_dir_alias .= $container_suffix;
                }
                if( !$container_suffix && $html_ext ){
                    $base_dir_alias .= $html_ext;
                }
                
                $context_child_ids = $modx->getChildIds( 0, 1, array( 'context' => $context_key ) );
                
                //Проверяем нужно ли переключать контекст
                $switch_action = false;
                foreach( $context_child_ids as $id ){
                    $temp_url = basename( $modx->makeURL( $id,$context_key, '', 'abs' ) );
                    if( $temp_url == $base_dir_alias ){
                        $switch_action = true;
                        break;
                    }
                }
                
                if( $switch_action ){
                    $modx->reloadContext( $context_key );
                    $modx->switchContext( $context_key );
                    $modx->config['site_start'] = $site_start;
                    $modx->setPlaceholder( 'context_key', $context_key );
                }
                
            }
            
        }else{
            
            $requestId = !empty($_REQUEST[$request_param_id]) && is_numeric($_REQUEST[$request_param_id]) ? $_REQUEST[$request_param_id] : 0;
            if(!$requestId) return '';
            $resource = $modx->getObject( 'modResource', $requestId );
            if( $resource && $resource->get('context_key') == $context_key ){
                $modx->reloadContext( $context_key );
                $modx->switchContext( $context_key );
                $modx->config['site_start'] = $site_start;
                $modx->setPlaceholder( 'context_key', $context_key );
            }
            
        }
    
    break;
    case "OnPageNotFound":
        
        if(!$prodClassName || $prodClassName=='modResource') break;
        
        $container_suffix = $modx->getOption('container_suffix', null, '');
        $request_param_alias = $modx->getOption('request_param_alias',null,'q');
        $request_param_id = $modx->getOption('request_param_id',null,'id');
        $url = isset($_GET[$request_param_alias]) ? $_GET[$request_param_alias] : '';
        
        $contentType = $modx->getObject('modContentType',array('name'=>'HTML'));
        $html_ext = $contentType->getExtension();

        $short_url = $html_ext
            ? substr( $url, 0, ( 0 - strlen( $html_ext ) ) )
            : $url;
        
        $parent_url = strpos( $short_url, '/' ) !== false
            ? substr( $short_url, 0, strrpos( $short_url, '/' ) ) . $container_suffix
            : '';

        $prod_alias = strpos( $short_url, '/' ) !== false
            ? substr( $short_url, (strrpos( $short_url, '/' )+1) )
            : $short_url;

        $resourceIdentifier = $modx->findResource($parent_url);
        if(!$resourceIdentifier) return '';
        
        $resource = $modx->getObject( 'modResource', array( 'id' => $resourceIdentifier, 'published' => true, 'deleted' => false ) );
        if( !$resource ) return '';
        
        //Определяем параметры шаблона
        $templateObj = $resource->getOne('Template');
        $templateProps = !empty($templateObj) ? $templateObj->getProperties() : array();
        if(!empty($templateProps['prodPackageName'])) $prodPackageName = $templateProps['prodPackageName'];
        if(!empty($templateProps['prodClassName'])) $prodClassName = $templateProps['prodClassName'];
        if(!empty($templateProps['prodTemplateId'])) $prodTemplateId = $templateProps['prodTemplateId'];
        
        $modelpath = $modx->getOption('core_path') . 'components/' . $prodPackageName . '/model/';
        $modx->addPackage($prodPackageName, $modelpath);
        
        $product = $modx->getObject( $prodClassName, array( 'resource_id'=>$resourceIdentifier,'alias'=>$prod_alias,'published'=>true,'deleted'=>false ) );
        
        if($product){
            
            if(!$product->get('template')) $product->set('template',$prodTemplateId);
            $product->set('parent',$resourceIdentifier);
            
            $cacheKey = 'shk_'.$prodPackageName.'/'.$product->get('id');
            
            $cachedResource = $modx->cacheManager->get( $cacheKey, $cacheOptions );
            
            //Вытаскиваем из кэша
            if ( is_array($cachedResource) && array_key_exists('resource', $cachedResource) && is_array($cachedResource['resource']) ) {
                
                $modx->resource = $modx->newObject($cachedResource['resourceClass']);
                
                if ($modx->resource) {
                    
                    $modx->resource->fromArray($cachedResource['resource'], '', true, true, true);
                    $modx->resource->_content = $cachedResource['resource']['_content'];
                    $modx->resource->_isForward = isset($cachedResource['resource']['_isForward']) && !empty($cachedResource['resource']['_isForward']);
                    if( !empty($cachedResource['resource']['_fieldMeta']) ){
                        $modx->resource->_fieldMeta = $cachedResource['resource']['_fieldMeta'];
                    }
                    
                    if (isset($cachedResource['elementCache'])) $modx->elementCache = $cachedResource['elementCache'];
                    if (isset($cachedResource['sourceCache'])) $modx->sourceCache = $cachedResource['sourceCache'];
                    if ($modx->resource->get('_jscripts')) $modx->jscripts = $modx->jscripts + $modx->resource->get('_jscripts');
                    if ($modx->resource->get('_sjscripts')) $modx->sjscripts = $modx->sjscripts + $modx->resource->get('_sjscripts');
                    if ($modx->resource->get('_loadedjscripts')) $modx->loadedjscripts = array_merge($modx->loadedjscripts, $modx->resource->get('_loadedjscripts'));
                    $isForward = $modx->resource->_isForward;
                    $modx->resource->setProcessed(true);
                    $fromCache = true;
                    
                    $modx->invokeEvent('OnLoadWebPageCache');
                    
                }
            
            //Если в кэше нет, создаём новый виртуальный ресурс товара
            }else{
                
                $modx->resource = $modx->newObject('modResource');
                
                ####################################################
                //Просчитываем поля по типам ввода связанных TV
                $product_fields = $product->toArray();
                $field_tv = array();
                
                $sql = "
                SELECT `formtabs`
                FROM `".$modx->config['table_prefix']."migx_configs`
                WHERE `name` = '{$prodPackageName}'
                ";
                $stmt = $modx->prepare($sql);
                if ($stmt && $stmt->execute()) {
                    $migx_config_formtabs = $stmt->fetchColumn();
                    if($migx_config_formtabs){
                        $migx_config_formtabs_arr = json_decode($migx_config_formtabs,true);
                        foreach($migx_config_formtabs_arr as $formtabs){
                            
                            if(!empty($formtabs['fields'])){
                                $temp_fields = !is_array($formtabs['fields'])? json_decode($formtabs['fields'],true) : $formtabs['fields'];
                            }else{
                                $temp_fields = array();
                            }
                            
                            foreach($temp_fields as $temp_field){
                                if(!empty($temp_field['inputTV'])) $field_tv[$temp_field['field']] = $temp_field['inputTV'];
                            }
                        }
                        
                    }
                }
                $stmt->closeCursor();
                
                foreach($field_tv as $field_name => $tv_name){
                    if(isset($product_fields[$field_name])){
                        
                        $tv_object = $modx->getObject('modTemplateVar',array('name'=>$tv_name));
                        if( is_object($tv_object) ){
                            if($tv_object->get('display')=='default') continue;
                            
                            $tv_object->set('name',$field_name);
                            $output_properties = array_merge($tv_object->get('output_properties'),array('param_name'=>$field_name,'id'=>$product_fields['id']));
                            $tv_object->set('output_properties',$output_properties);
                            $tv_object->set('value',$product_fields[$field_name]);
                            
                            $value = $product_fields[$field_name];
                            $value = $tv_object->prepareOutput($value);
                            $outputRenderPaths = $tv_object->getRenderDirectories('OnTVOutputRenderList','output');
                            
                            $value = $tv_object->getRender($output_properties,$value,$outputRenderPaths,'output',$product_fields['id'],$tv_object->get('display'));
                            
                            $product_fields[$field_name] = $value;
                        }
                    }
                }
                ####################################################
                
                $modx->resource->fromArray(array_merge($resource->toArray(),$product_fields));
                $modx->resource->_fieldMeta = array_merge($resource->_fieldMeta,$product->_fieldMeta);
                
                $modx->resource->set('id',$product->get('id'));
                $modx->resource->set('cacheable',false);
                $modx->resource->set('class_key',$prodClassName);
                $modx->resource->_content = '';
                $modx->resource->_output = '';
                $modx->resource->_isForward = true;
                
                $modx->elementCache = array();
                $modx->resourceGenerated = true;
                unset($resource);
                
            }
            
            unset($product);
            
            if($debug){
                echo '<pre>'.print_r($modx->resource->toArray(),true).'</pre>'; exit;
            }
            
            //Просчитываем и выводим HTML-код страницы товара
            $modx->resourceIdentifier = $modx->resource->get('id');
            $modx->resourceMethod = 'id';
            
            $modx->request->prepareResponse();
            
        }
        
    break;

    case "OnWebPageComplete":
        
        if(!$prodClassName || $prodClassName == 'modResource') break;
        
        $results= array();
        if ( is_object($modx->resource) && $modx->resource instanceof modResource && $modx->resource->getProcessed() && $modx->resource->get('id') ) {
            
            if( $modx->resource->class_key != $prodClassName ) break;
            
            $cacheKey = 'shk_'.$prodPackageName.'/'.$modx->resource->get('id');
            
            //$cacheProvider = $modx->cacheManager->getCacheProvider($modx->cacheManager->getOption(xPDO::OPT_CACHE_KEY, $cacheOptions));
            //$cachePath = $cacheProvider->getCacheKey( $cacheKey, $cacheOptions );
            
            $cachedResource = $modx->cacheManager->get( $cacheKey, $cacheOptions );
            
            if ( !is_array( $cachedResource ) || empty( $cachedResource ) ) {
                
                $results['resourceClass'] = $modx->resource->_class;
                $results['resource']['_processed'] = $modx->resource->getProcessed();
                $results['resource'] = $modx->resource->toArray('', true);
                $results['resource']['_content'] = $modx->resource->_content;
                $results['resource']['_isForward'] = $modx->resource->_isForward;
                $results['resource']['_fieldMeta'] = $modx->resource->_fieldMeta;
                if ($contentType = $modx->resource->getOne('ContentType')) {
                    $results['contentType'] = $contentType->toArray('', true);
                }
                $results['resourceGroups']= array();
                $context = $modx->resource->_contextKey ? $modx->resource->_contextKey : 'web';
                $policies = $modx->resource->findPolicy($context);
                if (is_array($policies)) {
                    $results['policyCache'] = $policies;
                }
                if (!empty($modx->elementCache)) {
                    $results['elementCache'] = $modx->elementCache;
                }
                if (!empty($modx->sourceCache)) {
                    $results['sourceCache'] = $modx->sourceCache;
                }
                if (!empty($modx->resource->_sjscripts)) {
                    $results['resource']['_sjscripts'] = $modx->resource->_sjscripts;
                }
                if (!empty($modx->resource->_jscripts)) {
                    $results['resource']['_jscripts'] = $modx->resource->_jscripts;
                }
                if (!empty($modx->resource->_loadedjscripts)) {
                    $results['resource']['_loadedjscripts'] = $modx->resource->_loadedjscripts;
                }
                
                $lifetime = (integer) $modx->cacheManager->getOption('cache_resource_expires', $cacheOptions, $modx->cacheManager->getOption(xPDO::OPT_CACHE_EXPIRES, $cacheOptions, 0));
                
                if (!$modx->cacheManager->set( $cacheKey, $results, $lifetime, $cacheOptions ) ) {
                    $modx->log( modX::LOG_LEVEL_ERROR, "[contextSwitch] Could not cache resource " . $modx->resource->get('id') );
                }
                
            }
            
            unset($cachedResource);
            
        }
        
    break;

}

return '';