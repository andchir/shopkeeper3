<?php
/**
 * giftCards
 *
 *
 * @package giftCards
 */
/**
 * Get a list of Discounts
 *
 * @package giftCards
 * @subpackage processors
 */
$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start',$_REQUEST,0);
$limit = $modx->getOption('limit',$_REQUEST,20);
$sort = $modx->getOption('sort',$_REQUEST,'discount');
$dir = $modx->getOption('dir',$_REQUEST,'DESC');

$c = $modx->newQuery('modUserGroup');
$c->select('`modUserGroup`.*, `giftCardsDiscount`.`discount`, `giftCardsDiscount`.`condition`');
$c->leftJoin ('giftCardsDiscount','giftCardsDiscount','`giftCardsDiscount`.`id_group` = `modUserGroup`.`id`');
$count = $modx->getCount('modUserGroup',$c);

$c->sortby((in_array($sort,array('id','name')) ? "`modUserGroup`.`{$sort}`" : "`giftCardsDiscount`.`{$sort}`"),$dir);
if ($isLimit) $c->limit($limit,$start);
$items = $modx->getCollection('modUserGroup',$c);

$list = array();
$key = 0;
foreach ($items as $item) {
    $itemArray = $item->toArray();
    $list[$key] = $itemArray;
    if(!$itemArray['discount']) $list[$key]['discount'] = 0;
    if(!$itemArray['condition']) $list[$key]['condition'] = 0;
    $key++;
}

return $this->outputArray($list,$count);
