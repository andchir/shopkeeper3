<?php
/**
 * @package giftcards
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/giftcardsitem.class.php');
class giftCardsItem_mysql extends giftCardsItem {}
?>