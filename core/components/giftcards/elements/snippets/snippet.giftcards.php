<?php
/**
 * giftCards snippet
 *
 * @package giftcards
 */

$giftCards = $modx->getService('giftcards','giftCards',$modx->getOption('giftcards.core_path',null,$modx->getOption('core_path').'components/giftcards/').'model/giftcards/',$scriptProperties);
if (!($giftCards instanceof giftCards)) return '';

$tpl = $modx->getOption('tpl',$scriptProperties,'giftCards_form');
$max_attempts = $modx->getOption('max_attempts', $scriptProperties, 10);
$card_code = isset($_POST['giftcard_code']) ? trim($_POST['giftcard_code']) : '';
$money = !empty($_SESSION['shk_orderdata']['giftcard_amount']) ? $_SESSION['shk_orderdata']['giftcard_amount'] : 0;
$display = 'block';
$msg = '';
$attempt_count = 0;

$modx->getService('lexicon','modLexicon');
$modx->lexicon->load($modx->config['manager_language'].':giftcards:default');

$output = '';

//Проверка кода карты
if(!empty($card_code)){
    
    $ip_addr = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $attempt_count = $modx->getCount('giftCardsAttempt',array("ip = '$ip_addr' AND date > NOW() - INTERVAL 10 MINUTE"));
    
    //var_dump($attempt_count,$max_attempts);
    
    if($attempt_count < $max_attempts){
        
        $c_card = $modx->getObject('giftCardsItem',array("code" => $card_code, "state:!=" => "expire"));
        
        //Если карта найдена
        if(is_object($c_card) && ($c_group = $modx->getObject('giftCardsGroup',array('id' => $c_card->get('parent'))))){
            
            $money = $c_group->get('nominal');
            
            if(!isset($_SESSION['shk_orderdata'])) $_SESSION['shk_orderdata'] = array();
            $_SESSION['shk_orderdata']['giftcard'] = $card_code;
            $_SESSION['shk_orderdata']['giftcard_amount'] = $money;
            
            $c_card->set('state', 'expire');
            $c_card->set('date', date('Y-m-d H:i:s',strtotime("now")));
            $c_card->save();
            
            $pageUrl = $modx->makeUrl($modx->resource->get('id'), '', '', 'full');
	    $modx->sendRedirect($pageUrl,array('type' => 'REDIRECT_HEADER'));
            exit;
            
        //Если код карты не найден
        }else{
            
            $attempt = $modx->newObject('giftCardsAttempt');
            $attempt->set('ip', $ip_addr);
            $attempt->set('date', date('Y-m-d H:i:s',strtotime("now")));
            $insert = $attempt->save();
            
            $msg = $modx->lexicon('giftcards.no_matches');
            $display = 'block';
            
        }
        
    }else{
        
        $msg = $modx->lexicon('giftcards.limit_exceeded');
        
    }
    
    //Удаляем старые записи
    $remove = $modx->removeCollection('giftCardsAttempt', array("date < NOW() - INTERVAL 30 MINUTE"));
    
}

//Если номер карты уже введён, формируем сообщение
if($money){
    
    $totalprice = isset($modx->placeholders['shk.price_without_discount']) ? floatval(str_replace(array(' ',','),array('','.'),$modx->placeholders['shk.price_without_discount'])) : (isset($modx->placeholders['shk.price_total']) ? floatval(str_replace(array(' ',','),array('','.'),$modx->placeholders['shk.price_total'])) : 0);
    $money_left = $totalprice - $money;
    $msg = $modx->lexicon('giftcards.card_valid',array('total'=>$totalprice,'money'=>$money));
    if($totalprice > $money){
        $msg .= ' '.$modx->lexicon('giftcards.left_money',array('left'=>$money_left));
    }
    $display = 'none';
    
}

$modx->setPlaceholder('gift_money',$money);

$chunkArr = array(
    'code' => $card_code,
    'msg' => $msg,
    'display' => $display,
    'money' => $money,
    'attempt' => $attempt_count,
    'attempt_left' => $max_attempts - $attempt_count
);

$output = $giftCards->getChunk($tpl,$chunkArr);

return $output;