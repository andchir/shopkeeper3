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

$templates = array( 'home' );

//$modx->smarty->assign('var', $results);
$modx->smarty->caching = false;
$modx->smarty->template_dir = $modx->getOption('core_path') . 'components/tag_manager2/templates/';
$template_name = isset($_GET['a']) && in_array( $_GET['a'], $templates ) ? $_GET['a'] : 'home';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>tagManager2</title>
    
    <link href="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/css/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/mgr/angular-bootstrap-nav-tree/abn_tree.css" rel="stylesheet">
    <link href="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/css/mgr/tag_manager.css" rel="stylesheet">
    
    <!--[if lt IE 9]>
        <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/html5shiv.js"></script>
        <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/respond.min.js"></script>
    <![endif]-->
    
    <!-- tagManager config -->
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/tm_config.js.php?wctx=mgr"></script>
    
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/jquery-1.11.0.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/mgr/jquery-ui-1.10.2.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/css/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/mgr/angular.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/mgr/angular-animate.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/mgr/ui-bootstrap-tpls-0.13.4.min.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/mgr/sortable.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/mgr/angular-bootstrap-nav-tree/abn_tree_directive.js"></script>
    
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/mgr/tag_mgr_app.js"></script>
    <script src="<?php echo $modx->config['assets_url']; ?>components/tag_manager2/js/mgr/controllers/home_controller.js"></script>
    
</head>
<body>
    
    <div id="modx-content">
        
    <?php
    
    $modx->smarty->display( $template_name . '.tpl' );
    
    ?>
    
    </div>
    
</body>
</html>