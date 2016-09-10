<?php

$widgets = array();
$widgets[0]= $modx->newObject('modDashboardWidget');
$widgets[0]->fromArray(array (
  'name' => 'shk3.widget_name',
  'description' => 'shk3.widget_desc',
  'type' => 'file',
  'size' => 'full',
  'content' => '[[++core_path]]components/shopkeeper3/elements/widgets/widget.shk_stat.php',
  'namespace' => 'shopkeeper3',
  'lexicon' => 'shopkeeper3:manager',
), '', true, true);

return $widgets;
