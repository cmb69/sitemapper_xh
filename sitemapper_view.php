<?php

/**
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

/**
 * @return string
 */
function Sitemapper_view(array $pageData)
{
    global $_Sitemapper;

    return $_Sitemapper->pageDataTab($pageData);
}
