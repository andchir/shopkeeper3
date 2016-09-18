<?php

/**
 * @package shopkeeper3
 */

abstract class shkBaseManagerController extends modExtraManagerController {
    
    public function initialize() {
        
        
        
        return parent::initialize();
        
    }
    
    public function getPageTitle() {
        return $this->modx->lexicon('shopkeeper3');
    }
    
    public function loadCustomCssJs() {
        
        $this->addHtml( '<script src="' . $this->modx->config['assets_url'] . 'components/shopkeeper3/shk_config.php"  type="text/javascript"></script>' );
        $this->addCss( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/css/bootstrap-custom/css/bootstrap.min.css' );
        
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/jquery-1.11.1.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/css/bootstrap/js/bootstrap.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/angular/angular.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/angular/angular-sanitize.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/ui-bootstrap-tpls-1.3.3.min.js' );
        
        $this->addCss( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/bootstrap-daterangepicker/daterangepicker-bs3.css' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/bootstrap-daterangepicker/momentjs/moment.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/bootstrap-daterangepicker/momentjs/locale/'.$this->modx->config['manager_language'].'.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/bootstrap-daterangepicker/daterangepicker.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/bootstrap-daterangepicker/daterangepicker_directive.js' );
        
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/bootstrap-multiselect.js' );
        
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/ng-table/ng-table.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/ng-table/ng-table-export.src.js' );
        $this->addCss( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/ng-table/ng-table.min.css' );
        
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/jquery-minicolors/jquery.minicolors.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/jquery-minicolors/angular-minicolors.js' );
        $this->addCss( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/jquery-minicolors/jquery.minicolors.css' );
        
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/angular-spinner/spin.min.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/angular-spinner/angular-spinner.min.js' );
        
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/shk_mgr_app.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/app_tpls.js' );
        $this->addLastJavascript( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/js/controllers/home_controller.js' );
        
        $this->addCss( $this->modx->config['assets_url'] . 'components/shopkeeper3/mgr/css/shk-style.css' );
        
    }
    
    public function getLanguageTopics() {
        return array('shopkeeper3:default');
    }
    
    public function checkPermissions() {
        return true;
    }
    
    public function getTemplateFile() {
        return 'home.tpl';
    }
    
}

class Shopkeeper3IndexManagerController extends shkBaseManagerController {
    //public static function getDefaultController() { return 'home'; }
}
