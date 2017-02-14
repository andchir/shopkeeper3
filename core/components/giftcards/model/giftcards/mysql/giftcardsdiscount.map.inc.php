<?php
/**
 * @package giftcards
 */
$xpdo_meta_map['giftCardsDiscount']= array (
  'package' => 'giftcards',
  'version' => NULL,
  'table' => 'giftcard_discount',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'id_group' => NULL,
    'discount' => NULL,
    'condition' => NULL,
  ),
  'fieldMeta' => 
  array (
    'id_group' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'int',
      'null' => true,
    ),
    'discount' => 
    array (
      'dbtype' => 'float',
      'phptype' => 'float',
      'null' => true,
    ),
    'condition' => 
    array (
      'dbtype' => 'float',
      'phptype' => 'float',
      'null' => true,
    ),
  ),
);
