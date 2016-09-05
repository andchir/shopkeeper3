
Simple and cool CAPTCHA PHP implementation
http://code.google.com/p/cool-php-captcha

Andchir
http://modx-shopkeeper.ru/
http://wdevblog.net.ru/

==========================================

coolcaptcha_fihook

Captcha hook for FormIt

==========================================

Usage:

[[!FormIt?
&preHooks=`coolcaptcha_fihook`
&hooks=`spam,coolcaptcha_fihook,email`
&invalidCaptchaMessage=`Invalid captcha!`
&submitVar=`captcha`
...
]]

[[!+fi.coolcaptcha]]
[[!+fi.error.captcha]]
<br />
<input type="text" name="captcha" id="captcha" autocomplete="off" />


&submitVar=`captcha` - It is only example, you can specify any field.

