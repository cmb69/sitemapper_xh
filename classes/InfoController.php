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

class InfoController
{
    /** @var Model */
    private $model;

    /** @var View */
    private $view;

    public function __construct(Model $model, View $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function execute()
    {
        global $pth;

        $sitemaps = $this->sitemaps();
        foreach (array('ok', 'warn', 'fail') as $state) {
            $images[$state] = "{$pth['folder']['plugins']}sitemapper/images/$state.png";
        }
        $checks = $this->systemChecks();
        $icon = $pth['folder']['plugins'] . 'sitemapper/sitemapper.png';
        $version = SITEMAPPER_VERSION;
        $bag = compact('sitemaps', 'images', 'checks', 'icon', 'version');
        return $this->view->render('info', $bag);
    }

    /**
     * @return array<int,array>
     */
    private function sitemaps()
    {
        global $cf;

        $sitemap = array(
            'name' => 'index',
            'href' => CMSIMPLE_ROOT . '?sitemapper_index'
        );
        $sitemaps = array($sitemap);
        foreach ($this->model->installedLanguages() as $lang) {
            $subdir = $lang != $cf['language']['default'] ? "$lang/" : '';
            $sitemap = array(
                'name' => $lang,
                'href' => CMSIMPLE_ROOT . $subdir . '?sitemapper_sitemap'
            );
            $sitemaps[] = $sitemap;
        }
        return $sitemaps;
    }

    /**
     * @return array<string,string>
     */
    private function systemChecks()
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['sitemapper'];
        $phpVersion = '7.0.0';
        $xhVersion = '1.7.0';
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        $checks[sprintf($ptx['syscheck_xhversion'], $xhVersion)]
            = version_compare(substr(CMSIMPLE_XH_VERSION, 12), $xhVersion) >= 0 ? 'ok' : 'fail';
        $folders = array();
        foreach (array('config/', 'languages/') as $folder) {
            $folders[] = "{$pth['folder']['plugins']}sitemapper/$folder";
        }
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        return $checks;
    }
}
