<?php

/**
 * Class for getProducts.
 *
 * @package getproducts
 * @version 1.5beta1
 * @author Andchir <andchir@gmail.com>
 */

class getProducts
{
    
    protected $total = 0;
    public $modx = null;
    public $config = array();
    public $products = array();
    public $ids_arr = array();
    public $parent_ids = array();
    public $parents_data = array();
    public $table_fields = array('id');
    public $tvRenderers = array();
    
    public function __construct(modX &$modx, array $config = array())
    {
        
        $this->modx =& $modx;
        
        $this->config = array_merge(array(
            "tplPath" => 'core/components/getproducts/elements/chunks/',
            'parents' => is_object( $this->modx->resource ) ? $this->modx->resource->get('id') : '1',
            'resources' => '',
            'tpl' => '',
            'outerTpl' => '',
            'depth' => 0,
            'tvFilters' => array(),
            'where' => '',
            'className' => 'modResource',
            'packageName' => '',
            'table_name' => $this->modx->config['table_prefix']."site_content",
            'table_prefix' => $this->modx->config['table_prefix'],
            'parent_field' => empty($config['className']) || $config['className']=='modResource' ? "parent" : "resource_id",
            'context' => '',
            'limit' => '',
            'offset' => '0',
            'groupby' => '',
            'sortby' => 'menuindex',
            'sortdir' => 'ASC',
            'orderby' => '',
            'orderbyResources' => false,
            'sortbyTV' => '',
            'sortdirTV' => 'ASC',
            'sortbyTVType' => 'string',
            'includeTVs' => false,
            'includeTVList' => '',
            'processTVs' => false,
            'processTVList' => '',
            'fromParentList' => '',
            'fromParentHeight' => 1,
            'activeClass' => 'active',
            'outputSeparator' => "\n",
            'addSubItemCount' => false,
            'subItemCountWhere' => '',
            'activeParentSnippet' => '',
            'includeContent' => false,
            'useSmarty' => false,
            'debug' => false
        ),$config);
        
        if( empty( $this->config['migx_configName'] ) ) $this->config['migx_configName'] = $this->config['packageName'];
        
        $this->config['tvFilters'] = !empty($this->config['tvFilters']) ? $this->modx->fromJSON($this->config['tvFilters']) : array();
        if(!$this->config['context']) $this->config['context'] = $this->modx->context->get('key');
        
        $this->config['outputSeparator'] = str_replace( '\n', PHP_EOL, $this->config['outputSeparator'] );
        $this->config['processTVList'] = !empty($this->config['processTVList']) ? explode(',',str_replace(' ','',$this->config['processTVList'])) : array();
        $this->config['includeTVList_arr'] = !empty($this->config['includeTVList']) ? explode(',',$this->config['includeTVList']) : array();
        $this->config['fromParentList_arr'] = !empty($this->config['fromParentList']) ? explode(',',str_replace(' ','',$this->config['fromParentList'])) : array();
        
        if($this->config['orderby']){
            $this->config['orderby'] = $this->modx->fromJSON($this->config['orderby']);
        }else{
            $this->config['orderby'] = array($this->config['sortby'] => $this->config['sortdir']);
        }
        
        $this->config['all_parents'] = array();
        if( is_object( $this->modx->resource ) ){
            $this->config['all_parents'] = $this->modx->getParentIds($this->modx->resource->get('id'), 10, array('context'=>$this->config['context']));
        }
        
        //Пакет класса объектов
        if( $this->config['className'] == 'modResource' ){
            $this->table_fields = array_merge($this->table_fields,array_keys($this->modx->map['modResource']['fields']));
        } else {
            $modelpath = $this->modx->getOption('core_path') . 'components/' . $this->config['packageName'] . '/model/';
            $added = $this->modx->addPackage($this->config['packageName'], $modelpath);
            if($added){
                $mapFile = $modelpath . $this->config['packageName'] .'/'. $this->modx->config['dbtype'] . '/' .strtolower($this->config['className']). '.map.inc.php';
                if(file_exists($mapFile)){
                    include $mapFile;
                    $metaMap = $xpdo_meta_map[ucfirst($this->config['className'])];
                    $this->config['table_name'] = $this->modx->config['table_prefix'].$metaMap['table'];
                    $this->table_fields = array_merge($this->table_fields,array_keys($metaMap['fields']));
                }
            }
        }
        
    }
    
