<?php
/**
 * Build Schema script
 *
 * @package shopkeeper
 * @subpackage build
 */

define('MODX_BASE_PATH', dirname(dirname(dirname(__FILE__))) . '/');
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

//require_once dirname(__FILE__) . '/build.config.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->loadClass('transport.modPackageBuilder','',false, true);
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$root = MODX_BASE_PATH;

$sources = array(
    'root' => $root,
    'core' => $root.'core/components/tag_manager2/',
    'model' => $root.'core/components/tag_manager2/model/',
    'assets' => $root.'assets/components/tag_manager2/',
    'schema' => $root.'_build/tag_manager2/',
    'schema_file' => 'tag_manager2.mysql.schema.xml'
);


$manager= $modx->getManager();
$generator= $manager->getGenerator();

$generator->parseSchema($sources['schema'].$sources['schema_file'], $sources['model']);

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

echo "\nExecution time: {$totalTime}\n";

exit ();