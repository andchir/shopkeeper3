<?php

/**
 * shkSaveSettingsListProcessor
 *
 * @package shopkeeper3
 * @subpackage processors
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class shkSaveSettingsProcessor extends modProcessor {

    public function process() {
        
        $output = array(
            'success' => true,
            'message' => ''
        );
        
        //check permissions
        if( !$this->modx->hasPermission('save') ){
            $output['success'] = false;
            $output['message'] = $this->modx->lexicon('permission_denied');
            return $output;
        }
        
        $modelpath = $this->modx->getOption('core_path') . 'components/shopkeeper3/model/';
        $this->modx->addPackage( 'shopkeeper3', $modelpath );
        
        $scriptProperties = $this->getProperties();
        
        //print_r($scriptProperties); exit;
        
        if( !empty( $scriptProperties['data'] ) ){
            
            foreach( $scriptProperties['data'] as $key => $data ){
                
                if( is_array( $data ) ){
                    
                    $s_value = array();
                    $type = 'array';
                    
                    foreach( $data as $k => $val ){
                        $val['id'] = $k + 1;
                        array_push( $s_value, $val );
                    }
                    
                    //sorting
                    if( isset( $s_value[0]['rank'] ) && is_numeric($s_value[0]['rank']) ){
                        
                        $this->array_sort( $s_value );
                        
                    }
                    
                    $s_value = defined('JSON_UNESCAPED_UNICODE') ? json_encode( $s_value, JSON_UNESCAPED_UNICODE ) : json_encode( $s_value );
                    
                }else{
                    
                    $s_value = $data;
                    $type = 'string';
                    
                }
                
                $setting = array(
                    'setting' => $key,
                    'value' => $s_value,
                    'xtype' => $type
                );
                
                /*
                if( $this->modx->getCount( 'shk_config', array( 'setting' => $key ) ) > 1 ){
                    $this->modx->removeCollection( 'shk_config', array( 'setting' => $key ) );
                }
                */
                
                $config_item = $this->modx->getObject( 'shk_config', array( 'setting' => $key ) );
                if( !$config_item ){
                    $config_item = $this->modx->newObject( 'shk_config' );
                }
                $config_item->fromArray($setting);
                
                $saved = $config_item->save();
                
            }
            
        }
        
        return $output;
        
    }
    
    
    /**
     * array_sort
     *
     */
    public function array_sort( &$array ){
        
        if( !function_exists('cmp') ){
            function cmp($a, $b){
                if( empty( $a['rank'] ) ){
                    $a['rank'] = 0;
                }
                if( empty( $b['rank'] ) ){
                    $b['rank'] = 0;
                }
                if ($a['rank'] == $b['rank']) {
                    return 0;
                }
                return ($a['rank'] < $b['rank']) ? -1 : 1;
            }
        }
        
        usort($array, "cmp");
        
        foreach( $array as $k => &$val ){
            $val['id'] = $k + 1;
            $val['rank'] = $k;
        }
        
    }
    
    
}
return 'shkSaveSettingsProcessor';
