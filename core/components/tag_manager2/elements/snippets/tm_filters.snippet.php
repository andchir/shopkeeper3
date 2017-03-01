<?php
/**
 * snippet tmFilters
 *
 */

//ini_set('display_errors',1);
//error_reporting(E_ALL);

$create = $modx->getOption('create', $scriptProperties, '');
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, '');
$optStyles = $modx->getOption('style', $scriptProperties, 1);
$optJsScripts = $modx->getOption('jsScript', $scriptProperties, 1);

require_once MODX_CORE_PATH."components/tag_manager2/model/tm_filters.class.php";
$tm_filters = new tmFilters($modx, $scriptProperties);

if( $create ){
    $output = $tm_filters->createFilters( $create );
}else{
    $output = $tm_filters->getFiltersOutput();
}

if($toPlaceholder){
    $modx->setPlaceholder($toPlaceholder,$output);
    $output = '';
}

if( $optStyles ){
    $modx->regClientCSS($modx->config['assets_url']."components/tag_manager2/css/web/tm-style.css");
}

if( $optJsScripts ){

    $filtersType = $modx->getOption('filtersType', $scriptProperties, 'filters');
    $shk_currency_default = $modx->getOption('shk3.currency_default', $scriptProperties, '');
    $shk_currency_rate = $modx->getOption('shk3.currency_rate', $scriptProperties, '');

    //Get currency rate
    if( $shk_currency_default && empty( $shk_currency_rate ) ){
        require_once $modx->getOption('core_path') . "components/shopkeeper3/model/shopkeeper.class.php";
        $shkConfig = Shopkeeper::getConfig('currency_rate');
        $shk_currency_rate = isset( $shkConfig['currency_rate'] )
            ? $shkConfig['currency_rate']
            : array();
    }

    if( $optJsScripts != 2 ){
        $modx->regClientScript($modx->config['assets_url']."components/tag_manager2/js/web/jquery-ui-1.10.3.custom.min.js");
        $modx->regClientScript($modx->config['assets_url']."components/tag_manager2/js/web/jquery.history.js");
        $modx->regClientScript($modx->config['assets_url']."components/tag_manager2/js/web/{$filtersType}.js");
        $modx->regClientScript($modx->config['assets_url']."components/tag_manager2/js/web/view_switch.js");
    }

    $options = array(
        'base_url' => $modx->getOption('base_url', null, '/'),
        'currency_default' => $shk_currency_default,
        'currency_rate' => $shk_currency_rate
    );
    $headHtml = '
    <script type="text/javascript">
    var tmFiltersOptions = '. json_encode( $options ) .';
    jQuery(document).ready(function(){
        tmFilters.init( tmFiltersOptions );
    });
    </script>';
    
    $modx->regClientScript($headHtml,true);
    
}

return $output;