<?php

//***********************************
//Сниппет для MODx 2.x
//***********************************

/*

[[param_edit_table?
&docId=`1`
&tvName=`param1`
&tpl=`properties_table`
]]

[[param_edit_table?
&tvValue=`[[+tv.param1]]`
&tpl=`properties_table`
]]

[[param_edit_table?
&docId=`1`
&tvName=`all_width`
&postName=`w`
&tpl=`@CODE:
<select name="w">
    <option value=""></option>
    [[+inner]]
</select>
<!--tpl_separator-->
<option value="[[+field1]]"[[+selected1]]>[[+field1]]</option>
`]]

Примеры чанка:

1.

<h1>Заголовок</h1>
[[+inner]]
<!--tpl_separator-->
<div class="one">
    <img src="[[+field1]]" width="203" height="144" alt="" />
    <h3>[[+field2]]</h3>
    <p>[[+field3]]</p>
</div>

2.

<table>[[+inner]]</table>
<!--tpl_separator-->
<tr>
    [[+inner]]
</tr>
<!--tpl_separator-->
<td>[[+col_num]]. [[+field]]</td>

3.

<div class="product-options">
    [[+inner]]
</div>
<!--tpl_separator-->
<label>
    <input type="radio" class="shk_param" value="[[*idx]]__[[+field2]]" name="size__[[+id]]" onclick="SHK.additOpt(this)" [[+idx:eq=`0`:then=`checked`]] />
    [[+field1]]
</label>

*/

$docId = $modx->getOption('docId',$scriptProperties,$modx->resource->get('id'));
$tvName = $modx->getOption('tvName',$scriptProperties,'param');
$s_id = $modx->getOption('id',$scriptProperties,$tvName);
$tpl = $modx->getOption('tpl',$scriptProperties,'');
$postName = $modx->getOption('postName',$scriptProperties,'none');
$postName_arr = explode(',',$postName);
$tvValue = $modx->getOption('tvValue',$scriptProperties,'');
$noEmpty = $modx->getOption('noEmpty',$scriptProperties,true);
$separateCols = $modx->getOption('separateCols',$scriptProperties,false);
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,'');
$minCount = $modx->getOption('minCount',$scriptProperties,0);
$rowIndex = $modx->getOption('rowIndex',$scriptProperties,'');
$defaultValue = $modx->getOption('defaultValue',$scriptProperties,'');
$defaultTpl = $modx->getOption('defaultTpl',$scriptProperties,'');
$placeholderPrefix = $modx->getOption('placeholderPrefix',$scriptProperties,'');
if(!$tpl) return '';
$output = '';
$out_arr = array();

if(!function_exists('fetchTpl')){
function fetchTpl($tpl){
    global $modx;
    $template = "";
    if(substr($tpl, 0, 6) == "@FILE:"){
      $tpl_file = MODX_BASE_PATH . substr($tpl, 6);
        $template = file_get_contents($tpl_file);
    }else if(substr($tpl, 0, 6) == "@CODE:"){
        $template = substr($tpl, 6);
    }else if($modx->getChunk($tpl) != ""){
        $template = $modx->getChunk($tpl);
    }else{
        $template = false;
    }
    return $template;
}
}

if(!$tvValue){
    $tv = $modx->getObject('modTemplateVar',array('name'=>$tvName));
    if($tv) $tvValue = $tv->getValue($docId);
}
$rowChunk = explode('<!--tpl_separator-->', fetchTpl($tpl));
$fields = $tvValue ? explode('||',$tvValue) : array();
if(strlen($rowIndex)>0) $fields = array_slice($fields, $rowIndex, 1);

$row_unique = uniqid();
$col_unique = uniqid();

if(count($fields)>0 && count($fields) >= $minCount){

    foreach($fields as $key => $val){
        $row = explode('==',$val);
        $rowArr = array();
        foreach($row as $k => $v){
            if(!empty($v) || !$noEmpty){
                
                $index = $separateCols ? $k : 0;
                $rowArr[$index][$placeholderPrefix.'field'.($separateCols ? '1' : ($k+1))] = $v;
                
                if(isset($postName_arr[$k])) $postName = $postName_arr[$k];
                if(isset($_POST[$postName])){
                    $selected = $_POST[$postName] == $v ? ' selected="selected"' : '';
                }if(isset($_GET[$postName])){
                    $selected = $_GET[$postName] == $v ? ' selected="selected"' : '';
                }else{
                    $selected = isset($modx->placeholders['form_'.$postName]) && $modx->placeholders['form_'.$postName] == $v ? ' selected="selected"' : '';
                }
                
                $rowArr[$index]['selected'.($separateCols ? '1' : ($k+1))] = $selected;
                if(!isset($rowArr[$index][$placeholderPrefix.'inner'])) $rowArr[$index][$placeholderPrefix.'inner'] = '';
                
                if(!empty($rowChunk[2])){
                    $colArr = array(
                        $placeholderPrefix.'col_num' => $k+1,
                        $placeholderPrefix.'field' => $v,
                        $placeholderPrefix.'idx' => $key,
                        $placeholderPrefix.'num' => $key+1
                    );
                    $chunk = $modx->newObject('modChunk');
                    $chunk->fromArray(array('name'=>"@INLINE-{$col_unique}",'snippet'=>$rowChunk[2]));
                    $chunk->setCacheable(false);
                    
                    $rowArr[$index][$placeholderPrefix.'inner'] .= $chunk->process($colArr);
                    
                }
            }
        }
        unset($k,$v);
        
        //echo '<pre>'; print_r($rowArr); echo '</pre>'; exit;
        
        foreach($rowArr as $k => $v){
            $chunk = $modx->newObject('modChunk');
            $chunk->fromArray(array('name'=>"@INLINE-{$row_unique}",'snippet'=>(isset($rowChunk[1]) ? $rowChunk[1] : '')));
            $chunk->setCacheable(false);
            $v[$placeholderPrefix.'idx'] = $key;
            $v[$placeholderPrefix.'num'] = $key+1;
            $v[$placeholderPrefix.'id'] = $docId;
            $temp_out = $chunk->process($v);
            if(!$noEmpty || ($temp_out != $rowChunk[1])){
                if(!isset($out_arr[$k])) $out_arr[$k] = '';
                $out_arr[$k] .= $temp_out;
            }
        }
        unset($k,$v);
    }
    unset($key,$val);
    
    //echo '<pre>'; print_r($out_arr); echo '</pre>'; exit;
    
    if(strlen($out_arr[0])>0){
        $chunk = $modx->newObject('modChunk');
        $chunk->fromArray(array('name'=>"@INLINE-".uniqid(),'snippet'=>(isset($rowChunk[0]) ? $rowChunk[0] : '')));
        $chunk->setCacheable(false);
        $out_arr[0] = $chunk->process(array($placeholderPrefix.'inner'=>$out_arr[0]));
        $output = $out_arr[0];
        
        //Ставим отдельные плейсхолдеры для всех колонок таблицы
        if($separateCols){
            array_shift($out_arr);
            array_shift($postName_arr);
            foreach($out_arr as $key => $val){
                $temp_id = isset($postName_arr[$key]) ? $postName_arr[$key] : $key+1;
                $modx->setPlaceholder($s_id.'_'.$temp_id, $val);
            }
        }
        
    }

}else if($defaultValue && $defaultTpl){
    
    $output = $modx->getChunk($defaultTpl, array('value'=>$defaultValue));
    
}

if($output && $toPlaceholder){
    $modx->setPlaceholder($toPlaceholder, $output);
    $output = '';
}

return $output;