    /**
    * Возвращает массив данных чанка и кэширует чанк
    * 
    * @param string $source
    * @param array $properties
    * @return string
    */
    public function getChunk( $source, $properties = null )
    {
       if(!$source) return array('name'=>'','snippet'=>'');
       $chunk_arr = array();
       $uniqid = uniqid();
       $_validTypes = array('@CHUNK','@FILE','@INLINE');
       $type = '@CHUNK';
       if (strpos($source, '@') === 0) {
           $endPos = strpos($source, ' ');
           if ($endPos > 2 && $endPos < 10) {
               $tt = substr($source, 0, $endPos);
               if (in_array($tt, $_validTypes)) {
                   $type = $tt;
                   $source = substr($source, $endPos + 1);
               }
           }
       }
       if (!is_string($type) || !in_array($type, $_validTypes)) $type = $this->modx->getOption('tplType', $properties, '@CHUNK');
       $content = false;
       switch ($type) {
           case '@FILE':
               $path = $this->modx->getOption('tplPath', $properties, $this->config['tplPath']);
               $key = $path . $source;
               $key = MODX_BASE_PATH . $path . $source;
               if (file_exists($key)) {
                   $content = file_get_contents($key);
               }
               if (!empty($content) && $content !== '0') {
                   $chunk_arr = array('name'=>$key,'snippet'=>$content);
               }
           break;
           case '@INLINE':
               $chunk_arr = array('name'=>"{$type}-{$uniqid}",'snippet'=>$source);
           break;
           case '@CHUNK':
           default:
               $chunk = null;            
               $chunk = $this->modx->getObject('modChunk', array('name' => $source));
               if ($chunk) {
                   $chunk_arr = $chunk->toArray();
               }
           break;
       }
       
       $chunk = $this->modx->newObject('modChunk');
       $chunk->fromArray($chunk_arr);
       $chunk->setCacheable(false);
       
       return $chunk;
    }
   
   
    /**
     * Парсит чанк, возвращает HTML-код
     * 
     * @param string $source
     * @param array $properties
     * @return string
     */
    public function parseTpl($mainChunk, $properties = null)
    {
        
        $output = '';
        
        //Smarty
        if( $this->config['useSmarty'] ){
            
            $this->modx->smarty->clear_assign('item');
            $this->modx->smarty->assign('item',$properties);
            $output = $this->modx->smarty->fetch('string:'.$mainChunk->get('snippet'));
            
        }
        //Парсер MODX
        else{
            
            if(is_object($mainChunk)){
                $chunk = $this->modx->newObject('modChunk');
                $chunk->fromArray($mainChunk->toArray());
                $chunk->setCacheable(false);
                $output = $chunk->process($properties);
            }
            if (empty($output) && $output !== '0') {
                $prefix = $this->modx->getOption('tplPrefix', $properties, '');
                $chunk = $this->modx->newObject('modChunk');
                $chunk->setCacheable(false);
                $output = $chunk->process(array("{$prefix}output" => print_r($properties, true)), "<pre>[[+{$prefix}output]]</pre>");
            }
            
        }
        
        return $output;
    }
    
    /**
     * Создание SQL для фильтрации
     *
     * @param string $flt_act
     * @param mixed $ftl_val
     * @param boolean $convert
     */
    public function prepareFiltersSQL( $flt_act, $ftl_val, $convert = false, $f_index = 0 )
    {
        $output = array( 'sql' => '', 'bind' => array() );
        $flt_act = explode(':', $flt_act);
        
        if( isset( $flt_act[1] ) ) {
            if( strpos( $flt_act[1], ',' ) !== false ) {
                $flt_act[1] = explode( ',', $flt_act[1] );
            }
        } else {
            $flt_act[1] = '=';
        }
        
        if( !is_array( $flt_act[1] ) && in_array( strtoupper( $flt_act[1] ), array('IN','NOT IN') ) ){
            
            $sql = "`tv`.`name` = :tvname{$f_index} AND ";
            $output['bind'][] = array("tvname{$f_index}", $flt_act[0], PDO::PARAM_STR);
            
            $ids_arr = $ftl_val;
            $valpl_arr = array();
            foreach($ids_arr as $k => $id){
                $output['bind'][] = array($flt_act[0].'_in'.$k, $id, PDO::PARAM_STR);
                $valpl_arr[] = ':'.$flt_act[0].'_in'.$k;
            }
            $inQuery = implode(', ', $valpl_arr);
            $sql .= " `tvc`.`value` {$flt_act[1]} ({$inQuery}) ";
            
        }else{
            
            if(!is_array($ftl_val)) $ftl_val = array($ftl_val);
            
            $sql = "`tv`.`name` = :tvname{$f_index} AND (";
            $output['bind'][] = array("tvname{$f_index}", $flt_act[0], PDO::PARAM_STR);
            
            foreach( $ftl_val as $key => $val ){
                
                if( is_array( $flt_act[1] ) && !empty( $flt_act[1] ) ){
                    $f_act = isset( $flt_act[1][$key] ) ? $flt_act[1][$key] : $flt_act[1][0];
                }else{
                    $f_act = !empty($flt_act[1]) ? $flt_act[1] : '=';
                }
                $f_val = isset( $ftl_val[$key] ) ? $ftl_val[$key] : $ftl_val[0];
                
                if( count( $ftl_val ) % 2 == 0 && isset( $flt_act[2] ) && $flt_act[2] == 'BETWEEN' ){
                    
                    if($key%2==1){ continue; }
                    
                    if($key>0) $sql .= " OR ";
                    $sql .= " (`tvc`.`value` BETWEEN ".$ftl_val[$key]." AND ".$ftl_val[$key+1]." ) ";
                    
                }else{
                    
                    if($key>0) $sql .= isset($flt_act[2]) ? " {$flt_act[2]} " : ' OR ';
                    
                    if((in_array($f_act,array('<','>','<=','>=')) && is_numeric($f_val))){
                        if($convert)
                            $sql .= "CAST(`tvc`.`value` AS DECIMAL(10,2)) {$f_act} :tvvalue{$f_index}".($key+1);
                        else
                            $sql .= "`tvc`.`value` {$f_act} :tvvalue{$f_index}".($key+1);
                    }else{
                        $sql .= "`tvc`.`value` {$f_act} :tvvalue{$f_index}".($key+1);
                    }
                    $output['bind'][] = array("tvvalue{$f_index}".($key+1), $f_val, PDO::PARAM_STR);
                    
                }
                
            }
            
            $sql .= ")";
            
        }
        
        $output['sql'] = $sql;
        
        return $output;
        
    }
    
    
    /**
     * Создание SQL из параметра where
     * 
     * @param string $where_input
     */
    public function prepareWhereSQL( $where_input, $as = "sc" )
    {
        
        $output = array('sql'=>'','bind'=>array());
        $where_arr = $where_input ? json_decode( $where_input, true ) : array();
        if( empty( $where_arr ) ) { return $output; }
        
        $as = $this->modx->escape($as);
        $where_str = '';
        $cnt = 1;
        foreach($where_arr as $key => $val){
            
            $wh = explode(':',$key);
            if(!isset($wh[1])) $wh[1] = '=';
            if(!isset($wh[2])) $wh[2] = 'AND';
            if(in_array(strtoupper($wh[1]),array('IN','NOT IN')) && is_array($val)){
                
                $ids_arr = $val;
                $valpl_arr = array();
                foreach($ids_arr as $k => $id){
                    $output['bind'][] = array($wh[0].'_in'.$k, $id, PDO::PARAM_STR);
                    $valpl_arr[] = ':'.$wh[0].'_in'.$k;
                }
                $inQuery = implode(', ', $valpl_arr);
                $where_str .= " {$wh[2]} {$as}.`{$wh[0]}` {$wh[1]} ({$inQuery}) \n";
                
            }else{
                
                $temp_arr = is_array($val) ? $val : array($val);
                
                $count = 0;
                $where_str .= " AND (";
                foreach($temp_arr as $k => $v){
                    $wh = strlen($k)>1 ? explode(':',$k) : explode(':',$key);
                    if(!isset($wh[1])) $wh[1] = '=';
                    if(!isset($wh[2])) $wh[2] = 'OR';
                    
                    if( $count > 0 ) $where_str .= " {$wh[2]} ";
                    if(in_array($wh[1],array('<','>','<=','>=')) && is_numeric($v)){
                        $output['bind'][] = array($wh[0].$count.$cnt, str_replace(',', '.', $v), PDO::PARAM_INT);
                    }else{
                        $output['bind'][] = array($wh[0].$count.$cnt, $v, PDO::PARAM_STR);
                    }
                    $where_str .= $as.".`{$wh[0]}` {$wh[1]} :{$wh[0]}{$count}{$cnt}";
                    $count++;
                }
                $where_str .= ")\n";
                
            }
            $cnt++;
        }
        
        $output['sql'] = $where_str;
        
        return $output;
        
    }
    
    
    public function getMainWhereSQL()
    {
        
        $where_str = "`sc`.`".$this->config['parent_field']."` IN (" . implode(',',$this->parent_ids) . ")\n";
        
        $where_str .= $this->appendSQLResourcesIds();
        $where_str .= "AND `sc`.`published` = 1 AND `sc`.`deleted` = 0";
        
        if($this->config['className'] == "modResource"){
            $where_str .= "\n";
            $where_str .= "AND `sc`.`context_key` = '" . $this->config['context'] . "'";
        }
        
        return $where_str;
    }
    
    
    /**
     * добавляет значения в PDO SQL-запрос
     * 
     * @param PDOStatement $stmt
     * @param array $bind_arr
     */
    public function bindParams( &$stmt, $bind_arr )
    {
        
        if( !empty( $bind_arr ) ){
            foreach( $bind_arr as $key => $bind ){
                
                if( is_array( $bind[1] ) ){
                    
                    foreach ($bind[1] as $k => $val){
                        $stmt->bindValue(':'.$bind[0][$k], $val, (!empty($bind[2][$k]) ? $bind[2][$k] : PDO::PARAM_STR));
                    }
                    
                }else{
                    $result = $stmt->bindValue(':'.$bind[0], $bind[1], (!empty($bind[2]) ? $bind[2] : PDO::PARAM_STR));
                }
                
            }
        }
        
        return true;
        
    }
    
