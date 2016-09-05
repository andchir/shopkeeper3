<?php

/**
 * snippet shk_sitemap
 *
 * @author slaad
 * 
 */

/*

http://modx-shopkeeper.ru/forum/viewtopic.php?pid=20545#p20545

Examples

1. Only resources (one or more contexts):

[[shk_sitemap?
&packageNames=`modResource`
&classNames=`modResource`
]]

2. Resources and shop package (Shopkeeper), two contexts:

[[shk_sitemap?
&packageNames=`modResource,shop`
&classNames=`modResource,ShopContent`
&contexts=`web,catalog`
]]

*/

$config =  array( 
    'packageNames' => 'modResource,shop',
    'classNames' => 'modResource,ShopContent',
    'contexts' => 'web,catalog'
);

$config = array_merge( $config, $scriptProperties );
$contentType = $modx->getObject('modContentType',array('name'=>'HTML'));
$config['urlSuffix'] = $contentType->getExtension();
$config['containerSuffix'] = $modx->getOption('container_suffix');

$output = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">" . PHP_EOL;

if(!function_exists('getMapQuery')){
function getMapQuery($className,$select){
    global $modx;
        $query = $modx->newQuery($className);
        $query->select($select);
        $query->where( array( 'published' => 1, 'hidemenu' => 0 ) );
        if ( $query->prepare() && $query->stmt->execute() ){     
            $query_out= array();
            //$modx->log(modX::LOG_LEVEL_ERROR, $query->toSql());
            $query_out=$query->stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($query_out as $r){
                if (!isset($resources[$r['id']])) $resources[$r['id']]=array();
                foreach ($select as $s){
                    $resources[$r['id']][$s] = $r[$s];
                }
            }         
        }
        return $resources;
    }
}

$packageNames = explode(',',$config['packageNames']);
$classNames = explode(',',$config['classNames']);
$contexts = explode(',',$config['contexts']);

foreach ( $packageNames as $key => $packageName ){
    
    $parentName = $packageName == 'modResource' ? "parent" : "resource_id";
    $select = array('id','alias','editedon',$parentName);
    if( $packageName != 'modResource' ){
        $modelpath = $modx->getOption('core_path') . 'components/' . $packageName . '/model/';
        $modx->addPackage($packageName, $modelpath);
    }
    else{
        $select = array_merge( $select, array('context_key','isfolder') );
    }
    
    $resources = getMapQuery($classNames[$key],$select);
    
    foreach ( $resources as $resource ){
        
        if (!isset($resource['context_key'])) $resource['context_key'] = !empty( $contexts[$key] ) ? $contexts[$key] : $contexts[0];
        if ( $resource[$parentName] != 0 ){
            $url = $modx->makeUrl($resource[$parentName],$resource['context_key'],'','full');
        }
        else{
            $url = $modx->getOption('site_url');
        }
        $url .= $resource['alias'];
        $url .= !empty( $resource['isfolder'] ) ? $config['containerSuffix'] : $config['urlSuffix'];
        $editedon = !empty( $resource['editedon'] ) ? $resource['editedon'] : $resource['createdon'];
        $date = strftime( '%Y-%m-%d', $editedon );
        $output .= "
        <url>
            <loc>{$url}</loc>
            <lastmod>{$date}</lastmod>
            <priority>0.9</priority>
            <changefreq>monthly</changefreq>
        </url>";
    }

}

$output .= PHP_EOL . "</urlset>";

return $output;
