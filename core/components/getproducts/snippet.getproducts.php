<?php
/**
 * getProducts 1.4rc2
 *
 * Snippet for a print list of resources and objects from any tables. For MODX 2.x.
 *
 * @author Andchir <andchir@gmail.com>
 * @copyright Copyright 2015 http://modx-shopkeeper.ru/
/

/*

getProducts + getPage:

[[!getPage?
&elementClass=`modSnippet`
&element=`getProducts`
&parents=`10`
&limit=`20`
&tvFilters=`{"param":"value"}`
&where=`{"param":"value"}`
&includeTVs=`1`
&includeTVList=`price,image,width,inventory`
&tpl=`product`
&pageFirstTpl=`<li class="control"><a [[+classes]] href="[[+href]]">Первая</a></li>`
&pageLastTpl=`<li class="control"><a [[+classes]] href="[[+href]]">Последняя</a></li>`
]]
<br class="clear" />
<ul class="pages">
[[!+page.nav]]
</ul>
[[+total]]

*/

if( !empty( $scriptProperties['debug'] ) ){
    ini_set('display_errors',1);
    error_reporting(E_ALL);
}

$cached = array();

//Настройки кэширования
if(!empty($scriptProperties['gp_cache'])){
    
    if(empty($scriptProperties['cache_key'])) $scriptProperties['cache_key'] = $modx->getOption('cache_resource_key', null, 'resource');
    if(empty($scriptProperties['cache_checkURL'])) $scriptProperties['cache_checkURL'] = false;//Не рекомендуется менять значение
    if(empty($scriptProperties['cacheId'])) $scriptProperties['cacheId'] = 'gpCache';
    if(empty($scriptProperties['cache_handler'])) $scriptProperties['cache_handler'] = $modx->getOption('cache_resource_handler', null, 'xPDOFileCache');
    if(empty($scriptProperties['cache_expires'])) $scriptProperties['cache_expires'] = 0;
    
    if($scriptProperties['cache_checkURL']){
        $scriptProperties['cachePageKey'] = $modx->resource->getCacheKey() . '/' . $scriptProperties['cacheId'] . md5(http_build_query($modx->request->getParameters()));
    }else{
        $scriptProperties['cachePageKey'] = $scriptProperties['cacheId'];
    }
    $scriptProperties['cacheOptions'] = array(
        xPDO::OPT_CACHE_KEY => $scriptProperties['cache_key'],
        xPDO::OPT_CACHE_HANDLER => $scriptProperties['cache_handler'],
        xPDO::OPT_CACHE_EXPIRES => $scriptProperties['cache_expires'],
    );
    
    $cached = $modx->cacheManager->get($scriptProperties['cachePageKey'], $scriptProperties['cacheOptions']);
    
    //Если есть в кэше, выводим его содержимое
    if(!empty($cached) && isset($cached['placeholders']) && isset($cached['output'])){
        
        $output = $cached['output'];
        $modx->setPlaceholders($cached['placeholders']);
        
        if($toPlaceholder){
            $modx->setPlaceholder($toPlaceholder,$output);
            $output = '';
        }
        
        return $output;
        
    }else{
        
        $cached = array();
        
    }
    
}

$output = '';

$checkPlaceholders = $modx->placeholders;

require_once MODX_CORE_PATH.'components/getproducts/model/getproducts.class.php';
$getProducts = new getProducts($modx,$scriptProperties);

$noResults = $modx->getOption('noResults',$scriptProperties,'');
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,'');
$returnIDs = $modx->getOption('returnIDs',$scriptProperties,false);
$totalVar = $modx->getOption('totalVar', $scriptProperties, 'total');
$debug = $modx->getOption('debug', $scriptProperties, false);

$parents_data = array();

//Ищем товары по заданным условиям
$getProducts->searchProducts();

$total = $getProducts->getTotal();
$modx->setPlaceholder( $totalVar, $total );
if( !$total ) return $noResults;
if( $returnIDs ) return implode( ',', $getProducts->ids_arr );

//Собираем TV
$getProducts->appendTVs();

//Вытаскиваем данные от родителей, если нужно
$getProducts->appendFromParents();

//Создаём HTML код по шаблону
$output .= $getProducts->getHTMLOutput();

//Кэшируем
if(!empty($scriptProperties['gp_cache'])){
    
    $cached = array(
        'output' => $output,
        'placeholders' => array_diff_assoc($modx->placeholders, $checkPlaceholders)
    );
    unset($checkPlaceholders);
    
    $modx->cacheManager->set($scriptProperties['cachePageKey'], $cached, $scriptProperties['cache_expires'], $scriptProperties['cacheOptions']);
    
}

if($toPlaceholder){
    $modx->setPlaceholder($toPlaceholder,$output);
    $output = '';
}

return $output;