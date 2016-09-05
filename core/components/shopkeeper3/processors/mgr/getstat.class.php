<?php

/**
 * shkGetStatProcessor
 *
 * @package shopkeeper3
 * @subpackage processors
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class shkGetStatProcessor extends modProcessor {

    public function process() {
        
        $modelpath = $this->modx->getOption('core_path') . 'components/shopkeeper3/model/';
        $this->modx->addPackage( 'shopkeeper3', $modelpath );
        
        $scriptProperties = $this->getProperties();
        $filters = $this->modx->getOption( 'filters', $scriptProperties, array() );
        
        if( $filters && is_string( $filters ) ){
            $filters = json_decode( $filters, true );
        }
        
        if( $filters && !empty( $filters['date'] ) ){
            
            $date_from = strtotime( str_replace('/', '-', $filters['date'][0] ) );
            $date_to = strtotime( str_replace('/', '-', $filters['date'][1] ) ) ? strtotime( "+1 day", strtotime( str_replace('/', '-', $filters['date'][1] ) ) ) : false;
            
            if( $date_from && $date_to ){
                $filters['date'] = array(
                    date( 'Y-m-d', $date_from ),
                    date( 'Y-m-d', $date_to )
                );
            }else{
                unset( $filters['date'] );
            }
            
        }
        
        $this->modx->getService('lexicon','modLexicon');
        $this->modx->lexicon->load($this->modx->config['manager_language'].':shopkeeper3:manager');
        
        $data = array( 'columns' => array(), 'names' => array(), 'colors' =>array() );
        
        //settings
        $settings = array();
        $response = $this->modx->runProcessor( 'getsettings',
            array( 'settings' => array( 'statuses' ) ),
            array('processors_path' => $this->modx->getOption( 'core_path' ) . 'components/shopkeeper3/processors/mgr/')
        );
        if ( !$response->isError() ) {
            $settings = $response->getObject();
        }
        
        //Собираем статистику
        $stat_data = array();
        $stat_keys = array();
        foreach( $settings['statuses'] as $status ){
            
            $stat_val = $this->getStatusStat( $status[ 'id' ], $filters );
            
            $stat_data[ 'status' . $status['id'] ] = $stat_val;
            $data['names'][ 'status' . $status['id'] ] = $status['label'];
            $data['colors'][ 'status' . $status['id'] ] = $status['color'];
            
            foreach( $stat_val as $k => $v ){
                if( !in_array( $k, $stat_keys ) ){
                    array_push( $stat_keys, $k );
                }
            }
            
        }
        
        //Сортируем даты
        function cmp( $a, $b ) {
            return ( strtotime($a) < strtotime($b) ) ? -1 : 1;
        }
        usort( $stat_keys, "cmp" );
        
        $data['x'] = 'x';
        $data['columns'][0] = array_merge( array('x'), $stat_keys );
        
        //Формируем массивы со статистикой
        foreach( $settings['statuses'] as $status ){
            
            $k = 'status' . $status['id'];
            $tmp = array( $k );
            
            foreach( $stat_keys as $stat_key ){
                
                $tmp[] = isset( $stat_data[ $k ][ $stat_key ] ) ? intval( $stat_data[ $k ][ $stat_key ] ) : 0;
                
            }
            
            $data['columns'][] = $tmp;
            
        }
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => $data
        );
        
        return $output;
        
    }
    
    public function getStatusStat( $status_id = 0, $filters = array() ){
        
        $stat_month = array();
        
        $sql = "
        SELECT month(`date`) AS `order_month`, year(`date`) AS `order_year`, count(*) AS `order_count`
        FROM ".$this->modx->getTableName('shk_order')."
        ";
        
        if( $status_id ){
            $sql .= "
            WHERE `status` = '{$status_id}'
            ";
        }
        
        if( !empty( $filters ) ){
            
            $sql .= $status_id ? " AND" : " WHERE";
            
            foreach( $filters as $k => $v ){
                if( $k == 'date' ){
                    $sql .= " ( `date` >= '{$v[0]}' AND `date` <= '{$v[1]}' )";
                }
            }
            
        }
        
        $sql .= "
        GROUP BY year(`date`), month(`date`)
        ORDER BY year(`date`), month(`date`) DESC
        LIMIT 5
        ";
        
        $stmt = $this->modx->prepare($sql);
        if ($stmt && $stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $d_num = cal_days_in_month(CAL_GREGORIAN, $row['order_month'], $row['order_year']);
                $stat_month[ $row['order_year'] . '-' . $row['order_month'] . '-' . $d_num ] = $row['order_count'];
            }
            $stmt->closeCursor();
        }
        
        return $stat_month;
        
    }
    
    
}

return 'shkGetStatProcessor';
