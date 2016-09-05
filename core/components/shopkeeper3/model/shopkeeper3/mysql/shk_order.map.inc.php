<?php
$xpdo_meta_map['shk_order']= array (
  'package' => 'shopkeeper3',
  'version' => NULL,
  'table' => 'shopkeeper3_orders',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'id' => NULL,
    'contacts' => '',
    'options' => '',
    'price' => 0,
    'currency' => '',
    'date' => '0000-00-00 00:00',
    'sentdate' => '0000-00-00 00:00',
    'note' => '',
    'email' => '',
    'delivery' => '',
    'delivery_price' => 0,
    'payment' => '',
    'tracking_num' => '',
    'status' => '',
    'userid' => NULL,
  ),
  'fieldMeta' => 
  array (
    'id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'contacts' => 
    array (
      'dbtype' => 'text',
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
    'price' => 
    array (
      'dbtype' => 'float',
      'precision' => '15',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'currency' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'date' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'string',
      'null' => true,
      'default' => '0000-00-00 00:00',
    ),
    'sentdate' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'string',
      'null' => true,
      'default' => '0000-00-00 00:00',
    ),
    'note' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'email' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '40',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'delivery' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '40',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'delivery_price' => 
    array (
      'dbtype' => 'float',
      'precision' => '15',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'payment' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '40',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'tracking_num' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '40',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'status' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '40',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'userid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'columns' => 
      array (
        'id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'shk_purchases' => 
    array (
      'class' => 'shk_purchases',
      'local' => 'id',
      'foreign' => 'order_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Users' => 
    array (
      'class' => 'modUsers',
      'local' => 'userid',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
