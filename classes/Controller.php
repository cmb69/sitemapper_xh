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

class Controller
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var View
     */
    private $view;

    public function __construct(View $view)
    {
        global $c, $pth, $cf, $plugin_cf, $pd_router;

        $this->model = new Model(
            $cf['language']['default'],
            $pth['folder']['base'],
            $c,
            $pd_router->find_all(),
            $plugin_cf['sitemapper']['ignore_hidden_pages'],
            $plugin_cf['sitemapper']['changefreq'],
            $plugin_cf['sitemapper']['priority']
        );
        $this->view = $view;
    }

    /**
     * @return string
     */
    private function sitemapIndex()
    {
        global $cf;

        $sitemaps = array();
        foreach ($this->model->installedLanguages() as $lang) {
            $base = CMSIMPLE_URL;
            if ($lang != $cf['language']['default']) {
                $base .= $lang . '/';
            }
            $sitemap = array(
                'loc' => $base . '?sitemapper_sitemap',
                'time' => $this->model->languageLastMod($lang)
            );
            array_walk($sitemap, 'XH_hsc');
            $sitemaps[] = $sitemap;
        }
        return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
            . $this->view->render('index', array('sitemaps' => $sitemaps));
    }

    /**
     * @return string
     */
    private function languageSitemap()
    {
        global $u, $cl, $plugin_cf, $xh_publisher;

        $startpage = $xh_publisher->getFirstPublishedPage();
        $urls = array();
        for ($i = 0; $i < $cl; $i++) {
            if (!$this->model->isPageExcluded($i)) {
                $separator = $plugin_cf['sitemapper']['clean_urls'] ? '' : '?';
                $priority = $this->model->pagePriority($i);
                $url = array(
                    'loc' => CMSIMPLE_URL
                        . ($i == $startpage ? '' : ($separator . $u[$i])),
                    'lastmod' => $this->model->pageLastMod($i),
                    'changefreq' => $this->model->pageChangefreq($i),
                    'priority' => $priority
                );
                array_walk($url, 'XH_hsc');
                $urls[] = $url;
            }
        }
        return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
            . $this->view->render('sitemap', array('urls' => $urls));
    }

    /**
     * @return array
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
     * @return array
     */
    private function systemChecks()
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['sitemapper'];
        $phpVersion = '5.4.0';
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

    /**
     * @return string
     */
    private function info()
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
     * @param string $body
     */
    private function respondWithSitemap($body)
    {
        header('HTTP/1.0 200 OK');
        header('Content-Type: application/xml; charset=utf-8');
        echo $body;
        exit;
    }

    private function dispatch()
    {
        global $admin, $action, $plugin, $o, $f, $sl, $cf;

        if (XH_ADM && XH_wantsPluginAdministration('sitemapper')) {
            $o .= print_plugin_admin('off');
            switch ($admin) {
                case '':
                    $o .= $this->info();
                    break;
                default:
                    $o .= plugin_admin_common($action, $admin, $plugin);
            }
        } elseif (isset($_GET['sitemapper_index']) && $sl == $cf['language']['default']) {
            $f = 'sitemapper_index';
        } elseif (isset($_GET['sitemapper_sitemap'])) {
            $f = 'sitemapper_sitemap';
        }
    }

    public function init()
    {
        global $pth, $plugin_tx, $pd_router;

        $pd_router->add_interest('sitemapper_changefreq');
        $pd_router->add_interest('sitemapper_priority');
        XH_afterPluginLoading(array($this, 'dispatchAfterPluginLoading'));
        if (XH_ADM) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(false);
            }
            $pd_router->add_tab(
                $plugin_tx['sitemapper']['tab'],
                $pth['folder']['plugins'] . 'sitemapper/sitemapper_view.php'
            );
        }
        $this->dispatch();
    }

    /**
     * @return string
     */
    public function pageDataTab(array $pageData)
    {
        global $sn, $su, $pth;

        $action = "$sn?$su";
        $helpIcon = $pth['folder']['plugins'] . 'sitemapper/images/help.png';
        $changefreqOptions = $this->model->changefreqs;
        array_unshift($changefreqOptions, '');
        $changefreqOptions = array_flip($changefreqOptions);
        foreach (array_keys($changefreqOptions) as $opt) {
            $changefreqOptions[$opt]
                = $pageData['sitemapper_changefreq'] == $opt;
        }
        $priority = $pageData['sitemapper_priority'];
        $bag = compact('action', 'helpIcon', 'changefreqOptions', 'priority');
        return $this->view->render('pdtab', $bag);
    }

    public function dispatchAfterPluginLoading()
    {
        global $f;

        switch ($f) {
            case 'sitemapper_index':
                $this->respondWithSitemap($this->sitemapIndex());
                break;
            case 'sitemapper_sitemap':
                $this->respondWithSitemap($this->languageSitemap());
                break;
        }
    }
}
