<?php

$settings = array();

$settings['shk3.property_sets']= $modx->newObject('modSystemSetting');
$settings['shk3.property_sets']->fromArray(array (
  'key' => 'shk3.property_sets',
  'value' => 'cart_catalog,cart_order_page',
  'xtype' => 'textfield',
  'namespace' => 'shopkeeper3',
  'area' => '',
  'editedon' => null,
), '', true, true);

$settings['shk3.currency']= $modx->newObject('modSystemSetting');
$settings['shk3.currency']->fromArray(array (
  'key' => 'shk3.currency',
  'value' => 'руб.',
  'xtype' => 'textfield',
  'namespace' => 'shopkeeper3',
  'area' => '',
  'editedon' => null,
), '', true, true);

$settings['shk3.currency_default']= $modx->newObject('modSystemSetting');
$settings['shk3.currency_default']->fromArray(array (
  'key' => 'shk3.currency_default',
  'value' => '1',
  'xtype' => 'textfield',
  'namespace' => 'shopkeeper3',
  'area' => '',
  'editedon' => null,
), '', true, true);

$settings['shk3.mail_order_data_tpl']= $modx->newObject('modSystemSetting');
$settings['shk3.mail_order_data_tpl']->fromArray(array (
  'key' => 'shk3.mail_order_data_tpl',
  'value' => 'orderDataOuter',
  'xtype' => 'textfield',
  'namespace' => 'shopkeeper3',
  'area' => '',
  'editedon' => null,
), '', true, true);

$settings['shk3.mail_order_data_row_tpl']= $modx->newObject('modSystemSetting');
$settings['shk3.mail_order_data_row_tpl']->fromArray(array (
  'key' => 'shk3.mail_order_data_row_tpl',
  'value' => 'orderDataRow',
  'xtype' => 'textfield',
  'namespace' => 'shopkeeper3',
  'area' => '',
  'editedon' => null,
), '', true, true);

$settings['shk3.mail_contacts_row_tpl']= $modx->newObject('modSystemSetting');
$settings['shk3.mail_contacts_row_tpl']->fromArray(array (
  'key' => 'shk3.mail_contacts_row_tpl',
  'value' => 'mailContactsRow',
  'xtype' => 'textfield',
  'namespace' => 'shopkeeper3',
  'area' => '',
  'editedon' => null,
), '', true, true);

$settings['shk3.first_status']= $modx->newObject('modSystemSetting');
$settings['shk3.first_status']->fromArray(array (
  'key' => 'shk3.first_status',
  'value' => '1',
  'xtype' => 'textfield',
  'namespace' => 'shopkeeper3',
  'area' => '',
  'editedon' => null,
), '', true, true);

return $settings;
