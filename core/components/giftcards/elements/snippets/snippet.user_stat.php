<?php

/*

userStat

Выводит статистику покупок пользователя

*/

$output = 0;

if($modx->user->isAuthenticated($modx->context->get('key'))){
    
    $user_stat_field = $modx->getOption('giftcards.user_stat_field', $modx->config, 'order_stat');
    
    $profile = $modx->user->getOne('Profile');
    $fields_ext = $profile->get('extended');
    $output = $modx->getOption($user_stat_field, $fields_ext, 0);
    
}

return $output;
