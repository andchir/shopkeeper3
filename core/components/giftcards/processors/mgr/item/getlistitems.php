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

$group_id = $modx->getOption('id',$_REQUEST,-1);
if($group_id < 0){
    $modx->error->addField('name',$modx->lexicon('modextra.item_err_ns'));
}

$modx->getService('lexicon','modLexicon');
$modx->lexicon->load($modx->config['manager_language'].':giftcards:default');

$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start',$_REQUEST,0);
$limit = $modx->getOption('limit',$_REQUEST,10);
$sort = $modx->getOption('sort',$_REQUEST,'id');
$dir = $modx->getOption('dir',$_REQUEST,'ASC');

$c = $modx->newQuery('giftCardsItem');
$c->where(array('parent'=>$group_id));
$count = $modx->getCount('giftCardsItem',$c);

$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$items = $modx->getCollection('giftCardsItem',$c);

$list = array();
$key = 0;
foreach ($items as $item) {
    $itemArray = $item->toArray();
    $list[$key] = $itemArray;
    $list[$key]['state_value'] = $itemArray['state'];
    $list[$key]['state'] = $modx->lexicon('giftcards.'.$itemArray['state']);
    $key++;
}

return $this->outputArray($list,$count);

