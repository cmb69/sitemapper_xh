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
 * Returns plugin version information.
 *
 * @return string  The (X)HTML.
 */
function Sitemapper_version()
{
    return '<h1>Sitemapper_XH</h1>' . PHP_EOL
	. '<p>Version: ' . SITEMAPPER_VERSION . '</p>' . PHP_EOL
	. '<p>Copyright &copy; 2011-2013 Christoph M. Becker</p>' . PHP_EOL
	. '<p style="text-align: justify">This program is free software: you can redistribute it and/or modify'
	. ' it under the terms of the GNU General Public License as published by'
	. ' the Free Software Foundation, either version 3 of the License, or'
	. ' (at your option) any later version.</p>' . PHP_EOL
	. '<p style="text-align: justify">This program is distributed in the hope that it will be useful,'
	. ' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	. ' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	. ' GNU General Public License for more details.</p>' . PHP_EOL
	. '<p style="text-align: justify">You should have received a copy of the GNU General Public License'
	. ' along with this program.  If not, see'
	. ' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>' . PHP_EOL;
}


/**
 * Returns requirements information.
 *
 * @return string  The (X)HTML
 */
function Sitemapper_systemCheck() // RELEASE-TODO
{
    global $pth, $tx, $plugin_tx;

    define('SITEMAPPER_PHP_VERSION', '4.0.7');
    $ptx = $plugin_tx['sitemapper'];
    $imgdir = $pth['folder']['plugins'] . 'sitemapper/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $htm = tag('hr') . '<h4>' . $ptx['syscheck_title'] . '</h4>'
	. (version_compare(PHP_VERSION, SITEMAPPER_PHP_VERSION) >= 0 ? $ok : $fail)
	. '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], SITEMAPPER_PHP_VERSION)
	. tag('br') . tag('br') . PHP_EOL;
    foreach (array('date') as $ext) {
	$htm .= (extension_loaded($ext) ? $ok : $fail)
	    . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext) . tag('br') . PHP_EOL;
    }
    $htm .= (!get_magic_quotes_runtime() ? $ok : $fail)
	. '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes'] . tag('br') . PHP_EOL;
    $htm .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	. '&nbsp;&nbsp;' . $ptx['syscheck_encoding'] . tag('br') . tag('br') . PHP_EOL;
    $sss = array_merge(array(''), Sitemapper_installedSubsites());
    // TODO: check folders writable
    return $htm;
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
	$o .= Sitemapper_version().Sitemapper_systemCheck();
	break;
    case 'plugin_main':
	$o .= Sitemapper_admin();
	break;
    default:
	$o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
