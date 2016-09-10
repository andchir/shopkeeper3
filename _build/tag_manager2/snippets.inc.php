<?php

$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'tmFilters',
    'description' => 'Print filters blocks for catalog.',
    'snippet' => getSnippetContent($sources['source_core']."elements/snippets/tm_filters.snippet.php"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);

$properties = array(
    /*
    array(
        "name" => "innerTpl",
        "desc" => "",
        "xtype" => "textfield",
        "options" => "",
        "value" => "@FILE core/components/tag_manager/elements/chunks/innerTpl.tpl",
        "lexicon" => "tag_manager:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    )
    */
);

$snippet->setProperties($properties);
$category->addMany($snippet);


$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'tmCatalog',
    'description' => 'Оutput of productы in the catalog.',
    'snippet' => getSnippetContent($sources['source_core']."elements/snippets/tm_catalog.snippet.php"),
    'static' => 0,
    'source' => 1,
    'static_file' => ''
),'',true,true);

$properties = array();

$snippet->setProperties($properties);
$category->addMany($snippet);

