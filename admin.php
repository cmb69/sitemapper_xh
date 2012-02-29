<?php

/**
 * Back-end functionality of Sitemapper_XH.
 * Copyright (c) 2011-2012 Christoph M. Becker (see license.txt)
 */


// utf-8 marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('SITEMAPPER_VERSION', '1');


/**
 * Returns plugin version information.
 *
 * @return string
 */
function sitemapper_version() {
    return '<h1>Sitemapper_XH</h1>'."\n"
	    .'<p>Version: '.SITEMAPPER_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2011-2012 Christoph M. Becker</p>'."\n"
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
    $ptx =& $plugin_tx['sitemapper'];
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
 * Returns sitemap conforming timestamp.
 *
 * @param  int $timestamp
 * @return string
 */
function sitemapper_date($timestamp) {
    $res = date('Y-m-d\TH:i:sO', $timestamp);
    $res = substr($res, 0, strlen($res)-2).':'.substr($res, -2);
    return $res;
}


/**
 * Returns all installed subsites (incl. languages).
 *
 * @return array
 */
function sitemapper_installed_subsites() {
    global $pth, $cf;

    $res = array($cf['language']['default']);
    $dir = $pth['folder']['base'];
    $dh = opendir($dir);
    while (($fn = readdir($dh)) !== FALSE) {
	if (strlen($fn) == 2 && $fn != '..'
		|| $fn != '2site' && is_dir($dir.$fn) && file_exists($dir.$fn.'/cmsimplesubsite.htm')) {
	    $res[] = $fn;
	}
    }
    closedir($dh);
    return $res;
}


/**
 * Returns sitemap index XML.
 *
 * @return string
 */
function sitemapper_sitemap_index() {
    global $pth, $plugin_cf;

    $res = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
	    .'<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n"
	    .'    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'
		.' http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"'."\n"
	    .'    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    $host = empty($plugin_cf['sitemapper']['canonical_hostname'])
	    ? $_SERVER['SERVER_NAME']
	    : $plugin_cf['sitemapper']['canonical_hostname'];
    foreach (sitemapper_installed_subsites() as $ss) {
	$fn = $pth['folder']['base'].'sitemap-'.$ss.'.xml';
	if (file_exists($fn)) {
	    $res .= '  <sitemap>'."\n"
		    .'    <loc>'.htmlspecialchars('http://'.$host.CMSIMPLE_ROOT.'sitemap-'.$ss.'.xml').'</loc>'."\n"
		    .'    <lastmod>'.sitemapper_date(filemtime($fn)).'</lastmod>'."\n"
		    .'  </sitemap>'."\n";
	}
    }
    $res .= '</sitemapindex>'."\n";
    return $res;
}


/**
 * Returns sitemap XML for current subsite/language.
 *
 * @return string
 */
function sitemapper_subsite_sitemap() {
    global $pth, $u, $pd_router, $plugin_cf, $sl, $c, $function, $s, $text;

    $sitemapper_cf =& $plugin_cf['sitemapper'];

    $res = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
	    .'<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n"
	    .'    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'
		.' http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"'."\n"
	    .'    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    $host = empty($plugin_cf['sitemapper']['canonical_hostname'])
	    ? $_SERVER['SERVER_NAME']
	    : $plugin_cf['sitemapper']['canonical_hostname'];
    $pd = $pd_router->find_all();
    foreach ($pd as $i => $page) {
	$cnt = $function == 'save' && $i == $s ? $text : $c[$i];
	if ($page['published'] != '0' && !cmscript('remove', $cnt)
		&& !$sitemapper_cf['ignore_hidden_pages']
		|| $page['linked_to_menu'] != '0' && !cmscript('hide', $cnt)) {
	    $last_edit = $page['last_edit'];
	    $changefreq = !empty($page['sitemapper_changefreq'])
		    ? $page['sitemapper_changefreq']
		    : $sitemapper_cf['changefreq'];
	    $priority = !empty($page['sitemapper_priority'])
		    ? $page['sitemapper_priority']
		    : $sitemapper_cf['priority'];
	    $res .= '  <url>'."\n"
		    .'    <loc>'.htmlspecialchars('http://'.$host.CMSIMPLE_ROOT.'?'.$u[$i]).'</loc>'."\n";
	    if (!empty($last_edit)) {
		$res .= '    <lastmod>'.sitemapper_date($last_edit).'</lastmod>'."\n";
	    }
	    if (!empty($changefreq)) {
		$res .= '    <changefreq>'.htmlspecialchars($changefreq).'</changefreq>'."\n";
	    }
	    if (!empty($priority)) {
		$res .= '    <priority>'.htmlspecialchars($priority).'</priority>'."\n";
	    }
	    $res .= '  </url>'."\n";
	}
    }
    $res .= '</urlset>'."\n";
    return $res;
}


/**
 * Writes sitemap or sitemap index file.
 *
 * @param  string $ss
 * @return void
 */
function sitemapper_write_sitemap($ss = NULL) {
    global $pth;

    $suffix = isset($ss) ? '-'.$ss : '';
    $fn = $pth['folder']['base'].'sitemap'.$suffix.'.xml';
    $sitemap = (isset($ss)) ? sitemapper_subsite_sitemap() : sitemapper_sitemap_index();
    if (($fp = fopen($fn, 'w')) && fputs($fp, $sitemap)) {
	if ($fp) {fclose($fp);}
    } else {
	e('cntwriteto', 'file', $fn);
    }
}


/**
 * Returns the list of all sitemaps.
 *
 * @return string
 */
function sitemapper_admin() {
    global $plugin_tx, $pth, $sl;

    sitemapper_write_sitemap($sl);
    sitemapper_write_sitemap();
    $res = '<h1>'.$plugin_tx['sitemapper']['menu_main'].'</h1>'."\n"
	    .'<ul>'."\n"
	    .'<li><a href="'.CMSIMPLE_ROOT.'sitemap.xml">sitemap.xml</a></li>'."\n";
    foreach(sitemapper_installed_subsites() as $ss) {
	if (file_exists($pth['folder']['base'].'sitemap-'.$ss.'.xml')) {
	    $res .= '<li><a href="'.CMSIMPLE_ROOT.'sitemap-'.$ss.'.xml">'
			.'sitemap-'.$ss.'.xml</a></li>'."\n";
	}
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
 * Write the sitemap files.
 */
if ($function == 'save'								// changes from the editor
	|| isset($menumanager) && $menumanager && $action == 'saverearranged'	// changes from menumanager
	|| isset($pagemanager) && $pagemanager && $action == 'plugin_save'	// changes from pagemanager
	|| $s >= 0 && isset($_POST['save_page_data'])) {			// changes to pagedata
    sitemapper_write_sitemap($sl);
    sitemapper_write_sitemap();
}


/**
 * Plugin administration
 */
if (isset($sitemapper)) {
    initvar('admin');
    initvar('action');

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
