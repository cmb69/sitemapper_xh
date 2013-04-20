<?php

/**
 * Front-end functionality of Sitemapper_XH.
 * Copyright (c) 2011-2013 Christoph M. Becker (see license.txt)
 */


// TODO: htmlspecialchars(..., , UTF_8)


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('SITEMAPPER_VERSION', '2alpha1');


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
	if ($fn[0] != '.'
	    && (strlen($fn) == 2
		|| ($fn != '2site' && is_dir($dir . $fn)
		    && file_exists($dir . $fn . '/cmsimplesubsite.htm'))))
	{
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
    global $pth, $plugin_cf, $cf;

    $res = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
	    .'<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n"
	    .'    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'
		.' http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"'."\n"
	    .'    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    $host = empty($plugin_cf['sitemapper']['canonical_hostname'])
	    ? $_SERVER['SERVER_NAME']
	    : $plugin_cf['sitemapper']['canonical_hostname'];
    foreach (sitemapper_installed_subsites() as $ss) {
        $folder = './' . ($ss != $cf['language']['default'] ? "$ss/" : '') . 'content/';
        $time = max(filemtime("${folder}content.htm"), filemtime("${folder}pagedata.php"));
        $res .= '  <sitemap>'."\n"
                .'    <loc>'.htmlspecialchars('http://'.$host.CMSIMPLE_ROOT.($ss != $cf['language']['default'] ? $ss.'/' : '').'?sitemapper_sitemap'). '</loc>'."\n"
                .'    <lastmod>'.sitemapper_date($time).'</lastmod>'."\n" // fix time
                .'  </sitemap>'."\n";
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
    global $pth, $u, $pd_router, $plugin_cf, $sl, $c, $function, $s, $text, $sn;

    $sitemapper_cf = $plugin_cf['sitemapper'];

    $res = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
	    .'<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n"
	    .'    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'
		.' http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"'."\n"
	    .'    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    $host = empty($plugin_cf['sitemapper']['canonical_hostname'])
	    ? $_SERVER['SERVER_NAME']
	    : $plugin_cf['sitemapper']['canonical_hostname'];
    $dir = preg_replace('/index.php$/i', '', $sn);
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
		    .'    <loc>'.htmlspecialchars('http://'.$host.$dir.'?'.$u[$i]).'</loc>'."\n";
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
//function sitemapper_write_sitemap($ss = NULL) {
//    global $pth;
//
//    $suffix = isset($ss) ? '-'.$ss : '';
//    $fn = $pth['folder']['base'].'sitemap'.$suffix.'.xml';
//    $sitemap = (isset($ss)) ? sitemapper_subsite_sitemap() : sitemapper_sitemap_index();
//    if (($fp = fopen($fn, 'w')) && fputs($fp, $sitemap)) {
//	if ($fp) {fclose($fp);}
//    } else {
//	e('cntwriteto', 'file', $fn);
//    }
//}


/**
 * Handles sitemap requests.
 *
 * @return void
 */
function sitemapper()
{
    global $cf, $sl;

    if (isset($_GET['sitemapper_index']) && $sl == $cf['language']['default']) {
        header('Content-Type: application/xml');
        echo sitemapper_sitemap_index();
        exit;
    } elseif (isset($_GET['sitemapper_sitemap'])) {
        header('Content-Type: application/xml');
        echo sitemapper_subsite_sitemap();
        exit;
    }
}

?>
