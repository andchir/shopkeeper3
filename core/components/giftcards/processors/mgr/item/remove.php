<?php
/**
 * giftCards
 *
 *
 * @package giftcards
 */
/**
 * Remove an Item.
 * 
 * @package giftcards
 * @subpackage processors
 */

/* get board */

if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('giftcards.item_err_ns'));

//Удаляем группу
$group = $modx->getObject('giftCardsGroup',$scriptProperties['id']);
if (!$group) return $modx->error->failure($modx->lexicon('giftcards.item_err_nf'));

if ($group->remove() == false) {
    return $modx->error->failure($modx->lexicon('giftcards.item_err_remove'));
}

//Удаляем карты группы
$remove = $this->modx->removeCollection('giftCardsItem', array("parent" => $scriptProperties['id']));

if ($remove == false) {
    return $modx->error->failure($modx->lexicon('giftcards.item_err_remove'));
}


/* output */
return $modx->error->success('',$group);
