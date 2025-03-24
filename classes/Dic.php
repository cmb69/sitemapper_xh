<?php

/**
 * Copyright 2011-2021 Christoph M. Becker
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

namespace Sitemapper;

use Plib\SystemChecker;
use Plib\View;
use XH\Pages;

class Dic
{
    public static function makeInfoController(): InfoController
    {
        global $cf, $pth;

        return new InfoController(
            $pth["folder"]["base"],
            $cf['language']['default'],
            "{$pth['folder']['plugins']}sitemapper",
            self::model(),
            new SystemChecker(),
            self::view()
        );
    }

    public static function makePageDataController(): PageDataController
    {
        global $pth;

        return new PageDataController(
            "{$pth['folder']['plugins']}sitemapper/images",
            self::model(),
            self::view()
        );
    }

    public static function makeSitemapController(): SitemapController
    {
        global $cf, $pth, $xh_publisher;

        return new SitemapController(
            $pth["folder"]["base"],
            $cf["language"]["default"],
            self::model(),
            new Pages(),
            $xh_publisher,
            self::view()
        );
    }

    /**
     * @return Model
     */
    private static function model()
    {
        global $pth, $cf, $plugin_cf, $pd_router, $xh_publisher;

        return new Model(
            $cf['language']['default'],
            XH_secondLanguages(),
            $pth['folder']['base'],
            $pd_router,
            $xh_publisher,
            $plugin_cf['sitemapper']['ignore_hidden_pages'],
            $plugin_cf['sitemapper']['changefreq'],
            $plugin_cf['sitemapper']['priority']
        );
    }

    /**
     * @return View
     */
    private static function view()
    {
        global $pth, $plugin_tx;

        return new View("{$pth['folder']['plugins']}sitemapper/views/", $plugin_tx["sitemapper"]);
    }
}
