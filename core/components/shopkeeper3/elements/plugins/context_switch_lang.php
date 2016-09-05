<?php

/**
 * OnHandleRequest
 *
 */

switch($modx->event->name){
    
    case "OnHandleRequest":
        
        $contexts = $modx->getOption('contexts', null, 'en,de');
        $contexts = array_map( 'trim', explode(',',$contexts) );
        
        $request_uri = substr($_SERVER['REQUEST_URI'],0,1)=='/' ? substr($_SERVER['REQUEST_URI'],1) : $_SERVER['REQUEST_URI'];
        if( strpos( $request_uri, '/' ) !== false ){
            $lang_key = substr( $request_uri, 0, strpos( $request_uri, '/' ) );
        }else{
            $lang_key = $request_uri;
        }
        
        if( in_array( $lang_key, $contexts ) ){
            
            $modx->reloadContext( $lang_key );
            $modx->switchContext( $lang_key );
            $modx->setOption( 'cultureKey', $lang_key );
            
        }
        
    break;
}

return '';
