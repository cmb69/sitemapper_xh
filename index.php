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


require_once $pth['folder']['plugin_classes'] . 'model.php';


$_Sitemapper = new Sitemapper_Model($cf['language']['default'],
				    $pth['folder']['base'],
				    $c, $pd_router->find_all());


/**
 * Returns sitemap index.
 *
 * @return string  The XML.
 */
function Sitemapper_sitemapIndex()
{
    global $pth, $plugin_cf, $cf, $_Sitemapper;

    $host = empty($plugin_cf['sitemapper']['canonical_hostname'])
	    ? $_SERVER['SERVER_NAME']
	    : $plugin_cf['sitemapper']['canonical_hostname'];
    $sitemaps = array();
    foreach ($_Sitemapper->installedSubsites() as $ss) {
	$time = $_Sitemapper->subsiteLastMod($ss);
        $loc = 'http://' . $host . CMSIMPLE_ROOT
            . ($ss != $cf['language']['default'] ? $ss.'/' : '')
            . '?sitemapper_sitemap';
	$loc = htmlspecialchars($loc);
	$sitemap = array('loc' => $loc, 'time' => $time);
	$sitemaps[] = $sitemap;
    }
    return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
	. Sitemapper_renderXML('index', array('sitemaps' => $sitemaps));
}


/**
 * Returns sitemap of current subsite/language.
 *
 * @return string  The XML.
 */
function Sitemapper_subsiteSitemap()
{
    global $pth, $u, $pd_router, $plugin_cf, $sl, $c, $function, $s, $text, $sn;

    $urls = array();
    $pcf = $plugin_cf['sitemapper'];
    $host = empty($plugin_cf['sitemapper']['canonical_hostname'])
	    ? $_SERVER['SERVER_NAME']
	    : $plugin_cf['sitemapper']['canonical_hostname'];
    $dir = preg_replace('/index.php$/i', '', $sn);
    $pd = $pd_router->find_all();
    foreach ($pd as $i => $page) {
	$cnt = $function == 'save' && $i == $s ? $text : $c[$i]; // TODO
        // TODO: remove is already removed ;)
	if ($page['published'] != '0' && !cmscript('remove', $cnt)
            && !$pcf['ignore_hidden_pages'] // TODO: bugfix, as ( ) is missing!!!
            || $page['linked_to_menu'] != '0' && !cmscript('hide', $cnt))
        {
	    $last_edit = $page['last_edit'];
	    $changefreq = !empty($page['sitemapper_changefreq'])
                ? $page['sitemapper_changefreq']
                : $pcf['changefreq'];
	    $priority = !empty($page['sitemapper_priority']) // TODO: '0' is empty!
                ? $page['sitemapper_priority']
                : $pcf['priority'];
            $loc = 'http://' . $host . $dir . '?' . $u[$i];
	    $url = array(
		'loc' => htmlspecialchars($loc),
		'lastmod' => sitemapper_date($last_edit),
		'changefreq' => htmlspecialchars($changefreq),
		'priority' => htmlspecialchars($priority)
	    );
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
 * @param  string  $_template  The name of the template.
 * @param  string  $_bag  Variables available in the template.
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
 * @return void
 */
function sitemapper()
{
    global $cf, $sl;

    if (isset($_GET['sitemapper_index']) && $sl == $cf['language']['default']) {
	header('HTTP/1.0 200 OK');
        header('Content-Type: application/xml');
        echo Sitemapper_sitemapIndex();
        exit;
    } elseif (isset($_GET['sitemapper_sitemap'])) {
	header('HTTP/1.0 200 OK');
        header('Content-Type: application/xml');
        echo Sitemapper_subsiteSitemap();
        exit;
    }
}

?>
