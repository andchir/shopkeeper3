<?php
/**
* Resolve creating custom db tables during install.
*
* @package shopkeeper3
* @subpackage build
*/
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('core_path').'components/shopkeeper3/model/';
            $modx->addPackage('shopkeeper3',$modelPath);
            
            $manager = $modx->getManager();
            
            $manager->createObjectContainer('shk_config');
            $manager->createObjectContainer('shk_order');
            $manager->createObjectContainer('shk_purchases');
            
            //config
            $conf = $modx->newObject('shk_config');
            $conf->set('setting','statuses');
            $conf->set('value','[{"label":"Новый","tpl":"userMail","color":"#b2d6ff","id":1},{"label":"Принят к оплате","tpl":"userMail","color":"#c7fff7","id":2},{"label":"Отправлен","tpl":"userMail","color":"#fffdb8","id":3},{"label":"Выполнен","tpl":"userMail","color":"#c9ffc2","id":4},{"label":"Отменен","tpl":"userMail","color":"#ffc9c9","id":5},{"label":"Оплата получена","tpl":"userMail","color":"#ffdbad","id":6}]');
            $conf->set('xtype','array');
            $conf->save();
            
            $conf = $modx->newObject('shk_config');
            $conf->set('setting','order_fields');
            $conf->set('value','[{"name":"status","label":"Статус","rank":0,"id":1},{"name":"id","rank":1,"label":"ID","id":2},{"name":"date","rank":2,"label":"Время","id":3},{"name":"price","rank":3,"label":"Цена","id":4},{"name":"email","label":"Эл. адрес","rank":5,"id":5},{"name":"username","rank":6,"label":"Пользователь","id":6}]');
            $conf->set('xtype','array');
            $conf->save();
            
            $conf = $modx->newObject('shk_config');
            $conf->set('setting','contacts_fields');
            $conf->set('value','[{"name":"fullname","label":"Имя","id":1,"rank":0},{"rank":1,"name":"email","label":"Адрес эл. почты","id":2},{"rank":2,"name":"phone","label":"Телефон","id":3},{"rank":3,"name":"message","label":"Комментарий","id":4}]');
            $conf->set('xtype','array');
            $conf->save();
            
        break;
        case xPDOTransport::ACTION_UPGRADE:
            
            
            
        break;
    }
}
return true;