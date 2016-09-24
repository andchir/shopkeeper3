<?php

if (!defined('MODX_API_MODE')) {
    define('MODX_API_MODE', false);
}

include(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php');
if (!defined('MODX_CORE_PATH')) define('MODX_CORE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/core/');

include_once (MODX_CORE_PATH . "model/modx/modx.class.php");
$modx = new modX();
$modx->initialize('mgr');
$modx->lexicon->load('core:default');

if( !$modx->user->isAuthenticated('mgr') ){
    echo $modx->lexicon('permission_denied');
    exit;
}

$modx->getService('error','error.modError');
$modx->getService('smarty','smarty.modSmarty');

$templates = array( 'home', 'stats', 'settings' );

//$modx->smarty->assign('var', $results);
$modx->smarty->caching = false;
$modx->smarty->template_dir = $modx->getOption('core_path') . 'components/shopkeeper3/templates/';
$template_name = isset($_GET['a']) && in_array( $_GET['a'], $templates ) ? $_GET['a'] : 'home';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopkeeper</title>
    
    <link href="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/css/bootstrap-custom/css/bootstrap.min.css" rel="stylesheet">
    
    <!--[if lt IE 9]>
        <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/html5shiv.min.js"></script>
        <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/respond.min.js"></script>
    <![endif]-->
    
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/shk_config.php"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/jquery-1.11.1.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/css/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/angular/angular.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/angular/angular-sanitize.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/ui-bootstrap-tpls-1.3.3.min.js"></script>
    
    <link href="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/bootstrap-daterangepicker/momentjs/moment.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/bootstrap-daterangepicker/momentjs/locale/<?php echo $modx->config['manager_language']; ?>.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/bootstrap-daterangepicker/daterangepicker_directive.js"></script>
    
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/bootstrap-multiselect.js"></script>
    
    <link href="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/ng-table/ng-table.min.css" rel="stylesheet">
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/ng-table/ng-table.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/ng-table/ng-table-export.js"></script>
    
    <link href="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/jquery-minicolors/jquery.minicolors.css" rel="stylesheet">
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/jquery-minicolors/jquery.minicolors.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/jquery-minicolors/angular-minicolors.js"></script>
    
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/angular-spinner/spin.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/angular-spinner/angular-spinner.min.js"></script>
    
    <link href="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/c3/c3.css" rel="stylesheet" type="text/css">
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/c3/d3/d3.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/c3/c3.min.js"></script>
    
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/shk_mgr_app.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/app_tpls.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/js/controllers/<?php echo $template_name; ?>_controller.js"></script>
    
    <link href="<?php echo $modx->config['assets_url']; ?>components/shopkeeper3/mgr/css/shk-style.css" rel="stylesheet">
    
</head>
<body>
    
    <div id="modx-content">
        
    <?php
    
    $modx->smarty->display( $template_name . '.tpl' );
    
    ?>
    
    </div>
    
</body>
</html>