<?php
/**
 * giftCards
 *
 *
 * @package giftcards
 */
/**
 * Create an Item
 * 
 * @package giftcards
 * @subpackage processors
 */

$conf = array(
    'group_count' => 3,
    'group_length' => array(4,4),
    'group_sep' => '-'
);

$conf['chars'] = array(
    array("A","B","C","D","E","F","G","H"),
    array("J","K","L","M","N","P","Q","R"),
    array("S","T","U","V","W","X","Y","Z"),
    array("1","2","3","4","5","6","7","8","9")
);

function generateCode($batch,$group_count,$group_length,$chars,$group_sep='-'){
    $batch = sprintf("%03d", $batch);
    $output = strtoupper(substr(date('l'),2,1)).$batch;
    for($i=0;$i<$group_count;$i++){
        $length = rand($group_length[0],$group_length[1]);
        $output .= $group_sep;
        $chars_gr = $chars[rand(0,count($chars)-2)];
        $chars_gr = array_merge($chars_gr,$chars[3]);
        $keys = array();
        while(count($keys) < $length){
            $keys[] = mt_rand(0, count($chars_gr)-1);
        }
        foreach($keys as $key){
            $output .= $chars_gr[$key];
        }
        unset($keys,$chr);
    }
    return $output;
}

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$group = $modx->newObject('giftCardsGroup');
$group->set('date', date('Y-m-d H:i:s',strtotime("now")));
$group->set('nominal', $modx->getOption('nominal',$_REQUEST,0));
if ($group->save() == false) {
    return $modx->error->failure($modx->lexicon('giftcards.item_err_save'));
}

$count = $modx->getOption('count',$_REQUEST,1);

$group_count = $modx->getCount('giftCardsGroup',array());
$group_count++;

//Генерируем коды карт
for($i=0; $i < $count; $i++){
    
    $card_code = generateCode($group_count,$conf['group_count'],$conf['group_length'],$conf['chars'],$conf['group_sep']);
    
    $card = $modx->newObject('giftCardsItem');
    $card->set('parent', $group->get('id'));
    $card->set('code', $card_code);
    $card->set('date', date('Y-m-d H:i:s',strtotime("now")));
    $card->set('orderid', 0);
    $card->set('state', 'valid');
    if ($card->save() == false) {
        return $modx->error->failure($modx->lexicon('giftcards.item_err_save'));
    }
    
}

return $modx->error->success('',$group);
