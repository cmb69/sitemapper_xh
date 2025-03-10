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
    /** @var string */
    private $root;

    /** @var string */
    private $defaultLanguage;

    /** @var string */
    private $pluginDir;

    /** @var string */
    private $xhVersion;

    /** @var Model */
    private $model;

    /** @var View */
    private $view;

    public function __construct(
        string $root,
        string $defaultLanguage,
        string $pluginDir,
        string $xhVersion,
        Model $model,
        View $view
    ) {
        $this->root = $root;
        $this->defaultLanguage = $defaultLanguage;
        $this->pluginDir = $pluginDir;
        $this->xhVersion = $xhVersion;
        $this->model = $model;
        $this->view = $view;
    }

    public function execute(): string
    {
        $sitemaps = $this->sitemaps();
        $checks = $this->systemChecks();
        $version = SITEMAPPER_VERSION;
        $bag = compact('sitemaps', 'checks', 'version');
        return $this->view->render('info', $bag);
    }

    /** @return list<array{name:string,href:string}> */
    private function sitemaps(): array
    {
        $sitemap = [
            'name' => 'index',
            'href' => $this->root . '?sitemapper_index'
        ];
        $sitemaps = array($sitemap);
        foreach ($this->model->installedLanguages() as $lang) {
            $subdir = $lang != $this->defaultLanguage ? "$lang/" : '';
            $sitemap = [
                'name' => $lang,
                'href' => $this->root . $subdir . '?sitemapper_sitemap'
            ];
            $sitemaps[] = $sitemap;
        }
        return $sitemaps;
    }

    /** @return list<array{label:HtmlString,class:string}> */
    private function systemChecks(): array
    {
        $phpVersion = '7.0.0';
        $xhVersion = '1.7.0';
        $checks = array();
        $checks[] = [
            "label" => new HtmlString($this->view->text('syscheck_phpversion', $phpVersion)),
            "class" => version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'xh_success' : 'xh_fail',
        ];
        $checks[] = [
            "label" => new HtmlString($this->view->text('syscheck_xhversion', $xhVersion)),
            "class" => version_compare(substr($this->xhVersion, 12), $xhVersion) >= 0 ? 'xh_success' : 'xh_fail',
        ];
        $folders = array();
        foreach (array('config/', 'languages/') as $folder) {
            $folders[] = "$this->pluginDir/$folder";
        }
        foreach ($folders as $folder) {
            $checks[] = [
                "label" => new HtmlString($this->view->text('syscheck_writable', $folder)),
                "class" => is_writable($folder) ? 'xh_success' : 'xh_warn',
            ];
        }
        return $checks;
    }
}
