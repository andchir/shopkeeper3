<?php

/**
 * tagManager2
 *
 * @package tag_manager2
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

abstract class tagManagerBaseManagerController extends modExtraManagerController {
    
    public function initialize() {
        
        
        
        return parent::initialize();
        
    }
    
    public function getPageTitle() {
        return $this->modx->lexicon('tag_manager2');
    }
    
    public function loadCustomCssJs() {
        
        $this->addHtml( '<script src="' . $this->modx->config['assets_url'] . 'components/tag_manager2/tm_config.js.php"  type="text/javascript"></script>' );
        $this->addCss( $this->modx->config['assets_url'] . 'components/tag_manager2/css/bootstrap/css/bootstrap.css' );
        
        //$this->addCss( $this->modx->config['assets_url'] . 'components/tag_manager2/css/bootstrap/css/bootstrap-theme.css' );
        $this->addCss( $this->modx->config['assets_url'] . 'components/tag_manager2/js/mgr/angular-bootstrap-nav-tree/abn_tree.css' );
        $this->addCss( $this->modx->config['assets_url'] . 'components/tag_manager2/css/mgr/tag_manager.css' );
        
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/js/jquery-1.11.0.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/js/mgr/jquery-ui-1.10.2.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/css/bootstrap/js/bootstrap.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/js/mgr/angular.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/js/mgr/angular-animate.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/js/mgr/ui-bootstrap-tpls-0.13.4.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/js/mgr/sortable.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/js/mgr/angular-bootstrap-nav-tree/abn_tree_directive.js' );
        
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/js/mgr/tag_mgr_app.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/tag_manager2/js/mgr/controllers/home_controller.js' );
        
    }
    
    public function getLanguageTopics() {
        return array('tag_manager2:default');
    }
    
    public function checkPermissions() {
        return true;
    }
    
    public function getTemplateFile() {
        return 'home.tpl';
    }
    
}

class tag_manager2IndexManagerController extends tagManagerBaseManagerController {
    //public static function getDefaultController() { return 'home'; }
}
