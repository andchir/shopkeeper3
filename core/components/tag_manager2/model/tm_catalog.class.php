<?php

/**
 * tagManager2
 *
 * tmCatalog class
 *
 * @author Andchir <andchir@gmail.com>
 * @package tag_manager2
 * @version 2.3
 */

require_once dirname(__FILE__)."/tm_base.class.php";

class tmCatalog extends tagManagerBase {
    
    public $modx = null;
    public $config = array();
    
    /**
     *
     *
     */
    function __construct(modX &$modx, $config = array()){
        
        parent::__construct($modx, $config);
        
    }
    
    
    /**
     * getTVMap
     * 
     * Создает массив с данными TV
     */
    public function getTVMap(){
        
        $output = array();
        
        if($this->config['className'] == 'modResource'){
            
            //var_dump($this->config['className']);
            
            
            
            /*
            $tv_categoryid = $this->modx->getOption('tag_mgr.tv_categoryid',null,0);
            $tv_categoryid_arr = $tv_categoryid ? explode(',',$tv_categoryid) : array();
            $tv_idlist = $this->modx->getOption('tag_mgr.tvs',null,'');
            $tv_idlist_arr = $tv_idlist ? explode(',',$tv_idlist) : array();
            
            $tv_query = $this->modx->newQuery('modTemplateVar');
            if(!empty($tv_categoryid_arr)){
                $tv_query->where(array('category:IN'=>$tv_categoryid_arr));
            }else if(!empty($tv_idlist_arr)){
                $tv_query->where(array('id:IN'=>$tv_idlist_arr));
            }
            $tvs = $this->modx->getCollection('modTemplateVar',$tv_query);
            
            foreach($tvs as $tv){
                
                $output[$tv->name] = array($tv->caption,$tv->type);
                
            }
            */
        }
        
        return $output;
        
    }
    
    /**
     * Создает массив с параметрами фильтрации из $_GET
     * 
     */
    public function getRequestParams(){
        
        $filter_arr = $_GET;
        $flt_arr = array();
        $parents = array();
        
        $guard_key = '';
        if( $this->config['guardKey'] === false ){
            $guard_key = $this->modx->getOption('tag_mgr2.guard_key', null, '');
        }
        
        if($this->config['className']=='modResource'){
            $fields_map = $this->getTVMap();
        }else{
            $fields_map = array();//$this->getMigxFieldsMap();
        }
        
        if(!empty($_GET)){
            foreach($filter_arr as $key => $flt_val){
                
                if(substr($key,0,2)!='f_') continue;
                $f_name = substr($key,2);
                
                if(is_array($flt_val)){
                    
                    foreach($flt_val as $k => $flt){
                        
                        if( is_array($flt) ) $flt = implode('|',$flt);
                        
                        $flt = htmlspecialchars(rawurldecode(trim($flt)));
                        $flt = str_replace('_n_','&',$flt);
                        
                        if($f_name == 'parent' && is_numeric($flt)){
                            if(!empty($flt)) $parents[] = $flt;
                        }else{
                            $operator = '=';//count($flt_val)>1 ? 'IN' : '=';
                            if($k==='from') $operator = '>=';
                            if($k==='to') $operator = '<=';
                            if($k==='like') $operator = 'LIKE';
                            
                            if(!empty($flt)){
                                
                                if(strpos($flt,'|') !== false){
                                    
                                    $temp_arr = explode('|',$flt);
                                    foreach($temp_arr as $val){
                                        //Если нужно искать подстроку
                                        if($k==='like'){
                                            if(isset($fields_map[$f_name]) && $fields_map[$f_name][1]=='param-edit'){
                                                $val = '%'.$val.'%';
                                            }else{
                                                $val = '%'.$guard_key.$val.$guard_key.'%';
                                            }
                                        }
                                        $flt_arr[$f_name.':'.$operator][] = $val;
                                    }
                                    
                                }else{
                                    
                                    //Если нужно искать подстроку
                                    if($k==='like'){
                                        if(isset($fields_map[$f_name]) && $fields_map[$f_name][1]=='param-edit'){
                                            $flt = '%'.$flt.'%';
                                        }else{
                                            $flt = '%'.$guard_key.$flt.$guard_key.'%';
                                        }
                                    }
                                    
                                    if( is_numeric(str_replace(',','.',$flt)) ) $flt = str_replace(',','.',$flt);
                                    $flt = $this->priceFilter( $f_name, $flt, true );//если это цена то возвращаем исходную цену - мультивалютность
                                    
                                    $flt_arr[$f_name.':'.$operator][] =  $flt;
                                }
                                
                            }
                        }
                    }
                    unset($k,$flt);
                    
                }else{
                    
                    $flt_val = htmlspecialchars(rawurldecode(trim($flt_val)));
                    $flt_val = str_replace('_n_','&',$flt_val);
                    if($f_name == 'parent' && is_numeric($flt_val)){
                        if(!empty($flt_val)) $parents[] = $flt_val;
                    }else{
                        if(!empty($flt_val)) $flt_arr[$f_name] = $flt_val;
                    }
                    
                }
                
            }
        }
        
        return array($flt_arr,$parents);
        
    }
    
    
    /**
     * Если это цена, пропускаем значение через плагин 'OnSHKgetProductPrice'
     *
     */
    public function priceFilter($name, $value, $inverse=false){

        //Если это цена, пропускаем через плагин
        if($this->config['priceName'] && $this->config['priceName'] == $name){
            //OnSHKgetProductPrice
            $evtOut = $this->invokeEventCombine('OnSHKgetProductPrice',array('purchaseArray' => array(), 'price' => $value, 'inverse' => $inverse));
            if( !empty($evtOut) && is_array($evtOut) ){
                $new_price = (float) str_replace(array(' ',','), array('','.'), end($evtOut));
                if($new_price != $value){
                    $value = $new_price;
                }
            }
            $value = str_replace(',','.',strval($value));
        }
        
        return $value;
    }
    
    
    /**
     * getSorting
     *
     */
    public function getSorting($sortby, $sortdir){
        
        $output = array();
        
        if( $this->config['className'] != 'modResource' || in_array($sortby,array('id','pagetitle','longtitle','menuindex','pub_date','parent','createdon','publishedon','RAND()')) ){
            $output['sortby'] = $sortby;
            $output['sortdir'] = $sortdir;
        }else{
            $output['sortby'] = '';
            $output['sortdir'] = '';
            $output['sortbyTV'] = $sortby;
            $output['sortdirTV'] = $sortdir;
            $output['sortbyTVType'] = in_array($sortby, $this->config['numericFields']) ? 'integer' : 'string';
        }
        
        return $output;
        
    }
    
   
}

