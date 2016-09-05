<?php
/*
 * numFormat snippet
 * example: [[*price:num_format]]
 */

if(strlen($input)==0) return '';

$input = floatval(str_replace(array(' ',','), array('','.'), $input));
return number_format($input,(floor($input) == $input ? 0 : 2),'.',' ');