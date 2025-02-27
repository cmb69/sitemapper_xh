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

use XH\Pages;

class Plugin
{
    public static function makeInfoController(): InfoController
    {
        global $cf, $pth;

        return new InfoController(
            CMSIMPLE_ROOT,
            $cf['language']['default'],
            "{$pth['folder']['plugins']}sitemapper",
            CMSIMPLE_XH_VERSION,
            self::model(),
            self::view()
        );
    }

    /**
     * @param array<string,string> $pageData
     * @return string
     */
    public static function pageDataTab(array $pageData)
    {
        global $sn, $su, $pth;

        $controller = new PageDataController(
            "$sn?$su",
            "{$pth['folder']['plugins']}sitemapper/images",
            $pageData,
            self::model(),
            self::view()
        );
        return $controller->execute();
    }

    /**
     * @return void
     */
    public static function dispatchAfterPluginLoading()
    {
        global $f;

        switch ($f) {
            case 'sitemapper_index':
                self::makeSitemapController()->sitemapIndex();
                break;
            case 'sitemapper_sitemap':
                self::makeSitemapController()->languageSitemap();
                break;
        }
    }

    private static function makeSitemapController(): SitemapController
    {
        global $cf, $xh_publisher, $plugin_cf;

        $respond = function ($body) {
            header('HTTP/1.0 200 OK');
            header('Content-Type: application/xml; charset=utf-8');
            echo $body;
            exit;
        };
        return new SitemapController(
            CMSIMPLE_URL,
            $cf["language"]["default"],
            $plugin_cf["sitemapper"],
            self::model(),
            new Pages(),
            $xh_publisher,
            self::view(),
            $respond
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

        return new View("{$pth['folder']['plugins']}sitemapper/views", $plugin_tx["sitemapper"]);
    }
}
