<?php

/**
 * shkGetSettingsListProcessor
 *
 * @package shopkeeper3
 * @subpackage processors
 */

class shkGetSettingsProcessor extends modProcessor {

    public function process() {
        
        $modelpath = $this->modx->getOption('core_path') . 'components/shopkeeper3/model/';
        $this->modx->addPackage( 'shopkeeper3', $modelpath );
        
        $scriptProperties = $this->getProperties();
        $settings = $this->modx->getOption( 'settings', $scriptProperties, array() );
        
        $data = array();
        
        $query = $this->modx->newQuery( 'shk_config' );
        if( !empty( $settings ) ){
            $query->where( array( "setting:IN" => $settings ) );
        }
        $config = $this->modx->getIterator( 'shk_config', $query );
        
        if( !empty( $config ) ){
            
            foreach( $config as $key => $conf ){
                
                if( $conf->xtype == 'array' ){
                    $config_value = json_decode( $conf->value, true );
                }else{
                    $config_value = $conf->value;
                }
                
                $data[ $conf->setting ] = $config_value;
                
            }
            
        }
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => $data
        );
        
        return $output;
        
    }
    
}
return 'shkGetSettingsProcessor';
