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
3. Excluding resources 
  
excluding resources with ids 17,23,31,16:

[[shk_sitemap?
&packageNames=`modResource,shop`
&classNames=`modResource,ShopContent`
&contexts=`web,catalog`
&excludeModResIds=`17,23,31,16`    
]]
 and excluding resources with ids 17,23,31,16 and 1 level childs of resources with ids 17,11,23,14
[[shk_sitemap?
&packageNames=`modResource,shop`
&classNames=`modResource,ShopContent`
&contexts=`web,catalog`
&excludeModResIds=`17,23,31,16`
&excludeModContIds=`17,11,23,14`
]]
 
  
  
  
*/

$config =  array(
    'packageNames' => 'modResource,shop',
    'classNames' => 'modResource,ShopContent',
    'contexts' => 'web,catalog',
    'site_url'=> $modx->getOption('site_url'),
    'site_start'=> $modx->getOption('site_start', null, 1),
    'excludeModResIds'=>'17,23,31,16,19,20',
    'excludeModContIds'=>'17,11,23,14'
);
$contentType = $modx->getObject('modContentType',array('name'=>'HTML'));
$config['urlSuffix']= $contentType->getExtension();
$config['containerSuffix']=$modx->getOption('container_suffix');
$config = array_merge( $config, $scriptProperties );


$packageNames = explode(',',$config['packageNames']);
$classNames = explode(',',$config['classNames']);
$contexts = explode(',',$config['contexts']);

$output = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

if(!function_exists('getMapQuery')){
    function getMapQuery($className,$select,$where){
        global $modx;
        $query = $modx->newQuery($className);
        $query->select($select);
        $query->where($where);
        $query->sortby('id','ASC');
        if ( $query->prepare() && $query->stmt->execute() ){
            $query_out= array();
            //$modx->log(modX::LOG_LEVEL_ERROR, $query->toSql());
            $query_out = $query->stmt->fetchAll(PDO::FETCH_ASSOC);

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
if (!function_exists('datediff')) {
    /**
     * @param string $interval Can be:
    yyyy - Number of full years
    q - Number of full quarters
    m - Number of full months
    y - Difference between day numbers
    (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The
    datediff is "-32".)
    d - Number of full days
    w - Number of full weekdays
    ww - Number of full weeks
    h - Number of full hours
    n - Number of full minutes
    s   - Number of full seconds (default)
     * @param $datefrom
     * @param $dateto
     * @param bool $using_timestamps
     * @return float|int|string
     * @package googlesitemap
     */
    function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
        if (!$using_timestamps) {
            $datefrom = strtotime($datefrom, 0);
            $dateto = strtotime($dateto, 0);
        }
        $difference = $dateto - $datefrom; /* Difference in seconds */
        switch($interval) {
            case 'yyyy': /* Number of full years */
                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                    $years_difference--;
                }
                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                    $years_difference++;
                }
                $datediff = $years_difference;
                break;

            case 'q': /* Number of full quarters */
                $quarters_difference = floor($difference / 8035200);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $quarters_difference++;
                }
                $quarters_difference--;
                $datediff = $quarters_difference;
                break;

            case 'm': /* Number of full months */
                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $months_difference--;
                $datediff = $months_difference;
                break;

            case 'y': /* Difference between day numbers */
                $datediff = date('z',$dateto) - date('z',$datefrom);
                break;

            case 'd': /* Number of full days */
                $datediff = floor($difference / 86400);
                break;

            case 'w': /* Number of full weekdays */
                $days_difference = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); /* Complete weeks */
                $first_day = date('w', $datefrom);
                $days_remainder = floor($days_difference % 7);
                /* Do we have a Saturday or Sunday in the remainder? */
                $odd_days = $first_day + $days_remainder;
                if ($odd_days > 7) { /* Sunday */
                    $days_remainder--;
                }
                if ($odd_days > 6) { /* Saturday */
                    $days_remainder--;
                }
                $datediff = ($weeks_difference * 5) + $days_remainder;
                break;

            case 'ww': /* Number of full weeks */
                $datediff = floor($difference / 604800);
                break;

            case 'h': /* Number of full hours */
                $datediff = floor($difference / 3600);
                break;

            case 'n': /* Number of full minutes */
                $datediff = floor($difference / 60);
                break;

            default: /* Number of full seconds (default) */
                $datediff = $difference;
                break;
        }
        return $datediff;
    }
}


foreach ( $packageNames as $key => $packageName ){

    $parentName = $packageName == 'modResource' ? "parent" : "resource_id";
    $select = array('id','alias','editedon','createdon',$parentName);
    if( $packageName != 'modResource' ){
        $modelpath = $modx->getOption('core_path') . 'components/' . $packageName . '/model/';
        $modx->addPackage($packageName, $modelpath);
    }
    else{
        $select = array_merge( $select, array('context_key','isfolder') );
    }

    $where=array( 'published' => 1 );
    if($config['excludeModResIds'] && $packageName == 'modResource'){
        $ids=array('id:NOT IN' => explode(',',$config['excludeModResIds'] ));
        array_push($where,$ids);
    }
    if($config['excludeModContIds']&& $packageName == 'modResource'){
        $ids=array('parent:NOT IN' => explode(',',$config['excludeModContIds'] ));
        array_push($where,$ids);
    }
    $resources = getMapQuery($classNames[$key],$select,$where);
    if(!empty($resources)){
        foreach ( $resources as $resource ){
            if(!empty($resource['alias'])){
                if (!isset($resource['context_key'])) {
                    $resource['context_key'] = !empty( $contexts[$key] ) ? $contexts[$key] : $contexts[0];
                }
                if ( $resource[$parentName] != 0 ){
                    $url = $modx->makeUrl($resource[$parentName],$resource['context_key'],'','full');
                }
                else{
                    $url = $config['site_url'];
                }

                $url .=substr($url, -1)=='/' ? $resource['alias'] : '/'.$resource['alias'];
                $url .= !empty( $resource['isfolder'] ) ? $config['containerSuffix'] : $config['urlSuffix'];

                if ($packageName == 'modResource'&& $resource['id']==$config['site_start']){
                    $url=$config['site_url'];
                }

                $date = !empty( $resource['editedon'] ) ? $resource['editedon'] : $resource['createdon'];
                $date = strftime( '%Y-%m-%d', $date );
                $date = date("Y-m-d", strtotime($date));

                /* Get the date difference */
                $datediff = datediff("d", $date, date("Y-m-d"));
                if ($datediff <=1) {
                    $priority = '1.0';
                    $update = 'daily';
                } elseif (($datediff >1) && ($datediff<=7)) {
                    $priority = '0.75';
                    $update = 'weekly';
                } elseif (($datediff >7) && ($datediff<=30)) {
                    $priority = '0.50';
                    $update = 'weekly';
                } else {
                    $priority = '0.25';
                    $update = 'monthly';
                }

                $output .= "
            <url>
                <loc>{$url}</loc>
                <lastmod>{$date}</lastmod>
                <priority>{$priority}</priority>
                <changefreq>{$update}</changefreq>
            </url>";
            }
        }
    }
}
unset($key);

$output .= PHP_EOL . "</urlset>";

return $output;
