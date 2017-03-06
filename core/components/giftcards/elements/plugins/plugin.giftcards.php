<?php
/*

giftCards

События:
OnSHKcalcTotalPrice, OnSHKsaveOrder, OnSHKChangeStatus, OnSHKgetProductPrice, OnWebLogin, OnWebLogout

[[+shk.price_without_discount]] - Цена без скидки

*/

switch($modx->event->name) {
    
    //Если введён код подарочной карты, вычитаем её номинал из цены заказа и возвращаем новую цену
    case 'OnSHKcalcTotalPrice':

        if( !empty( $modx->event->returnedValues ) ){//Get from previous plugin
            $price_total = end( $modx->event->returnedValues );
        } else {
            $price_total = (float) str_replace(array(' ',','), array('','.'), $modx->getOption('price_total', $scriptProperties, 0));
        }
        
        $modx->setPlaceholder('shk.price_without_discount',$price_total);
        
        if($price_total && isset($_SESSION['shk_orderdata']['giftcard_amount'])){
            
            $order_discount = (int) str_replace(array(' ',','),array('','.'),$_SESSION['shk_orderdata']['giftcard_amount']);
            
            if($order_discount){
                
                $price_total = max( 0, round(($price_total - $order_discount),2) );
                
            }
            
        }
        
        $modx->event->output($price_total);

    break;

    //Если введён код подарочной карты, ставим заметку заказу и очищаем сессии карты при отправке заказа
    case 'OnSHKsaveOrder':
        
        $card_code = isset($_SESSION['shk_orderdata']['giftcard']) ? $_SESSION['shk_orderdata']['giftcard'] : '';
        $card_money = isset($_SESSION['shk_orderdata']['giftcard_amount']) ? $_SESSION['shk_orderdata']['giftcard_amount'] : 0;
        
        if(isset($_SESSION['shk_orderdata']['discount'])){
            $discount = (int) $_SESSION['shk_orderdata']['discount'];
        }else{
            $discount = (int) $modx->runSnippet('userDiscount');
        }
        
        if( ( $card_code && $card_money ) || $discount ){
            $modx->addPackage('shopkeeper3',MODX_CORE_PATH."components/shopkeeper3/model/");
            
            $order_id = $modx->getOption('order_id',$scriptProperties,0);
            if($order_id && ($order = $modx->getObject('shk_order',array('id'=>$order_id)))){
                
                $note = $order->get('note');
                $options = $order->get('options');
                $options = $options ? json_decode($options,true) : array();
                
                if($card_code) {
                    $note .= ($note ? ' ' : '')."{$card_code} ({$card_money})";
                    $options['gift'] = min( $card_money, $order->get('price') );
                }
                if($discount){
                    $note .= ($note ? ' ' : '')."- {$discount}%";
                    $options['discount'] = $discount;
                }
                $order->set('note',$note);
                $order->set('options', ( $options ? json_encode($options) : '' ) );
                $order->save();
                
                $_SESSION['shk_orderdata']['giftcard_amount'] = '';
                $_SESSION['shk_orderdata']['giftcard'] = '';
                $_SESSION['shk_orderdata']['discount'] = '';
                unset($_SESSION['shk_orderdata']['giftcard_amount'],$_SESSION['shk_orderdata']['giftcard'],$_SESSION['shk_orderdata']['discount']);
                
            }
        }
        
    break;
    
    //При смене статуса заказа на "Выполнен" сохраняем статистику для пользователя
    case 'OnSHKChangeStatus':
        
        $modx->addPackage('giftcards', $modx->getOption('core_path').'components/giftcards/model/');
        
        $order_ids = $modx->getOption('order_ids',$scriptProperties,0);
        $status = $modx->getOption('status',$scriptProperties,0);
        $status_done = $modx->getOption('giftcards.status_done', null, 3);
        $user_stat_field = $modx->getOption('giftcards.user_stat_field', null, 'order_stat');
        
        //Обновляем статистику покупок пользователя
        if( !empty( $order_ids ) && $status == $status_done ){
            
            $orders = $modx->getCollection( 'shk_order', array( 'id:IN' => $order_ids ) );
            
            foreach( $orders as $order ){
                
                if ( $userid = $order->get('userid') ){
                    
                    $user = $modx->getObject('modUser',$userid);
                    $profile = $user->getOne('Profile');
                    
                    $fields_ext = $profile->get('extended');
                    $user_stat = (float) str_replace(array(' ',','),array('','.'),$modx->getOption($user_stat_field, $fields_ext, 0));
                    $user_stat += (float) str_replace(array(' ',','),array('','.'),$order->get('price'));
                    $fields_ext[$user_stat_field] = $user_stat;
                    
                    $profile->set('extended',$fields_ext);
                    $profile->save();
                    
                    //Проверяем достигнута ли сумма покупок, которая даёт право на скидку
                    //и переводим пользователя в соответствующую группу
                    $c = $modx->newQuery('giftCardsDiscount');
                    $c->select($modx->getSelectColumns('giftCardsDiscount'));
                    $c->where(array('condition:<=' => round($user_stat)));
                    $c->sortby($modx->escape('condition'),'DESC');
                    $dsc_groups = $modx->getCollection('giftCardsDiscount',$c);
                    
                    if(count($dsc_groups)>0){
                        
                        $new_dsc_group = current($dsc_groups);
                        $group = $modx->getObject('modUserGroup',array('id'=>$new_dsc_group->get('id_group')));
                        if($group){
                            
                            $userGroups = $user->getUserGroups();
                            
                            if(!$user->isMember($group->get('name'))){
                                
                                //Ищем все группы пользователя, которым назначены скидки
                                if(count($userGroups)>0){
                                    
                                    $cc = $modx->newQuery('giftCardsDiscount');
                                    $cc->select($modx->getSelectColumns('giftCardsDiscount'));
                                    $cc->where(array('id_group:IN' => $userGroups, 'id_group:!=' => $group->get('id')));
                                    $ugroups = $modx->getCollection('modUserGroup',$cc);
                                    
                                    foreach($ugroups as $ugroup){
                                        $user->leaveGroup($ugroup->get('id'));
                                    }
                                    
                                }
                                
                                //Добавляем пользователя в новую группу со скидкой
                                $user->joinGroup($group->get('id'));
                                
                            }
                            
                        }
                        
                    }
                    
                }
                
            }
            
        }
        
    break;
    
    //Возвращает цену товара с учётом скидки пользователя
    case 'OnSHKgetProductPrice':

        if( !empty( $modx->event->returnedValues ) ){//Get from previous plugin
            $price = end( $modx->event->returnedValues );
        } else {
            $price = $modx->getOption('price', $scriptProperties, 0);
        }

        if(isset($_SESSION['shk_orderdata']['discount'])){
            $discount = (int) $_SESSION['shk_orderdata']['discount'];
        }else{
            $discount = (int) $modx->runSnippet('userDiscount');
        }
        
        if($discount > 0){
            $price = round( $price * ( 1 - $discount / 100 ), 2 );
        }
        
        $modx->event->output($price);
        
    break;
    
    //При авторизации пересчитываем цены в корзине с учётом скидки
    case 'OnWebLogin':
        
        $curSavedP = !empty( $_SESSION['shk_order'] ) ? $_SESSION['shk_order'] : array();
        if( isset( $_SESSION['shk_orderdata']['discount'] ) ){
            $discount = (int) $_SESSION['shk_orderdata']['discount'];
        }else{
            $discount = (int) $modx->runSnippet('userDiscount', array('user_id' => $user->get('id')));
        }
        
        if($discount){
            foreach( $curSavedP as &$purchase ){
                
                if( $purchase['price'] > 0 ){
                    $temp_price = $purchase['price'];
                    $purchase['price'] = max( 0, round( $temp_price * ( 1 - $discount / 100 ), 2 ) );
                    $purchase['old_price'] = $temp_price;
                }
                
                //options
                if( !empty( $purchase[ 'options' ] ) && is_array( $purchase[ 'options' ] ) ){
                    
                    foreach( $purchase[ 'options' ] as &$options ){
                        if( !$options[0] ) continue;
                        $temp_price = $options[0];
                        $options[0] = max( 0, round( $temp_price * ( 1 - $discount / 100 ), 2 ) );
                        $options[3] = $temp_price;
                    }
                    
                }
                
            }
        }
        
        $_SESSION['shk_order'] = $curSavedP;
        
    break;
    
    
    //При выходе пользователя возвращаем цены без скидок
    case 'OnWebLogout':
        
        $curSavedP = !empty($_SESSION['shk_order']) ? $_SESSION['shk_order'] : array();
        
        foreach( $curSavedP as &$purchase ){
            
            if( isset( $purchase['old_price'] ) ){
                
                $purchase['price'] = $purchase['old_price'];
                unset($purchase['old_price']);
                
                //options
                if( !empty( $purchase[ 'options' ] ) && is_array( $purchase[ 'options' ] ) ){
                    
                    foreach( $purchase[ 'options' ] as &$options ){
                        if( !isset( $options[3] ) ) continue;
                        $options[0] = $options[3];
                        unset($options[3]);
                    }
                    
                }
                
            }
            
        }
        
        $_SESSION['shk_order'] = $curSavedP;
        $_SESSION['shk_orderdata']['discount'] = '';
        unset($_SESSION['shk_orderdata']['discount']);
        
    break;
    
}

return '';