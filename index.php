<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Sitemapper_XH.
 *
 * Sitemapper_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Sitemapper_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Sitemapper_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

use Plib\Request;
use Sitemapper\Dic;
use XH\PageDataRouter;

if (!defined("CMSIMPLE_XH_VERSION")) {
    http_response_code(403);
    exit;
}

const SITEMAPPER_VERSION = "3.1-dev";

/**
 * @var PageDataRouter $pd_router
 * @var array<string,array<string,string>> $cf
 * @var string $sl
 * @var string $f
 */

$pd_router->add_interest('sitemapper_changefreq');
$pd_router->add_interest('sitemapper_priority');

if (isset($_GET['sitemapper_index']) && $sl == $cf['language']['default']) {
    $f = 'sitemapper_index';
} elseif (isset($_GET['sitemapper_sitemap'])) {
    $f = 'sitemapper_sitemap';
}

XH_afterPluginLoading(function () use ($f) {
    Dic::makeSitemapController()->execute(Request::current(), $f)();
});
