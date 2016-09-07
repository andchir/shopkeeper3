<?php
$xpdo_meta_map['tagManager']= array (
  'package' => 'tag_manager2',
  'version' => NULL,
  'table' => 'tag_manager2_tags',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'category' => 0,
    'tvid' => 0,
    'tvname' => '',
    'tvcaption' => '',
    'tags' => '',
    'index' => 0,
  ),
  'fieldMeta' => 
  array (
    'category' => 
    array (
      'dbtype' => 'int',
      'precision' => '24',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
    'tvid' => 
    array (
      'dbtype' => 'int',
      'precision' => '24',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
    'tvname' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '128',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'tvcaption' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '128',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'tags' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'index' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
  ),
);
