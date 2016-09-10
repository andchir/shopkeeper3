<?php

/**
 * tagManager2
 *
 * tmFilters class
 *
 * @author Andchir <andchir@gmail.com>
 * @package tag_manager2
 * @version 2.3
 */

require_once dirname(__FILE__)."/tm_base.class.php";

class tmFilters extends tagManagerBase {
    
    public $modx = null;
    public $config = array();
    
    /**
     *
     *
     */
    function __construct(modX &$modx, $config = array()){
        
        parent::__construct($modx, $config);
        
        $modelpath = $this->modx->config['core_path'].'components/tag_manager2/model/';
        $this->modx->addPackage('tag_manager2', $modelpath);
        
    }
    
    /**
     * getCateroryIds
     * 
     * Возвращает массив ID категорий сохраненных в БД tag_manager
     *
     */
    public function getCateroryIds(){
        
        $output = array();
        
        $c = $this->modx->newQuery('tagManager');
        $c->select(array('id','category'));
        $c->sortby('category','asc');
        $c->groupby('category');
        if ($c->prepare() && $c->stmt->execute()) {
            
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                
                array_push($output, $row['category']);
                
            }
            
        }
        
        return $output;
    
    }
    
    
    /**
     * getFiltersOutput
     *
     */
    public function getFiltersOutput(){
        
        $output = '';
        
        $tags_names_arr = array();
        $c_ids = $this->getCateroryIds();
        
        //Если для текущей категории нет сохраненных фильтров, ищем сохраненные в родительских
        if(!in_array($this->config['categoryId'], $c_ids)){
            
            $this->getParentSiblingId($c_ids);
            
        }
        
        $c = $this->modx->newQuery('tagManager');
        $c->where(array(
            'category' => $this->config['categoryId']
        ));
        $c->sortby('`index`','ASC');
        $c->select(array('id','tvid','tvname','tvcaption','tags','index'));
        if ($c->prepare() && $c->stmt->execute()) {
            
            $index = 0;
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                
                $inner_out = '';
                $row['tags'] = $row['tags'] ? json_decode($row['tags'],true) : array();
                
                $is_numeric = in_array( $row['tvname'], $this->config['numericFields'] ) && count($row['tags']) >= 2;
                $is_multitag = in_array( $row['tvname'], $this->config['multitagsFields'] );
                $flt_field_name = $is_multitag ? 'f_'.$row['tvname'].'[like][]' : 'f_'.$row['tvname'].'[]';
                
                if(!in_array( $row['tvname'], $tags_names_arr )) array_push( $tags_names_arr, $row['tvname'] );
                
                if( $is_numeric ){
                    
                    $chunk_name_outer = $this->config['filterNumericOuterTpl'];
                    $chunk_name = $this->config['filterNumericTpl'];
                    
                    $chunkArr = array(
                        'name' => $row['tvname'],
                        'min' => ( $row['tags'][0]['active'] ? $row['tags'][0]['value'] : '0' ),
                        'max' => ( $row['tags'][1]['active'] ? $row['tags'][1]['value'] : '0' ),
                        'idx' => $index
                    );
                    
                    $inner_out .= $this->modx->getChunk( $chunk_name, $chunkArr );
                    $inner_out .= "\n";
                    
                }else{
                    
                    $chunk_name_outer = $this->config['filterOuterTpl'];
                    $chunk_name = $this->config['filterTpl'];
                    
                    foreach($row['tags'] as $key => $val){
                        
                        if($val['active']){
                            $chunkArr = array(
                                'name' => $row['tvname'],
                                'flt_name' => $flt_field_name,
                                'value' => str_replace('&','_n_',$val['value']),
                                'value_name' => htmlspecialchars($val['value']),
                                'idx' => $key,
                                'num' => $key + 1
                            );
                            
                            $inner_out .= $this->modx->getChunk( $chunk_name, $chunkArr );
                            $inner_out .= "\n";
                        }
                        
                    }
                    
                }
                
                $chunkArr = array(
                    'name' => $row['tvname'],
                    'flt_name' => $flt_field_name,
                    'caption' => $row['tvcaption'],
                    'inner' => $inner_out,
                    'idx' => $index,
                    'num' => $index + 1
                );
                
                $output .= $this->modx->getChunk( $chunk_name_outer, $chunkArr );
                $output .= "\n";
                
                $index++;
                
                //echo '<pre>'.print_r($row, true).'</pre>';
                
            }
            
        }
        
        if( $this->config['jsMap'] ) $output .= $this->generateJSMap($tags_names_arr);
        
        return $output;
        
    }
    
    
    /**
     * getParentSiblingId
     * 
     *
     */
    function getParentSiblingId($c_ids){
        
        if(count($c_ids) > 1){
            
            $p_ids = $this->modx->getParentIds($this->config['categoryId'], 10, array('context'=>$this->config['context']));
            $intersect = array_intersect($c_ids,$p_ids);
            
            $this->config['categoryId'] = count($intersect) > 0 ? end($intersect) : 0;
            
        }else{
            
            $this->config['categoryId'] = 0;//count($c_ids) ? $c_ids[0] : 0;
            
        }
        
    }
    
    
    /**
     * generateJSMap
     *
     */
    function generateJSMap($tags_names_arr){
        
        $output = '';
        
        if(count($tags_names_arr) > 0){
            
            $getproductsPath = $this->modx->getOption('core_path') . "components/getproducts/model/getproducts.class.php";
            
            if( file_exists( $getproductsPath ) ){
                
                require_once $getproductsPath;
                
                $snippetProperties = $this->getSnippetProperties();
                
                $snippetProperties['limit'] = 0;
                $getProducts = new getProducts($this->modx, $snippetProperties);
                
                if( !empty($this->config['className']) && $this->config['className'] != 'modResource' ){
                    array_push($tags_names_arr,'id');
                    $getProducts->table_fields = $tags_names_arr;
                }
                else {
                    $getProducts->table_fields = array('id');
                }
                
                $getProducts->searchProducts();
                $total = $getProducts->getTotal();
                
                if( $total > 0 ){
                    
                    if( empty($this->config['className']) || $this->config['className'] == 'modResource' ){
                        
                        $getProducts->config['includeTVs'] = 1;
                        $getProducts->config['includeTVList'] = implode(',',$tags_names_arr);
                        $getProducts->config['includeTVList_arr'] = $tags_names_arr;
                        $getProducts->appendTVs('');
                        
                    }
                    
                    foreach($getProducts->ids_arr as $r_id){
                        
                        if(isset($getProducts->products[$r_id])){
                            
                            $product = $getProducts->products[$r_id];
                            ksort($product);
                            
                            $output .= $this->php2js( array_merge(array("id"=>$r_id), $product) ).",\n";
                            
                        }
                        
                    }
                    
                }
            
            }
            else {
                $this->modx->log( modX::LOG_LEVEL_ERROR, 'tagManager: Unable to find a snippet getProducts.');
            }
            
        }
        
        if( strlen( $output ) > 0 ){
        
            $output = "\n<script type=\"text/javascript\">
            var flt_data = {\"products\": [\n".(strlen($output)>2 ? substr($output,0,-2) : "")."\n]};\n
            </script>\n";
        
        }
        
        return $output;
        
    }
    
    
    /**
     * php2js
     *
     */
    public function php2js($a=false){
        if (is_null($a)) return 'null';
        if ($a === false) return 'false';
        if ($a === true) return 'true';
        if (is_scalar($a)){
            if (is_float($a)){
                $a = str_replace(",", ".", strval($a));
            }
            static $jsonReplaces = array(
                array("\n", "\t", "\r", "\b", "\f", '"', '&'),
                array('\\n', '\\t', '\\r', '\\b', '\\f', '\"', '_n_')
            );
            return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
        }
        $isList = true;
        for ($i = 0, reset($a); $i < count($a); $i++, next($a)){
            if (key($a) !== $i){
                $isList = false;
                break;
            }
        }
        $result = array();
        if ($isList){
            foreach ($a as $v) $result[] = $this->php2js($v);
            return '[ ' . join(', ', $result) . ' ]';
        }else{
            foreach ($a as $k => $v) $result[] = $this->php2js($k).': '.$this->php2js($v);
            return '{ ' . join(', ', $result) . ' }';
        }
    }
    
    /**
     * createFilters
     *
     */
    public function createFilters( $filters_names = '' ){
        
        $output = '';
        
        $values_arr = array();
        $response = $this->modx->runProcessor('getfilterdata',
            array(
                'parent_id' => $this->modx->resource->id,
                'tvname' => $filters_names,
                'get_child_parents' => false,
                'outtype' => 'array'
            ),
            array( 'processors_path' => $this->modx->getOption( 'core_path' ) . 'components/tag_manager2/processors/mgr/' )
        );
        if(!$response->isError()){
            
            $response_data = $response->getResponse();
            
            //echo '<pre>' . print_r($response_data,true) . '</pre>';
            
        }
        
        return $output;
        
    }
    

}


