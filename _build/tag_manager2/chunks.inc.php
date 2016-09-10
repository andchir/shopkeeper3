<?php

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'tm2_filterNumericTpl',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/tm2_filterNumericTpl.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => '',
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'tm2_filterOuterTpl',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/tm2_filterOuterTpl.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => '',
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'tm2_filterOuterTpl_select',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/tm2_filterOuterTpl_select.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => '',
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'tm2_filterTpl',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/tm2_filterTpl.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => '',
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'tm2_filterTpl_select',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/tm2_filterTpl_select.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => '',
),'',true,true);
$category->addMany($chunk);


