<?php
/*
 plugin shk_multicurrency
 System event: OnLoadWebDocument, OnSHKgetProductPrice, OnSHKgetProductAdditParamPrice, OnSHKgetDeliveryPrice

<div>
    Валюта:
    <select id="site_currency" name="curency">
        <option value="1">руб.</option>
        <option value="2">грн.</option>
        <option value="3">USD</option>
        <option value="4">euro</option>
    </select>
</div>

*/

$scriptProperties = array_merge(
    array(
        'currency_default' => $modx->getOption( 'shk3.currency_default', null, 1 ),
        'currency_selected' => $modx->getOption( 'shk3.currency_selected', null, 0 )
    ),
    $scriptProperties
);

$shk_currency = !empty($_COOKIE['shk_currency']) && is_numeric($_COOKIE['shk_currency'])
    ? abs(intval($_COOKIE['shk_currency']))
    : $scriptProperties['currency_default'];
//Если нужен всегда преевод в одну валюту
if( !empty( $scriptProperties['currency_selected'] ) && $shk_currency != $scriptProperties['currency_selected'] ){
    setcookie( 'shk_currency', intval($scriptProperties['currency_selected']), time()+3600*24, "/" );
    $shk_currency = intval($scriptProperties['currency_selected']);
}
$currency_id = isset($_GET['scurr']) && is_numeric($_GET['scurr']) ? intval($_GET['scurr']) : $shk_currency;

if(!function_exists('shk_currency_calc')){
    function shk_currency_calc($properties, $base_price, $currency_id, $rate_ratio = 0){
        
        $inverse = isset($properties['inverse']) ? $properties['inverse'] : false;//обратный перевод цены
        
        if( !$rate_ratio ){
            
            if( isset( $_SESSION['shk_curr_rate'] ) && is_numeric( $_SESSION['shk_curr_rate'] ) && !$inverse ){
                
                $rate_ratio = $_SESSION['shk_curr_rate'];
                
            }else{
                
                if( !isset( $properties['currency_rate'] ) ){
                    require_once MODX_CORE_PATH . "components/shopkeeper3/model/shopkeeper.class.php";
                    $config = Shopkeeper::getConfig( array('currency_rate') );
                    $properties['currency_rate'] = $config['currency_rate'];
                }
                
                $rate_ratio = 1;
                
                if( $properties['currency_default'] != $currency_id ){
                    
                    $rate_default = 1;
                    $rate = 1;
                    //Определяем курс по умолчанию и новый курс
                    foreach( $properties['currency_rate'] as $rt ){
                        if( $rt['id'] == $properties['currency_default'] ){
                            $rate_default = Shopkeeper::cleanNumber( $rt['value'], 'float' );
                        }
                        else if( $rt['id'] == $currency_id ){
                            $rate = Shopkeeper::cleanNumber( $rt['value'], 'float' );
                        }
                    }
                    
                    if( !$inverse ){
                        $rate_ratio = $rate_default / $rate;
                        $_SESSION['shk_curr_rate'] = $rate_ratio;
                    }else{
                        $rate_ratio = $rate / $rate_default;
                    }
                    
                }
                
            }
            
        }
        
        //Считаем цену по курсу
        if( $rate_ratio ){
            
            $price = $base_price * $rate_ratio;
            $price = round( $price, ( ceil( $price ) == $price ? 0 : 2 ) );
            return $price;
        
        }else{
            return $base_price;
        }
        
    }
}

