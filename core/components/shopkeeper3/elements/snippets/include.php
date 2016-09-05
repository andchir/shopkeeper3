<?php
if (file_exists(MODX_BASE_PATH.$file)){
   $o = include MODX_BASE_PATH.$file;
}else{ $o = 'File not found at: '.$file; }
return $o;