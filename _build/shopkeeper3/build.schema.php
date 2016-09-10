<?php

/**
 * Build Schema script
 *
 * @package shopkeeper3
 * @subpackage build
 */

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

require_once dirname(__FILE__) . '/build.config.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->loadClass('transport.modPackageBuilder','',false, true);
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$root = MODX_BASE_PATH;

$sources = array(
    'root' => $root,
    'core' => $root.'core/components/shopkeeper3/',
    'model' => $root.'core/components/shopkeeper3/model/',
    'assets' => $root.'assets/components/shopkeeper3/',
    'schema' => $root.'_build/shopkeeper3/schema/',
    'schema_file' => 'shopkeeper3.mysql.schema.xml'
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

$modelPath = $modx->getOption('core_path').'components/shopkeeper3/model/';
$modx->addPackage('shopkeeper3',$modelPath);
$manager = $modx->getManager();
$manager->createObjectContainer('shk_config');
$manager->createObjectContainer('shk_order');
$manager->createObjectContainer('shk_purchases');
echo "Tables created\n";

exit;

