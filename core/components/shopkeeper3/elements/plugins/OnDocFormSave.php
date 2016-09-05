<?php

/**
 * OnDocFormSave
 *
 */

/*

$modx->invokeEvent( 'OnDocFormSave', array( 'id' => $object->get('id'), 'resource' => &$object ) );

*/

$namefield = $modx->getOption( 'namefield', $scriptProperties, 'pagetitle' );

if( !empty($resource) && is_object($resource) && $resource->get('resource_id') ){
    
    if( !$resource->get('alias') ){
        
        $res = $modx->newObject('modResource');
        $alias = $res->cleanAlias( $resource->get( $namefield ) );
        
        $resource->set( 'alias', $alias );
        $resource->save();
        
    }
    
}

return '';
