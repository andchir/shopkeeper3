<?php

$plugins = array();

/* plugin */
$plugins[0]= $modx->newObject('modPlugin');
$plugins[0]->fromArray(array(
    'id' => 1,
    'name' => 'shk_contextSwitch',
    'description' => 'Switch to catalog context.',
    'plugincode' => getSnippetContent($sources['source_core'].'elements/plugins/context_switch.php'),
    'static' => 0,
    'source' => 1
),'',true,true);

$events = array();

$events['OnHandleRequest'] = $modx->newObject('modPluginEvent');
$events['OnHandleRequest']->fromArray(array(
    'event' => 'OnHandleRequest',
    'priority' => 1,
    'propertyset' => 0,
),'',true,true);

$events['OnPageNotFound'] = $modx->newObject('modPluginEvent');
$events['OnPageNotFound']->fromArray(array(
    'event' => 'OnPageNotFound',
    'priority' => 2,
    'propertyset' => 0,
),'',true,true);

$events['OnWebPageComplete'] = $modx->newObject('modPluginEvent');
$events['OnWebPageComplete']->fromArray(array(
    'event' => 'OnWebPageComplete',
    'priority' => 3,
    'propertyset' => 0,
),'',true,true);

$plugins[0]->addMany($events);
$properties = array(
    array(
        'name' => 'context_param_alias',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => 'c',
    ),array(
        'name' => 'request_param_id',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => 'id',
    ),array(
        'name' => 'catalog_id',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => '0',
    ),array(
        'name' => 'context_key',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => 'catalog',
    ),array(
        'name' => 'site_start',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => '1',
    ),array(
        'name' => 'prodPackageName',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),array(
        'name' => 'prodClassName',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),array(
        'name' => 'prodTemplateId',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => '1',
    )
);
$plugins[0]->setProperties($properties);
unset($events,$properties);


/* plugin */
$plugins[1]= $modx->newObject('modPlugin');
$plugins[1]->fromArray(array(
    'id' => 1,
    'name' => 'shk_updateInventory',
    'description' => 'Update inventory data.',
    'plugincode' => getSnippetContent($sources['source_core'].'elements/plugins/update_inventory.php'),
    'static' => 0,
    'source' => 1,
    'disabled' => 1
),'',true,true);
$events = array();
$events['OnSHKChangeStatus'] = $modx->newObject('modPluginEvent');
$events['OnSHKChangeStatus']->fromArray(array(
    'event' => 'OnSHKChangeStatus',
    'priority' => 1,
    'propertyset' => 0,
),'',true,true);
$plugins[1]->addMany($events);
$properties = array(
    array(
        'name' => 'plugin_status',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => '2'
    ),array(
        'name' => 'inventory_fieldname',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => 'inventory'
    ),array(
        'name' => 'context',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => 'web'
    )
);
$plugins[1]->setProperties($properties);
unset($events);

/* plugin */
/*
$plugins[2]= $modx->newObject('modPlugin');
$plugins[2]->fromArray(array(
    'id' => 1,
    'name' => 'shk_delivery',
    'description' => 'Add delivery data to order.',
    'plugincode' => '',
    'static' => 1,
    'source' => 1,
    'static_file' => '/core/components/shopkeeper/elements/plugins/delivery.php'
),'',true,true);
$events = array();
$events['OnSHKbeforeCartLoad'] = $modx->newObject('modPluginEvent');
$events['OnSHKbeforeCartLoad']->fromArray(array(
    'event' => 'OnSHKbeforeCartLoad',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$plugins[2]->addMany($events);
$properties = array(
    array(
        'name' => 'tpl',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => '@INLINE <option value="[[+name]]" [[+selected]]>[[+name]] [[+price:gt=`0`:then=`([[+price]] [[++shk.currency]])`]]</option>',
    ),array(
        'name' => 'deliveryName',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => 'Доставка',
    )
);
$plugins[2]->setProperties($properties);
unset($events,$properties);
*/


/* plugin */
$plugins[3]= $modx->newObject('modPlugin');
$plugins[3]->fromArray(array(
    'id' => 1,
    'name' => 'shk_tv_input_output',
    'description' => 'Print Shopkeeper`s input and output types for TV.',
    'plugincode' => getSnippetContent($sources['source_core'].'elements/plugins/tv_input_output.php'),
    'static' => 0,
    'source' => 1
),'',true,true);

$events = array();

$events['OnTVInputRenderList'] = $modx->newObject('modPluginEvent');
$events['OnTVInputRenderList']->fromArray(array(
    'event' => 'OnTVInputRenderList',
    'priority' => 1,
    'propertyset' => 0,
),'',true,true);

$events['OnTVInputPropertiesList'] = $modx->newObject('modPluginEvent');
$events['OnTVInputPropertiesList']->fromArray(array(
    'event' => 'OnTVInputPropertiesList',
    'priority' => 2,
    'propertyset' => 0,
),'',true,true);

$events['OnTVOutputRenderList'] = $modx->newObject('modPluginEvent');
$events['OnTVOutputRenderList']->fromArray(array(
    'event' => 'OnTVOutputRenderList',
    'priority' => 3,
    'propertyset' => 0,
),'',true,true);

$events['OnTVOutputRenderPropertiesList'] = $modx->newObject('modPluginEvent');
$events['OnTVOutputRenderPropertiesList']->fromArray(array(
    'event' => 'OnTVOutputRenderPropertiesList',
    'priority' => 4,
    'propertyset' => 0,
),'',true,true);

$plugins[3]->addMany($events);
unset($events,$properties);


/* plugin */
$plugins[5]= $modx->newObject('modPlugin');
$plugins[5]->fromArray(array(
    'id' => 1,
    'name' => 'shk_multicurrency',
    'description' => 'Multicurrency in store.',
    'plugincode' => getSnippetContent($sources['source_core'].'elements/plugins/multi_currency.php'),
    'static' => 0,
    'source' => 1,
    'disabled' => 1
),'',true,true);

$events = array();

$events['OnLoadWebDocument'] = $modx->newObject('modPluginEvent');
$events['OnLoadWebDocument']->fromArray(array(
    'event' => 'OnLoadWebDocument',
    'priority' => 8,
    'propertyset' => 0,
),'',true,true);

$events['OnSHKgetProductPrice'] = $modx->newObject('modPluginEvent');
$events['OnSHKgetProductPrice']->fromArray(array(
    'event' => 'OnSHKgetProductPrice',
    'priority' => 9,
    'propertyset' => 0,
),'',true,true);

$events['OnSHKgetProductAdditParamPrice'] = $modx->newObject('modPluginEvent');
$events['OnSHKgetProductAdditParamPrice']->fromArray(array(
    'event' => 'OnSHKgetProductAdditParamPrice',
    'priority' => 10,
    'propertyset' => 0,
),'',true,true);

$events['OnSHKgetDeliveryPrice'] = $modx->newObject('modPluginEvent');
$events['OnSHKgetDeliveryPrice']->fromArray(array(
    'event' => 'OnSHKgetDeliveryPrice',
    'priority' => 11,
    'propertyset' => 0,
),'',true,true);

$plugins[5]->addMany($events);
unset($events);

return $plugins;
