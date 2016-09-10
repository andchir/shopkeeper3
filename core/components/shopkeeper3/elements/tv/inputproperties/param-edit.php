<?php
/**
 * @package modx
 * @subpackage processors.element.tv.renders.mgr.inputproperties
 */

$modx->getService('lexicon','modLexicon');
$modx->lexicon->load($modx->config['manager_language'].':shopkeeper3:default');
$modx->smarty->assign('_lang', $modx->lexicon->fetch());

return $modx->controller->fetchTemplate(MODX_CORE_PATH.'components/shopkeeper3/elements/tv/tpl/inputproperties_param-edit.tpl');