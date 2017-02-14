<?php
/**
 * @package giftcards
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/giftcardsgroup.class.php');
class giftCardsGroup_mysql extends giftCardsGroup {}
?>