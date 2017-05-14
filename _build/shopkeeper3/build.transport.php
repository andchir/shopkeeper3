<?php
/**
 * Shopkeeper3 build script
 *
 * @package shopkeeper
 * @subpackage build
 */

ini_set('display_errors',1);
error_reporting(E_ALL);

header("Content-Type: text/html; charset=UTF-8");

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

$root = dirname(dirname(dirname(__FILE__))).'/';
$sources= array (
    'root' => $root,
    'build' => $root . '_build/',
    'lexicon' => $root . 'core/components/shopkeeper3/lexicon/',
    'resolvers' => $root . '_build/shopkeeper3/resolvers/',
    'data' => $root . '_build/shopkeeper3/',
    'source_core' => $root . 'core/components/shopkeeper3/',
    'source_assets' => $root . 'assets/components/shopkeeper3/',
    'docs' => $root . 'core/components/shopkeeper3/docs/'
);

require_once $root.'config.core.php';
require_once MODX_CORE_PATH . 'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['data'] . 'functions.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(MODX_LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage('shopkeeper3','3.2.7','pl3');
$builder->registerNamespace('shopkeeper3',false,true,'{core_path}components/shopkeeper3/');

/* load action/menu */
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'shopkeeper3',
    'parent' => 'components',
    'description' => 'shk3.menu_desc',
    'action' => 'index',
    'namespace' => 'shopkeeper3',
    'icon' => '',
    'menuindex' => '0',
    'params' => '',
    'handler' => '',
),'',true,true);
$vehicle= $builder->createVehicle($menu,array (
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'text',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Action' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
        ),
    ),
));
$builder->putVehicle($vehicle);
unset($vehicle,$action);

/* load system settings */
$settings = include $sources['data'].'system_settings.inc.php';
$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
);
foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting,$attributes);
    $builder->putVehicle($vehicle);
}
unset($settings,$setting,$attributes);

/* create category */
$category = $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category','Shopkeeper3');

/* add snippet */
include $sources['data'].'snippets.inc.php';
$modx->log(modX::LOG_LEVEL_INFO,'Packaged snippets.'); flush();

/* add plugins events */
$sys_events = array();
$sys_events[0]= $modx->newObject('modEvent');
$sys_events[0]->fromArray(array ('name' => 'OnSHKaddProduct','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[1]= $modx->newObject('modEvent');
$sys_events[1]->fromArray(array ('name' => 'OnSHKgetProductPrice','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[2]= $modx->newObject('modEvent');
$sys_events[2]->fromArray(array ('name' => 'OnSHKcalcTotalPrice','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[3]= $modx->newObject('modEvent');
$sys_events[3]->fromArray(array ('name' => 'OnSHKcartLoad','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[4]= $modx->newObject('modEvent');
$sys_events[4]->fromArray(array ('name' => 'OnSHKChangeStatus','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[5]= $modx->newObject('modEvent');
$sys_events[5]->fromArray(array ('name' => 'OnSHKsaveOrder','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[6]= $modx->newObject('modEvent');
$sys_events[6]->fromArray(array ('name' => 'OnSHKbeforeCartLoad','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[7]= $modx->newObject('modEvent');
$sys_events[7]->fromArray(array ('name' => 'OnSHKScriptsLoad','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[8]= $modx->newObject('modEvent');
$sys_events[8]->fromArray(array ('name' => 'OnSHKsendOrderMail','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[9]= $modx->newObject('modEvent');
$sys_events[9]->fromArray(array ('name' => 'OnSHKAfterAddProduct','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[10]= $modx->newObject('modEvent');
$sys_events[10]->fromArray(array ('name' => 'OnSHKgetProductAdditParams','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[11]= $modx->newObject('modEvent');
$sys_events[11]->fromArray(array ('name' => 'OnSHKcalcTotaQuantity','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[12]= $modx->newObject('modEvent');
$sys_events[12]->fromArray(array ('name' => 'OnSHKgetProductAdditParamPrice','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[13]= $modx->newObject('modEvent');
$sys_events[13]->fromArray(array ('name' => 'OnSHKprintOrderData','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[14]= $modx->newObject('modEvent');
$sys_events[14]->fromArray(array ('name' => 'OnSHKgetDeliveryPrice','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[15]= $modx->newObject('modEvent');
$sys_events[15]->fromArray(array ('name' => 'OnSHKafterAddProduct','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[16]= $modx->newObject('modEvent');
$sys_events[16]->fromArray(array ('name' => 'OnSHKAfterRemoveProduct','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);
$sys_events[17]= $modx->newObject('modEvent');
$sys_events[17]->fromArray(array ('name' => 'OnSHKAfterClearCart','service' => 6,'groupname' => 'Shopkeeper3'), '', true, true);

$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
);
foreach ($sys_events as $sys_event) {
    $vehicle = $builder->createVehicle($sys_event,$attributes);
    $builder->putVehicle($vehicle);
}
unset($sys_events,$sys_event,$attributes);

$modx->log(modX::LOG_LEVEL_INFO,'Packaged plugin events.'); flush();

/* add plugins */
$plugins = include $sources['data'].'plugins.inc.php';

$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
        ),
    ),
);
foreach ($plugins as $plugin) {
    $category->addMany($plugin);
}
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($plugins).' plugins.'); flush();
unset($plugins,$plugin,$attributes);

/* add chunks */
include $sources['data'].'chunks.inc.php';
$modx->log(modX::LOG_LEVEL_INFO,'Packaged chunks.'); flush();


/* modDashboardWidget */
$widgets = include 'widget.data.php';
if (is_array($widgets)) {
    $attributes = array (
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => array ('name'),
    );
    $ct = count($widgets);
    $idx = 0;
    foreach ($widgets as $widget) {
        $idx++;
        $vehicle = $builder->createVehicle($widget,$attributes);
        if ($ct == $idx) {
            $vehicle->resolve('file',array(
                'source' => $sources['source_core'],
                'target' => "return MODX_CORE_PATH . 'components/';",
            ));
        }
        $builder->putVehicle($vehicle);
    }
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($widgets).' default dashboard widgets.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not load dashboard widgets!'); flush();
}
unset ($widgets,$widget,$attributes,$ct,$idx);

$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name'
        ),
        'Chunks' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name'
        ),
        'TemplateVars' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name'
        ),
        'Plugins' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                'PluginEvents' => array(
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => false,
                    xPDOTransport::UNIQUE_KEY => array('pluginid','event')
                )
            )
        )
    )
);
$vehicle = $builder->createVehicle($category,$attr);

/* resolvers */
$modx->log(modX::LOG_LEVEL_INFO,'Adding file resolvers...');
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));


$modx->log(modX::LOG_LEVEL_INFO,'Adding in PHP resolvers...');
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'resolve.tables.php',
));
/*
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'setupoptions.resolver.php',
));
*/
$builder->putVehicle($vehicle);


/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    'setup-options' => array(),
));

$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(MODX_LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();


