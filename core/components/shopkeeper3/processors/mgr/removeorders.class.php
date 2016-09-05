<?php

/**
 * shkRemoveOrdersProcessor
 *
 * @package shopkeeper3
 * @subpackage processors
 */

class shkRemoveOrdersProcessor extends modProcessor {

    public function process() {
        
        $output = array(
            'success' => $success,
            'message' => ''
        );
        
        //check permissions
        if( !$this->modx->hasPermission('remove') ){
            $output['success'] = false;
            $output['message'] = $this->modx->lexicon('permission_denied');
            return $output;
        }
        
        $success = false;
        
        $modelpath = $this->modx->getOption('core_path') . 'components/shopkeeper3/model/';
        $this->modx->addPackage( 'shopkeeper3', $modelpath );
        
        $scriptProperties = $this->getProperties();
        $order_ids = $this->modx->getOption( 'order_id', $scriptProperties, array() );
        
        if( !empty( $order_ids ) ){
            
            /*
            $affected = $this->modx->removeCollection(
                'shk_order',
                array( "id:IN" => $order_ids )
            );
            */
            
            foreach( $order_ids as $order_id ){
                
                $order = $this->modx->getObject( 'shk_order', $order_id );
                if( $order ){
                    $order->remove();
                }
                
            }
            
            $success = true;
            
        }
        
        $output['success'] = $success;
        
        return $output;
        
    }
    
}

return 'shkRemoveOrdersProcessor';
