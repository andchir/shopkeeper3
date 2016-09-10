<?php
/**
* Resolve creating custom db tables during install.
*
* @package tag_manager
* @subpackage build
*/
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('core_path').'components/tag_manager2/model/';
            $modx->addPackage('tag_manager2',$modelPath);

            $manager = $modx->getManager();
            $manager->createObjectContainer('tagManager');

        break;
        case xPDOTransport::ACTION_UPGRADE:
            
        break;
    }
}
return true;