switch($modx->event->name){
    
    case 'OnLoadWebDocument':
        
        if( !empty( $modx->placeholders['shk_currency'] ) ) return '';
        
        if(empty($scriptProperties['noScript'])){
            
            $script_str = '
            <script type="text/javascript">
                var shk_cindex = document.cookie.indexOf("shk_currency=") > -1 ? document.cookie.indexOf("shk_currency=") + new String("shk_currency=").length : -1;
                var shk_currency = shk_cindex > -1 ? document.cookie.substring(shk_cindex,shk_cindex+1) : 1;
                jQuery("#site_currency")
                .val(shk_currency)
                .on("change",function(){
                    var loc_href = window.location.pathname+document.location.search;
                    window.location.href = loc_href+(loc_href.indexOf("?") > -1 ? "&" : "?") + "scurr=" + this.value;
                });
            </script>
            ';
            $modx->regClientScript( $script_str, true );
        }
        
        if( isset( $_GET['scurr'] ) ){
            
            if( $currency_id != $shk_currency ){
                
                require_once MODX_CORE_PATH . "components/shopkeeper3/model/shopkeeper.class.php";
                
                $config = Shopkeeper::getConfig( array( 'currency_rate', 'delivery' ) );
                $scriptProperties['currency_rate'] = $config['currency_rate'];
                $conf_delivery = $config['delivery'];
                
            }
            
            $_SESSION['shk_curr_rate'] = null;
            unset($_SESSION['shk_curr_rate']);
            
            //Доставка
            if( !empty( $conf_delivery ) ){
                
                $delivery_label = !empty( $_SESSION['shk_delivery']['label'] ) ? $_SESSION['shk_delivery']['label'] : '';
                $delivery_price = !empty( $_SESSION['shk_delivery']['price'] ) ? $_SESSION['shk_delivery']['price'] : 0;
                $_SESSION['shk_delivery'] = array();
                
                foreach( $conf_delivery as $opt ){
                    
                    if( $opt['label'] == $delivery_label ){
                        
                        $_SESSION['shk_delivery'] = array(
                            'label' => $opt['label'],
                            'price' => $delivery_price > 0 ? Shopkeeper::cleanNumber( $opt['price'], 'float' ) : 0,
                            'old_price' => Shopkeeper::cleanNumber( $opt['price'], 'float' ),
                            'free_start' => Shopkeeper::cleanNumber( $opt['free_start'], 'float' ),
                            'old_free_start' => Shopkeeper::cleanNumber( $opt['free_start'], 'float' )
                        );
                        
                        break;
                    }
                    
                }
                
                if( !empty( $_SESSION['shk_delivery']['price'] ) ){
                    $_SESSION['shk_delivery']['price'] = shk_currency_calc( $scriptProperties, $_SESSION['shk_delivery']['price'], $currency_id );
                }
                if( !empty( $_SESSION['shk_delivery']['old_price'] ) ){
                    $_SESSION['shk_delivery']['old_price'] = shk_currency_calc( $scriptProperties, $_SESSION['shk_delivery']['old_price'], $currency_id );
                }
                if( !empty( $_SESSION['shk_delivery']['free_start'] ) ){
                    $_SESSION['shk_delivery']['free_start'] = shk_currency_calc( $scriptProperties, $_SESSION['shk_delivery']['free_start'], $currency_id );
                    $_SESSION['shk_delivery']['old_free_start'] = $_SESSION['shk_delivery']['free_start'];
                }
                
            }
            
            $purchases = !empty( $_SESSION['shk_order'] ) ? $_SESSION['shk_order'] : array();
            
            if( !empty( $scriptProperties['currency_rate'] ) ){
                
                //Изменяем цены товаров в корзине
                if( !empty( $purchases ) ){
                    
                    foreach( $purchases as $key => &$purchase ){
                        
                        if( isset( $purchase['old_price'] ) ){
                            $base_price = $purchase['old_price'];
                        }
                        else if( !isset( $purchase['old_price'] ) ) {
                            $purchase['old_price'] = $purchase['price'];
                            $base_price = $purchase['price'];
                        }
                        
                        $purchase['price'] = shk_currency_calc( $scriptProperties, $base_price, $currency_id );
                        
                        //Доп. параметры
                        if( !empty( $purchase['options'] ) ){
                            foreach( $purchase['options'] as &$addit_param ){
                                
                                if( !isset( $addit_param[3] ) ) $addit_param[3] = $addit_param[1];
                                $addit_param[1] = shk_currency_calc( $scriptProperties, $addit_param[3], $currency_id );
                                
                            }
                        }
                        
                    }
                    
                    $_SESSION['shk_order'] = $purchases;
                    
                }else{
                    
                    //Если нет товаров, просто переключаем валюту
                    shk_currency_calc( $scriptProperties, 0, $currency_id );
                    
                }
                
                $shk_currency = $currency_id;
                setcookie( 'shk_currency', $shk_currency, time()+3600*24, "/" );
                
                //Сохраняем название валюты
                $currency_name = '';
                foreach( $scriptProperties['currency_rate'] as $rt ){
                    if( $rt['id'] == $currency_id ){
                        $currency_name = $rt['label'];
                        break;
                    }
                }
                $_SESSION['shk_currency_name'] = $currency_name;
                
            }
            
            $back_url = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $modx->makeURL($modx->resource->get('parent'),'','','abs');
            if( $modx->config['friendly_urls'] ){
                if( strpos($back_url,'?') !== false ) $back_url = substr( $back_url, 0, strpos($back_url,'?') );
            }
            
            $modx->sendRedirect( $back_url, 0 );
            
        }
        
        $currency_name = !empty($_SESSION['shk_currency_name']) ? $_SESSION['shk_currency_name'] : '';
        if( !$currency_name ){
            $currency_name = $modx->getOption('shk3.currency',null,'');
        }
        $modx->setPlaceholder( 'shk_currency', $currency_name );
        
    break;
    case 'OnSHKgetProductAdditParamPrice':
    case 'OnSHKgetDeliveryPrice':
    case 'OnSHKgetProductPrice':

        if( !empty( $modx->event->returnedValues ) ){//Get from previous plugin
            $output = end( $modx->event->returnedValues );
        } else {
            $output = $modx->getOption( 'price', $scriptProperties, 0 );
        }
        
        if( is_numeric( $output ) ){
            
            //Считаем цену по курсу
            $output = shk_currency_calc( $scriptProperties, $output, $currency_id );
            
            $modx->event->_output = '';
            $modx->event->output( $output );
            
        }else{
            $modx->event->output( false );
        }
        
    break;
    
}

return '';