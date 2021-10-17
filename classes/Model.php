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

use XH\PageDataRouter;
use XH\Publisher;

class Model
{
    /**
     * @var array<int,string>
     */
    public $changefreqs = array(
        'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'
    );

    /**
     * @var string
     */
    private $defaultLang;

    /**
     * @var string
     */
    private $baseFolder;

    /**
     * @var PageDataRouter
     */
    private $pageDataRouter;

    /** @var Publisher */
    private $publisher;

    /**
     * @var bool
     */
    private $excludeHidden;

    /**
     * @var string
     */
    private $defaultChangefreq;

    /**
     * @var string
     */
    private $defaultPriority;

    /**
     * @param string $defaultLang
     * @param string $baseFolder
     * @param bool $excludeHidden
     * @param string $defaultChangefreq
     * @param string $defaultPriority
     */
    public function __construct(
        $defaultLang,
        $baseFolder,
        PageDataRouter $pageDataRouter,
        Publisher $publisher,
        $excludeHidden,
        $defaultChangefreq,
        $defaultPriority
    ) {
        $this->defaultLang = $defaultLang;
        $this->baseFolder = $baseFolder;
        $this->pageDataRouter = $pageDataRouter;
        $this->publisher = $publisher;
        $this->excludeHidden = $excludeHidden;
        $this->defaultChangefreq = $defaultChangefreq;
        $this->defaultPriority = $defaultPriority;
    }

    /**
     * @param int $timestamp
     * @return string
     */
    private function sitemapDate($timestamp)
    {
        return gmdate('Y-m-d\TH:i:s\Z', $timestamp);
    }

    /**
     * @param string $lang
     * @return string
     */
    private function languageContentFolder($lang)
    {
        $res = $this->baseFolder . 'content/';
        if ($lang != $this->defaultLang) {
            $res .= $lang . '/';
        }
        return $res;
    }

    /**
     * @param int $index
     * @return bool
     */
    private function isPageHidden($index)
    {
        return $this->publisher->isHidden($index);
    }

    /**
     * @param int $index
     * @return bool
     */
    private function isPagePublished($index)
    {
        return $this->publisher->isPublished($index);
    }

    /**
     * @param int $index
     * @return bool
     */
    public function isPageExcluded($index)
    {
        return !$this->isPagePublished($index)
            || $this->excludeHidden && $this->isPageHidden($index);
    }

    /**
     * @param int $index
     * @return string|false
     */
    public function pageLastMod($index)
    {
        $lastEdit = $this->pageDataRouter->find_page($index)['last_edit'];
        return !empty($lastEdit) ? $this->sitemapDate($lastEdit) : false;
    }

    /**
     * @param int $index
     * @return string
     */
    public function pageChangefreq($index)
    {
        $pagedata = $this->pageDataRouter->find_page($index);
        return !empty($pagedata['sitemapper_changefreq'])
            ? $pagedata['sitemapper_changefreq']
            : $this->defaultChangefreq;
    }

    /**
     * @param int $index
     * @return float
     */
    public function pagePriority($index)
    {
        $pagedata = $this->pageDataRouter->find_page($index);
        return isset($pagedata['sitemapper_priority'])
            && $pagedata['sitemapper_priority'] != ''
            ? $pagedata['sitemapper_priority']
            : $this->defaultPriority;
    }

    /**
     * @param string $lang
     * @return string
     */
    public function languageLastMod($lang)
    {
        $contentFolder = $this->languageContentFolder($lang);
        $contentFile = $contentFolder . 'content.htm';
        return $this->sitemapDate((int) filemtime($contentFile));
    }

    /**
     * @return array<int,string>
     */
    public function installedLanguages()
    {
        $res = XH_secondLanguages();
        array_unshift($res, $this->defaultLang);
        return $res;
    }
}
