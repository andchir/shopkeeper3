<?php
 
/*
 
snippet getImage
 
1 - phpthumbof options
2 - image replacement url
3 - image TV basePath
|| - separator
 
<img src="[[+tv.image:getImage=`w=105&h=105&far=1&bg=ffffff&q=99||images/nophoto.jpg||assets/`]]" width="105" height="105" alt="" />
 
*/
 
$input = !empty($input) ? $input : '';
$options = !empty($options) ? $options : '';
$opt_arr = explode('||',$options);
if(!isset($opt_arr[1])) $opt_arr[1] = '';
if(!isset($opt_arr[2])) $opt_arr[2] = '';
 
$input = preg_replace($modx->sanitizePatterns['tags1'], '', $input);//MODX 2.2.6+
$input = preg_replace($modx->sanitizePatterns['tags2'], '', $input);
 
if($input)
    return $modx->runSnippet('phpthumbof',array('input'=>$opt_arr[2].$input,'options'=>$opt_arr[0]));
else
    return $opt_arr[2].$opt_arr[1];