<?php

/**
 * Shopkeeper
 *
 * Shopping cart class
 *
 * @author Andchir <andchir@gmail.com>
 * @package shopkeeper
 * @version 3.2.7
 */

class Shopkeeper {
    
    public $lang = array();
    public $purchase = null;
    public $data = array( 'price' => 0 );
    public $delivery = array();
    public static $price_total = 0;
    public static $items_total = 0;
    public static $items_unique_total = 0;
    protected $version = '3.2.7pl3';
    
    public function __construct( modX &$modx, $config = array(), $applyRequest = false ){
        
        $this->modx = &$modx;
        
        $this->config = array_merge(array(
            'lang' => $this->modx->getOption('manager_language'),
            'tplPath' => 'core/components/shopkeeper3/elements/chunks/ru/',
            'cartTpl' => 'shopCart',
            'cartRowTpl' => 'shopCartRow',
            'className' => '',
            'packageName' => '',
            'migx_configName' => '',
            'fieldPrice' => 'price',
            'fieldName' => 'pagetitle',
            'additParamSource' => '',
            'getUnpublished' => false,
            'allowFloatCount' => false,
            'changePrice' => false,
            'excepDigitGroup' => true,
            'orderFormPageId' => 1,
            'currency' => 'руб.',
            'processParams' => false,
            'savePurchasesFields' => '',
            'pluralWords' => '',
            'groupBy' => ''
        ),$config);
        
        if(!empty($_SESSION['shk_currency_name'])) $this->config['currency'] = $_SESSION['shk_currency_name'];
        
        if( empty( $this->config['migx_configName'] ) ) $this->config['migx_configName'] = $this->config['packageName'];
        if( !isset($this->config['pageId']) ) $this->config['pageId'] = $this->modx->resourceIdentifier;
        
        $this->modx->getService('lexicon','modLexicon');
        $this->modx->lexicon->load($this->config['lang'].':shopkeeper3:default');
        $this->lang = $this->modx->lexicon->fetch('shk3.');
        
        if( !isset( $_SESSION['shk_order'] ) ) $_SESSION['shk_order'] = array();
        if( !isset( $_SESSION['shk_delivery'] ) ) $_SESSION['shk_delivery'] = array();
        $this->data = &$_SESSION['shk_order'];
        $this->delivery = &$_SESSION['shk_delivery'];

        if($applyRequest){
            $this->applyRequest();
        }
        $this->getTotal();
        
    }
    
    
    /**
     * Возвращает массив данных чанка и кэширует чанк
     * 
     * @param string $source
     * @param array $properties
     * @return string
     */
    public function getChunk( $source, $properties = null ){
        
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
     * Очищает строку от тегов MODx
     *
     * @param string $string
     * @return string
     */
    public function stripModxTags($string){
        if(isset($this->modx->sanitizePatterns['tags'])) $string = preg_replace($this->modx->sanitizePatterns['tags'], '', $string);
        if(isset($this->modx->sanitizePatterns['tags1'])) $string = preg_replace($this->modx->sanitizePatterns['tags1'], '', $string);
        if(isset($this->modx->sanitizePatterns['tags2'])) $string = preg_replace($this->modx->sanitizePatterns['tags2'], '', $string);
        return $string;
    }
    
    /**
     * isAjax
     *
     */
    public function isAjax(){
        
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        
    }
    
    
    /**
     * Парсит чанк, возвращает HTML-код
     * 
     * @param string $source
     * @param array $properties
     * @return string
     */
    public function parseTpl( $mainChunk, $properties = null, $chunkName = '' ) {
        $output = '';
        if(is_object($mainChunk)){
            $chunk = $this->modx->newObject('modChunk');
            $chunk->fromArray($mainChunk->toArray());
            $chunk->setCacheable(false);
            if( !is_array( current( $properties ) ) ){
                $output = $chunk->process($properties);
            }else{
                foreach( $properties as $props ){
                    $output .= $this->parseTpl( $mainChunk, $props );
                }
            }
        }
        if (empty($output) && $output !== '0') {
            $prefix = $this->modx->getOption('tplPrefix', $properties, '');
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setCacheable(false);
            $output = $chunk->process(array("{$prefix}output" => print_r($properties, true)), "Chunk <b>{$chunkName}</b> not found<pre>[[+{$prefix}output]]</pre>");
        }
        return $output;
    }
    
    
    /**
     * numberFormat
     *
     */
    public static function numberFormat( $number ){
        
        if(!is_numeric($number)) return '';
        
        $output = floatval(str_replace(array(' ',','), array('','.'), $number));
        $output = number_format($number,(floor($number) == $number ? 0 : 2),'.',' ');
        
        return $output;
    }
    
    
    /**
     * getPlural
     *
     */
    public static function getPlural( $number, $plural_txt )
    {
        
        $output = '';
        
        if( !is_array( $plural_txt ) ){
            $plural_txt = explode( ',', $plural_txt );
        }
        
        if( count( $plural_txt ) >= 3 ){
            $output = $number % 10 == 1 && $number % 100 != 11 ? $plural_txt[0] : ($number % 10 >= 2 && $number % 10 <= 4 && ( $number % 100 < 10 || $number % 100 >= 20 ) ? $plural_txt[1] : $plural_txt[2]);
        }else{
            $output = $number == 1 ? $plural_txt[0] : $plural_txt[1];
        }
        
        return trim( $output );
        
    }
    
    
    /**
     * getCartContent
     *
     */
    public function getCartContent()
    {
        
        $mainChunk = $this->getChunk( $this->config['cartTpl'] );
        $chunk_parts = $mainChunk->get('snippet') ? explode('<!--tpl_separator-->',$mainChunk->get('snippet')) : array();
        
        $output = '';
        
        $OnSHKbeforeCartLoad = $this->getFromEvent( 'OnSHKbeforeCartLoad', array(), 'string' );

        $this->getTotal();
        
        if( !empty( $this->data ) ){
            
            $this_page_url = $this->config['pageId'] && is_numeric($this->config['pageId']) ? $this->modx->makeUrl($this->config['pageId'], '', '', 'full') : '/';
            $url_qs = strpos($this_page_url, "?") !== false ? "&amp;" : "?";
            
            $cartInner = $this->getProductsList();
            $plural_words = $this->config['pluralWords'] ? $this->config['pluralWords'] : $this->lang['shk3.plural'];
            
            $chunkArr = array(
                'inner' => $cartInner,
                'price_total' => $this->config['excepDigitGroup'] ? $this->numberFormat( self::$price_total ) : self::$price_total,
                'items_total' => self::$items_total,
                'items_unique_total' => self::$items_unique_total,
                'delivery_price' => !empty( $this->delivery['price'] ) ? $this->delivery['price'] : 0,
                'delivery_name' => !empty( $this->delivery['label'] ) ? $this->delivery['label'] : $this->lang['shk3.delivery_not_selected'],
                'plural' => self::getPlural( self::$items_total, $plural_words ),
                'this_page_url' => $this_page_url,
                'empty_url' => $this_page_url.$url_qs.'shk_action=empty',
                'order_page_url' => $this->modx->makeUrl( $this->config['orderFormPageId'], '', '', 'full'),
                'currency' => $this->config['currency']
            );
            
            $this->modx->setPlaceholders(array(
                'price_total' => $this->config['excepDigitGroup'] ? $this->numberFormat( self::$price_total ) : self::$price_total,
                'items_total' => self::$items_total,
                'items_unique_total' => self::$items_unique_total,
                'delivery_price' => !empty( $this->delivery['price'] ) ? $this->delivery['price'] : 0
            ),'shk.');
            
            $chunk = $this->getChunk( '@INLINE ' . ( !empty( $chunk_parts[1] ) ? $chunk_parts[1] : '' ) );
            $output = $this->parseTpl( $chunk, $chunkArr, $this->config['cartTpl'] );
            
        }else{
            
            $chunk = $this->getChunk( '@INLINE ' . ( !empty( $chunk_parts[0] ) ? $chunk_parts[0] : '' ) );
            $output = $this->parseTpl( $chunk, array(), $this->config['cartTpl'] );
            
        }
        
        //создаём плейсхолдеры с данными последнего заказа
        if(isset($_SESSION['shk_lastOrder']) && is_array($_SESSION['shk_lastOrder'])){
            $this->modx->setPlaceholders($_SESSION['shk_lastOrder'],'shk.');
        }
        
        $OnSHKcartLoad = $this->getFromEvent( 'OnSHKcartLoad', array(), 'string' );
        
        $output = $OnSHKbeforeCartLoad . $output . $OnSHKcartLoad;
        $output = $this->stripModxTags( $output );
        
        return $output;
        
    }
    
    
    /**
     * getProductsList
     *
     * 
     */
    public function getProductsList( $purchasesData = array(), $chunkName = '' ){
        
        if( empty( $purchasesData ) ){ $purchasesData = $this->getProductsData(); }
        if( !$chunkName ){ $chunkName = $this->config['cartRowTpl']; }
        
        $output = '';
        
        if( !empty( $purchasesData ) ){
            
            if( $this->config['groupBy'] ){
                $purchasesData = $this->groupBy( $purchasesData, $this->config['groupBy'] );
            }
            
            $this_page_url = $this->config['pageId'] && is_numeric($this->config['pageId']) ? $this->modx->makeUrl($this->config['pageId'], '', '', 'full') : '';
            $url_qs = strpos($this_page_url, "?")!==false ? "&amp;" : "?";
            
            $mainChunk = $this->getChunk( $chunkName );
            $plural_words = $this->config['pluralWords'] ? $this->config['pluralWords'] : $this->lang['shk3.plural'];
            
            foreach( $purchasesData as $i => $purchase ){
                
                $additPrice = 0;
                
                //определяем общую цену доп. параметров
                if( isset( $purchase['options'] ) && is_array( $purchase['options'] ) ){
                    
                    $p_options = array();
                    if( !empty( $purchase['options'] ) ){
                        $p_options = $this->getPurchasesOptionsData( $purchase['options'] );
                    }
                    $purchase = array_merge( $purchase, $p_options );
                    unset( $purchase['options'] );
                    
                }
                
                foreach( $purchase as $k => $v ){
                    if( strpos( $k, 'shk_' ) !== false && isset( $purchase[ $k . '_price' ] ) ){
                        $additPrice += $purchase[ $k . '_price' ];
                    }
                }
                
                if( $this->config['groupBy'] ){
                    $price_total = $price_count = $price_count_total = $purchase['price'];
                }
                else {
                    $price_total = $purchase['price'] + $additPrice;
                    $price_count = $purchase['price'] * $purchase['count'];
                    $price_count_total = ( $purchase['price'] + $additPrice ) * $purchase['count'];
                }
                
                $data = array(
                    'index' => $i,
                    'num' => $i + 1,
                    'even' => ($i + 1) % 2 == 0 ? 1 : 0,
                    'comma' => ($i + 1) < count( $purchasesData ) ? ',' : '',
                    'plural' => self::getPlural( $purchase['count'], $plural_words ),
                    'price_total' => $this->config['excepDigitGroup'] ? $this->numberFormat( $price_total ) : $price_total,
                    'price_count' => $this->config['excepDigitGroup'] ? $this->numberFormat( $price_count ) : $price_count,
                    'price_count_total' => $this->config['excepDigitGroup'] ? $this->numberFormat( $price_count_total ) : $price_count_total,
                    'url_del_item' => $this_page_url . $url_qs . "shk_action=remove&amp;n=" . $i,
                    'currency' => $this->config['currency']
                );
                
                $chunkArr = array_merge( $data, $purchase );
                if( is_array( $chunkArr['id'] ) ){
                    $chunkArr['id'] = implode( ',', $chunkArr['id'] );
                }
                
                $output .= $this->parseTpl( $mainChunk, $chunkArr, $chunkName );
                
            }
            
        }
        
        return $output;
        
    }
    
    
    /**
     * Группировка товаров по полю
     * 
     * @param array $purchasesData
     * @param string $groupBy
     */
    public function groupBy( $purchasesData = array(), $groupBy = '' ){
        
        if( !$groupBy ) return $purchasesData;
        $outputArray = array();
        
        if( count( $purchasesData ) > 0 ){
            
            $group = array();
            foreach( $purchasesData as $product ){
                
                if( isset( $product[ $this->config['groupBy'] ] ) ){
                    
                    $index = array_search( $product[ $groupBy ], $group );
                    if( $index === false ){
                        $product['name'] = $product[ $groupBy ];
                        $product['id'] = array( $product['id'] );
                        array_push( $outputArray, $product );
                        array_push( $group, $product[ $this->config['groupBy'] ] );
                    }
                    else{
                        array_push( $outputArray[ $index ][ 'id' ], $product[ 'id' ] );
                        $outputArray[ $index ][ 'count' ] += $product[ 'count' ];
                        $outputArray[ $index ][ 'price' ] += $product[ 'price' ];
                    }
                    
                }
                else{
                    
                    array_push( $purchasesData, $product );
                    
                }
                
            }
            
        }
        
        return $outputArray;
        
    }
    
    
    /**
     * Сохранение данных товара в корзине
     *
     * @param array $purchaseArray
     * @return boolean
     */
    public function savePurchaseData( $purchaseArray = array() ){
        
        if( empty( $purchaseArray ) ) $purchaseArray = $_POST;
        $packageData = $this->getCatalogClass();
        
        $product = $this->getProduct( $purchaseArray, $packageData );
        if( empty( $product ) ) return false;
        
        //OnSHKaddProduct
        $product = $this->getFromEvent( 'OnSHKaddProduct', array( 'product' => $product ), 'link' );
        
        $intersect = $this->checkIntersect( $product );
        
        if( $intersect === false ){
            array_push( $this->data, $product );
        }else{
            $this->data[ $intersect ]['count'] += $product['count'];
        }
        
        //OnSHKafterAddProduct
        $this->modx->invokeEvent( 'OnSHKAfterAddProduct', array( 'purchaseArray' => $purchaseArray, 'index' => $intersect, 'id' => $product['id'] ) );
        
        $this->updateDelivery( '', true );
        
        return true;
        
    }
    
    
    /**
     * Формирование данных товара при добавлении в корзину
     *
     * @param array $purchaseArray
     * @param array $packageData
     * @return array
     */
    public function getProduct( $purchaseArray = array(), $packageData = array() ){
        
        $purchaseOutput = array();
        
        $p_id = 0;
        $p_count = !empty($purchaseArray['count']) ? $this->cleanCount($purchaseArray['count']) : (!empty($purchaseArray['shk-count']) ? $this->cleanCount($purchaseArray['shk-count']) : 1);
        
        //определяем ID товара и если передано имя TV с ценой
        if( isset($purchaseArray['shk-id']) && strpos( $purchaseArray['shk-id'], '__' ) !== false ){
            
            $prodIdArr = explode('__',$purchaseArray['shk-id']);
            $p_id = intval($prodIdArr[0]);
            $this->config['fieldPrice'] = $prodIdArr[1];
            
        }else{
            
            $p_id = intval( $purchaseArray['shk-id'] );
            
        }
        
        //проверяем есть ли такой ID в БД
        $this->getPurchaseFromDB( $p_id, $packageData['className'] );
        if(!is_object($this->purchase)) return $purchaseOutput;
        
        list( $p_price, $old_price ) = $this->getProductPrice(true);
        
        //Добавление к заказу доп. параметров товара с ценами
        $p_options = $this->getProductOptions( $purchaseArray );
        
        //Проверяем нужно ли умножать и обновляем общую цену товара если надо
        foreach( $p_options as &$opts ){
            
            if( $this->config['changePrice'] || $opts['multiplication'] ){
                
                if( $this->config['changePrice'] == 'replace' ){
                    if( $opts[1] > 0 ){
                        $p_price = $opts[1];
                        if( empty( $opts[2] ) ) $opts[2] = $p_price;
                    }
                }else{
                    $p_price = $opts['multiplication'] ? $p_price * $opts[1] : $p_price + $opts[1];
                    if( empty( $opts[2] ) ) $opts[2] = $opts[1];
                }
                
                $opts[1] = 0;
                unset( $opts['multiplication'] );
                
            }
            
        }
        
        $linkUrl = $this->getPurchaseUrl();
        
        $purchaseOutput = array(
            'id' => $p_id,
            'count' => $p_count,
            'price' => $p_price,
            'name' => htmlspecialchars( $this->purchase->get($this->config['fieldName']) ),
            'className' => $packageData['className'],
            'packageName' => $packageData['packageName'],
            'options' => $p_options,
            'url' => $linkUrl
        );
        
        if( isset( $old_price ) ){
            $purchaseOutput['old_price'] = $old_price;
        }
        
        return $purchaseOutput;
        
    }
    
    
    /**
     * Получает товар из БД
     *
     * @param integer $p_id
     * @param string $className
     */
    public function getPurchaseFromDB( $p_id, $className = 'modResource' ){

        $this->purchase = $this->modx->getObject(
            (empty($className) ? 'modResource' : $className),
            array(
                'id' => $p_id,
                'published' => $this->config['getUnpublished'] ? null : 1,
                'deleted' => 0
            )
        );
        
    }
    
    
    /**
     * getProductPrice
     *
     */
    public function getProductPrice( $return_old = false ){
        
        $price = 0;
        
        if( !empty( $this->purchase->class_key ) && in_array( $this->purchase->class_key, array( 'modResource', 'modDocument' ) ) ){
            
            $price_tv = $this->modx->getObject( 'modTemplateVar', array( 'name' => $this->config['fieldPrice'] ) );
            if( $price_tv ){
                $price = $this->config['processParams']
                    ? $price_tv->renderOutput( $this->purchase->id )
                    : $price_tv->getValue( $this->purchase->id );
            }
            if( !$price ){
                $price = $price_tv->get('default_text');
            }
            
        }else{
            
            $price = $this->purchase->get( $this->config['fieldPrice'] );
            //Default value
            if( empty( $price ) ){
                $migx_config = $this->getMigxConfig();
                if( !empty( $migx_config[ $this->config['fieldPrice'] ] )
                    && isset( $migx_config[ $this->config['fieldPrice'] ]['default'] )
                    && !empty( $migx_config[ $this->config['fieldPrice'] ]['useDefaultIfEmpty'] ) ){
                        $price = $migx_config[ $this->config['fieldPrice'] ]['default'];
                }
            }
            
        }
        
        $price = $price ? floatval( str_replace( array(',',' '), array('.',''), strval( $price ) ) ) : 0;
        
        //OnSHKgetProductPrice
        $new_price = $this->getFromEvent(
            'OnSHKgetProductPrice',
            array( 'price' => $price, 'id' => $this->purchase->id, 'purchaseArray' => $this->purchase->toArray() ),
            'float'
        );
        
        $old_price = $price;
        if( $new_price !== false ){
            $price = $new_price;
        }
        
        return $return_old ? array( $price, $old_price ) : $price;
        
    }
    
    
    /**
     * Возвращает URL товара при добавлении в корзину
     *
     */
    public function getPurchaseUrl(){
        
        if( !empty( $this->purchase->class_key ) && in_array( $this->purchase->class_key, array( 'modResource', 'modDocument' ) ) ){
            
            $url = $this->modx->makeUrl( $this->purchase->id, $this->purchase->context_key, '', 'full' );
            
        }else{
            
            $parent_url = $this->modx->makeUrl( $this->purchase->resource_id, '', '', 'full' );
            if( substr( $parent_url, -1, 1 ) != '/' ){
                $parent_url .= '/';
            }
            $url = $parent_url;
            if( !empty( $this->purchase->alias ) ){
                $url .= $this->purchase->alias;
            }
            $contentType = $this->modx->getObject('modContentType',array('name'=>'HTML'));
            $html_ext = $contentType->getExtension();
            if( $html_ext ){
                $url .= $html_ext;
            }
        }
        
        return $url;
    }
    
    
    /**
     * Добавление к заказу доп. параметров товара с ценами
     *
     * @param array $purchaseArray
     * @return array
     */
    public function getProductOptions( $purchaseArray = array() ){
        
        if( empty( $purchaseArray ) ) $purchaseArray = $_POST;
        $is_resource = !empty( $this->purchase->class_key ) && in_array( $this->purchase->class_key, array( 'modResource', 'modDocument' ) );
        $output = array();
        
        //обрабатываем доп.параметры товара
        foreach($purchaseArray as $key => $value){
            
            if( !preg_match("/__\d/",$key) || empty($value) ) continue;
            
            if(
                ( empty( $this->config['additParamSource'] ) && !preg_match( "/__{$this->purchase->id}/", $key ) ) ||
                ( !empty( $this->config['additParamSource'] ) && !preg_match( "/__{$this->purchase->id}/", $key ) && !preg_match( "/__".$this->config['additParamSource']."/", $key ) )
            ) continue;
            
            list( $a_fieldname, $a_id, $a_string ) = substr_count($key,'__')==2 ? explode('__',$key) : explode('__',$key.'__');
            
            //если параметр не нужно брать из TV
            if(!empty($a_string) && $a_string=='add'){
                
                $output[ $a_fieldname ] = array( $value, 0 );
                continue;
                
            }
            //если цену доп.параметра нужно брать из TV или из поля таблицы товара
            else{
                
                //разбиваем строку из значения поля параметра из $_POST
                list( $afi, $afp, $afn ) = substr_count($value,'__')==2 ? explode('__',$value) : explode('__',$value.'__');
                if(empty($afn)) $afn = '';
                
                list( $a_full_value, $a_price, $a_value, $old_price ) = $this->getAdditDataField( $a_fieldname, $afi, $afn );
                
            }
            
            if( strlen( (string) $a_price ) == 0 ) continue;
            
            //проверяем нужно ли умножать
            if(strpos($a_price,'*')!==false){
                $multiplication = true;
                $a_price = str_replace('*','',$a_price);
            }else{
                $multiplication = false;
            }
            
            $a_price = self::cleanNumber( $a_price, 'float' );
            
            if( !empty( $output[ $a_fieldname ] ) ){
                $a_fieldname .= '_' . $a_string;
            }
            
            $output[ $a_fieldname ] = array( $a_full_value, $a_price );
            if( isset( $old_price ) ){
                $output[ $a_fieldname ][3] = $old_price;
            }
            if( !empty( $a_value ) ) array_push( $output[ $a_fieldname ], $a_value );
            
            $output[ $a_fieldname ]['multiplication'] = $multiplication;
            
        }
        
        return $output;
        
    }
    
    
    /**
     * getAdditDataField
     *
     */
    public function getAdditDataField( $a_fieldname, $index, $default_name = '' ){
        
        $index = intval($index);
        $is_resource = !empty( $this->purchase->class_key ) && in_array( $this->purchase->class_key, array( 'modResource', 'modDocument' ) );

        //modResource
        if( $is_resource ){
            
            $field_name = '';
            $a_tv = $this->modx->getObject( 'modTemplateVar', array( 'name' => $a_fieldname ) );
            if( $a_tv ){
                $a_val_res = $this->config['processParams']
                    ? $a_tv->renderOutput( $this->purchase->id )
                    : $a_tv->getValue( $this->purchase->id );
                if( !$a_val_res && $a_tv->get('default_text') ){
                    $a_val_res = $a_tv->get('default_text');
                }
                $field_name = $a_tv->caption;
            }else{
                $a_val_res = '';
            }
            
        }
        //migxdb
        else{
            
            $a_val_res = $this->purchase->get( $a_fieldname );
            if( !$a_val_res ) $a_val_res = '';
            
            $migx_config = $this->getMigxConfig();
            $field_name = isset( $migx_config[ $a_fieldname ] ) && isset( $migx_config[ $a_fieldname ]['caption'] )
                ? $migx_config[ $a_fieldname ]['caption']
                : '';
            
        }
        
        //Если задан источник доп.параметров
        if( !$a_val_res && !empty( $this->config['additParamSource'] ) ){
            
            $a_tv = $this->modx->getObject( 'modTemplateVar', array( 'name' => $a_fieldname ) );
            $a_val_res = $this->config['processParams'] ? $a_tv->renderOutput( $this->config['additParamSource'] ) : $a_tv->getValue( $this->config['additParamSource'] );
            
        }
        
        //разбиваем строку из значения TV
        $a_val_arr = explode( '||', $a_val_res );
        
        if( isset( $a_val_arr[$index] ) ){
            //разбиваем значение параметра
            $output = empty( $default_name ) ? explode( '==', $a_val_arr[$index] ) : array( $default_name, 0 );
        }else{
            $output = array( $default_name, '' );
        }
        
        //OnSHKgetProductAdditParamPrice
        $new_price = $this->getFromEvent(
            'OnSHKgetProductAdditParamPrice',
            array( 'price' => $output[1], 'id' => $this->purchase->id, 'purchaseArray' => $this->purchase->toArray() ),
            'float'
        );
        
        if( $new_price !== false ){
            $output[3] = $output[1];
            $output[1] = $new_price;
        }
        
        if( $field_name ){
            $output[2] = $output[0];
            $output[0] = $field_name . ' ' . $output[0];
        }
        
        return $output;
        
    }
    
    
    /**
     * checkIntersect
     *
     */
    public function checkIntersect( $product ){
        
        $output = false;
        
        for( $i=0; $i < count($this->data); $i++ ){
            
            if( $this->data[$i]['id'] == $product['id'] && $this->data[$i]['className'] == $product['className'] ){
                
                if( $this->data[$i]['price'] == $product['price'] ){
                    
                    if( serialize($this->data[$i]['options']) == serialize($product['options']) ){
                        $output = $i;
                        break;
                    }
                    
                }
                
            }
            
        }
        
        return $output;
        
    }
    
    
    /**
     * getFromEvent
     *
     * @param string $event_name
     * @param array $data
     * @param string $outtype
     * @return string
     */
    public function getFromEvent( $event_name, $data = array(), $outtype = 'string' ){
        
        if( $outtype == 'link' ){
            
            $key = current( array_keys( $data ) );
            $object = new stdClass();
            $object->content = current( array_values( $data ) );
            $this->invokeEventCombine( $event_name, array( $key => &$object ) );
            
            $output = $object->content;
            
        }else{
            
            $evtOut = $this->invokeEventCombine( $event_name, $data );
            
            if( empty( $evtOut ) || $evtOut[0] === false ) { return false; }
            
            if( $outtype == 'string' ){
                $output = implode( '', $evtOut );
            }else{
                $output = end( $evtOut );
                $output = str_replace( array(' ',','), array('','.'), $output );
                if( $outtype = 'float' && !is_float( $output ) ){
                    $output = floatval( $output );
                }else{
                    $output = intval( $output );
                }
            }
            
        }
        
        return $output;
    }

    /**
     * Invokes a specified Event with combine plugins results by returnedValues
     * @param $eventName
     * @param array $params
     * @return array|bool
     */
    public function invokeEventCombine($eventName, array $params= array ()) {
        if (!$eventName)
            return false;
        if ($this->modx->eventMap === null && $this->modx->context instanceof modContext) {
            //$this->modx->_initEventMap($this->modx->context->get('key'));
        }
        if (!isset($this->modx->eventMap[$eventName])) {
            $this->modx->log(modX::LOG_LEVEL_DEBUG,'System event '.$eventName.' was executed but does not exist.');
            return false;
        }
        $this->modx->event->returnedValues = array();
        $results = array ();
        if (count($this->modx->eventMap[$eventName])) {
            $this->modx->event = new modSystemEvent();
            foreach ($this->modx->eventMap[$eventName] as $pluginId => $pluginPropset) {
                $plugin = null;
                $this->modx->Event = & $this->modx->event;

                $this->modx->event->name = '';
                $this->modx->event->_output = '';
                $this->modx->event->activated = false;

                $this->modx->event->name = $eventName;
                if (isset ($this->modx->pluginCache[$pluginId])) {
                    $plugin = $this->modx->newObject('modPlugin');
                    $plugin->fromArray($this->modx->pluginCache[$pluginId], '', true, true);
                    $plugin->_processed = false;
                    if ($plugin->get('disabled')) {
                        $plugin = null;
                    }
                } else {
                    $plugin= $this->modx->getObject('modPlugin', array ('id' => intval($pluginId), 'disabled' => '0'), true);
                }
                if ($plugin && !$plugin->get('disabled')) {
                    $this->modx->event->plugin =& $plugin;
                    $this->modx->event->activated = true;

                    /* merge in plugin properties */
                    $eventParams = array_merge($plugin->getProperties(),$params);

                    $msg = $plugin->process($eventParams);
                    $results[] = $this->modx->event->returnedValues[] = $this->modx->event->_output;
                    if ($msg && is_string($msg)) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[' . $this->modx->event->name . ']' . $msg);
                    } elseif ($msg === false) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, '[' . $this->modx->event->name . '] Plugin ' . $plugin->name . ' failed!');
                    }
                    $this->modx->event->plugin = null;
                    if (!$this->modx->event->isPropagatable()) {
                        break;
                    }
                }
            }
        }
        $this->modx->event->returnedValues = null;
        return $results;
    }

    /**
     * Combine plugins output for change price events
     * @param $evtData
     * @param $evtOut
     * @return int
     */
    public function pluginsChangePriceCombine( $evtData, $evtOut )
    {
        $output = 0;



        return $output;
    }
    
    
    /**
     * getCatalogClass
     *
     */
    public function getCatalogClass(){
        
        $packageData = array(
            'className' => '',
            'packageName' => ''
        );
        
        $className_arr = explode(',',str_replace(' ','',$this->config['className'] ) );
        $packageName_arr = explode(',',str_replace(' ','',$this->config['packageName'] ) );
        
        //Если передан номер каталога
        if( isset($_POST['shk-catalog']) && is_numeric($_POST['shk-catalog']) ){
            
            $catalogClassIdx = intval($_POST['shk-catalog']);
            $packageData['className'] = isset( $className_arr[$catalogClassIdx-1] ) ? $className_arr[$catalogClassIdx-1] : current( $className_arr );
            $packageData['packageName'] = isset( $packageName_arr[$catalogClassIdx-1] ) ? $packageName_arr[$catalogClassIdx-1] : current( $packageName_arr );
            
        }else{
            
            $packageData['className'] = current( $className_arr );
            $packageData['packageName'] = current( $packageName_arr );
            
        }
        
        $this->addPackage( $packageData['packageName'] );
        if( empty( $this->config['migx_configName'] ) ) $this->config['migx_configName'] = $packageData['packageName'];
        
        return $packageData;
        
    }
    
    
    /**
     * addPackage
     *
     */
    public function addPackage( $packageName ){
        
        if( empty( $packageName ) || $packageName == 'modResource' ){ return; }
        
        if( !in_array( $packageName, array_keys($this->modx->packages) ) ){
            $modelpath = $this->modx->getOption('core_path') . 'components/' . $packageName . '/model/';
            $this->modx->addPackage( $packageName, $modelpath );
        }
        
    }
    
    
    /**
     * Проверяет введенное число кол-ва товаров и приводит к нормальному виду
     * 
     * @param string $count
     * @return int|float
     */
    public function cleanCount($count){
        $output = str_replace(array(',',' '),array('.',''),$count);
        if(!is_numeric($output) || empty($output)) return 1;
        $output = $this->config['allowFloatCount'] ? floatval($output) : intval($output);
        return abs($output);
    }
    
    
    /**
     * Приводит строку с числом к правильному типу
     * 
     * @param string $number
     * @param string $type
     * @return int|float
     */
    public static function cleanNumber( $number, $type = 'integer' ){
        
        $output = str_replace( array(',',' '), array('.',''), $number );
        $output = $type == 'float' ? floatval($output) : intval($output);
        
        return $output;
    }
    
    /**
     * getTotal
     *
     */
    public function getTotal(){
        
        self::$price_total = 0;
        self::$items_total = 0;
        self::$items_unique_total = 0;
        
        if( !empty( $this->data ) ){
        
            foreach( $this->data as $parchase ){
                
                self::$price_total += $parchase['price'] * $parchase['count'];
                self::$items_total += $parchase['count'];
                
                if( !empty( $parchase['options'] ) ){
                    
                    //доп параметры товара
                    foreach( $parchase['options'] as $opts ){
                        
                        if( !empty( $opts[1] ) ){
                            self::$price_total += $opts[1] * $parchase['count'];
                        }
                        
                    }
                    
                }
                
            }
            
            self::$items_unique_total = count( $this->data );
            
        }

        $delvery_price = !empty( $this->delivery ) && !empty( $this->delivery['price'] )
            ? $this->delivery['price']
            : 0;
        
        //OnSHKcalcTotalPrice
        $new_price = $this->getFromEvent( 'OnSHKcalcTotalPrice', array(
            'price_total' => self::$price_total,
            'delvery_price' => $delvery_price
        ), 'float' );
        if( $new_price !== false ){
            self::$price_total = $new_price;
        }

        //Добавляем цену доставки
        self::$price_total += $delvery_price;

        return self::$price_total;
        
    }
    
    
    /**
     * Все поля товаров
     *
     */
    public function getProductsData( $only_from_bd = false ){
        
        $output = array();
        $ids_arr = array();
        
        if( empty( $this->data ) ){ return $output; }
        
        //Разделяем товары по className
        foreach( $this->data as $purchase ){
            
            if( empty( $purchase['className'] ) ){
                
                $purchase['className'] = 'modResource';
                $purchase['packageName'] = 'modResource';
                
            }/*else if( $purchase['className'] == 'none' ){
                
                array_push( $output, $purchase );
                continue;
                
            }*/
            
            if( !isset( $ids_arr[$purchase['className']] ) ){
                $ids_arr[$purchase['className']] = array();
                $this->addPackage( $purchase['packageName'] );
            }
            
            if( !in_array( $purchase['id'], $ids_arr[$purchase['className']] ) ){
                array_push( $ids_arr[$purchase['className']], $purchase['id'] );
            }
            
        }
        
        $data = array();
        
        //Вытаскиваем данные товаров из БД
        foreach( $ids_arr as $catalogClass => $ids ){
            
            $data[ $catalogClass ] = array();
            $fields = array();
            $tv_names = array();
            
            if( !empty( $this->config[ 'savePurchasesFields' ] ) ){
                
                $savePurchasesFields = explode( ',', $this->config[ 'savePurchasesFields' ] );
                $savePurchasesFields = array_map( 'trim', $savePurchasesFields );
                if( !in_array( 'id', $savePurchasesFields ) ) array_push( $savePurchasesFields, 'id' );
                $fields = array_keys( $this->modx->getFields( $catalogClass ) );
                
                $tv_names = array_diff( $savePurchasesFields, $fields );
                $fields = array_intersect( $fields, $savePurchasesFields );
                
                if( isset( $tv_names[$this->config['fieldPrice']] ) ){ unset( $tv_names[$this->config['fieldPrice']] ); }
                if( isset( $fields[$this->config['fieldPrice']] ) ){ unset( $fields[$this->config['fieldPrice']] ); }
                
                if( count( $fields ) == 1 && $fields[0] == 'id' ){
                    $fields = false;
                }
                
            }
            
            //get fields
            if( $fields !== false ){
                
                $query = $this->modx->newQuery( $catalogClass );
                if( !empty( $fields ) ){
                    $query->select( implode( ',', $fields ) );
                }
                $query->where( array( 'id:IN' => $ids ) );
                if( !$this->config['getUnpublished'] ){
                    $query->where( array( 'published' => 1 ) );
                }
                
                if( $this->modx->getCount( $catalogClass, $query ) > 0 ){
                    
                    $resources = $this->modx->getIterator( $catalogClass, $query );
                    
                    foreach( $resources as $idx => $product ){
                        
                        $p_data = $product->toArray( '', false, true );
                        
                        $data[$catalogClass][$p_data['id']] = $p_data;
                        
                    }
                    
                }
                
            }
            
            //get TVs
            if( !empty( $tv_names ) ){

                //Get default values
                $default_values = array();
                $query = $this->modx->newQuery( 'modTemplateVar' );
                $query->select( $this->modx->getSelectColumns( 'modTemplateVar', 'modTemplateVar', '', array( 'id', 'name', 'caption', 'default_text' ) ) );
                $query->where( array( 'modTemplateVar.name:IN' => $tv_names ) );

                $tvs = $this->modx->getIterator( 'modTemplateVar', $query );

                foreach ( $tvs as $key => $tv ) {
                    $default_values[] = $tv->toArray();
                }

                $query = $this->modx->newQuery( 'modTemplateVarResource' );
                $query->leftJoin( 'modTemplateVar', 'modTemplateVar', array( "modTemplateVar.id = tmplvarid" ) );
                $query->select( $this->modx->getSelectColumns( 'modTemplateVarResource', 'modTemplateVarResource', '', array( 'id', 'tmplvarid', 'contentid', 'value' ) ) );
                $query->select( $this->modx->getSelectColumns( 'modTemplateVar', 'modTemplateVar', '', array( 'name', 'default_text' ) ) );
                $query->where( array( 'contentid:IN' => $ids ) );
                $query->where( array( 'modTemplateVar.name:IN' => $tv_names ) );
                
                $tvars = $this->modx->getIterator( 'modTemplateVarResource', $query );
                
                if( $tvars ){
                    foreach ( $tvars as $key => $tv ) {
                        
                        $tv_data = $tv->toArray();
                        
                        if( !isset( $data[$catalogClass][$tv_data['contentid']] ) ){
                            $data[$catalogClass][$tv_data['contentid']] = array();
                        }
                        
                        $data[$catalogClass][$tv_data['contentid']][$tv_data['name']] = $tv_data['value']
                            ? $tv_data['value']
                            : $tv_data['default_text'];
                        
                    }
                }

                //set default values
                if( count( $data[$catalogClass] ) != count( $ids ) ){

                    foreach ( $ids as $id ){
                        if( !isset(  $data[$catalogClass][ $id ] ) ){
                            foreach ( $default_values as $default ){
                                $data[$catalogClass][$id][$default['name']] = $default['default_text'];
                            }
                        }
                    }

                }
            }
            
        }
        
        //Формируем полный массив данных
        foreach( $this->data as $key => $purchase ){
            
            $fields_data = isset( $data[$purchase['className']][$purchase['id']] ) ? $data[$purchase['className']][$purchase['id']] : array();
            
            if( !$only_from_bd ){
                
                $output[$key] = array_merge( $purchase, $fields_data );
                
                $p_options = array();
                if( !empty( $purchase['options'] ) ){
                    $p_options = $this->getPurchasesOptionsData( $purchase['options'] );
                }
                $output[$key] = array_merge( $output[$key], $p_options );
                
            }else{
                $output[$key] = $fields_data;
            }
            
            unset( $output[$key]['options'] );
            
        }
        
        return $output;
        
    }
    
    
    /**
     * getPurchasesOptionsData
     *
     */
    public function getPurchasesOptionsData( $options ){
        
        $output = array();
        $data_arr = array();
        
        if( !empty( $options ) ){
            
            foreach( $options as $name => $opt ){
                
                $output[ 'shk_' . $name ] = !empty( $opt[2] ) ? $opt[2] : $opt[0];
                $output[ 'shk_' . $name . '_price' ] = $opt[1];
                
                $full_name = $opt[0];
                if( !empty( $opt[1] ) ){
                    $full_name .= ' (' . ( $this->config['excepDigitGroup']
                            ? $this->numberFormat( $opt[1] )
                            : $opt[1] ) . ')';
                }
                array_push( $data_arr, $full_name );
                
            }
            
        }
        
        $output['addit_data'] = implode( ', ', $data_arr );
        
        return $output;
        
    }
    
    
    /**
     * Возвращает массив или строку ID товаров в корзине
     * 
     * @param string $outtype
     * @return mixed
     */
    public function getProdIds( $outtype = 'array' ){
        
        if( empty( $this->data ) ) return $outtype=='array' ? array() : '';
        
        $out_arr = array();
        foreach($this->data as $prod){
            array_push( $out_arr, intval( $prod['id'] ) );
        }
        
        return $outtype=='array'
            ? array_unique( $out_arr )
            : implode( ',',array_unique( $out_arr ) );
    }
    
    
    /**
     * Формирует полный HTML код с данными заказа
     *
     * @param integer $order_id
     * @return string
     */
    public function getOrderData( $order_id ){
        
        $output = '';
        
        //Get order
        $order_data = array();
        $response = $this->modx->runProcessor('getorder',
            array(
                'order_id' => $order_id,
                'date_format' => 'H:i:s d/m/Y'
            ),
            array('processors_path' => $this->modx->getOption('core_path') . 'components/shopkeeper3/processors/mgr/')
        );
        if( !$response->isError() && $result = $response->getResponse()){
            $order_data = $result['object'];
        }
        
        //Render output
        if( !empty( $order_data ) ){
            
            $mailContactsRowTpl = $this->modx->getOption( 'shk3.mail_contacts_row_tpl', null, 'mailContactsRow' );
            $orderDataRowTpl = $this->modx->getOption( 'shk3.mail_order_data_row_tpl', null, 'orderDataRow' );
            $orderOuterTpl = $this->modx->getOption( 'shk3.mail_order_data_tpl', null, 'orderDataOuter' );
            
            $response = $this->modx->runProcessor('renderorderdata',
                array(
                    'order_data' => $order_data,
                    'orderOuterTpl' => $orderOuterTpl,
                    'orderContactsTpl' => $mailContactsRowTpl,
                    'orderPurchaseRowTpl' => $orderDataRowTpl
                ),
                array('processors_path' => $this->modx->getOption('core_path') . 'components/shopkeeper3/processors/web/')
            );
            
            if( !$response->isError() && $result = $response->getResponse()){
                
                $output .= $result['object'];
                
            }
            
        }

        $output = $this->stripModxTags( $output );
        
        return $output;
    }
    
    
    /**
     * getMigxConfig
     *
     */
    public function getMigxConfig(){
        
        $packageData = $this->getCatalogClass();
        $output = array();
        
        $sql = "
        SELECT `formtabs`
        FROM `".$this->modx->config['table_prefix']."migx_configs`
        WHERE `name` = '".$this->config['migx_configName']."'
        ";
        $stmt = $this->modx->prepare($sql);
        if ($stmt && $stmt->execute()) {
            $migx_config_formtabs = $stmt->fetchColumn();
            if($migx_config_formtabs){
                $migx_config_formtabs_arr = json_decode($migx_config_formtabs,true);
                foreach($migx_config_formtabs_arr as $formtabs){
                    $temp_fields = !empty($formtabs['fields']) ? $formtabs['fields'] : array();
                    foreach($temp_fields as $temp_field){
                        $output[ $temp_field['field'] ] = $temp_field;
                    }
                }
                
            }
        }
        $stmt->closeCursor();
        
        return $output;
        
    }
    
    
    /**
     * Конфигурация компонента
     *
     * @param array $array
     * @return array
     */
    public static function getConfig( $settings ){
        
        $output = array();
        global $modx;
        if( !is_array( $settings ) ) { $settings = array( $settings ); }
        
        $modelpath = $modx->getOption('core_path') . 'components/shopkeeper3/model/';
        $modx->addPackage( 'shopkeeper3', $modelpath );
        
        $query = $modx->newQuery( 'shk_config' );
        if( !empty( $settings ) ){
            $query->where( array( "setting:IN" => $settings ) );
        }
        $config = $modx->getIterator( 'shk_config', $query );
        
        if( !empty( $config ) ){
            
            foreach( $config as $key => $conf ){
                
                if( $conf->xtype == 'array' ){
                    $config_value = json_decode( $conf->value, true );
                }else{
                    $config_value = $conf->value;
                }
                
                $output[ $conf->setting ] = $config_value;
                
            }
            
        }
        
        return $output;
        
    }
    
    
    /**
     * Обновление данных доставки
     *
     * @param string $delivery_name
     * @param boolean $update_price
     */
    public function updateDelivery( $delivery_name = false, $update_price = false ){
        
        //Обновляем доставку
        if( $delivery_name !== false ){
            
            $this->delivery = array();
            
            $config = self::getConfig( array( 'delivery' ) );
            if( !empty( $config[ 'delivery' ] ) ){
                
                foreach( $config[ 'delivery' ] as $opt ){
                    
                    if( $opt['label'] == $delivery_name ){
                        
                        $this->delivery = array(
                            'label' => $opt['label'],
                            'price' => self::cleanNumber( $opt['price'], 'float' ),
                            'free_start' => self::cleanNumber( $opt['free_start'], 'float' )
                        );
                        
                        $this->delivery['old_price'] = $this->delivery['price'];
                        $this->delivery['old_free_start'] = $this->delivery['free_start'];
                        
                        break;
                    }
                    
                }
                
                //OnSHKgetDeliveryPrice
                $new_price = $this->getFromEvent(
                    'OnSHKgetDeliveryPrice',
                    array(
                        'price' => !empty( $this->delivery['price'] ) ? $this->delivery['price'] : 0
                    ),
                    'float'
                );
                
                if( $new_price !== false ){
                    $this->delivery['price'] = $new_price;
                    $this->delivery['old_price'] = $new_price;
                }
                
                if( !empty( $this->delivery['free_start'] ) ){
                    $new_price = $this->getFromEvent(
                        'OnSHKgetDeliveryPrice',
                        array( 'price' => $this->delivery['free_start'] ),
                        'float'
                    );
                    if( $new_price !== false ){
                        $this->delivery['free_start'] = $new_price;
                        $this->delivery['old_free_start'] = $new_price;
                    }
                }
                
            }
            
        }
        
        //Проверяем общую цену заказа и бесплатную доставку
        if( !empty( $this->delivery ) && !empty( $this->delivery['free_start'] ) ){
            
            $this->getTotal();
            
            $delivery_price = !empty( $this->delivery['old_price'] ) ? $this->delivery['old_price'] : $this->delivery['price'];
            $delivery_free_price = !empty( $this->delivery['old_free_start'] ) ? $this->delivery['old_free_start'] : $this->delivery['free_start'];
            
            if( ( self::$price_total - $delivery_price ) >= $delivery_free_price ){
                
                $this->delivery['old_price'] = $delivery_price;
                $this->delivery['price'] = 0;
                
            }else{
                
                $this->delivery['price'] = $delivery_price;
                
            }
            
        }
        
        if( $update_price ){
            $this->getTotal();
        }
        
    }


    /**
     * print_arr
     * @param $array
     * @return string
     */
    public static function print_arr( $array ){
        
        return is_array( $array ) ? '<pre>' . print_r($array,true) . '</pre>' : (string) $array;
        
    }
    
    
    /**
     * applyRequest
     *
     */
    public function applyRequest(){
        
        if( isset($_POST['shk-id']) ){
            
            $this->savePurchaseData();
            
            if( !$this->isAjax() ){
                $this->modx->sendRedirect( $this->backUrl(), 0 );
                exit;
            }
            
        }elseif(isset($_REQUEST['shk_action'])){
            
            $action = htmlspecialchars( $_REQUEST['shk_action'] );
            
            if( method_exists( $this, 'request_' . $action ) ){
                
                call_user_func( array( $this, 'request_' . $action ) );
                
            }
            
        }else if( isset( $_POST['shk_delivery'] ) ){
            
            $this->request_update_delivery();
            
        }
        
    }
    
    
    /**
     * backUrl
     *
     */
    public function backUrl(){
        
        $back_url = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->modx->makeURL($this->modx->resource->get('id'),'','','abs');
        
        return $back_url;
        
    }
    
    /**
     * removeById
     *
     */
    public function removeById( $p_id ){
        
        $item_index = -1;
        foreach( $this->data as $k => $purchase ){
            
            if( $purchase['id'] == $p_id ){
                $item_index = $k;
                break;
            }
            
        }
        
        if( $item_index > -1 ){
            
            array_splice( $this->data, $item_index, 1 );
            
            //OnSHKAfterRemoveProduct
            $this->modx->invokeEvent( 'OnSHKAfterRemoveProduct', array( 'index' => $item_index, 'id' => $p_id ) );
            
            $this->removeById( $p_id );
            
        }
        
        return true;
    }
    
    
    /**
     * Сохраняет данные последнего заказа в сессию
     *
     * @param array $order
     * @return boolean
     */
    public function setOrderDataSession( $order = array() ){
        
        $_SESSION['shk_lastOrder'] = array(
            'id' => $order['id'],
            'price' => $order['price'],
            'currency' => $order['currency'],
            'date' => date('d.m.Y',strtotime($order['date'])),
            'full_date' => $order['date'],
            'email' => $order['email'],
            'phone' => $order['phone'],
            'delivery' => $order['delivery'],
            'payment' => $order['payment'],
            'status' => $order['status'],
            'userid' => !empty($order['userid']) ? $order['userid'] : 0
        );
        
        return true;
    }
    
    /**
     * action_del
     *
     */
    public function request_remove(){
        
        if( isset($_REQUEST['n']) && is_numeric($_REQUEST['n']) ){
            
            $item_index = intval( $_REQUEST['n'] );
            $group = array();
            
            if( $this->config['groupBy'] ){
                
                $purchasesData = $this->getProductsData();
                $purchasesData = $this->groupBy( $purchasesData, $this->config['groupBy'] );
                if( isset( $purchasesData[ $item_index ] ) ){
                    foreach( $purchasesData[ $item_index ]['id'] as $item_id ){
                        $this->removeById( $item_id );
                    }
                }
                
            }
            else{
                
                if( isset( $this->data[ $item_index ] ) ){
                    
                    $p_id = $this->data[ $item_index ][ 'id' ];
                    array_splice( $this->data, $item_index, 1 );
                    
                    //OnSHKAfterRemoveProduct
                    $this->modx->invokeEvent( 'OnSHKAfterRemoveProduct', array( 'index' => $item_index, 'id' => $p_id ) );
                    
                }
                
            }
            
        }else if( isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) ){
            
            $item_id = intval( $_REQUEST['id'] );
            $this->removeById( $item_id );
            
        }
        
        $this->updateDelivery();
        
        if( !$this->isAjax() ){
            $this->modx->sendRedirect( $this->backUrl(), 0 );
            exit;
        }
        
    }
    
    /**
     * request_empty
     *
     */
    public function request_empty( $redirectBack = true ){
        
        $this->data = array();
        $this->delivery = array();
        
        //OnSHKAfterClearCart
        $this->modx->invokeEvent( 'OnSHKAfterClearCart' );
        
        if( $redirectBack && !$this->isAjax() ){
            $this->modx->sendRedirect( $this->backUrl(), 0 );
            exit;
        }
        
    }
    
    
    /**
     * request_recount
     *
     */
    public function request_recount(){
        
        for( $i = 0; $i < count( $this->data ); $i++ ){
            
            if( isset( $_POST['count'][$i] ) && is_numeric( $_POST['count'][$i] ) && $_POST['count'][$i] >= 1 ){
                $this->data[$i]['count'] = $this->cleanCount( $_POST['count'][$i] );
            }
            
        }
        
        $this->updateDelivery();
        
        if( !$this->isAjax() ){
            $this->modx->sendRedirect( $this->backUrl(), 0 );
            exit;
        }
        
    }
    
    /**
     * request_add_from_array
     *
     */
    public function request_add_from_array(){
        
        $ids_arr = !empty($_POST['ids']) ? explode(',',$_POST['ids']) : array();
        $count_arr = !empty($_POST['count']) ? explode(',',$_POST['count']) : array();
        
        if( is_array($ids_arr) && count($ids_arr) > 0 ){
            
            $packageData = $this->getCatalogClass();
            
            foreach( $ids_arr as $key => $id ){
                
                $count = !empty($count_arr[$key]) && is_numeric($count_arr[$key]) ? abs($count_arr[$key]) : 1;
                
                $product = $this->savePurchaseData( array( 'shk-id' => $id, 'count' => $count ) );
                
            }
            
        }
        
        if( !$this->isAjax() ){
            $this->modx->sendRedirect( $this->backUrl(), 0 );
            exit;
        }
        
    }
    
    
    /**
     * request_update_delivery
     *
     */
    public function request_update_delivery(){
        
        $delivery_name = isset( $_POST['shk_delivery'] ) && !is_array( $_POST['shk_delivery'] ) ? trim( $_POST['shk_delivery'] ) : '';
        
        $this->updateDelivery( $delivery_name );
        
    }
    
    
    /**
     * getVersion
     *
     */
    public function getVersion(){
        
        return $this->version;
        
    }

}
