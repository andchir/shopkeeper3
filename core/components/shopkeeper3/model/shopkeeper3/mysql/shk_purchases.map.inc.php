<?php
$xpdo_meta_map['shk_purchases']= array (
  'package' => 'shopkeeper3',
  'version' => NULL,
  'table' => 'shopkeeper3_purchases',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'p_id' => 0,
    'order_id' => 0,
    'name' => '',
    'price' => 0,
    'count' => 0,
    'data' => '',
    'options' => '',
    'class_name' => '',
    'package_name' => '',
  ),
  'fieldMeta' => 
  array (
    'p_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'order_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'price' => 
    array (
      'dbtype' => 'float',
      'precision' => '15',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'count' => 
    array (
      'dbtype' => 'float',
      'precision' => '15',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'data' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'options' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'class_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '30',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'package_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '30',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
  ),
  'aggregates' => 
  array (
    'shk_order' => 
    array (
      'class' => 'shk_order',
      'local' => 'order_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
