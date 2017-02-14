<?php

/*

userDiscount

Возвращает скидку пользователя

*/

$discount = 0;

$user_id = $modx->getOption('user_id',$scriptProperties,0);
$user = $user_id ? $modx->getObject('modUser',$user_id) : $modx->user;

if($user->isAuthenticated($modx->context->get('key'))){
    
    if(!isset($_SESSION['shk_orderdata']['discount'])){
        
        $modx->addPackage('giftcards', $modx->getOption('core_path').'components/giftcards/model/');
        
        $c = $modx->newQuery('giftCardsDiscount');
        $c->select('`giftCardsDiscount`.*, `modUserGroupMember`.`role`, `modUserGroupMember`.`rank`');
        $c->leftJoin('modUserGroupMember','modUserGroupMember','`modUserGroupMember`.`user_group` = `giftCardsDiscount`.`id_group`');
        $c->where(array("`modUserGroupMember`.`member` = '".$user->get('id')."'"));
        $c->limit(1);
        $c->sortby('`giftCardsDiscount`.`discount`','DESC');
        $dsc_groups = $modx->getCollection('giftCardsDiscount',$c);
        
        if(count($dsc_groups)>0){
            $dsc_group = current($dsc_groups);
            $discount = $dsc_group->get('discount');
            $id_group = $dsc_group->get('id_group');
            $_SESSION['shk_orderdata']['discount'] = $discount;
        }
        
    }else{
        
        if($_SESSION['shk_orderdata']['discount']>0) $discount = (int) $_SESSION['shk_orderdata']['discount'];
        
    }
    
}

return $discount;
