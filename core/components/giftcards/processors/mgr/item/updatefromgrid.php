<?php
/**
 * @package giftcards
 * @subpackage processors
 */

/* parse JSON */
if (empty($scriptProperties['data'])) return $modx->error->failure('Invalid data.');
$data = $modx->fromJSON($scriptProperties['data']);
if (!is_array($data)) return $modx->error->failure('Invalid data.');

if (empty($data['id'])) return $modx->error->failure('error');

$disc_group = $modx->getObject('giftCardsDiscount',array('id_group'=>$data['id']));

if(!is_object($disc_group)){
    
    $disc_group = $modx->newObject('giftCardsDiscount');
    $disc_group->set('id_group',$data['id']);
    
}

$disc_group->set('discount',$data['discount']);
$disc_group->set('condition',$data['condition']);
$disc_group->save();

return $modx->error->success('');