    /**
     * Добавление ID ресурсов из параметра resources
     * 
     */
    public function appendSQLResourcesIds($as = "sc")
    {
        
        $as = $this->modx->escape($as);
        
        $out = '';
        
        if($this->config['resources']){
            $resources_arr = explode(',',str_replace(' ','',$this->config['resources']));
            if( count( $resources_arr ) > 0 ){
                $out = "
                OR {$as}.`id` IN (".implode(',',$resources_arr).")
                ";
            }
        }
        
        return $out;
        
    }
    
    /**
     * searchProducts
     * 
     */
    public function searchProducts()
    {
        
        $this->parent_ids = $this->getParents();
        $filters = array();
        
        if( !empty( $this->config['tvFilters'] ) ) {
            
            $filters = array( 'sql' => '', 'bind' => array() );
            $f_index = 0;
            
            $obj = new ArrayObject( $this->config['tvFilters'] );
            $it = $obj->getIterator();
            while( $it->valid() )
            {
                $f_data = $this->getFiltersSQL( $it->key(), $it->current(), $f_index );
                
                $union_key = strpos( $it->key(), ':' ) !== false
                    ? substr( $it->key(), strrpos( $it->key(), ':' ) + 1 )
                    : 'AND';
                if( !in_array( strtoupper( $union_key ), array( 'AND', 'OR' ) ) ){
                    $union_key = 'AND';
                }
                
                //UNION
                if( $f_index > 0 ){
                    
                    if( $union_key == 'AND' ){
                        
                        $filters['sql'] .= "\n";
                        $filters['sql'] .= "AND `tvc`.`contentid` IN\n" . str_repeat( " ", 4 ) . "(" . $f_data['sql'];
                        $filters['sql'] .= "\n" . str_repeat( " ", 4 ) . ")";
                        
                    }
                    else {
                        $filters['sql'] .= "\nUNION" . $f_data['sql'];
                    }
                    
                }
                else {
                    
                    $filters['sql'] .= $f_data['sql'];
                    
                }
                
                $filters['bind'] = call_user_func_array( 'array_merge', array( $filters['bind'], $f_data['bind'] ) );
                
                $f_index++;
                $it->next();
            }
            
        }
        
        $this->getProductsData( $filters );
        
    }
    
