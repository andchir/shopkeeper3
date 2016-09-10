<?php

$modx->getService('lexicon','modLexicon');
$modx->lexicon->load($modx->config['manager_language'].':shopkeeper3:default');
$modx->smarty->assign('_lang', $modx->lexicon->fetch());

$tv = $modx->getObject('modTemplateVar',array('id'=>$_GET['tv']));
if(!empty($tv)){
    $modx->smarty->assign('tv_name', $tv->get('name'));
}

return $modx->smarty->fetch(MODX_CORE_PATH.'components/shopkeeper3/elements/tv/tpl/shk_widget.tpl');