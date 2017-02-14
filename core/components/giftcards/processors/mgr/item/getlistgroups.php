<?php
/**
 * giftCards
 *
 *
 * @package giftCards
 */
/**
 * Get a list of Items
 *
 * @package giftCards
 * @subpackage processors
 */
$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start',$_REQUEST,0);
$limit = $modx->getOption('limit',$_REQUEST,20);
$sort = $modx->getOption('sort',$_REQUEST,'date');
$dir = $modx->getOption('dir',$_REQUEST,'DESC');

$c = $modx->newQuery('giftCardsGroup');
$count = $modx->getCount('giftCardsGroup',$c);

$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$items = $modx->getCollection('giftCardsGroup',$c);

$list = array();
$key = 0;
foreach ($items as $item) {
    $itemArray = $item->toArray();
    $list[$key] = $itemArray;
    $list[$key]['count'] = $modx->getCount('giftCardsItem',array('parent'=>$itemArray['id']));
    $list[$key]['valids'] = $modx->getCount('giftCardsItem',array('parent'=>$itemArray['id'],'state'=>'valid'));
    $list[$key]['expired'] = $modx->getCount('giftCardsItem',array('parent'=>$itemArray['id'],'state'=>'expire'));
    $key++;
}




return $this->outputArray($list,$count);

