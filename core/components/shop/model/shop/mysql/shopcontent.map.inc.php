<?php
$xpdo_meta_map['ShopContent']= array (
  'package' => 'shop',
  'version' => '1.1',
  'table' => 'shop_content',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'resource_id' => NULL,
    'pagetitle' => '',
    'longtitle' => '',
    'alias' => '',
    'deleted' => 0,
    'deletedon' => 0,
    'deletedby' => 0,
    'introtext' => NULL,
    'content' => NULL,
    'template' => 0,
    'menuindex' => 0,
    'editedon' => 0,
    'editedby' => 0,
    'createdon' => 0,
    'createdby' => 0,
    'publishedon' => 0,
    'unpublishedon' => 0,
    'published' => 0,
    'publishedby' => 0,
    'hidemenu' => 0,
    'price' => 0,
    'image' => '',
    'gallery' => NULL,
    'country' => '',
    'weight' => 0,
    'inventory' => 0,
    'rating' => 0,
    'articul' => '',
    'tags' => '',
    'param1' => '',
  ),
  'fieldMeta' => 
  array (
    'resource_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'index' => 'index',
    ),
    'pagetitle' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'fulltext',
      'indexgrp' => 'shop_content_ft_idx',
    ),
    'longtitle' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'fulltext',
      'indexgrp' => 'shop_content_ft_idx',
    ),
    'alias' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
      'index' => 'index',
    ),
    'deleted' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'deletedon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 0,
    ),
    'deletedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'introtext' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'index' => 'fulltext',
      'indexgrp' => 'shop_content_ft_idx',
    ),
    'content' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => true,
      'index' => 'fulltext',
      'indexgrp' => 'shop_content_ft_idx',
    ),
    'template' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'menuindex' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'editedon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 0,
    ),
    'editedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'createdon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 0,
    ),
    'createdby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'publishedon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 0,
    ),
    'unpublishedon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 0,
    ),
    'published' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'publishedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'hidemenu' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'price' => 
    array (
      'dbtype' => 'float',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'image' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'null' => true,
      'default' => '',
    ),
    'gallery' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => true,
      'index' => 'fulltext',
    ),
    'country' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'null' => true,
      'default' => '',
    ),
    'weight' => 
    array (
      'dbtype' => 'int',
      'precision' => '255',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'inventory' => 
    array (
      'dbtype' => 'int',
      'precision' => '255',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'rating' => 
    array (
      'dbtype' => 'int',
      'precision' => '255',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'articul' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'tags' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'null' => true,
      'default' => '',
    ),
    'param1' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
  ),
  'indexes' => 
  array (
    'resource_id' => 
    array (
      'alias' => 'resource_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'resource_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'alias' => 
    array (
      'alias' => 'alias',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'alias' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
    'published' => 
    array (
      'alias' => 'published',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'published' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'hidemenu' => 
    array (
      'alias' => 'hidemenu',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'hidemenu' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'shop_content_ft_idx' => 
    array (
      'alias' => 'shop_content_ft_idx',
      'primary' => false,
      'unique' => false,
      'type' => 'FULLTEXT',
      'columns' => 
      array (
        'pagetitle' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'longtitle' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'introtext' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
        'content' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
);
