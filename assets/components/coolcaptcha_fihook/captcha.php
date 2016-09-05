<?php

require dirname(dirname(dirname(dirname(__FILE__)))) . "/config.core.php";
require MODX_CORE_PATH . "/config/" . MODX_CONFIG_KEY . ".inc.php";
require_once MODX_CORE_PATH . "model/modx/modx.class.php";

$modx = new modX();
$modx->initialize('web');

require MODX_CORE_PATH . 'components/coolcaptcha_fihook/captcha.php';

$captcha = new SimpleCaptcha();

// OPTIONAL Change configuration...
$captcha->wordsFile = MODX_CORE_PATH . 'components/coolcaptcha_fihook/words/es.php';
//$captcha->session_var = 'secretword';
//$captcha->imageFormat = 'png';
//$captcha->lineWidth = 3;
//$captcha->scale = 3; $captcha->blur = true;
$captcha->resourcesPath = MODX_CORE_PATH . 'components/coolcaptcha_fihook/resources';

// OPTIONAL Simple autodetect language example
/*
if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $langs = array('en', 'es');
    $lang  = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if (in_array($lang, $langs)) {
        $captcha->wordsFile = "words/$lang.php";
    }
}
*/

// Image generation
$captcha->CreateImage();
