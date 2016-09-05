<?php

/**
 * shkUpdateOrderStatusProcessor
 *
 * @package shopkeeper3
 * @subpackage processors
 */

class shkUpdateOrderStatusProcessor extends modProcessor {

    public function process() {
        
        $output = array(
            'success' => false,
            'message' => ''
        );
        
        //check permissions
        if( $this->modx->user && $this->modx->user->hasSessionContext('mgr') && !$this->modx->hasPermission('save') ){
            $output['success'] = false;
            $output['message'] = $this->modx->lexicon('permission_denied');
            return $output;
        }
        
        $modelpath = $this->modx->getOption('core_path') . 'components/shopkeeper3/model/';
        $this->modx->addPackage( 'shopkeeper3', $modelpath );
        
        $scriptProperties = $this->getProperties();
        $status = $this->modx->getOption( 'status', $scriptProperties, 0 );
        $order_ids = $this->modx->getOption( 'order_id', $scriptProperties, array() );
        
        if( $status && $order_ids ){
            
            $affected = $this->modx->updateCollection(
                'shk_order',
                array( 'status' => $status ),
                array( "id:IN" => $order_ids )
            );
            
            if( $affected !== false ){
                
                $this->modx->invokeEvent( 'OnSHKChangeStatus', array( 'order_ids' => $order_ids, 'status' => $status ) );
                
                $output['success'] = true;
                
                $this->notifyBuyers( $order_ids, $status );
                
            }
            
        }
        
        return $output;
        
    }
    
    /**
     * notifyBuyers
     *
     * @param array $order_ids
     * @param integer $status
     * @return boolean
     */
    public function notifyBuyers( $order_ids, $status ){
        
        $output = true;
        
        if( !is_array( $order_ids ) ){
            $order_ids = array( $order_ids );
        } 
        
        //Параметры сниппета Shopkeeper3
        $snippet_properties = array();
        $response = $this->modx->runProcessor( 'getsnippetproperties',
            array(),
            array('processors_path' => $this->modx->getOption( 'core_path' ) . 'components/shopkeeper3/processors/mgr/')
        );
        if ( !$response->isError() ) {
            $snippet_properties = $response->getObject();
        }
        
        require_once $this->modx->getOption('core_path') . "components/shopkeeper3/model/shopkeeper.class.php";
        $shopCart = new Shopkeeper( $this->modx, $snippet_properties );
        
        $config =  $shopCart->getConfig( array( 'statuses' ) );
        
        foreach( $order_ids as $order_id ){
            
            //Данные заказа
            $response = $this->modx->runProcessor( 'getorder',
                array('order_id' => $order_id),
                array('processors_path' => $this->modx->getOption( 'core_path' ) . 'components/shopkeeper3/processors/mgr/')
            );
            if ($response->isError()) {
                $output = false;
                break;
            }else{
                
                $order_data = $response->getObject();
                
                if( empty( $order_data['email'] ) ) { continue; }
                
                //Параметры статуса
                $status_data = array();
                foreach( $config['statuses'] as $st ){
                    if( $st['id'] == $status ){
                        $status_data = $st;
                        break;
                    }
                }
                
                if( empty( $status_data ) || empty( $status_data['tpl'] ) ) { continue; }
                
                $mainChunk = $shopCart->getChunk( $status_data['tpl'] );
                
                //Данные товаров заказа
                $purchasesArray = $order_data['purchases'];
                foreach( $purchasesArray as &$purchase ){
                    
                    if( !empty( $purchase['options'] ) ){
                        $p_options = $shopCart->getPurchasesOptionsData( $purchase['options'] );
                        $purchase = array_merge( $purchase, $p_options );
                        unset( $purchase['options'] );
                    }
                    
                }
                
                //Формируем письмо
                $orderOutputData = $shopCart->getOrderData( $order_id );
                
                $chunkArr = array(
                    'orderID' => $order_data['id'],
                    'orderDate' => $order_data['date'],
                    'statusName' => $status_data['label'],
                    'orderCurrency' => $shopCart->config['currency'],
                    'orderOutputData' => $orderOutputData,
                );
                
                unset( $order_data['contacts'], $order_data['purchases'] );
                
                $chunkArr = array_merge( $order_data, $chunkArr );
                
                $mail_body = $shopCart->parseTpl( $mainChunk, $chunkArr, $status_data['tpl'] );
                
                $subject = $this->modx->lexicon( 'shk3.order_change_status' );
                
                $this->SHKsendMail( $subject, $mail_body, $order_data['email'] );
                
            }
            
            
        }
        
        return $output;
        
    }
    
    /**
     * sendMail
     *
     */
    public function SHKsendMail( $subject, $body, $to ){
        
        if( empty( $to ) ) return false;
        
        $email_from = !empty( $this->modx->config['emailto'] ) ? $this->modx->config['emailto'] : $this->modx->config['emailsender'];
        
        $this->modx->getService( 'mail', 'mail.modPHPMailer' );
        $this->modx->mail->set( modMail::MAIL_BODY, $body );
        $this->modx->mail->set( modMail::MAIL_FROM, $email_from );
        $this->modx->mail->set( modMail::MAIL_SENDER, $email_from );
        $this->modx->mail->set( modMail::MAIL_FROM_NAME, $this->modx->config['site_name'] );
        //$this->modx->mail->set( modMail::MAIL_SENDER, $this->modx->config['site_name'] );
        $this->modx->mail->set( modMail::MAIL_SUBJECT, $subject );
        $this->modx->mail->address( 'to', $to );
        $this->modx->mail->setHTML( true );
        if (!$this->modx->mail->send()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$this->modx->mail->mailer->ErrorInfo);
        }
        $this->modx->mail->reset();
        
        return true;
        
    }
    
}

return 'shkUpdateOrderStatusProcessor';
