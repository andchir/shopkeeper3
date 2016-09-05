<?php

/*
 * shk_curr_rate snippet
 * example: [[!*price:shk_curr_rate]] [[!+shk_currency]]
 */

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

$scriptProperties = array_merge(
    array(
        'currency_default' => $modx->getOption( 'shk3.currency_default', null, 1 ),
        'currency_selected' => $modx->getOption( 'shk3.currency_selected', null, 0 )
    ),
    $scriptProperties
);

$output = floatval(str_replace(array(' ',','), array('','.'), $scriptProperties['input']));
$shk_currency = !empty($_COOKIE['shk_currency']) && is_numeric($_COOKIE['shk_currency']) ? abs(intval($_COOKIE['shk_currency'])) : $scriptProperties['currency_default'];
//Если нужен всегда преевод в одну валюту
if( !empty( $scriptProperties['currency_selected'] ) && $shk_currency != $scriptProperties['currency_selected'] ){
    setcookie( 'shk_currency', intval($scriptProperties['currency_selected']), time()+3600*24, "/" );
    $shk_currency = intval($scriptProperties['currency_selected']);
}

//Считаем цену по курсу
$output = shk_currency_calc( $scriptProperties, $output, $shk_currency );

return $output;
