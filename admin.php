<?php

/**
 * Back-end functionality of Sitemapper_XH.
 * Copyright (c) 2011-2013 Christoph M. Becker (see license.txt)
 */


// utf-8 marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns plugin version information.
 *
 * @return string
 */
function sitemapper_version() {
    return '<h1>Sitemapper_XH</h1>'."\n"
	    .'<p>Version: '.SITEMAPPER_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2011-2013 Christoph M. Becker</p>'."\n"
	    .'<p style="text-align: justify">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p style="text-align: justify">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p style="text-align: justify">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns requirements information.
 *
 * @return string
 */
function sitemapper_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    define('SITEMAPPER_PHP_VERSION', '4.0.7');
    $ptx = $plugin_tx['sitemapper'];
    $imgdir = $pth['folder']['plugins'].'sitemapper/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $htm = tag('hr').'<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, SITEMAPPER_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], SITEMAPPER_PHP_VERSION)
	    .tag('br').tag('br')."\n";
    foreach (array('date') as $ext) {
	$htm .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $htm .= (!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br')."\n";
    $htm .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br').tag('br')."\n";
    $sss = array_merge(array(''), sitemapper_installed_subsites());
    foreach ($sss as $ss) {
	$fn = 'sitemap'.($ss != '' ? '-' : '').$ss.'.xml';
	$path = $pth['folder']['base'].$fn;
	$htm .= ((file_exists($path) && is_writable($path)
		|| !file_exists($path) && is_writable($pth['folder']['base']))
		? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $fn).tag('br')."\n";
    }
    return $htm;
}


/**
 * Returns the list of all sitemaps.
 *
 * @return string
 */
function sitemapper_admin() {
    global $cf, $plugin_tx, $pth, $sl;

    $res = '<h1>'.$plugin_tx['sitemapper']['menu_main'].'</h1>'."\n"
	    .'<ul>'."\n"
	    .'<li><a href="'.CMSIMPLE_ROOT.'?sitemapper_index" target="_blank">index</a></li>'."\n";
    foreach(sitemapper_installed_subsites() as $ss) {
	$res .= '<li><a href="'.CMSIMPLE_ROOT.($ss != $cf['language']['default'] ? $ss.'/' : '').'?sitemapper_sitemap" target="_blank">'
		    .$ss.'</a></li>'."\n";
    }
    $res .= '</ul>'."\n";
    return $res;
}


/**
 * The pagedata hook.
 */
$pd_router->add_interest('sitemapper_changefreq');
$pd_router->add_interest('sitemapper_priority');
$pd_router->add_tab('Sitemap', $pth['folder']['plugins'].'sitemapper/sitemapper_view.php');


/**
 * Handle plugin administration
 */
if (isset($sitemapper) && $sitemapper == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
	$o .= sitemapper_version().sitemapper_system_check();
	break;
    case 'plugin_main':
	$o .= sitemapper_admin();
	break;
    default:
	$o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
