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

class Model
{
    /**
     * @var array
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
     * @var array
     */
    private $content;

    /**
     * @var array
     */
    private $pagedata;

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
     * @param float $defaultPriority
     */
    public function __construct($defaultLang, $baseFolder, array $content, array $pagedata, $excludeHidden, $defaultChangefreq, $defaultPriority)
    {
        $this->defaultLang = $defaultLang;
        $this->baseFolder = $baseFolder;
        $this->content = $content;
        $this->pagedata = $pagedata;
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
        $pagedata = $this->pagedata[$index];
        return (isset($pagedata['linked_to_menu']) && $pagedata['linked_to_menu'] == '0')
            || hide($index);
    }

    /**
     * @param int $index
     * @return bool
     */
    private function isPagePublished($index)
    {
        $pagedata = $this->pagedata[$index];
        return (!isset($pagedata['published']) || $pagedata['published'] != '0')
            && $this->content[$index] != '#CMSimple hide#';
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
     * @return string or false on failure
     */
    public function pageLastMod($index)
    {
        $lastEdit = $this->pagedata[$index]['last_edit'];
        return !empty($lastEdit) ? $this->sitemapDate($lastEdit) : false;
    }

    /**
     * @param int $index
     * @return string
     */
    public function pageChangefreq($index)
    {
        $pagedata = $this->pagedata[$index];
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
        $pagedata = $this->pagedata[$index];
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
        return $this->sitemapDate(filemtime($contentFile));
    }

    /**
     * @return array
     */
    public function installedLanguages()
    {
        $res = XH_secondLanguages();
        array_unshift($res, $this->defaultLang);
        return $res;
    }
}
