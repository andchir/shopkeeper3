<?php
/*

shk_render_tv

[[*param1:shk_render_tv=`shk_select`]]

[[*param1:shk_render_tv=`shk_checkbox`]]

[[*param1:shk_render_tv=`shk_radio`]]

[[shk_render_tv?input=`[[+tv.param1]]`&options=`shk_select`&resourceId=`[[+id]]`]]

*/

$input = $modx->getOption('input',$scriptProperties,'');
$options = $modx->getOption('options',$scriptProperties,'shk_select');
$resourceId = $modx->getOption('resourceId',$scriptProperties,$modx->resource->id);
$tv_name = $modx->getOption('name',$scriptProperties,'tv');
$wraptag = $modx->getOption('wraptag',$scriptProperties,'div');
$first_selected = $modx->getOption('first_selected',$scriptProperties,true);

$output = '';

if($input){
    
    $tv = $modx->newObject('modTemplateVar');
    $tv->set('name', $tv_name);
    $tv->set('display', $options);
    $tv->set('value', $input);
    
    $params = array(
        'id' => $resourceId,
        'param_name' => $tv_name,
        'wraptag' => $wraptag,
        'first_selected' => $first_selected,
        'function' => 'SHK.additOpt(this)'
    );
    $outputRenderPaths = $tv->getRenderDirectories('OnTVOutputRenderList','output');
    
    $value = $tv->prepareOutput($input);
    $output = $tv->getRender($params, $value, $outputRenderPaths, 'output', $resourceId, $options);
    
}

return $output;