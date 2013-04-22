<?php

/**
 * Pagedata view of Sitemapper_XH.
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
 * Returns the pagedata form.
 *
 * @global string
 * @global array  The paths of system files and folders.
 * @global array  The localization of the plugins.
 * @global object  The sitemapper model.
 * @param  array $page	The page's data.
 * @return string  The (X)HTML.
 */
function Sitemapper_view($page)
{
    global $su, $pth, $plugin_tx, $_Sitemapper;

    $ptx = $plugin_tx['sitemapper'];
    $action = $pth['folder']['base'] . '?' . $su;
    $help = array(
	'changefreq' => $ptx['cf_changefreq'],
	'priority' => $ptx['cf_priority']
    );
    $changefreqs = $_Sitemapper->changefreqs;
    array_unshift($changefreqs, '');
    $changefreqs = array_flip($changefreqs);
    foreach ($changefreqs as $opt => $dummy) {
	$changefreqs[$opt] = $page['sitemapper_changefreq'] == $opt;
    }
    $priority = $page['sitemapper_priority'];
    $bag = array(
	'action' => $action,
	'helpIcon' => $pth['folder']['plugins'] . 'sitemapper/images/help.png',
	'help' => $help,
	'changefreqOptions' => $changefreqs,
	'priority' => $priority
    );
    return Sitemapper_render('pdtab', $bag);
}

?>
