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
function Sitemapper_date($timestamp)
{
    $res = date('Y-m-d\TH:i:sO', $timestamp);
    $res = substr($res, 0, strlen($res)-2) . ':' . substr($res, -2);
    return $res;
}


/**
 * Returns all installed subsites (incl. languages).
 *
 * @return array
 */
function Sitemapper_installedSubsites()
{
    global $pth, $cf;

    $res = array($cf['language']['default']);
    $dir = $pth['folder']['base'];
    $dh = opendir($dir);
    while (($fn = readdir($dh)) !== false) {
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
 * Returns sitemap index.
 *
 * @return string  The XML.
 */
function Sitemapper_sitemapIndex()
{
    global $pth, $plugin_cf, $cf;

    $res = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
        .'<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL
        .'    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'
        .' http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"' . PHP_EOL
        .'    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
    $host = empty($plugin_cf['sitemapper']['canonical_hostname'])
	    ? $_SERVER['SERVER_NAME']
	    : $plugin_cf['sitemapper']['canonical_hostname'];
    foreach (Sitemapper_installedSubsites() as $ss) {
        $folder = './' . ($ss != $cf['language']['default'] ? "$ss/" : '')
            . 'content/';
        $time = max(filemtime("${folder}content.htm"),
                    filemtime("${folder}pagedata.php"));
        $loc = 'http://' . $host . CMSIMPLE_ROOT
            . ($ss != $cf['language']['default'] ? $ss.'/' : '')
            . '?sitemapper_sitemap';
        $res .= '  <sitemap>' . PHP_EOL
            .'    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL
            . '    <lastmod>' . sitemapper_date($time) . '</lastmod>' . PHP_EOL // TODO: fix time
            . '  </sitemap>' . PHP_EOL;
    }
    $res .= '</sitemapindex>' . PHP_EOL;
    return $res;
}


/**
 * Returns sitemap of current subsite/language.
 *
 * @return string  The XML.
 */
function Sitemapper_subsiteSitemap()
{
    global $pth, $u, $pd_router, $plugin_cf, $sl, $c, $function, $s, $text, $sn;

    $sitemapper_cf = $plugin_cf['sitemapper'];

    $res = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
        . '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL
        . '    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'
        . ' http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"' . PHP_EOL
        . '    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
    $host = empty($plugin_cf['sitemapper']['canonical_hostname'])
	    ? $_SERVER['SERVER_NAME']
	    : $plugin_cf['sitemapper']['canonical_hostname'];
    $dir = preg_replace('/index.php$/i', '', $sn);
    $pd = $pd_router->find_all();
    foreach ($pd as $i => $page) {
	$cnt = $function == 'save' && $i == $s ? $text : $c[$i];
        // TODO: remove is already removed ;)
	if ($page['published'] != '0' && !cmscript('remove', $cnt)
            && !$sitemapper_cf['ignore_hidden_pages']
            || $page['linked_to_menu'] != '0' && !cmscript('hide', $cnt))
        {
	    $last_edit = $page['last_edit'];
	    $changefreq = !empty($page['sitemapper_changefreq'])
                ? $page['sitemapper_changefreq']
                : $sitemapper_cf['changefreq'];
	    $priority = !empty($page['sitemapper_priority']) // TODO: '0' is empty!
                ? $page['sitemapper_priority']
                : $sitemapper_cf['priority'];
            $loc = 'http://' . $host . $dir . '?' . $u[$i];
	    $res .= '  <url>' . PHP_EOL
		.'    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
	    if (!empty($last_edit)) {
		$res .= '    <lastmod>' . sitemapper_date($last_edit)
                    . '</lastmod>' . PHP_EOL;
	    }
	    if (!empty($changefreq)) {
		$res .= '    <changefreq>' . htmlspecialchars($changefreq)
                    . '</changefreq>' . PHP_EOL;
	    }
	    if (!empty($priority)) { // TODO: '0' is empty!
		$res .= '    <priority>' . htmlspecialchars($priority)
                    . '</priority>' . PHP_EOL;
	    }
	    $res .= '  </url>' . PHP_EOL;
	}
    }
    $res .= '</urlset>' . PHP_EOL;
    return $res;
}


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
        echo Sitemapper_sitemapIndex();
        exit;
    } elseif (isset($_GET['sitemapper_sitemap'])) {
        header('Content-Type: application/xml');
        echo Sitemapper_subsiteSitemap();
        exit;
    }
}

?>
