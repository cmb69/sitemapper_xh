<?php

/**
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

/**
 * @param array $pageData
 * @return string
 */
function Sitemapper_view($pageData)
{
    global $_Sitemapper;

    return $_Sitemapper->pageDataTab($pageData);
}
