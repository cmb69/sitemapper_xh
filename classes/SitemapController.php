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

class SitemapController
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
     * @return void
     */
    public function sitemapIndex()
    {
        global $cf;

        $sitemaps = array();
        foreach ($this->model->installedLanguages() as $lang) {
            $base = CMSIMPLE_URL;
            if ($lang != $cf['language']['default']) {
                $base .= $lang . '/';
            }
            $sitemap = (object) [
                'loc' => $base . '?sitemapper_sitemap',
                'time' => $this->model->languageLastMod($lang)
            ];
            $sitemaps[] = $sitemap;
        }
        $this->respondWithSitemap(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . $this->view->render('index', array('sitemaps' => $sitemaps))
        );
    }

    /**
     * @return void
     */
    public function languageSitemap()
    {
        global $u, $cl, $plugin_cf, $xh_publisher;

        $startpage = $xh_publisher->getFirstPublishedPage();
        $urls = array();
        for ($i = 0; $i < $cl; $i++) {
            if (!$this->model->isPageExcluded($i)) {
                $separator = $plugin_cf['sitemapper']['clean_urls'] ? '' : '?';
                $priority = $this->model->pagePriority($i);
                $url = (object) [
                    'loc' => CMSIMPLE_URL
                        . ($i == $startpage ? '' : ($separator . $u[$i])),
                    'lastmod' => $this->model->pageLastMod($i),
                    'changefreq' => $this->model->pageChangefreq($i),
                    'priority' => $priority
                ];
                $urls[] = $url;
            }
        }
        $this->respondWithSitemap(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . $this->view->render('sitemap', array('urls' => $urls))
        );
    }

    /**
     * @param string $body
     * @return void
     */
    private function respondWithSitemap($body)
    {
        header('HTTP/1.0 200 OK');
        header('Content-Type: application/xml; charset=utf-8');
        echo $body;
        exit;
    }
}
