<?php

/**
 * Pagedata view of Sitemapper_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Sitemapper
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */

/**
 * Returns the pagedata form.
 *
 * @param array $pageData The page's data.
 *
 * @return string  The (X)HTML.
 *
 * @global object
 */
function Sitemapper_view($pageData)
{
    global $_Sitemapper;

    return $_Sitemapper->pageDataTab($pageData);
}

?>
