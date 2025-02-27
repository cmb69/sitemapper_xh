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
use XH\Publisher;

class SitemapController
{
    /** @var string */
    private $url;

    /** @var string */
    private $defaultLanguage;

    /** @var array<string,string> */
    private $conf;

    /** @var Model */
    private $model;

    /** @var Pages */
    private $pages;

    /** @var Publisher */
    private $publisher;

    /** @var View */
    private $view;

    /** @var callable */
    private $respond;

    /** @param array<string,string> $conf */
    public function __construct(
        string $url,
        string $defaultLanguage,
        array $conf,
        Model $model,
        Pages $pages,
        Publisher $publisher,
        View $view,
        callable $respond
    ) {
        $this->url = $url;
        $this->defaultLanguage = $defaultLanguage;
        $this->conf = $conf;
        $this->model = $model;
        $this->pages = $pages;
        $this->publisher = $publisher;
        $this->view = $view;
        $this->respond = $respond;
    }

    /**
     * @return void
     */
    public function sitemapIndex()
    {
        $sitemaps = array();
        foreach ($this->model->installedLanguages() as $lang) {
            $base = $this->url;
            if ($lang != $this->defaultLanguage) {
                $base .= $lang . '/';
            }
            $sitemap = (object) [
                'loc' => $base . '?sitemapper_sitemap',
                'time' => $this->model->languageLastMod($lang)
            ];
            $sitemaps[] = $sitemap;
        }
        ($this->respond)(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . $this->view->render('index', array('sitemaps' => $sitemaps))
        );
    }

    /**
     * @return void
     */
    public function languageSitemap()
    {
        $startpage = $this->publisher->getFirstPublishedPage();
        $urls = array();
        for ($i = 0; $i < $this->pages->getCount(); $i++) {
            if (!$this->model->isPageExcluded($i)) {
                $separator = $this->conf['clean_urls'] ? '' : '?';
                $priority = $this->model->pagePriority($i);
                $url = (object) [
                    'loc' => $this->url
                        . ($i == $startpage ? '' : ($separator . $this->pages->url($i))),
                    'lastmod' => $this->model->pageLastMod($i),
                    'changefreq' => $this->model->pageChangefreq($i),
                    'priority' => $priority
                ];
                $urls[] = $url;
            }
        }
        ($this->respond)(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . $this->view->render('sitemap', array('urls' => $urls))
        );
    }
}
