<?php

$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'Shopkeeper3',
    'description' => 'Shopping cart',
    'snippet' => getSnippetContent($sources['source_core'].'elements/snippets/shopkeeper.php'),
    'static' => 0,
    'source' => 1
),'',true,true);
$properties = include $sources['data'].'properties.inc.php';
$snippet->setProperties($properties);
$category->addMany($snippet);

$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'shk_fihook',
    'description' => 'FormIt hook for Shopkeeper',
    'snippet' => getSnippetContent($sources['source_core'].'elements/snippets/shk_fihook.php'),
    'static' => 0,
    'source' => 1
),'',true,true);
$category->addMany($snippet);

$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'shkOptions',
    'description' => 'Print configuration of Shopkeeper',
    'snippet' => getSnippetContent($sources['source_core'].'elements/snippets/snippet.shkOptions.php'),
    'static' => 0,
    'source' => 1
),'',true,true);
$category->addMany($snippet);


$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'num_format',
    'description' => 'Number format output filter',
    'snippet' => getSnippetContent($sources['source_core'].'elements/snippets/snippet.num_format.php'),
    'static' => 0,
    'source' => 1
),'',true,true);
$category->addMany($snippet);

/*
$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'shk_include',
    'description' => 'Include snippets from PHP files',
    'snippet' => '',
    'static' => 1,
    'source' => 1,
    'static_file' => '/core/components/shopkeeper/elements/snippets/include.php'
),'',true,true);
$category->addMany($snippet);
*/

/*
$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'userOrders',
    'description' => 'Displays a list of customer orders',
    'snippet' => '',
    'static' => 1,
    'source' => 1,
    'static_file' => '/core/components/shopkeeper/elements/snippets/snippet.user_orders.php'
),'',true,true);
$category->addMany($snippet);
*/


$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'shk_curr_rate',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'elements/snippets/snippet.shk_curr_rate.php'),
    'static' => 0,
    'source' => 1
),'',true,true);
$category->addMany($snippet);

$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'param_edit_table',
    'description' => 'Print options of product (param-edit)',
    'snippet' => getSnippetContent($sources['source_core'].'elements/snippets/param_edit_table.php'),
    'static' => 0,
    'source' => 1
),'',true,true);
$category->addMany($snippet);

$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'shk_sitemap',
    'description' => 'Create sitemap.xml for catalog',
    'snippet' => getSnippetContent($sources['source_core'].'elements/snippets/snippet.sitemap.php'),
    'static' => 0,
    'source' => 1
),'',true,true);
$category->addMany($snippet);

$snippet = $modx->newObject('modSnippet');
$snippet->fromArray(array(
    'name' => 'shk_render_tv',
    'description' => 'Render parameters for products as checkbox, radio, select.',
    'snippet' => getSnippetContent($sources['source_core'].'elements/snippets/snippet.shk_render_tv.php'),
    'static' => 0,
    'source' => 1
),'',true,true);
$category->addMany($snippet);
