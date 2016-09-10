<?php

/**
 * Default snippet properties
 *
 * @package shopkeeper3
 * @subpackage build
 */
 
$properties = array(
    array(
        "name" => "lang",
        "desc" => "prop_shk.lang",
        "xtype" => "textfield",
        "options" => "",
        "value" => "ru",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "prodCont",
        "desc" => "prop_shk.prodcont",
        "xtype" => "textfield",
        "options" => "",
        "value" => "div.shk-item",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "tplPath",
        "desc" => "prop_shk.tplpath",
        "xtype" => "textfield",
        "options" => "",
        "value" => "core/components/shopkeeper3/elements/chunks/ru/",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "cartTpl",
        "desc" => "prop_shk.carttpl",
        "xtype" => "textfield",
        "options" => "",
        "value" => "@FILE shopCart.tpl",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "cartRowTpl",
        "desc" => "prop_shk.cartrowtpl",
        "xtype" => "textfield",
        "options" => "",
        "value" => "@FILE shopCartRow.tpl",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "orderDataTpl",
        "desc" => "prop_shk.orderdatatpl",
        "xtype" => "textfield",
        "options" => "",
        "value" => "@FILE orderData.tpl",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "flyToCart",
        "desc" => "prop_shk.flytocart",
        "xtype" => "list",
        "options" => array(
            array('text'=>'helper','value'=>'helper'),
            array('text'=>'image','value'=>'image'),
            array('text'=>'scrollimage','value'=>'scrollimage'),
            array('text'=>'nofly','value'=>'nofly')
        ),//'[{"text":"helper","value":"helper"},{"text":"image","value":"image"},{"text":"nofly","value":"nofly"}]',
        "value" => "helper",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "fieldPrice",
        "desc" => "prop_shk.pricetv",
        "xtype" => "textfield",
        "options" => "",
        "value" => "price",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "style",
        "desc" => "prop_shk.style",
        "xtype" => "textfield",
        "options" => "",
        "value" => "default",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "currency",
        "desc" => "prop_shk.currency",
        "xtype" => "textfield",
        "options" => "",
        "value" => "руб.",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "noCounter",
        "desc" => "prop_shk.nocounter",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => false,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "noLoader",
        "desc" => "prop_shk.noloader",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => false,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "orderFormPageId",
        "desc" => "prop_shk.orderformpage",
        "xtype" => "textfield",
        "options" => "",
        "value" => "1",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "goToOrderFormPage",
        "desc" => "prop_shk.gotoorderformpage",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => false,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "counterField",
        "desc" => "prop_shk.counterfield",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => false,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "counterFieldCart",
        "desc" => "prop_shk.counterFieldCart",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => true,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "excepDigitGroup",
        "desc" => "prop_shk.excepdigitgroup",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => true,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "changePrice",
        "desc" => "prop_shk.changeprice",
        "xtype" => "list",
        "options" => array(
            array('text'=>'yes','value'=>'1'),
            array('text'=>'no','value'=>'0'),
            array('text'=>'replace','value'=>'replace')
        ),
        "value" => "1",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "animCart",
        "desc" => "prop_shk.animcart",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => true,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "allowFloatCount",
        "desc" => "prop_shk.allowfloatcount",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => false,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "jsScript",
        "desc" => "prop_shk.nojavascript",
        "xtype" => "textfield",
        "options" => "",
        "value" => "1",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "noJQuery",
        "desc" => "prop_shk.nojquery",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => false,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "noConflict",
        "desc" => "prop_shk.noconflict",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => false,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "hideOn",
        "desc" => "prop_shk.hideon",
        "xtype" => "textfield",
        "options" => "",
        "value" => "",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "TVsaveList",
        "desc" => "prop_shk.TVsaveList",
        "xtype" => "textfield",
        "options" => "",
        "value" => "",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "fromParentList",
        "desc" => "prop_shk.fromParentList",
        "xtype" => "textfield",
        "options" => "",
        "value" => "",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "fromParentHeight",
        "desc" => "prop_shk.fromParentHeight",
        "xtype" => "textfield",
        "options" => "",
        "value" => "1",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "additParamSource",
        "desc" => "prop_shk.additParamSource",
        "xtype" => "textfield",
        "options" => "",
        "value" => "",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "className",
        "desc" => "prop_shk.className",
        "xtype" => "textfield",
        "options" => "",
        "value" => "modResource",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "packageName",
        "desc" => "prop_shk.packageName",
        "xtype" => "textfield",
        "options" => "",
        "value" => "",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "savePurchasesFields",
        "desc" => "prop_shk.savePurchasesFields",
        "xtype" => "textfield",
        "options" => "",
        "value" => "",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    )
    ,array(
        "name" => "debug",
        "desc" => "prop_shk.debug",
        "xtype" => "combo-boolean",
        "options" => "",
        "value" => false,
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),array(
        "name" => "processParams",
        "desc" => "prop_shk.processParams",
        "xtype" => "textfield",
        "options" => "",
        "value" => "0",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),
    array(
        "name" => "pluralWords",
        "desc" => "prop_shk.pluralWords",
        "xtype" => "textfield",
        "options" => "",
        "value" => "",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    ),
    array(
        "name" => "groupBy",
        "desc" => "prop_shk.groupBy",
        "xtype" => "textfield",
        "options" => "",
        "value" => "",
        "lexicon" => "shopkeeper3:properties",
        "overridden" => false,
        "desc_trans" => "",
        "menu" => null
    )
);
return $properties;
