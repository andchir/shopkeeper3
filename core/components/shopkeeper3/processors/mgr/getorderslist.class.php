<?php

/**
 * shkGetOrdersListProcessor
 *
 * @package shopkeeper3
 * @subpackage processors
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class shkGetOrdersListProcessor extends modProcessor {

    public function process() {
        
        $modelpath = $this->modx->getOption('core_path') . 'components/shopkeeper3/model/';
        $this->modx->addPackage( 'shopkeeper3', $modelpath );
        
        $scriptProperties = $this->getProperties();
        $filters = $this->modx->getOption( 'filters', $scriptProperties, array() );
        $page = $this->modx->getOption( 'page', $scriptProperties, 1 );
        $limit = $this->modx->getOption( 'count', $scriptProperties, 20 );
        $offset = $this->modx->getOption( 'offset', $scriptProperties, false );
        $sorting = $this->modx->getOption( 'sorting', $scriptProperties, array( 'id' => 'desc' ) );
        $date_format = $this->modx->getOption( 'date_format', $scriptProperties, 'd.m.Y H:i:s' );
        
        $table_fields = array_keys( $this->modx->getFields('shk_order') );
        
        //sorting
        $opt_sortby = '`shk_order`.`id`';
        $opt_sortdir = 'desc';
        foreach( $sorting as $sortby => $sortdir ){
            if( $sortby == 'username' ) $sortby = 'userid';
            if( in_array( $sortby, $table_fields ) ){
                $opt_sortby = "`shk_order`.`{$sortby}`";
                $opt_sortdir = $sortdir;
            }
            else if( $sortby == 'count_total' ){
                $opt_sortby = "`{$sortby}`";
                $opt_sortdir = $sortdir;
            }
        }
        
        $total = $this->modx->getCount('shk_order');
        if( $offset === false ){
            $offset = $limit * ( $page - 1 );
        }
        
        $userIds = array();
        $orderIds = array();
        $list = array();
        $query = $this->modx->newQuery( 'shk_order' );
        $query->sortby( $opt_sortby, $sortdir );
        $query->limit( $limit, $offset );
        $query->select( '`shk_order`.*, `User`.`username` AS `username`' );
        $query->leftJoin( 'modUser', 'User', '`User`.`id` = `shk_order`.`userid`' );
        $query->select( '(SELECT COUNT(*) FROM ' . $this->modx->getTableName('shk_purchases') . ' WHERE ' . $this->modx->getTableName('shk_purchases') . '.`order_id` = `shk_order`.`id`) AS `count_total`' );
        
        //filtering
        if( !empty( $filters ) ){
            
            foreach( $filters as $f_key => $f_val ){
                
                if( is_array( $f_val ) ){
                    
                    if( $f_key == 'date' ){
                        
                        if( count( $f_val ) >= 2 ){
                            
                            $f_val[0] = str_replace('/', '-', $f_val[0]);
                            $f_val[1] = str_replace('/', '-', $f_val[1]);
                            
                            $wh = array(
                                'date:>=' => date( 'Y-m-d', strtotime( $f_val[0] ) ),
                                'date:<=' => date( 'Y-m-d', strtotime( "+1 day", strtotime( $f_val[1] ) ) ),
                            );
                            
                            $query->where( $wh );
                            
                        }
                            
                        
                    }else{
                        $query->where( array( $f_key.':IN' => $f_val ) );
                    }
                    
                }else{
                    $query->where( array( $f_key => $f_val ) );
                }
                
            }
            
        }
        
        $orders = $this->modx->getIterator( 'shk_order', $query );
        
        if( $orders ){
            
            foreach( $orders as $order ){
                
                $order_data = $order->toArray();
                $order_data['date'] = date( $date_format, strtotime( $order_data['date'] ) );
                $order_data['sentdate'] = date( $date_format, strtotime( $order_data['sentdate'] ) );
                
                if( !empty( $order_data['contacts'] ) ){
                    
                    $contacts = json_decode( $order_data['contacts'], true );
                    $tmp_arr = array();
                    foreach( $contacts as $contact ){
                        $tmp_arr['contacts.'.$contact['name']] = $contact['value'];
                    }
                    $order_data = array_merge( $tmp_arr, $order_data );
                    unset($order_data['contacts']);
                    
                }
                
                array_push( $list, $order_data );
                array_push( $orderIds, $order->id );
                
                if( !empty( $order_data['userid'] ) && !in_array( $order_data['userid'], $userIds ) ){
                    array_push( $userIds, $order_data['userid'] );
                }
                
            }
            
        }
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => $list,
            'total' => $total
        );
        
        return $output;
    }
    
    
    /**
     * appendUserNames
     *
     */
    /*
    public function appendUserNames( &$order_data, $userIds ){
        
        if( !empty( $userIds ) ){
            
            $users_data = array();
            $c = $this->modx->newQuery('modUser');
            $c->where(array('id:IN' => $userIds));
            $c->select(array('id','username'));
            $users = $this->modx->getCollection('modUser',$c);
            foreach ($users as $user) {
                $userArray = $user->toArray();
                $users_data[$user->id] = $userArray['username'];
            }
            
            if( !empty( $users_data ) ){
                
                foreach( $order_data as &$order ){
                    
                    if( !empty( $order['userid'] ) && !empty( $users_data[$order['userid']] ) ){
                        
                        $order['username'] = $users_data[$order['userid']];
                        
                    }
                    
                }
                
            }
            
        }
        
    }
    */
    
    
}
return 'shkGetOrdersListProcessor';
