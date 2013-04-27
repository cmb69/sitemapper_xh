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


/**
 * Returns the pagedata form.
 *
 * @global object
 * @param  array $pageData  The page's data.
 * @return string  The (X)HTML.
 */
function Sitemapper_view($pageData)
{
    global $_Sitemapper;

    return $_Sitemapper->pageDataTab($pageData);
}

?>
