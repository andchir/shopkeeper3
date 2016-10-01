<?php
/*

Example:

<input type="search" name="f_pagetitle[like]" value="[[!getRequest?paramName=`f_pagetitle.like`]]">

*/

$paramName = $modx->getOption( 'paramName', $scriptProperties, '' );
$defaultValue = $modx->getOption( 'defaultValue', $scriptProperties, '' );
$requestType = $modx->getOption( 'requestType', $scriptProperties, 'get' );

$requestData = $requestType == 'post' ? $_POST : $_GET;
$output = $defaultValue;

if( strpos( $paramName, '.' ) !== false ){
    
    $pn = explode( '.', $paramName );
    if( count( $pn ) >= 2
       && isset( $requestData[ $pn[0] ] )
       && isset( $requestData[ $pn[0] ][ $pn[1] ] )
       && !is_array( $requestData[ $pn[0] ][ $pn[1] ] ) ){
        
        $output = trim( $requestData[ $pn[0] ][ $pn[1] ] );
        
    }
    
}
else {
    
    if( isset( $requestData[ $paramName ] )
       && !is_array( $requestData[ $paramName ] ) ){
        
        $output = trim( $requestData[ $paramName ] );
        
    }
    
}

$output = $modx->stripTags( $output );
$output = $modx->sanitizeString( $output );

return $output;