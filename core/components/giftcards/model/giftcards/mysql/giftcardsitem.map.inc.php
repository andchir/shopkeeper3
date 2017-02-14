<?php
/**
 * @package giftcards
 */
$xpdo_meta_map['giftCardsItem']= array (
  'package' => 'giftcards',
  'version' => NULL,
  'table' => 'giftcard_item',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'parent' => 0,
    'code' => '0',
    'date' => NULL,
    'orderid' => 0,
    'state' => '0',
  ),
  'fieldMeta' => 
  array (
    'parent' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'code' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'text',
      'null' => false,
      'default' => '0',
    ),
    'date' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'string',
      'null' => true,
    ),
    'orderid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'text',
      'null' => false,
      'default' => 0,
    ),
    'state' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'text',
      'null' => false,
      'default' => '0',
    ),
  ),
);