    /**
     * getFiltersSQL
     *
     * @param array $flt_act
     * @param array $ftl_val
     * @param integer $f_index
     */
    public function getFiltersSQL( $flt_act, $ftl_val, $f_index = 0 )
    {
        $output = array( 'sql' => '', 'bind' => array() );
        $t_prefix = $this->config['table_prefix'];
        
        $f_data = $this->prepareFiltersSQL( $flt_act, $ftl_val, true, $f_index );
        
        $output['sql'] = "
        SELECT `tvc`.`contentid`
        FROM `{$t_prefix}site_tmplvar_contentvalues` AS `tvc`,
             `{$t_prefix}site_tmplvars` AS `tv`
        WHERE `tv`.`id` = `tvc`.`tmplvarid`";
        
        $output['sql'] .= "AND " . $f_data['sql'];
        $output['bind'] = $f_data['bind'];
        
        return $output;
    }
    
    /**
     * Формирует строку SELECT с полями для выборки данных товара
     *
     * @param string $as
     */
    public function getSelectFields($as='sc')
    {
        
        $as = $this->modx->escape($as);
        $out = '';
        
        $fields_arr = $this->table_fields;
        
        if( !$this->config['includeContent'] ){
            unset($fields_arr['content']);
            $out = $as . ".`" . implode( "`, {$as}.`", $fields_arr ) . "`";
        }else{
            $out = $as . ".*";
        }
        
        return $out;
        
    }
    
    
    /**
     * Вытаскивает все данные товаров
     *
     * @param array $filters
     */
    public function getProductsData( $filters = array() )
    {
        
        $where_arr = array();
        $limit_str = $this->config['limit'] ? "LIMIT ".$this->config['offset'].', '.$this->config['limit'].' ' : '';
        
        $where_str = $this->getMainWhereSQL();
        
        if( !empty( $this->config['where'] ) ) {
            
            $where_arr = $this->prepareWhereSQL( $this->config['where'] );
            $where_str .= $where_arr['sql'];
            
        }
        
        if( !empty( $filters ) ){
            
            $where_str .= "\nAND `sc`.`id` IN (";
            $where_str .= $filters['sql'];
            $where_str .= "\n)";
            
        }
        
        $orderby_str = '';
        $select_str = $this->getSelectFields('sc');
        
        $subitemcount_str = '';
        
        if( $this->config['addSubItemCount'] && in_array( 'parent', $this->table_fields ) ){
            $where_arr = $this->prepareWhereSQL( $this->config['subItemCountWhere'], "sic" );
            $subitemcount_str = ", ( SELECT COUNT(`id`) FROM `{$this->config['table_name']}` AS `sic` WHERE `sic`.`parent` = `sc`.`id` AND `sic`.`published`=1 AND `sic`.`deleted`=0 {$where_arr['sql']} ) AS `subitemcount` ";
        }
        
        //Сортировка по TV
        if( $this->config['sortbyTV'] ){
            
            $tv_stmt = $this->modx->prepare("SELECT `id` FROM `".$this->config['table_prefix']."site_tmplvars` WHERE `name` = '{$this->config['sortbyTV']}'");
            $tv_id = 0;
            if($tv_stmt->execute()){
                $tv_id = $tv_stmt->fetchColumn();
                $tv_stmt->closeCursor();
            }
            $select_str .= ", (SELECT `value` FROM `".$this->config['table_prefix']."site_tmplvar_contentvalues` WHERE `tmplvarid` = '{$tv_id}' AND `contentid` = `sc`.`id` LIMIT 1) AS `{$this->config['sortbyTV']}`";
            $orderby_str = "ORDER BY ";
            if($this->config['sortbyTVType'] == 'integer'){
                $orderby_str .= "CAST(`{$this->config['sortbyTV']}` AS SIGNED)";
            }else{
                $orderby_str .= $this->config['sortbyTV'];
            }
            $orderby_str .= ' '.$this->config['sortdirTV'];
            
        //Сортировка по списку ID ресурсов
        }else if( $this->config['resources'] && $this->config['orderbyResources'] ){
            
            $orderby_str = 'ORDER BY FIND_IN_SET(id,"'.$this->config['resources'].'")';
            
        //Сортировка по полю ресурсов
        }else{
            
            if( count( $this->config['orderby'] ) > 0 ){
                $orderby_str = 'ORDER BY ';
                if( strtoupper(current(array_keys($this->config['orderby']))) == 'RAND()' ){
                    $orderby_str .= ' RAND() ';
                }else{
                    foreach( $this->config['orderby'] as $k => $v ){
                        $orderby_str .= '`sc`.' . $this->modx->escape($k) . " {$v}, ";
                    }
                    $orderby_str = substr($orderby_str,0,-2);
                }
            }
            
        }
        
        $this->getCount( $where_str, $where_arr, $filters );
        
        $sql = "SELECT {$select_str}{$subitemcount_str}
        FROM `{$this->config['table_name']}` `sc`
        WHERE {$where_str}
        {$orderby_str}
        {$limit_str}
        ";
        
        $stmt = $this->modx->prepare( $sql );
        
        if( isset( $where_arr ) && !empty( $where_arr['bind'] ) ) {
            $this->bindParams( $stmt, $where_arr['bind'] );
        }
        if( !empty( $filters ) && !empty( $filters['bind'] ) ) {
            $this->bindParams( $stmt, $filters['bind'] );
        }
        
        $this->ids_arr = array();
        if ( $stmt && $stmt->execute() ) {
            
            $stmt->setFetchMode( PDO::FETCH_ASSOC );
            $it = new IteratorIterator( $stmt );
            $it->rewind();
            
            while ( $it->valid() ) {
                $row = $it->current();
                $this->products[ $row['id'] ] = $row;
                array_push( $this->ids_arr, $row['id'] );
                $it->next();
            }
            
            $stmt->closeCursor();
            
        }else{
            $this->modx->log( modX::LOG_LEVEL_ERROR, '[ERROR] getProducts SQL error (line ' . __LINE__ . '): ' . print_r( $stmt->errorInfo(), true ) );
        }
        
        //Просчитываем значения полей по типу вывода TV
        if( $this->config['processTVs'] && $this->config['className'] != 'modResource' ){
            $this->processFieldFromTV();
        }
        
        if($this->config['debug']) {
            $log_str = $sql;
            if( !empty( $where_arr['bind'] ) ) $log_str .= "\n" . print_r( $where_arr['bind'], true );
            if( !empty( $filters['bind'] ) ) $log_str .= "\n" . print_r( $filters['bind'], true );
            $this->modx->log( $this->modx->config['log_level'], '[INFO] getProducts: total = ' . $this->getTotal() . ' - ' . $log_str );
        }
        
    }
    
