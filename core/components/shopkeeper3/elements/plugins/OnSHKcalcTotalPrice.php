<?php

/*

event: OnSHKcalcTotalPrice

*/

$price_total = $modx->getOption( 'price_total', $scriptProperties, 0 );

$price_total = round( $price_total * (1 - 10 / 100), 2 );//Скидка 10%

$modx->event->output( $price_total );

return '';
