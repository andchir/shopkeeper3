<?php
/*

menuCaching 1.0rc1

Сниппет позволяет не генерировать меню для каждой страницы отдельно только ради того чтобы поставить CSS-класс на активный пункт меню.
Меню генерируется только один раз для всего сайта, но активные пункты отмечаются.

Andchir
===========

[[menuCaching?
&snippetToCache=`Wayfinder`
&cacheSuffix=`top_menu`
&contexts=`web`
&startId=`0`
&level=`1`
&hereClass=`active`
&rowTpl=`@CODE: <li class="[[+wf.classnames]] {{active[[+id]]}}"><a href="[[+wf.link]]">[[+wf.linktext]]</a></li>`
&idSwitch=`2==26||3==27`
]]

Рекоммендуется создать набор параметров (например "top_menu")

[[menuCaching@top_menu]]

*/

$output = '';
$parent_id = $modx->getOption('parent_id', $scriptProperties, $modx->resource->get('parent'));
$current_id = $modx->getOption('current_id', $scriptProperties, $modx->resource->get('id'));
$snippetToCache = $modx->getOption('snippetToCache', $scriptProperties, 'Wayfinder');
$cacheSuffix = $modx->getOption('cacheSuffix', $scriptProperties, $parent_id);
$activeKey = $modx->getOption('activeKey', $scriptProperties, $current_id);
$activeAllParents = $modx->getOption('activeAllParents', $scriptProperties, true);
$mcHereClass = $modx->getOption('hereClass', $scriptProperties, 'active');
$idSwitch = $modx->getOption('idSwitch', $scriptProperties, '');

$cacheGroup = 'menuCaching';
$cacheKey = 'menu_'.$cacheSuffix;

$cacheOptions = array(
    xPDO::OPT_CACHE_KEY => $cacheGroup,
    xPDO::OPT_CACHE_HANDLER => 'xPDOFileCache',
);

//Если есть в кэше, берём HTML-код
$cache_out = $modx->cacheManager->get($cacheKey, $cacheOptions);

if($cache_out){
    
    $output = $cache_out;
    
//Если в кэше нет, запускаем сниппет, берём результат и кэшируем
}else{
    
    $scriptProperties['hereClass'] = '';
    $menu_out = $modx->runSnippet($snippetToCache, $scriptProperties);
    
    //Заменяем ID документов, если требуется
    if($idSwitch){
        $idSwitch_arr = explode('||',$idSwitch);
        foreach($idSwitch_arr as $val){
            $temp_arr = explode('==',$val);
            if(count($temp_arr)==2) $menu_out = str_replace("{{active".$temp_arr[0]."}}","{{active".$temp_arr[1]."}}",$menu_out);
        }
    }
    
    $modx->cacheManager->set($cacheKey, $menu_out, 0, $cacheOptions);
    $output = $menu_out;
    
}

$output = str_replace("{{active".$activeKey."}}", $mcHereClass, $output);
//Если активно для всех вложенных документов
if($activeAllParents){
    if(!isset($parents_arr)) $parents_arr = $modx->getParentIds($current_id);
    foreach($parents_arr as $active_key){
        $output = str_replace("{{active".$active_key."}}", $mcHereClass, $output);
    }
}

//Удаляем все лишние теги сниппета
$output = preg_replace("/\{\{active[^\}]+\}\}/si", '', $output);

return $output;