<?php

/**
 * Front-end of Sitemapper_XH.
 *
 * @package	Sitemapper
 * @copyright	Copyright (c) 2011-2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 * @link	http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */


/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * The version string.
 */
define('SITEMAPPER_VERSION', '2beta2');


/**
 * The model and controller class.
 */
require_once $pth['folder']['plugin_classes'] . 'model.php';
require_once $pth['folder']['plugin_classes'] . 'controller.php';


/**
 * Handles sitemap requests.
 *
 * @global object  The sitemapper controller.
 * @return void
 */
function sitemapper()
{
    global $_Sitemapper;

    $_Sitemapper->dispatchAfterPluginLoading();
}


/*
 * Create and initialize the controller.
 */
$_Sitemapper = new Sitemapper_Controller();
$_Sitemapper->init();

?>