    /**
     * getCount
     *
     * @param string $sql
     * @param array $where_arr
     * @param array $filters
     */
    public function getCount( $sql, $where_arr = array(), $filters = array() )
    {
        $sql = "SELECT COUNT( * ) FROM `".$this->config['table_name']."` `sc`
        WHERE " . $sql;
        
        $stmt = $this->modx->prepare( $sql );
        if( !empty( $where_arr ) && !empty( $where_arr['bind'] ) ) {
            $this->bindParams( $stmt, $where_arr['bind'] );
        }
        if( !empty( $filters ) && !empty( $filters['bind'] ) ) {
            $this->bindParams( $stmt, $filters['bind'] );
        }
        if ( $stmt && $stmt->execute() ) {
            $this->total = (int) current( $stmt->fetch( PDO::FETCH_NUM ) );
        }
        
    }
    
    /**
     * getTotal
     *
     */
    public function getTotal()
    {
        
        return $this->total;
        
    }
    
    
    /**
     * Возвращает массив ID родителей
     * 
     */
    public function getParents()
    {
        
        $out = $parent_ids = $this->config['parents'] ? explode(',',$this->config['parents']) : array();
        $out = array_map( 'trim', $out );
        
        if($this->config['depth']){
            foreach($parent_ids as $parent_id){
                
                $pchildrens = is_numeric($parent_id)
                    ? $this->modx->getChildIds($parent_id, $this->config['depth'], array('context' => $this->config['context']))
                    : array();
                $out = array_merge($out, $pchildrens);
                
            }
        }
        
        return $out;
        
    }
    
    
    /**
     * Добавляет данные из TV параметров
     *
     * @param string $tv_prefix
     */
    public function appendTVs( $tv_prefix = 'tv.' )
    {
        
        if($this->config['includeTVs']){
            
            //Вытаскиваем все значения по умолчанию
            $tv_sql = "
            SELECT `tv`.`id`, `tv`.`name`, `tv`.`default_text`
            ";
            
            if( $this->config['processTVs'] ){
                $tv_sql .= ", `tv`.`display`, `tv`.`elements`, `tv`.`output_properties`, `tv`.`type`";
            }
            
            $tv_sql .= "
            FROM `{$this->config['table_prefix']}site_tmplvars` AS `tv`
            WHERE `tv`.`name` IN ('" . implode( "','", $this->config['includeTVList_arr'] ) . "')
            ";
            
            $tv_default = array();
            $stmt = $this->modx->prepare($tv_sql);
            if ($stmt && $stmt->execute()) {
                
                $stmt->setFetchMode( PDO::FETCH_ASSOC );
                $it = new IteratorIterator( $stmt );
                $it->rewind();
                
                while ( $it->valid() ) {
                    
                    $row = $it->current();
                    if( !empty( $this->config['processTVs'] ) && ( empty($this->config['processTVList']) || in_array( $row['name'], $this->config['processTVList'] ) ) ){
                        
                        $tv_default_text = $this->processTV( $row, 0, $row['default_text'], true );
                        
                    }
                    else {
                        $tv_default_text = $row['default_text'];
                    }
                    
                    array_push( $tv_default, array(
                        'name' => $row['name'],
                        'default_text' => $tv_default_text
                    ));
                    $it->next();
                }
            }
            $stmt->closeCursor();
            
            
            //Вытаскиваем знаяения TV
            $tv_sql = "
            SELECT DISTINCT `tv`.`id`, `tv`.`name`, `tvc`.`contentid`
            ";
            
            if( $this->config['processTVs'] ){
                $tv_sql .= ", `tv`.`default_text`, `tv`.`display`, `tv`.`elements`, `tv`.`output_properties`, `tv`.`type`";
            }
            
            $tv_sql .= "
            , IF(`tvc`.`value`!='',`tvc`.`value`,`tv`.`default_text`) as `value`
            FROM `".$this->config['table_prefix']."site_tmplvars` AS `tv`
            LEFT JOIN `".$this->config['table_prefix']."site_tmplvar_contentvalues` AS `tvc` ON `tvc`.`tmplvarid`=`tv`.`id`
            LEFT JOIN `".$this->config['table_prefix']."site_tmplvar_access` AS `tva` ON `tva`.`tmplvarid`=`tv`.`id`
            ";
            
            if(!empty($this->config['includeTVList_arr'])){
                $tv_sql .= "
                WHERE `tv`.`name` IN ('".implode("','",$this->config['includeTVList_arr'])."')
                AND
                ";
            }else{
                $tv_sql .= " WHERE ";
            }
            
            $tv_sql .= "
            `tvc`.`contentid` IN (".implode(',',$this->ids_arr).") AND (ISNULL(`tva`.`documentgroup`))
            ORDER BY `tv`.`rank`
            ";
            
            $stmt = $this->modx->prepare($tv_sql);
            if ( $stmt && $stmt->execute() ) {
                
                $stmt->setFetchMode( PDO::FETCH_ASSOC );
                $it = new IteratorIterator( $stmt );
                $it->rewind();
                
                while ( $it->valid() ) {
                    $row = $it->current();
                    
                    if( !isset( $this->products[ $row['contentid'] ] ) ){
                        $this->products[$row['contentid']] = array();
                    }
                    if( $this->config['processTVs'] && ( empty($this->config['processTVList']) || in_array( $row['name'], $this->config['processTVList'] ) ) ){
                        $this->products[$row['contentid']][$tv_prefix.$row['name']] = $this->processTV( $row, $row['contentid'], $row['value'] );
                    } else {
                        $this->products[$row['contentid']][$tv_prefix.$row['name']] = $row['value'];
                    }
                    
                    $it->next();
                }
                
                $stmt->closeCursor();
                
            }
            
            //Заполняем "дыры" значениями
            if( !empty( $tv_default ) ){
                
                foreach($this->products as &$product){
                    foreach($tv_default as $tv_def){
                        $tv_name = $tv_def['name'];
                        if( !isset($product[$tv_prefix.$tv_name]) ){
                            
                            $product[$tv_prefix.$tv_name] = $tv_def['default_text'];
                            
                        }
                    }
                }
                
            }
            
        }
        
    }
    
    
    /**
     * Просчитываение кода TV по типу вывода
     * 
     * @param array $tv_data
     * @param string $contentid
     * @param string $value
     * @param boolean $is_default_text
     */
    public function processTV( $tv_data = array(), $contentid = '', $value = '', $is_default_text = false )
    {
        $output = $value;
        if( !is_array( $tv_data ) || count( $tv_data ) == 0 ){
            return $output;
        }
        
        //get renderer
        if( !isset( $this->tvRenderers[ $tv_data['name'] ] ) ){
            
            $output_properties = !is_array( $tv_data['output_properties'] )
                ? unserialize($tv_data['output_properties'])
                : $tv_data['output_properties'];
            
            if( !isset( $output_properties['param_name'] ) ){
                $output_properties['param_name'] = $tv_data['name'];
            }
            
            $templateVar = $this->modx->newObject('modTemplateVar');
            $templateVar->fromArray(array(
                'name' => $tv_data['name'],
                'caption' => '',
                'type' => $tv_data['type'],
                'display' => $tv_data['display'],
                'elements' => $tv_data['elements'],
                'output_properties' => $output_properties,
                'default_text' => $tv_data['default_text'],
                'value' => $value,
                'resourceId' => $contentid
            ));
            
            if( !empty( $tv_data['id'] ) ){
                $templateVar->set( 'id', intval( $tv_data['id'] ) );
            }
            
            if( empty( $this->tvRenderers['renderDirectories'] ) ){
                $this->tvRenderers['renderDirectories'] = $templateVar->getRenderDirectories('OnTVOutputRenderList', 'output');
            }
            
            $this->tvRenderers[ $tv_data['name'] ] = array(
                'templateVar' => $templateVar,
                'mediaSourceData' => array(),
                'rendererClass' => ''
            );
            
            $className = $templateVar->checkForRegisteredRenderMethod( $tv_data['display'], 'output' );
            if( $className ){
                $this->tvRenderers[ $tv_data['name'] ]['rendererClass'] = $className;
            }
            else {
                
                foreach ($this->tvRenderers['renderDirectories'] as $path) {
                    
                    $renderFile = $path . $tv_data['display'] . '.class.php';
                    if (file_exists($renderFile)) {
                        $className = include $renderFile;
                        $templateVar->registerRenderMethod($tv_data['display'], 'output', $className);
                        $this->tvRenderers[ $tv_data['name'] ]['rendererClass'] = $className;
                    }
                    
                }
                
            }
            
            //Media Source
            if( in_array( $tv_data['display'], array('image', 'file') ) ){
                
                $cacheManager = $this->modx->getCacheManager();
                $sourceCache = $cacheManager->getElementMediaSourceCache( $templateVar, $this->config['context']);
                if (!empty($sourceCache) && !empty($sourceCache['class_key'])) {
                    $this->tvRenderers[ $tv_data['name'] ]['mediaSourceData'] = array(
                        'basePath' => $sourceCache['basePath'],
                        'baseUrl' => $sourceCache['baseUrl']
                    );
                }
                
            }
            
            unset( $templateVar );
        }
        
        $rendererClass = $this->tvRenderers[ $tv_data['name'] ]['rendererClass'];
        $templateVar = $this->tvRenderers[ $tv_data['name'] ]['templateVar'];
        
        if( $value != '' && class_exists($rendererClass ) ){
            
            if( !empty( $contentid ) ){
                $templateVar->set( 'resourceId', intval( $contentid ) );
                $output_properties = $templateVar->get('output_properties');
                $output_properties['id'] = intval( $contentid );
                $templateVar->set( 'output_properties', $output_properties );
            }
            
            if( !empty( $this->tvRenderers[ $tv_data['name'] ]['mediaSourceData'] ) && in_array( $tv_data['display'], array('image', 'file') ) ){
                
                if( !empty( $this->tvRenderers[ $tv_data['name'] ]['mediaSourceData']['baseUrl'] ) && !$is_default_text ){
                    $value = $this->tvRenderers[ $tv_data['name'] ]['mediaSourceData']['baseUrl'] . $value;
                }
                
            }
            
            $render = new $rendererClass( $templateVar );
            $output = $render->render($value, $templateVar->get('output_properties'));
            
        }
        
        return $output;
    }
    
    
    /**
     * Просчитывает значения полей по типу ввода TV (migxdb)
     * 
     */
    public function processFieldFromTV()
    {
        
        if(empty($this->config['processTVs']) || $this->config['className']=='modResource') return false;
        
        $field_tv = array();
        
        $sql = "
        SELECT `formtabs`
        FROM `".$this->config['table_prefix']."migx_configs`
        WHERE `name` = '".$this->config['migx_configName']."'
        ";
        $stmt = $this->modx->prepare($sql);
        if ($stmt && $stmt->execute()) {
            $migx_config_formtabs = $stmt->fetchColumn();
            if($migx_config_formtabs){
                $migx_config_formtabs_arr = json_decode($migx_config_formtabs,true);
                foreach($migx_config_formtabs_arr as $formtabs){
                    $temp_fields = !empty($formtabs['fields']) ? $formtabs['fields'] : array();//$formtabs['fields'] ? json_decode($formtabs['fields'],true) : array();
                    foreach($temp_fields as $temp_field){
                        if(!empty($temp_field['inputTV'])){
                            $field_tv[$temp_field['field']] = $temp_field['inputTV'];
                        }
                    }
                }
                
            }
        }
        $stmt->closeCursor();
        
        $tv_data = array();
        
        foreach($this->products as &$product){
            
            foreach($this->config['processTVList'] as $field_name){
                
                if(isset($field_tv[$field_name])){
                    
                    $tv_name = $field_tv[$field_name];
                    
                    if( !isset( $tv_data[$tv_name] ) ){
                        $templateVar = $this->modx->getObject( 'modTemplateVar', array( 'name' => $tv_name ) );
                        $tv_data[$tv_name] = $templateVar->toArray();
                        $tv_data[$tv_name]['name'] = $field_name;
                    }
                    
                    if( isset( $tv_data[$tv_name] ) ){
                        $value = $product[$field_name];
                        $product[$field_name] = $this->processTV( $tv_data[$tv_name], $product['id'], $value );
                    }
                    
                }
                
            }
            
        }
        
        return true;
        
    }
    
