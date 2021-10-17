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

class Plugin
{
    const VERSION = '@SITEMAPPER_VERSION@';

    /**
     * @return void
     */
    public static function run()
    {
        global $pth, $plugin_tx, $pd_router;

        $pd_router->add_interest('sitemapper_changefreq');
        $pd_router->add_interest('sitemapper_priority');
        XH_afterPluginLoading(array(self::class, 'dispatchAfterPluginLoading'));
        if (defined("XH_ADM") && XH_ADM) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(false);
            }
            $pd_router->add_tab(
                $plugin_tx['sitemapper']['tab'],
                $pth['folder']['plugins'] . 'sitemapper/sitemapper_view.php'
            );
        }
        self::dispatch();
    }

    /**
     * @return void
     */
    private static function dispatch()
    {
        global $admin, $o, $f, $sl, $cf;

        if (defined("XH_ADM") && XH_ADM && XH_wantsPluginAdministration('sitemapper')) {
            $o .= print_plugin_admin('off');
            switch ($admin) {
                case '':
                    $controller = new InfoController(self::model(), new View());
                    $o .= $controller->execute();
                    break;
                default:
                    $o .= plugin_admin_common();
            }
        } elseif (isset($_GET['sitemapper_index']) && $sl == $cf['language']['default']) {
            $f = 'sitemapper_index';
        } elseif (isset($_GET['sitemapper_sitemap'])) {
            $f = 'sitemapper_sitemap';
        }
    }

    /**
     * @param array<string,string> $pageData
     * @return string
     */
    public static function pageDataTab(array $pageData)
    {
        $controller = new PageDataController($pageData, self::model(), new View());
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
                $controller = new SitemapController(self::model(), new View());
                $controller->sitemapIndex();
                break;
            case 'sitemapper_sitemap':
                $controller = new SitemapController(self::model(), new View());
                $controller->languageSitemap();
                break;
        }
    }

    /**
     * @return Model
     */
    private static function model()
    {
        global $c, $pth, $cf, $plugin_cf, $pd_router;

        return new Model(
            $cf['language']['default'],
            $pth['folder']['base'],
            $c,
            $pd_router->find_all(),
            $plugin_cf['sitemapper']['ignore_hidden_pages'],
            $plugin_cf['sitemapper']['changefreq'],
            $plugin_cf['sitemapper']['priority']
        );
    }
}