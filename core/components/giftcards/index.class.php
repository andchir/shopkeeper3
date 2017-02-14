<?php
/**
 * giftCards
 *
 * Copyright 2010 by Shaun McCormick <shaun+modextra@modx.com>
 *
 * giftCards is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * giftCards is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * giftCards; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package giftcards
 */
require_once dirname(__FILE__) . '/model/giftcards/giftcards.class.php';

/**
 * @package giftcards
 */
abstract class giftCardsBaseManagerController extends modExtraManagerController {
    /** @var giftCards $giftcards */
    public $giftcards;
    public function initialize() {
        $this->giftcards = new giftCards($this->modx);

        $this->addCss($this->giftcards->config['cssUrl'].'mgr.css');
        $this->addJavascript($this->giftcards->config['jsUrl'].'mgr/giftcards.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            giftCards.config = '.$this->modx->toJSON($this->giftcards->config).';
            giftCards.config.connector_url = "'.$this->giftcards->config['connectorUrl'].'";
        });
        </script>');
        return parent::initialize();
    }
    public function getLanguageTopics() {
        return array('giftcards:default');
    }
    public function checkPermissions() { return true;}
}

class IndexManagerController extends giftCardsBaseManagerController {
    public static function getDefaultController() { return 'home'; }
}
