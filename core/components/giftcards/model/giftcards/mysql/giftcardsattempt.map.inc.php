<?php
/**
 * @package giftcards
 */
$xpdo_meta_map['giftCardsAttempt']= array (
  'package' => 'giftcards',
  'version' => NULL,
  'table' => 'giftcard_attempt',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'ip' => '0',
    'date' => NULL,
  ),
  'fieldMeta' => 
  array (
    'ip' => 
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
  ),
);
