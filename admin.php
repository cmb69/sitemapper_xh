<?php

/**
 * Back-end of Sitemapper_XH.
 *
 * @package	Sitemapper
 * @copyright	Copyright (c) 2011-2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 * @link	http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns the plugin information view.
 *
 * @global array  The paths of system files and folders.
 * @global array  The localization of the core.
 * @global array  The localization of the plugins.
 * @return string  The (X)HTML.
 */
function Sitemapper_info() // RELEASE-TODO
{
    global $pth, $tx, $plugin_tx;

    $ptx = $plugin_tx['sitemapper'];
    $labels = array(
	'syscheck' => $ptx['syscheck_title'],
	'about' => $ptx['about']
    );
    $phpVersion = '4.0.7';
    foreach (array('ok', 'warn', 'fail') as $state) {
        $images[$state] = $pth['folder']['plugins']
	    . "sitemapper/images/$state.png";
    }
    $checks = array();
    $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)] =
        version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
    foreach (array('date') as $ext) {
	$checks[sprintf($ptx['syscheck_extension'], $ext)] =
	    extension_loaded($ext) ? 'ok' : 'fail';
    }
    $checks[$ptx['syscheck_magic_quotes']] =
        !get_magic_quotes_runtime() ? 'ok' : 'fail';
    $checks[$ptx['syscheck_encoding']] =
        strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
    $folders = array();
    foreach (array('config/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'] . 'sitemapper/' . $folder;
    }
    foreach ($folders as $folder) {
	$checks[sprintf($ptx['syscheck_writable'], $folder)] =
            is_writable($folder) ? 'ok' : 'warn';
    }
    $icon = $pth['folder']['plugins'] . 'sitemapper/sitemapper.png';
    $version = SITEMAPPER_VERSION;
    $bag = compact('labels', 'images', 'checks', 'icon', 'version');
    return Sitemapper_render('info', $bag);
}


/**
 * Returns the list of all sitemaps.
 *
 * @return string
 */
function Sitemapper_admin()
{
    global $cf, $plugin_tx, $pth, $sl;

    $res = '<h1>' . $plugin_tx['sitemapper']['menu_main'] . '</h1>' . PHP_EOL
	. '<ul>' . PHP_EOL
	. '<li><a href="' . CMSIMPLE_ROOT
	. '?sitemapper_index" target="_blank">index</a></li>' . PHP_EOL;
    foreach (Sitemapper_installedSubsites() as $ss) {
	$res .= '<li><a href="' . CMSIMPLE_ROOT
	    . ($ss != $cf['language']['default'] ? $ss.'/' : '')
	    . '?sitemapper_sitemap" target="_blank">'
	    . $ss . '</a></li>' . PHP_EOL;
    }
    $res .= '</ul>' . PHP_EOL;
    return $res;
}


/**
 * The pagedata hook.
 */
$pd_router->add_interest('sitemapper_changefreq');
$pd_router->add_interest('sitemapper_priority');
$pd_router->add_tab('Sitemap', // TODO i18n
		    $pth['folder']['plugins'] . 'sitemapper/sitemapper_view.php');


/**
 * Handle plugin administration
 */
if (isset($sitemapper) && $sitemapper == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
	$o .= Sitemapper_info();
	break;
    case 'plugin_main':
	$o .= Sitemapper_admin();
	break;
    default:
	$o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
