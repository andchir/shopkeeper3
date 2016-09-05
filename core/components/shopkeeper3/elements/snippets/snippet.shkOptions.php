<?php

/**
 * shkOptions
 * Сниппет выводит данные из конфигурации Shopkeeper
 *
 */

$output = array();

$get = $modx->getOption( 'get', $scriptProperties, '' );
$post_name = explode( ',', $modx->getOption( 'post_name', $scriptProperties, $get ) );
$get = explode( ',', $get );
$get = array_map( 'trim', $get );
$post_name = array_map( 'trim', $post_name );
$tpl = $modx->getOption( 'tpl', $scriptProperties, '' );
$toPlaceholders = $modx->getOption( 'toPlaceholders', $scriptProperties, false );
$pl_prefix = $modx->getOption( 'pl_prefix', $scriptProperties, 'shkopt_' );

if( empty( $get ) ) return '';

if( class_exists('Shopkeeper') ){
    
    $config = Shopkeeper::getConfig( $get );
    
    //echo '<pre>' . print_r( $config, true ) . '</pre>';
    
    if( !empty( $config ) ){
        
        foreach( $get as $index => $opt_name ){
            
            if( !empty( $config[ $opt_name ] ) ){
                
                $output[ $opt_name ] = '';
                
                foreach( $config[ $opt_name ] as $key => $conf ){
                    
                    if( empty( $conf['value'] ) ){
                        $conf['value'] = $conf['label'];
                    }
                    
                    $conf['selected'] = ( isset( $post_name[$index] ) && isset( $_POST[ $post_name[$index] ] ) && $_POST[ $post_name[$index] ] == $conf['value'] ? 'selected="selected"' : '' );
                    
                    $output[ $opt_name ] .= $modx->getChunk( $tpl, $conf ) . PHP_EOL . "\t";
                    
                }
                
            }
            
        }
        
    }
    
}

if( $toPlaceholders ){
    
    foreach( $output as $pl_name => $out ){
        $modx->setPlaceholder( $pl_prefix . $pl_name, $out );
    }
    
    $output = array();
    
}

return implode( '', $output );
