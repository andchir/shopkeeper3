<?php

/**
 * shkGetSnippetPropertiesProcessor
 *
 * @package shopkeeper3
 * @subpackage processors
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class shkGetSnippetPropertiesProcessor extends modProcessor {

    public function process() {
        
        $sys_property_sets = $this->modx->getOption( 'shk3.property_sets', $this->modx->config, 'default' );
        $sys_property_sets = explode(',', $sys_property_sets);
        $propertySetName = $sys_property_sets ? trim(array_shift($sys_property_sets)) : 'default';
        
        $snippet = $this->modx->getObject('modSnippet',array('name'=>'Shopkeeper3'));
        $properties = $snippet->getProperties();
        if( $propertySetName != 'default' && $this->modx->getCount( 'modPropertySet', array( 'name' => $propertySetName ) ) > 0 ){
            $propSet = $this->modx->getObject( 'modPropertySet', array( 'name' => $propertySetName ) );
            $propSetProperties = $propSet->getProperties();
            if(is_array($propSetProperties)) $properties = array_merge( $properties, $propSetProperties );
        }
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => $properties
        );
        
        return $output;
        
    }
    
}

return 'shkGetSnippetPropertiesProcessor';
