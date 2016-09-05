<?php

/**
 * migx_autoalias
 *
 * OnDocFormSave
 *
 */

/*

$modx->invokeEvent( 'OnDocFormSave', array( 'id' => $object->get('id'), 'object' => &$object ) );

*/

$namefield = $modx->getOption( 'namefield', $scriptProperties, 'pagetitle' );

if( $object->get('resource_id') ){
    
    if( !$object->get('alias') ){
        
        $resource = $modx->newObject('modResource');
        $alias = $resource->cleanAlias( $object->get( $namefield ) );
        
        $object->set( 'alias', $alias );
        $object->save();
        
    }
    
}

return '';
