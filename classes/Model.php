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

use XH\PageDataRouter;
use XH\Publisher;

class Model
{
    /** @var list<string> */
    public $changefreqs = array(
        'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'
    );

    /** @var string */
    private $defaultLang;

    /** @var list<string> */
    private $secondLanguages;

    /** @var string */
    private $baseFolder;

    /** @var PageDataRouter */
    private $pageDataRouter;

    /** @var Publisher */
    private $publisher;

    /** @var bool */
    private $excludeHidden;

    /** @var string */
    private $defaultChangefreq;

    /** @var string */
    private $defaultPriority;

    /** @param list<string> $secondLanguages */
    public function __construct(
        string $defaultLang,
        array $secondLanguages,
        string $baseFolder,
        PageDataRouter $pageDataRouter,
        Publisher $publisher,
        bool $excludeHidden,
        string $defaultChangefreq,
        string $defaultPriority
    ) {
        $this->defaultLang = $defaultLang;
        $this->secondLanguages = $secondLanguages;
        $this->baseFolder = $baseFolder;
        $this->pageDataRouter = $pageDataRouter;
        $this->publisher = $publisher;
        $this->excludeHidden = $excludeHidden;
        $this->defaultChangefreq = $defaultChangefreq;
        $this->defaultPriority = $defaultPriority;
    }

    private function sitemapDate(int $timestamp): string
    {
        return gmdate('Y-m-d\TH:i:s\Z', $timestamp);
    }

    private function languageContentFolder(string $lang): string
    {
        $res = $this->baseFolder . 'content/';
        if ($lang != $this->defaultLang) {
            $res .= $lang . '/';
        }
        return $res;
    }

    private function isPageHidden(int $index): bool
    {
        return $this->publisher->isHidden($index);
    }

    private function isPagePublished(int $index): bool
    {
        return $this->publisher->isPublished($index);
    }

    public function isPageExcluded(int $index): bool
    {
        $pagedata = $this->pageDataRouter->find_page($index);
        $include = $pagedata['sitemapper_include'] ?? "";
        if ($include === "yes") {
            return false;
        }
        if ($include === "no") {
            return true;
        }
        return !$this->isPagePublished($index)
            || $this->excludeHidden && $this->isPageHidden($index);
    }

    /** @return string|false */
    public function pageLastMod(int $index)
    {
        $lastEdit = $this->pageDataRouter->find_page($index)['last_edit'];
        return !empty($lastEdit) ? $this->sitemapDate($lastEdit) : false;
    }

    public function pageChangefreq(int $index): string
    {
        $pagedata = $this->pageDataRouter->find_page($index);
        return !empty($pagedata['sitemapper_changefreq'])
            ? $pagedata['sitemapper_changefreq']
            : $this->defaultChangefreq;
    }

    public function pagePriority(int $index): string
    {
        $pagedata = $this->pageDataRouter->find_page($index);
        return isset($pagedata['sitemapper_priority'])
            && $pagedata['sitemapper_priority'] != ''
            ? $pagedata['sitemapper_priority']
            : $this->defaultPriority;
    }

    public function languageLastMod(string $lang): string
    {
        $contentFolder = $this->languageContentFolder($lang);
        $contentFile = $contentFolder . 'content.htm';
        return $this->sitemapDate((int) filemtime($contentFile));
    }

    /** @return list<string> */
    public function installedLanguages(): array
    {
        $res = $this->secondLanguages;
        array_unshift($res, $this->defaultLang);
        return $res;
    }
}
