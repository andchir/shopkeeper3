<?php
/*

Проверка добавлен ли товар к сравнению

[[+id:in_compare=`checked="checked"`]]

*/

if( !isset( $options ) ) $options = 'checked';
$opt_arr = explode('||', $options);
if( count( $opt_arr ) < 2 ) $opt_arr[1] = ' ';

$compareIds_arr = !empty( $_COOKIE['shkCompareIds'] )
    ? explode( ',', str_replace(' ', '', $_COOKIE['shkCompareIds'] ) )
    : array();

return in_array( $input, $compareIds_arr ) ? $opt_arr[0] : $opt_arr[1];