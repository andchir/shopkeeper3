<?php
/**
 * giftCards
 *
 * Copyright 2010 by Shaun McCormick <shaun+giftcards@modx.com>
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
/**
 * giftCards Connector
 *
 * @package giftcards
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('giftcards.core_path',null,$modx->getOption('core_path').'components/giftcards/');
require_once $corePath.'model/giftcards/giftcards.class.php';
$modx->giftcards = new giftCards($modx);

$modx->lexicon->load('giftcards:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->giftcards->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));