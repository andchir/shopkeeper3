<?php
/**
 * @package giftcards
 */
$xpdo_meta_map['giftCardsGroup']= array (
  'package' => 'giftcards',
  'version' => NULL,
  'table' => 'giftcard_group',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'date' => NULL,
    'nominal' => '0',
  ),
  'fieldMeta' => 
  array (
    'date' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'string',
      'null' => true,
    ),
    'nominal' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'text',
      'null' => false,
      'default' => '0',
    ),
  ),
);