    /**
     * Вытаскивает данные от родителей
     * 
     */
    public function appendFromParents()
    {
        
        if($this->config['fromParentList']){
            
            //Если родитель не указан, ищем родителей по ID товаров
            if(empty($this->parent_ids) || (count($this->parent_ids) == 1 && $this->parent_ids[0] == '-1')){
                
                $parent_ids = array();
                
                $sql = "SELECT DISTINCT `".$this->config['parent_field']."` AS `parent` FROM `".$this->config['table_name']."`
                WHERE `id` IN (".implode(',',$this->ids_arr).")
                ";
                
                $stmt = $this->modx->prepare($sql);
                if($stmt && $stmt->execute()){
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        array_push($parent_ids,$row['parent']);
                    }
                    $stmt->closeCursor();
                }
                
            }else{
                $parent_ids = $this->parent_ids;
            }
            
            if(empty($parent_ids)) return false;
            
            $parents_data = array();
            
            $sql = "
            SELECT * FROM `".$this->config['table_prefix']."site_content` `sc`
            WHERE `sc`.`id` IN (".implode(',',$parent_ids).")
            ";
            
            $stmt = $this->modx->prepare($sql);
            if($stmt && $stmt->execute()){
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    foreach(array_intersect(array_keys($row),$this->config['fromParentList_arr']) as $k => $v){
                        if(!isset($parents_data[$row['id']])) $parents_data[$row['id']] = array();
                        $parents_data[$row['id']][$v] = $row[$v];
                    }
                    if(!isset($fromParentListTV_arr)) $fromParentListTV_arr = array_diff($this->config['fromParentList_arr'],array_keys($row));
                }
                $stmt->closeCursor();
            }
            
