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

use Plib\Request;
use Plib\Response;
use Plib\View;
use XH\Pages;
use XH\Publisher;

class SitemapController
{
    /** @var string */
    private $base;

    /** @var string */
    private $defaultLanguage;

    /** @var Model */
    private $model;

    /** @var Pages */
    private $pages;

    /** @var Publisher */
    private $publisher;

    /** @var View */
    private $view;

    public function __construct(
        string $base,
        string $defaultLanguage,
        Model $model,
        Pages $pages,
        Publisher $publisher,
        View $view
    ) {
        $this->base = $base;
        $this->defaultLanguage = $defaultLanguage;
        $this->model = $model;
        $this->pages = $pages;
        $this->publisher = $publisher;
        $this->view = $view;
    }

    public function execute(Request $request, string $f): Response
    {
        switch ($f) {
            case "sitemapper_index":
                return $this->sitemapIndex($request);
            case "sitemapper_sitemap":
                return $this->languageSitemap($request);
        }
        return Response::create();
    }

    private function sitemapIndex(Request $request): Response
    {
        $sitemaps = array();
        foreach ($this->model->installedLanguages() as $lang) {
            $base = $this->base;
            if ($lang != $this->defaultLanguage) {
                $base .= $lang . '/';
            }
            $sitemap = [
                'loc' => $request->url()->path($base)->page("sitemapper_sitemap")->absolute(),
                'time' => $this->model->languageLastMod($lang)
            ];
            $sitemaps[] = $sitemap;
        }
        return Response::create('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . $this->view->render('index', array('sitemaps' => $sitemaps)))
            ->withContentType("application/xml; charset=utf-8");
    }

    private function languageSitemap(Request $request): Response
    {
        $startpage = $this->publisher->getFirstPublishedPage();
        $urls = array();
        for ($i = 0; $i < $this->pages->getCount(); $i++) {
            if (!$this->model->isPageExcluded($i)) {
                $priority = $this->model->pagePriority($i);
                $page = ($i == $startpage ? '' : ($this->pages->url($i)));
                $url = [
                    'loc' => $request->url()->page($page)->absolute(),
                    'lastmod' => $this->model->pageLastMod($i),
                    'changefreq' => $this->model->pageChangefreq($i),
                    'priority' => $priority
                ];
                $urls[] = $url;
            }
        }
        return Response::create('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . $this->view->render('sitemap', array('urls' => $urls)))
            ->withContentType("application/xml; charset=utf-8");
    }
}
