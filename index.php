<?php

/**
 * Front-end of Sitemapper_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Sitemapper
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */

/*
 * Prevent direct access and usage from unsupported CMSimple_XH versions.
 */
if (!defined('CMSIMPLE_XH_VERSION')
    || strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') !== 0
    || version_compare(CMSIMPLE_XH_VERSION, 'CMSimple_XH 1.5.4', 'lt')
) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/plain; charset=UTF-8');
    die(<<<EOT
Sitemapper_XH detected an unsupported CMSimple_XH version.
Uninstall Sitemapper_XH or upgrade to a supported CMSimple_XH version!
EOT
    );
}

/**
 * The version string.
 */
define('SITEMAPPER_VERSION', '@SITEMAPPER_VERSION@');

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