            if(!empty($fromParentListTV_arr)){
                $tv_sql = "
                SELECT DISTINCT `tv`.`id`, `tv`.`name`, `tvc`.`contentid`, IF(`tvc`.`value`!='',`tvc`.`value`,`tv`.`default_text`) as `value`
                FROM `".$this->config['table_prefix']."site_tmplvars` AS `tv`
                LEFT JOIN `".$this->config['table_prefix']."site_tmplvar_contentvalues` AS `tvc` ON `tvc`.`tmplvarid`=`tv`.`id`
                LEFT JOIN `".$this->config['table_prefix']."site_tmplvar_access` AS `tva` ON `tva`.`tmplvarid`=`tv`.`id`
                WHERE `tv`.`name` IN ('".implode("','",$fromParentListTV_arr)."')
                AND `tvc`.`contentid` IN (".implode(',',$parent_ids).") AND (ISNULL(`tva`.`documentgroup`))
                ORDER BY `tv`.`rank`
                ";
                
                $stmt = $this->modx->prepare($tv_sql);
                if ($stmt && $stmt->execute()) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        if(!isset($parents_data[$row['contentid']])) $parents_data[$row['contentid']] = array();
                        $parents_data[$row['contentid']][$row['name']] = $row['value'];
                    }
                    $stmt->closeCursor();
                }
            }
            
            //Формируем окончательный массив данных
            foreach($parent_ids as $p_id){
                
                $parent_id_arr = array_merge(array($p_id),$this->modx->getParentIds($p_id, abs($this->config['fromParentHeight']-1), array('context'=>$this->config['context'])));
                
                foreach($parent_id_arr as $level => $parent_id){
                    if(isset($parents_data[$parent_id])){
                        if(!isset($this->parents_data[$p_id])) $this->parents_data[$p_id] = array();
                        foreach($parents_data[$parent_id] as $k => $v){
                            $this->parents_data[$p_id]['parent'.($level>0 ? $level+1 : '').'.'.$k] = $v;
                        }
                    }
                }
                
            }
            
        }
        
        return true;
        
    }
    
    /**
     * isActive
     * 
     */
    public function isActive( $id )
    {
        if( is_object( $this->modx->resource ) ){
            $out = in_array( $id, array_merge( $this->config['all_parents'], array( $this->modx->resource->get('id') ) ) );
        }
        else{
            $out = in_array( $id, $this->config['all_parents'] );
        }
        return $out;
    }
    
    
    /**
     * getDivisors
     *
     */
    public function getDivisors()
    {
        
        $divisors = array( array(), array() );
        
        foreach( $this->config as $k => $tpl ){
            
            if( strpos( $k, 'tpl_n' ) === 0 ){
                
                array_push( $divisors[0], intval( substr( $k, 5 ) ) );
                array_push( $divisors[1], $tpl );
                
            }
            if( empty( $divisors['separator'] ) && strpos( $k, 'outputSeparator_n' ) === 0 ){
                $chunk = $this->getChunk($tpl);
                $divisors['separator'] = array( intval( substr( $k, 17 ) ), $chunk->snippet );
            }
            
        }
        
        return $divisors;
        
    }
    
    
    /**
     * getChunkName
     *
     */
    public function getChunkName( $iteration, $divisors = array() )
    {
        
        $chunk_name = $this->config['tpl'];
        
        if( !empty( $divisors ) && !empty( $divisors[0] ) ){
            
            for ($i = count($divisors[0])-1; $i >= 0; $i--) {
                
                if( $iteration%$divisors[0][$i] == 0 ){
                    $chunk_name = $divisors[1][$i];
                    break;
                }
                
            }
            
        }
        
        return $chunk_name;
        
    }
    
    
    /**
     * Создаёт HTML код по шаблону
     *
     */
    public function getHTMLOutput()
    {
        
        $output = '';
        
        $this->tvRenderers = array();
        $chunk = $this->getChunk( $this->config['tpl'] );
        
        $divisors = $this->getDivisors();
        
        $idx = 0;
        foreach($this->products as $product){
            $properties = array_merge(
                array(
                    'idx' => $idx,
                    'num' => $idx + 1,
                    'first' => $idx==0 ? 1 : 0,
                    'last' => $idx+1==count($this->products) ? 1 : 0,
                    'odd' => $idx%2==0 ? 1 : 0,
                    'activeClass' => $this->isActive($product['id']) ? $this->config['activeClass'] : '',
                    'active' => $this->isActive($product['id']) ? 1 : 0,
                    'activeParent_snippet' => ''
                ),
                $product
            );
            
            $properties['classnames'] = ($properties['first'] ? 'first ' : '')
            .($properties['last'] ? 'last ' : '')
            .($properties['odd'] ? 'odd ' : '')
            .($properties['activeClass'] ? $this->config['activeClass'] : '');
            
            $properties['classnames'] = trim($properties['classnames']);
            
            if(/*$properties['activeParent'] && */ $properties['active'] && $this->config['activeParentSnippet']){
                
                $this->config['activeParentSnippet'] = str_replace('[[+id]]',$properties['id'],$this->config['activeParentSnippet']);
                $tag_end = strpos($this->config['activeParentSnippet'],"?");
                $tagName = $tag_end!==false ? substr($this->config['activeParentSnippet'],0,$tag_end) : $this->config['activeParentSnippet'];
                $tagPropString = $tag_end!==false ? substr($this->config['activeParentSnippet'],(0-(strlen($this->config['activeParentSnippet']) - $tag_end - 1))) : '';
                
                $elementOutput = '';
                if ($element = $this->modx->parser->getElement('modSnippet', $tagName)) {
                    $element->set('name', $tagName);
                    //$element->setTag($outerTag);
                    $element->setCacheable(false);
                    $elementOutput = $element->process($tagPropString);
                }
                
                if($elementOutput) $properties['activeParent_snippet'] = $elementOutput;
                else $properties['active'] = 0;
                
            }
            
            if(isset($this->parents_data[$product[$this->config['parent_field']]])){
                $properties = array_merge($properties,$this->parents_data[$product[$this->config['parent_field']]]);
            }
            
            //fromParentList
            if(!empty($this->config['fromParentList']) && isset($this->parents_data[$product[$this->config['parent_field']]])){
                
                $properties = array_merge($properties,$this->parents_data[$product[$this->config['parent_field']]]);
                
            }
            
            $chunk_name = $this->getChunkName( $properties['num'], $divisors );
            
            if( $chunk_name != $this->config['tpl'] ){
                $output .= $this->modx->getChunk($chunk_name,$properties);
            }else{
                $output .= $this->parseTpl($chunk,$properties);
            }
            
            if( !$properties['last'] ) $output .= $this->config['outputSeparator'];
            
            //separator
            if( !$properties['last'] && !empty( $divisors['separator'] ) ){
                if( $properties['num'] % $divisors['separator'][0] === 0 ){
                    $output .= $divisors['separator'][1] . $this->config['outputSeparator'];
                }
            }
            
            $idx++;
        }
        
        if(!empty($this->config['outerTpl'])){
            $outerChunk = $this->getChunk($this->config['outerTpl']);
            $output = $outerChunk->process(array('inner'=>$output));
        }
        
        return $output;
        
    }
    
}

