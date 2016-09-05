<?php
/*
 
[[getIDLikeByTV?tmplvarid=`5`&like=`:[[*id]]:`]]

Код для вывода категорий в админке, поле TV "Возможные значения":
@EVAL return $modx->runSnippet('getResources',array('parents'=>10,'depth'=>3,'limit'=>0,'where'=>'{"isfolder:=":1}','sortby'=>'{"id":"ASC"}','tpl'=>'@INLINE [[+pagetitle]]==:[[+id]]:','outputSeparator'=>'||'));

Другой вариант, если один уровень вложенностей:
@SELECT `pagetitle` AS `name`,`id` FROM `[[+PREFIX]]site_content` WHERE `parent` = 10 AND `published` = 1 AND `deleted` = 0

Вывод:
[[!getResources?
$depth=`1`
&hideContainers=`1`
&resources=`[[getIDLikeByTV?tmplvarid=`13`&like=`:[[*id]]:`]]`
&tpl=`product`
&includeTVs=`1`
&prepareTVs=`0`
&includeTVList=`image,price`
&limit=`10`
&sortby=`{"pagetitle":"asc"}`
&where=`{"published":"1"}`
]]

[[!getProducts?
&resources=`[[getIDLikeByTV?tmplvarid=`13`&like=`:[[*id]]:`]]`
&tpl=`product`
&includeTVs=`1`
&includeTVList=`image,price`
&limit=`10`
&sortby=`{"pagetitle":"asc"}`
&where=`{"published":"1"}`
]]

*/

if( !isset( $tmplvarid ) ) $tmplvarid = 1;
if( !isset( $like ) ) $like = '';

$ids_arr = array();

$query = $modx->newQuery( 'modTemplateVarResource' );
$query->select( $modx->getSelectColumns( 'modTemplateVarResource', 'modTemplateVarResource', '', array( 'id', 'contentid' ) ) );
$query->where(array(
    'tmplvarid' => intval( $tmplvarid ),
    'value:LIKE' => '%'.$like.'%'
));
$query->sortby( 'contentid', 'ASC' );

$results = $modx->getCollection( 'modTemplateVarResource', $query );

if( $results ){
    foreach ( $results as $result ) {
        array_push( $ids_arr, $result->get( 'contentid' ) );
    }
}

return implode( ',', $ids_arr );