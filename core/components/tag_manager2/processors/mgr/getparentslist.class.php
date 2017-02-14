<?php

/**
 * getParentsList
 * 
 */

//error_reporting(E_ALL);
//ini_set('display_errors',1);

class getParentsListProcessor extends modProcessor {

    public function process() {
        
        $scriptProperties = $this->getProperties();
        
        $options = array(
            'parent_id' => $this->modx->getOption('parent_id', $scriptProperties, $this->modx->config['tag_mgr2.catalog_id']),
            'context_key' => $this->modx->config['tag_mgr2.catalog_context'],
            'current_id' => $this->modx->getOption('current_id', $scriptProperties, 0)
        );
        
        $modelpath = $this->modx->config['core_path'].'components/tag_manager2/model/';
        $this->modx->addPackage('tag_manager2', $modelpath);
        
        $list = array();
        $ids_arr = array();
        
        //Создаем массив сохраненных категорий
        $saved_parents = array();
        
        $c = $this->modx->newQuery('tagManager');
        //$c->where(array('active'=>true));
        $c->select(array('id','category'));
        $c->groupby('category');
        $results = $this->modx->getCollection('tagManager', $c);
        if ($c->prepare() && $c->stmt->execute()) {
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                if( !in_array( $row['category'], $saved_parents ) ){
                    array_push($saved_parents, intval($row['category']));
                }
            }
            $c->stmt->closeCursor();
        }
        
        /**
         * $list
         *
         * Создаем массив всех категорий
         *
         */
        if($options['parent_id'] > 0){
            
            $c = $this->modx->newQuery("modResource");
            $c->where(
                array(
                    'id' => $options['parent_id'],
                    'context_key' => $options['context_key'],
                    'isfolder' => 1,
                    'published' => 1,
                    'deleted' => 0
                )
            );
            $c->sortby('menuindex','asc');
            $c->select(array('id','parent','pagetitle'));
            if ($c->prepare() && $c->stmt->execute()) {
                $row = $c->stmt->fetch(PDO::FETCH_ASSOC);
                if(!empty($row)){
                    
                    $childs = $this->getParents($options['parent_id'], $options['context_key'], $saved_parents, $options['current_id']);
                    $ids_arr[] = $options['parent_id'];
                    $ids_arr = array_merge($ids_arr, $childs[0]);
                    $row['children'] = $childs[1];
                    
                    $row['label'] = $row['pagetitle'];
                    $row['expanded'] = true;
                    $row['active'] = in_array($row['id'],$saved_parents);
                    $row['selected'] = !$options['current_id'] || ($row['id'] == $options['current_id']) ? true : false;
                    $list[] = $row;
                    
                }
                $c->stmt->closeCursor();
            }
            
        }else{
            
            $childs = $this->getParents($options['parent_id'], $options['context_key'], $saved_parents, $options['current_id']);
            $ids_arr[] = 0;
            $ids_arr = array_merge($ids_arr, $childs[0]);
            
            $list = array(
                array(
                    'id' => 0,
                    'label' => $this->modx->lexicon('tag_mgr2.catalog'),
                    'expanded' => true,
                    'active' => in_array(0,$saved_parents),
                    'selected' => (!$options['current_id'] || ($options['current_id'] == 0) ? true : false),
                    'children' => $childs[1]
                )
            );
            
        }
        
        //var_dump($ids_arr,$list);
        
        $count = count($ids_arr);
        
        $output = array(
            'success' => true,
            'message' => '',
            'object' => array($ids_arr, $list)
        );
        
        return $output;
        
    }
    
    
    /**
     * getParents
     *
     */
    function getParents($parent_id, $context_key, $saved_parents = array(), $current_id = 0){
        
        global $modx;
        
        $data = array(array(),array());
        
        $c = $this->modx->newQuery("modResource");
        $c->where(
            array(
                'parent' => $parent_id,
                'context_key' => $context_key,
                'isfolder' => 1,
                'published' => 1,
                'deleted' => 0
            )
        );
        $c->sortby('menuindex','asc');
        $c->select(array('id','parent','pagetitle','context_key'));
        if ($c->prepare() && $c->stmt->execute()) {
            
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                
                $childs = $this->getParents($row['id'], $row['context_key'], $saved_parents, $current_id);
                $row['children'] = $childs[1];
                $data[0] = array_merge($data[0], $childs[0]);
                
                $row['label'] = $row['pagetitle'];
                $row['active'] = in_array($row['id'],$saved_parents);
                if($current_id && $current_id == $row['id']) $row['selected'] = true;
                array_push($data[0], $row['id']);
                array_push($data[1], $row);
                
            }
            
            $c->stmt->closeCursor();
        }
        
        return $data;
        
    }
    
}

return 'getParentsListProcessor';
