<?php

/*

subitemCount 1.3

[[subitemCount?
&id=`[[+id]]`
&templateid=`[[+template]]`
&className=`10==ShopContent`
&packageName=`10==shop`
&context=`catalog`
]]

&className=`Имя класса пакета`
&className=`ID шаблона1,ID шаблона 2==Имя класса пакета`

*/

$className = $modx->getOption('className',$scriptProperties,'');
$packageName = $modx->getOption('packageName',$scriptProperties,'');
$id = $modx->getOption('id',$scriptProperties,1);
$templateid = $modx->getOption('templateid',$scriptProperties,1);
$context = $modx->getOption('context',$scriptProperties,'web');
$output = 0;

if(strpos($className,'==') !== false){
    $className_arr = explode('==',$className);
    if(in_array($templateid,explode(',',$className_arr[0]))){
        $className = $className_arr[1];
    }else{
        $className = 'modResource';
    }
}

//Если ресурсы MODX
if($id && (empty($className) || $className=='modResource')){
    
    $output = count($modx->getChildIds($id,1,array('context'=>$context)));

//Таблица в БД, созданная при помощи MIGXDB
}else if($id){
    
    //Пакет класса объектов
    if(strpos($packageName,'==') !== false){
        $packageName_arr = explode('==',$packageName);
        if(in_array($templateid,explode(',',$packageName_arr[0]))){
            $packageName = $packageName_arr[1];
        }else{
            $className = 'modResource';
        }
    }
    $modelpath = $modx->getOption('core_path') . 'components/' . $packageName . '/model/';
    $added = $modx->addPackage($packageName, $modelpath);
    
    $output = $modx->getCount($className,array('resource_id'=>$id,'published'=>1,'hidemenu'=>0));
    
}

return $output;