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

namespace Sitemapper;

use Plib\Request;
use Plib\SystemChecker;
use Plib\View;

class InfoController
{
    /** @var string */
    private $base;

    /** @var string */
    private $defaultLanguage;

    /** @var string */
    private $pluginDir;

    /** @var Model */
    private $model;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(
        string $base,
        string $defaultLanguage,
        string $pluginDir,
        Model $model,
        SystemChecker $systemChecker,
        View $view
    ) {
        $this->base = $base;
        $this->defaultLanguage = $defaultLanguage;
        $this->pluginDir = $pluginDir;
        $this->model = $model;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function execute(Request $request): string
    {
        $sitemaps = $this->sitemaps($request);
        $checks = $this->systemChecks();
        $version = SITEMAPPER_VERSION;
        $bag = compact('sitemaps', 'checks', 'version');
        return $this->view->render('info', $bag);
    }

    /** @return list<array{name:string,href:string}> */
    private function sitemaps(Request $request): array
    {
        $sitemap = [
            'name' => 'index',
            'href' => $request->url()->path($this->base)->page("sitemapper_index")->relative(),
        ];
        $sitemaps = array($sitemap);
        foreach ($this->model->installedLanguages() as $lang) {
            $subdir = $lang != $this->defaultLanguage ? "$lang/" : '';
            $sitemap = [
                'name' => $lang,
                'href' => $request->url()->path($this->base . $subdir)->page("sitemapper_sitemap")->relative(),
            ];
            $sitemaps[] = $sitemap;
        }
        return $sitemaps;
    }

    /** @return list<array{label:string,class:string}> */
    private function systemChecks(): array
    {
        $phpVersion = '7.1.0';
        $xhVersion = '1.7.0';
        $checks = array();
        $checks[] = [
            "label" => $this->view->plain('syscheck_phpversion', $phpVersion),
            "class" => $this->systemChecker->checkVersion(PHP_VERSION, $phpVersion) ? 'xh_success' : 'xh_fail',
        ];
        $success = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $xhVersion");
        $checks[] = [
            "label" => $this->view->plain('syscheck_xhversion', $xhVersion),
            "class" => $success ? 'xh_success' : 'xh_fail',
        ];
        $folders = array();
        foreach (array('config/', 'languages/') as $folder) {
            $folders[] = "$this->pluginDir/$folder";
        }
        foreach ($folders as $folder) {
            $checks[] = [
                "label" => $this->view->plain('syscheck_writable', $folder),
                "class" => $this->systemChecker->checkWritability($folder) ? 'xh_success' : 'xh_warn',
            ];
        }
        return $checks;
    }
}
