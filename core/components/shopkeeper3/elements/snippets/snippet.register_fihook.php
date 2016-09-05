<?php

/*

Hook for FormIt. Registration on sent mail.

Author: munk
http://modx-shopkeeper.ru/forum/viewtopic.php?id=2260

Changed by Andchir

[[!FormIt?
&hooks=`spam,register_fihook,email,FormItAutoResponder,redirect`
&registerGroupsList=`Покупатели`
...
]]

*/

$hook->setValues(array('password' => ''));

//Если пользователь залогинен выходим
if($modx->getLoginUserID()) return true;

$emailField = $modx->getOption('fiarToField',$hook->config,'email');
$registerGroupsList = $modx->getOption('registerGroupsList',$hook->config,'');

//получаем данные формы
$form_fields = $hook->getValues();
$username = !empty($form_fields[$emailField]) ? $form_fields[$emailField] : '';//username = email
$email = !empty($form_fields[$emailField]) ? $form_fields[$emailField] : '';
$fullname = !empty($form_fields['fullname']) ? $form_fields['fullname'] : '';
$phone = !empty($form_fields['phone']) ? $form_fields['phone'] : '';
$address = !empty($form_fields['address']) ? $form_fields['address'] : '';

//проверяем  есть ли такой пользователь в базе
$count = $modx->getCount('modUser', array('username' => $username));
if($count > 0) return true;  // пользователь с таким именем есть - выходим

// создаем пользователя и сохраняем
$user = $modx->newObject('modUser');
$password = $user->generatePassword($modx->getOption('password_generated_length',null,8));
$user->set('username', $username);
$user->set('password', $password);
$user->save();

// создаем профиль, добавляем к пользователю и сохраняем
$profile = $modx->newObject('modUserProfile');

$profile->set('email',    $email);
$profile->set('fullname', $fullname);
$profile->set('phone',    $phone);
$profile->set('address',  $address);

$user->addOne($profile);

$profile->save();
$user->save();


//Добавляем пользователя в группу
$registerGroupsList = $registerGroupsList ? explode(',',$registerGroupsList) : array(); //Список групп в уоторые добавить пользователя.

$groups = array();
foreach($registerGroupsList as $groupName){
    // получаем группу по имени
    $group = $modx->getObject('modUserGroup', array('name' => $groupName));
    // добавляем пользователя в группу
    if(is_object($group)) $user->joinGroup($group->id, 1);
}

//Авторизуем пользователя
$logindata = array(
    'username' => $username,
    'password' => $password,
    'rememberme' => true
);
// сам процесс авторизации
$response = $modx->runProcessor('/security/login', $logindata);
// проверяем, успешно ли
if ($response->isError()) {
    
    // произошла ошибка
    $modx->log(modX::LOG_LEVEL_ERROR, 'Ошибка авторизации в $register-on-order. Message: '.$response->getMessage());
    
} else {
    
    //Отправляем пароль в письмо
    $hook->setValues(array(
        'password' => $password
    ));
    
}

return true;