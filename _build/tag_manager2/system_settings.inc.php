<?php

$settings = array();

$settings['setting_tag_mgr2.catalog_context']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.catalog_context']->fromArray(array (
  'key' => 'tag_mgr2.catalog_context',
  'value' => 'web',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);

$settings['setting_tag_mgr2.catalog_id']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.catalog_id']->fromArray(array (
  'key' => 'tag_mgr2.catalog_id',
  'value' => '0',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);

$settings['setting_tag_mgr2.className']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.className']->fromArray(array (
  'key' => 'tag_mgr2.className',
  'value' => 'modResource',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);

$settings['setting_tag_mgr2.packageName']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.packageName']->fromArray(array (
  'key' => 'tag_mgr2.packageName',
  'value' => 'modResource',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);

$settings['setting_tag_mgr2.guard_key']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.guard_key']->fromArray(array (
  'key' => 'tag_mgr2.guard_key',
  'value' => '#',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);

$settings['setting_tag_mgr2.multitags']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.multitags']->fromArray(array (
  'key' => 'tag_mgr2.multitags',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);

$settings['setting_tag_mgr2.numeric']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.numeric']->fromArray(array (
  'key' => 'tag_mgr2.numeric',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);

$settings['setting_tag_mgr2.prod_templates']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.prod_templates']->fromArray(array (
  'key' => 'tag_mgr2.prod_templates',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);

$settings['setting_tag_mgr2.propertySetName']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.propertySetName']->fromArray(array (
  'key' => 'tag_mgr2.propertySetName',
  'value' => 'catalog_filters',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);

$settings['setting_tag_mgr2.propertySetSnippet']= $modx->newObject('modSystemSetting');
$settings['setting_tag_mgr2.propertySetSnippet']->fromArray(array (
  'key' => 'tag_mgr2.propertySetSnippet',
  'value' => 'getPage',
  'xtype' => 'textfield',
  'namespace' => 'tag_manager2',
  'area' => 'module',
  'editedon' => null,
), '', true, true);


return $settings;
