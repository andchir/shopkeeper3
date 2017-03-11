<?php
/**
 * tagManager build script
 *
 * @package tag_manager
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
    'lexicon' => $root . 'core/components/tag_manager2/lexicon/',
    'resolvers' => $root . '_build/tag_manager2/resolvers/',
    'data' => $root . '_build/tag_manager2/',
    'source_core' => $root . 'core/components/tag_manager2/',
    'source_assets' => $root . 'assets/components/tag_manager2/',
    'docs' => $root . 'core/components/tag_manager2/docs/',
);

/* override with your own defines here (see build.config.sample.php) */
//require_once dirname(__FILE__) . '/build.config.php';
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
$builder->createPackage('tagmanager2','2.3.1','pl2');
$builder->registerNamespace('tag_manager2',false,true,'{core_path}components/tag_manager2/');

/* load action/menu */
//$action = include $sources['data'].'transport.action.php';
/*
$action= $modx->newObject('modAction');

$action->fromArray(array(
    'id' => 1,
    'namespace' => 'tag_manager2',
    'parent' => 'components',
    'controller' => 'index',
    'haslayout' => '1',
    'lang_topics' => 'tag_manager2:default',
    'assets' => '',
),'',true,true);
*/

$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'tag_manager2',
    'parent' => 'components',
    'description' => 'tag_manager2_desc',
    'action' => 'index',
    'namespace' => 'tag_manager2',
    'icon' => '',
    'menuindex' => '0',
    'params' => '',
    'handler' => '',
),'',true,true);
//$menu->addOne($action);

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
$category->set('category','tagManager2');


/* add snippet */
include $sources['data'].'snippets.inc.php';
$modx->log(modX::LOG_LEVEL_INFO,'Packaged snippets.'); flush();


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
    $vehicle = $builder->createVehicle($plugin, $attributes);
    $builder->putVehicle($vehicle);
//    $category->addMany($plugin);
}
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($plugins).' plugins.'); flush();
unset($plugins,$plugin,$attributes);


/* add chunks */
include $sources['data'].'chunks.inc.php';
$modx->log(modX::LOG_LEVEL_INFO,'Packaged chunks.'); flush();


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


$builder->putVehicle($vehicle);

/* load lexicon strings */
//$builder->buildLexicon($sources['lexicon']);

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
