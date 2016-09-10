<?php

$lang = 'ru';//$modx->config['manager_language']=='ru' ? 'ru' : 'en';

/*
$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'mod_contacts',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/mod_contacts.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => '',
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'mod_contacts_small',
    'description' => '',
    'snippet' => '',//getSnippetContent($sources['source_core']."elements/chunks/{$lang}/mod_contacts_small.tpl"),
    'static' => 1,
    'source' => 1,
    'static_file' => '/core/components/shopkeeper/elements/chunks/[[++manager_language]]/mod_contacts_small.tpl'
),'',true,true);
$category->addMany($chunk);
*/

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'shopCartRow',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/shopCartRow.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'shopCart',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/shopCart.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'shopOrderForm',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/shopOrderForm.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'shopOrderReport',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/shopOrderReport.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'orderDataOuter',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/orderDataOuter.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'orderDataRow',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/orderDataRow.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'mailContactsRow',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/mailContactsRow.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'select_option',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/select_option.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);
$category->addMany($chunk);

$chunk = $modx->newObject('modChunk');
$chunk->fromArray(array(
    'name' => 'userMail',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core']."elements/chunks/{$lang}/userMail.tpl"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);
$category->addMany($chunk);
