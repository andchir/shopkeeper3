<?php
/**
 * modExtra
 *
 * Copyright 2010 by Shaun McCormick <shaun+giftcards@modx.com>
 *
 * modExtra is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * modExtra is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * modExtra; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package giftcards
 */
/**
 * Loads the home page.
 *
 * @package giftcards
 * @subpackage controllers
 */
class giftCardsHomeManagerController extends giftCardsBaseManagerController {
    public function process(array $scriptProperties = array()) {

    }
    public function getPageTitle() { return $this->modx->lexicon('giftcards'); }
    public function loadCustomCssJs() {
        $this->addJavascript($this->giftcards->config['jsUrl'].'mgr/widgets/items.grid.js');
        $this->addJavascript($this->giftcards->config['jsUrl'].'mgr/widgets/home.panel.js');
        $this->addLastJavascript($this->giftcards->config['jsUrl'].'mgr/sections/home.js');
    }
    public function getTemplateFile() { return $this->giftcards->config['templatesPath'].'home.tpl'; }
}