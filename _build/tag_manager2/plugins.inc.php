<?php

$plugins = array();

/* plugin */
$plugins[0]= $modx->newObject('modPlugin');
$plugins[0]->fromArray(array(
    'id' => 1,
    'name' => 'tmRefresh',
    'description' => 'Refresh tagManager filters data.',
    'plugincode' => '',
    'static' => 1,
    'source' => 1,
    'static_file' => 'core/components/tag_manager2/elements/plugins/plugin.tm_refresh.php',
    'disabled' => 1
),'',true,true);

$events = array();

$events['OnDocFormSave'] = $modx->newObject('modPluginEvent');
$events['OnDocFormSave']->fromArray(array(
    'event' => 'OnDocFormSave',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnCacheUpdate'] = $modx->newObject('modPluginEvent');
$events['OnCacheUpdate']->fromArray(array(
    'event' => 'OnCacheUpdate',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$plugins[0]->addMany($events);

$properties = array();
$plugins[0]->setProperties($properties);
unset($events,$properties);

return $plugins;
