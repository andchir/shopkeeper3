<?php

/**
 * prodCompare Class
 *
 * @package compare
 * @version 1.1
 * @author Andchir <andchir@gmail.com>
 */

class prodCompare {
    
    public $config = array();
    public $modx = null;
    
    function __construct(&$modx, $config = array()){
        $this->modx = $modx;
        
        $this->config = array_merge(array(
            'tplPath' => 'core/components/compare/elements/chunks/',
            'className' => 'modResource',
            'packageName' => '',
            'renderTVDisplayFormat' => true,
            'minProducts' => 2,
            'limitProducts' => 0,
            'onlyThisParentId' => false,
            'toCompare_tpl' => 'toCompare',
            'product_tpl' => 'compare_product',
            'comparePageId' => '1',
            'filterTVID' => '',
            'removeLastTwo' => false,
            'targetActiveClass' => '',
            'noResults' => '',
            'jsScript' => true
        ),$config);
        
    }
    
    /**
     * Возвращает массив данных чанка и кэширует чанк
     * 
     * @param string $source
     * @param array $properties
     * @return string
     */
    function getChunk($source, $properties = null){
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
                $path = $this->modx->getOption('tplPath', $properties, MODX_BASE_PATH.$this->config['tplPath']);
                $key = $path . $source;
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
    function parseTpl($mainChunk, $properties = null) {
        $output = '';
        if(is_object($mainChunk)){
            $chunk = $this->modx->newObject('modChunk');
            $chunk->fromArray($mainChunk->toArray());
            $output = $chunk->process($properties);
        }
        if (empty($output) && $output !== '0') {
            $prefix = $this->modx->getOption('tplPrefix', $properties, '');
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setCacheable(false);
            $output = $chunk->process(array("{$prefix}output" => print_r($properties, true)), "<pre>[[+{$prefix}output]]</pre>");
        }
        return $output;
    }
        
    
    /**
     * Вытаскивает товары из БД
     * 
     * @return array
     */
    function getProducts($prodIds=''){
        
        if(!$prodIds) return array();
        
        $prodIdsArr = explode(',',str_replace(' ','',$prodIds));
        
        $query = $this->modx->newQuery('modResource');
        $query->where(array('id:IN' => $prodIdsArr, 'published' => 1));
        $query->select($this->modx->getSelectColumns('modResource','modResource','',array('id','pagetitle','template')));
        $query->sortby('pagetitle','ASC');//ORDER BY FIND_IN_SET(id,'{$prodIds}')
        if($this->config['limitProducts'] && is_numeric($this->config['limitProducts'])) $query->limit($this->config['limitProducts']);
        //$resources = $this->modx->getCollection('modResource',$query);
        $resources = $this->modx->getIterator('modResource',$query);
        
        /*
        $query = "
            SELECT id, pagetitle, alias, template FROM ".$this->config['tbl_catalog']."
            WHERE id IN ({$prodIds}) AND published = '1'
            ORDER BY FIND_IN_SET(id,'{$prodIds}')
        ";
        if($this->config['limitProducts'] && is_numeric($this->config['limitProducts'])) $query .= " LIMIT ".$this->config['limitProducts'];
        $result = $this->modx->db->query($query);
        */
        $products = array();
        
        if(count($resources)>0){
            
            foreach ($resources as $key => $resource) {
                $products[] = $resource->toArray();
            }
            
            $tvVars = $this->getTmplVars($prodIdsArr);
            $products = $this->mergeContentAndTV($products,$tvVars);
        }
        
        return $products;
        
    }
    
    /**
     * Вытаскивает из БД TV параметры
     * 
     * @param array $contentIds
     * @return array
     */
    function getTmplVars($contentIds){
        $templateVars = array();
        
        if(count($contentIds)==0) return array();
        
        $tv_notempty_arr = array();
        $tv_names = array();
        
        $query = $this->modx->newQuery('modTemplateVar');
        $query->leftJoin('modTemplateVarTemplate','modTemplateVarTemplate',array("modTemplateVar.id = modTemplateVarTemplate.tmplvarid"));
        $query->where("modTemplateVarTemplate.templateid IN (SELECT DISTINCT `template` FROM ".$this->modx->getTableName('modResource')." WHERE `id` IN (".implode(',',$contentIds)."))");
        $query->select($this->modx->getSelectColumns('modTemplateVar','modTemplateVar','',array('id','name','default_text')));
        
        $collection = $this->modx->getIterator('modTemplateVar',$query);
        if($collection){
            foreach ($collection as $idx => $tv) {
                $tv_names[$tv->get('name')] = $tv->get('default_text');
            }
        }
        
        if(count($tv_names)){
            
            $query = $this->modx->newQuery('modTemplateVar');
            $query->leftJoin('modTemplateVarResource','modTemplateVarResource',array("modTemplateVar.id = modTemplateVarResource.tmplvarid"));
            $query->where(array("modTemplateVarResource.contentid:IN"=>$contentIds));
            $query->select($this->modx->getSelectColumns('modTemplateVar','modTemplateVar','',array('id','name','display','type')));
            $query->select($this->modx->getSelectColumns('modTemplateVarResource','modTemplateVarResource','',array('value','contentid')));
            
            $collection = $this->modx->getIterator('modTemplateVar',$query);
            
            if($collection){
                
                foreach($collection as $idx => $tv){
                    $tv_val = trim($tv->get('value'));
                    $templateVars[$tv->get('contentid')] = !isset($templateVars[$tv->get('contentid')]) ? array($tv->get('name')=>$tv_val) : array_merge($templateVars[$tv->get('contentid')],array( $tv->get('name')=>$tv_val ));
                    if(!empty($tv_val) && !in_array($tv->get('id'),$tv_notempty_arr)) $tv_notempty_arr[] = $tv->get('name');
                }
                
            }
            
            //заполняем "дыры" значениями по умолчанию
            foreach($templateVars as $key => &$val){
                $hole = array_diff(array_keys($tv_names),array_keys($val));
                foreach($hole as $k => $v){
                    if(in_array($v,$tv_notempty_arr)) $val[$v] = str_replace('||','',$tv_names[$v]);
                }
            }
            
        }
        
        //echo '<pre>'.print_r($tv_notempty_arr,true).print_r($templateVars,true).'</pre>';
        
        return $templateVars;
    }
    
    /**
     * Соединяет массивы полей товара и его TV
     * 
     * @param array $cont_arr
     * @param array $tv_arr
     * @return array
     */
    function mergeContentAndTV($cont_arr,$tv_arr){
        if(!is_array($cont_arr)) $cont_arr = array();
        if(!is_array($tv_arr)) $tv_arr = array();
        if(count($cont_arr)==0 && count($tv_arr)==0) return array();
        foreach($cont_arr as $key => &$val){
            $tvs = isset($tv_arr[$val['id']]) ? $tv_arr[$val['id']] : array();
            $val = array_merge($val,$tvs);
        }
        return $cont_arr;
    }
    
    /**
     * Возвращает URL категорий товара
     * 
     * @param array $parents
     * @return array
     */
    function getParentsPaths($parents){
        if(!is_array($parents) && count($parents)>0) return array();
        $this->qsStart = $this->modx->config['friendly_urls'] ? '?' : '&amp;';
        $parentsPaths = array();
        foreach($parents as $key => $val){
            $prodId = (int)$val;
            $parentsPaths[$prodId] = $this->modx->makeUrl( $prodId, '', '', 'abs');
        }
        return $parentsPaths;
    }
    
    /**
     * Возвращает названия параметров, по которым будет происходить сравнение
     *
     */
    function getParameters($template=''){
        $out = array(array(),array());
        
        $compareIds = !empty($_COOKIE['shkCompareIds']) ? $_COOKIE['shkCompareIds'] : '';
        
        //определяем список TV ID, которые не нужно выводить для категории данной товаров
        $categoryId = isset($_COOKIE['shkCompareParent']) ? $_COOKIE['shkCompareParent'] : 0;
        $temp_ct_tvids = explode('||',str_replace(' ','',$this->config['filterTVID']));
        $filterTVID = array();
        if($categoryId && count($temp_ct_tvids)>0){
            foreach($temp_ct_tvids as $key => $val){
                $temp = explode('~',$val);
                if(count($temp_ct_tvids)>0 && $key==0) $filterTVID = isset($temp[1]) ? explode(',',$temp[1]) : explode(',',$temp[0]);
                if(isset($temp[1]) && $temp[0]==$categoryId){
                    $filterTVID = explode(',',$temp[1]);
                    break;
                }
            }
            unset($key,$val);
        }
        
        if($compareIds){
            
            $prodIdsArr = explode(',',str_replace(' ','',$compareIds));
            
            $query = $this->modx->newQuery('modTemplateVar');
            $query->leftJoin('modTemplateVarTemplate','modTemplateVarTemplate',array("modTemplateVar.id = modTemplateVarTemplate.tmplvarid"));
            $query->where(array('modTemplateVarTemplate.templateid'=>$template));
            if(count($filterTVID)>0 && !empty($filterTVID[0])) $query->andCondition(array('modTemplateVar.id:NOT IN' => $filterTVID));
            $query->select($this->modx->getSelectColumns('modTemplateVar','modTemplateVar','',array('id','name','caption')));
            $query->sortby('modTemplateVar.rank','ASC');
            
            //$tvars = $this->modx->getCollection('modTemplateVar',$query);
            $tvars = $this->modx->getIterator('modTemplateVar',$query);
            
            foreach ($tvars as $key => $tv) {
                $out[0][] = $tv->get('name');
                $out[1][] = $tv->get('caption');
            }
            
        }
        
        return $out;
    }
    
    
    /**
     * Возвращает HTML-код вывода ссылки на страницу сравнения с числом выбранных товаров по шаблону
     * 
     * @return string
     */
    function toCompareContent(){
        
        if( $this->config['jsScript'] ){
            $config = array(
                'minProducts' => $this->config['minProducts'],
                'limitProducts' => $this->config['limitProducts'],
                'targetActiveClass' => $this->config['targetActiveClass'],
                'onlyThisParentId' => (
                        !empty($this->config['onlyThisParentId']) && is_numeric($this->config['onlyThisParentId'])
                        ? $this->config['onlyThisParentId'] : false
                    )
            );
            $scriptCode = '
                <script type="text/javascript" src="'.MODX_BASE_URL.'assets/components/compare/js/compare.js"></script>
                <script type="text/javascript">
                    var cmpr_config = ' . json_encode( $config ) .  ';
                </script>
            ';
            $this->modx->regClientStartupScript($scriptCode);
        }
        
        $compareIds_arr = !empty($_COOKIE['shkCompareIds']) ? explode(',',$_COOKIE['shkCompareIds']) : array();
        
        $chunkArr = array(
            'count_current' => count($compareIds_arr),
            'count_max' => $this->config['limitProducts'],
            'href_compare' => $this->modx->makeUrl( $this->config['comparePageId'] , '', '', 'abs' ),
            'href_cancel' => $this->modx->resource && $this->modx->resource->id
                ? $this->modx->makeUrl( $this->modx->resource->get('id'), '', array( 'cmpr_action' => 'empty' ), 'abs' )
                : '',
            'display_cancel' => count($compareIds_arr)==0 ? 'none' : 'inline'
        );
        
        if($this->config['toCompare_tpl']){
            $chunk = $this->getChunk($this->config['toCompare_tpl']);
            return $this->parseTpl($chunk,$chunkArr);
        }else{
            return '';
        }
        
    }
    
    /**
     * Выводит список ID товаров, выбранных для сравнения
     *
     */
    function printIDList(){
        $compareIds = !empty($_COOKIE['shkCompareIds']) ? $_COOKIE['shkCompareIds'] : '';
        return $compareIds;
    }
    
    /**
     * Выводит таблицу с параметрами товаров, выбранных для сравнения
     *
     */
    function printCompareProducts(){
        
        $out = '';
        
        $chunk = $this->getChunk($this->config['product_tpl']);
        $tpl = $chunk->get('snippet');
        $tpl_arr = explode('<!--tpl_separator-->',$tpl);
        if(count($tpl_arr)<7) return '[Ошибка] Шаблон не соответствует правилам.';
        
        $compareIds = !empty($_COOKIE['shkCompareIds']) ? $_COOKIE['shkCompareIds'] : '';
        if($compareIds){
            
            $products = $this->getProducts($compareIds);
            
            if(count($products)>0){
                $template_id = $products[0]['template'];
                $parameters = $this->getParameters($template_id);
                
                $out .= $tpl_arr[0];//верхняя часть таблицы
                
                //верхняя строка таблицы
                if(preg_match('/[\w]/',$tpl_arr[2])){
                    $out .= $tpl_arr[2];
                    $chunk = $this->getChunk('@INLINE '.$tpl_arr[3]);
                    foreach($products as $key => $prod){
                        $c_classes = $key%2==0 ? 'even' : 'odd';
                        if($key+1==count($products)) $c_classes .= ' last';
                        $chunkArr = array(
                            'iteration'=>$key+1,
                            'classes'=>$c_classes
                        );
                        $out .= $this->parseTpl($chunk,array_merge($chunkArr,$prod));
                    }
                    unset($key,$prod);
                }
                
                $iteration = 0;
                
                //строки с параметрами товаров
                foreach($parameters[0] as $p_key => $p_name){
                    $row_str = '';
                    
                    if(!in_array($p_name,array_keys($products[0]))) continue;
                    
                    $iteration++;
                    $param_name = $parameters[1][$p_key];
                    $r_classes = $iteration%2==0 ? 'even' : 'odd';
                    if($p_key==count($parameters[0])) $r_classes .= ' last';
                    
                    //Строка с наименованием параметра
                    $chunkArr = array('param_name'=>$param_name,'row_number'=>$iteration);
                    $chunk = $this->getChunk('@INLINE '.$tpl_arr[4]);
                    $row_str .= $this->parseTpl($chunk,$chunkArr);
                    
                    $row_chunk = $this->getChunk('@INLINE '.$tpl_arr[5]);
                    
                    //строка с параметрами
                    foreach($products as $key => $prod){
                        $c_classes = $key%2==0 ? 'even' : 'odd';
                        if($key+1==count($products)) $c_classes .= ' last';
                        $chunkArr = array(
                            'param_name'=>$param_name,
                            'tv_name'=>$p_name,
                            'param_value'=>(isset($prod[$p_name]) ? $prod[$p_name] : ''),
                            'iteration'=>$key+1,
                            'row_number'=>$iteration,
                            'classes'=>$c_classes
                        );
                        $row_str .= $this->parseTpl($row_chunk,$chunkArr);//$this->phx->Parse($tpl_arr[5]);
                    }
                    $this->phx->placeholders = array();
                    $chunkArr = array('inner'=>$row_str,'classes'=>$r_classes);
                    $chunk = $this->getChunk('@INLINE '.$tpl_arr[1]);
                    $out .= $this->parseTpl($chunk,$chunkArr);
                }
                
                $out .= $tpl_arr[6];//нижняя часть таблицы
                
            }
            
        }else{
            $out = $this->config['noResults'];
        }
        return $out;
        
    }
    
    /**
     * Удаление товара из списка для сравнения
     *
     */
    function deleteCompareProduct(){
        $prod_id = isset($_GET['pid']) && is_numeric($_GET['pid']) ? $_GET['pid'] : 0;
        $compareIds = !empty($_COOKIE['shkCompareIds']) ? $_COOKIE['shkCompareIds'] : '';
        if($prod_id && $compareIds){
            $prodIdsArr = explode(',',str_replace(' ','',$compareIds));
            $out_arr = array();
            foreach($prodIdsArr as $key => $id){
                if($id!=$prod_id) array_push($out_arr,$id);
            }
            if(($this->config['removeLastTwo'] && count($out_arr)==1) || count($out_arr)==0){
                setcookie('shkCompareParent', '', 0, '/');
                setcookie('shkCompareIds', '', 0, '/');
            }else{
                setcookie('shkCompareIds', implode(',',$out_arr), time()+3600*24*365, '/');
            }
            
        }
        $this->modx->sendRedirect($this->modx->makeUrl( $this->modx->resource->get('id'), '', '', 'abs' ), 0, 'REDIRECT_HEADER');
        exit;
    }
    
    /**
     * Очищает список ID товаров, выбранных для сравнения
     *
     */
    function emptyCompare(){
        setcookie('shkCompareParent', '', 0, '/');
        setcookie('shkCompareIds', '', 0, '/');
        $this->modx->sendRedirect($this->modx->makeUrl( $this->modx->resource->get('id'), '', '', 'abs' ), 0, 'REDIRECT_HEADER');
        exit;
    }

}
