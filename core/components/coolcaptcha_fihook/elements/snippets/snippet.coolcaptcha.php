<?php
/*

coolcaptcha_fihook

[[!FormIt?
&preHooks=`coolcaptcha_fihook`
&hooks=`spam,coolcaptcha_fihook,mail`
&invalidCaptchaMessage=`Invalid captcha!`
...
]]

[[!+fi.coolcaptcha]]
[[!+fi.error.captcha]]
<br />
<input type="text" name="captcha" id="captcha" autocomplete="off" />

*/

$output = true;

$invalidCaptchaMessage = $modx->getOption('invalidCaptchaMessage', $hook->config,'Invalid captcha.');
$submitVar = !empty($hook->config['submitVar']) ? $hook->config['submitVar'] : '';
$core_dirname = str_replace($modx->config['base_path'], '', $modx->config['core_path']);
$core_url = $modx->config['base_url'] . $core_dirname;
$assets_url = $modx->config['base_url'] . 'assets/';

$img_captcha = '<a href="#" onclick="document.getElementById(\'captcha'.$submitVar.'\').src=\''.$assets_url.'components/coolcaptcha_fihook/captcha.php?\'+Math.random();return false;">';
$img_captcha .= '<img id="captcha'.$submitVar.'" src="'.$assets_url.'components/coolcaptcha_fihook/captcha.php" width="200" height="70" alt="captcha" /></a>';

$modx->setPlaceholder('fi.coolcaptcha',$img_captcha);

if(isset($_POST['captcha'])){

    if (empty($_SESSION['captcha']) || trim(strtolower($_POST['captcha'])) != $_SESSION['captcha']) {
        
        $hook->addError('captcha', $invalidCaptchaMessage);
        $output = false;
        
    }
    
}

return $output;