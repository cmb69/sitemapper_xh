<?php

/**
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

if (!defined('CMSIMPLE_XH_VERSION')
    || strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') !== 0
    || version_compare(CMSIMPLE_XH_VERSION, 'CMSimple_XH 1.6', 'lt')
) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/plain; charset=UTF-8');
    die(<<<EOT
Sitemapper_XH detected an unsupported CMSimple_XH version.
Uninstall Sitemapper_XH or upgrade to a supported CMSimple_XH version!
EOT
    );
}

define('SITEMAPPER_VERSION', '@SITEMAPPER_VERSION@');

require_once $pth['folder']['plugin_classes'] . 'model.php';
require_once $pth['folder']['plugin_classes'] . 'controller.php';

$_Sitemapper = new Sitemapper\Controller();
$_Sitemapper->init();
