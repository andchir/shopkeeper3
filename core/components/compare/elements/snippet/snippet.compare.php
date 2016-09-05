<?php
/*

compare 1.2
Сравнение товаров и Избранное

Andchir - http://modx-shopkeeper.ru/

*/

$default_toCompareTpl = '
    <p>
        Выбрано <span id="skolko_vibrano">[[+count_current]]</span> из [[+count_max]]
        / <a href="[[+href_compare]]" onclick="return shkCompare.toCompareLink();">сравнить</a>
        <span id="sravnenie_otmena" style="display:[[+display_cancel]];"> / <a href="[[+href_cancel]]">отменить</a></span>
    </p>
    <br clear="all" />
';

$action = isset($action) ? $action : 'to_compare';

require_once MODX_CORE_PATH . 'components/compare/compare.class.php';
$compare = new prodCompare( $modx, $scriptProperties );

//действия, переданные по $_GET
$cmpr_action = isset($_GET['cmpr_action']) && !is_array($_GET['cmpr_action']) ? $_GET['cmpr_action'] : '';
if($cmpr_action == 'del_product' && !in_array( $action, array('print_products', 'print_id_list') ) ) return;
switch($cmpr_action){
    //удаление одного товара из списка для сравнения
    case 'del_product':
        if(!empty($_GET['pid'])) $compare->deleteCompareProduct();
    break;
    //очистка списка товаров, выбранных для сравнения
    case 'empty':
        $compare->emptyCompare();
    break;
}

//действия для вывода в месте вызова сниппета
switch($action){
    //вывод строки со ссылками на страницу сравнения
    case 'to_compare':
        $output = $compare->toCompareContent();
    break;
    //вывод списка ID товаров, выбранных для сравнения
    case 'print_id_list':
        $output = $compare->printIDList();
    break;
    //вывод списка товаров, выбранных для сравнения
    case 'print_products':
        $output = $compare->printCompareProducts();
    break;
    //вывод ID категории товаров, выбранных для стравнения
    case 'print_parent_id':
        $output = isset($_COOKIE['shkCompareParent']) ? $_COOKIE['shkCompareParent'] : '';
    break;
    default:
        $output = '';
    break;
}

return $output;