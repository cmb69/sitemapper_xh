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


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


require_once $pth['folder']['plugin_classes'] . 'model.php';


/**
 * The version string.
 */
define('SITEMAPPER_VERSION', '2alpha1');


/**
 * The fully qualified absolute URL to the current (sub)site.
 */
define('SITEMAPPER_URL', 'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://'
    . (empty($plugin_cf['sitemapper']['canonical_hostname'])
	? $_SERVER['SERVER_NAME']
	: $plugin_cf['sitemapper']['canonical_hostname'])
    . ($_SERVER['SERVER_PORT'] < 1024 ? '' : ':' . $_SERVER['SERVER_PORT'])
    . preg_replace('/index.php$/', '', $_SERVER['SCRIPT_NAME']));


/**
 * Returns a string with special HTML characters escaped.
 *
 * @param  string $str
 * @return string
 */
function Sitemapper_hsc($str)
{
    return htmlspecialchars($str, ENT_COMPAT, 'UTF_8');
}


/**
 * Returns sitemap index.
 *
 * @global array  The configuration of the core.
 * @global object  The sitemapper model.
 * @return string  The XML.
 */
function Sitemapper_sitemapIndex()
{
    global $cf, $_Sitemapper;

    $sitemaps = array();
    foreach ($_Sitemapper->installedSubsites() as $ss) {
	$base = SITEMAPPER_URL;
	if ($ss != $cf['language']['default']) {
	    $base .= $ss . '/';
	}
	$sitemap = array(
	    'loc' => $base . '?sitemapper_sitemap',
	    'time' => $_Sitemapper->subsiteLastMod($ss)
	);
	array_walk($sitemap, 'Sitemapper_hsc');
	$sitemaps[] = $sitemap;
    }
    return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
	. Sitemapper_renderXML('index', array('sitemaps' => $sitemaps));
}


/**
 * Returns sitemap of current subsite/language.
 *
 * @global array  The "urls" of the pages.
 * @global int  The number of pages.
 * @global object  The sitemapper model.
 * @return string  The XML.
 */
function Sitemapper_subsiteSitemap()
{
    global $u, $cl, $_Sitemapper;

    $urls = array();
    for ($i = 0; $i < $cl; $i++) {
	if (!$_Sitemapper->isPageExcluded($i)) {
	    $url = array(
		'loc' => SITEMAPPER_URL . '?' . $u[$i],
		'lastmod' => $_Sitemapper->pageLastMod($i),
		'changefreq' => $_Sitemapper->pageChangefreq($i),
		'priority' => $_Sitemapper->pagePriority($i)
	    );
	    array_walk($url, 'Sitemapper_hsc');
	    $urls[] = $url;
	}
    }
    return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
	. Sitemapper_renderXML('sitemap', array('urls' => $urls));
}


/**
 * Renders a template.
 *
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the core.
 * @param  string $_template  The name of the template.
 * @param  array $_bag  Variables available in the template.
 * @return string
 */
function Sitemapper_render($_template, $_bag)
{
    global $pth, $cf;

    $_template = "{$pth['folder']['plugins']}sitemapper/views/$_template.htm";
    $_xhtml = $cf['xhtml']['endtags'];
    unset($pth, $cf);
    extract($_bag);
    ob_start();
    include $_template;
    $o = ob_get_clean();
    if (!$_xhtml) {
	$o = str_replace('/>', '>', $o);
    }
    return $o;
}


/**
 * Renders an XML template.
 *
 * @global array  The paths of system files and folders.
 * @param string $_template  The name of the template.
 * @param array $_bag  Variables available in the template.
 * @retun string
 */
function Sitemapper_renderXML($_template, $_bag)
{
    global $pth;

    $_template = "{$pth['folder']['plugins']}sitemapper/views/$_template.xml";
    unset($pth, $cf);
    extract($_bag);
    ob_start();
    include $_template;
    $o = ob_get_clean();
    return $o;
}


/**
 * Handles sitemap requests.
 *
 * @global array $cf  The configuration of the core.
 * @return void
 */
function sitemapper()
{
    global $cf, $sl;

    if (isset($_GET['sitemapper_index']) && $sl == $cf['language']['default']) {
        $body = Sitemapper_sitemapIndex();
    } elseif (isset($_GET['sitemapper_sitemap'])) {
        $body = Sitemapper_subsiteSitemap();
    }
    if (isset($body)) {
	header('HTTP/1.0 200 OK');
        header('Content-Type: application/xml');
        echo $body;
        exit;
    }
}


/*
 * Create model object.
 */
$_Sitemapper = new Sitemapper_Model($cf['language']['default'],
				    $pth['folder']['base'],
				    $c, $pd_router->find_all());

?>
