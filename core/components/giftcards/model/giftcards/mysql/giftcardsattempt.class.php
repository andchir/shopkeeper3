<?php
/**
 * @package giftcards
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/giftcardsattempt.class.php');
class giftCardsAttempt_mysql extends giftCardsAttempt {}
?>