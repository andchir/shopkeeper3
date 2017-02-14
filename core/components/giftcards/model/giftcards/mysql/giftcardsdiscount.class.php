<?php
/**
 * @package giftcards
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/giftcardsdiscount.class.php');
class giftCardsDiscount_mysql extends giftCardsDiscount {}
?>