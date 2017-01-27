<?php

/**
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
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
     * @param string $defaultLang       Default language.
     * @param string $baseFolder        Path of the root folder.
     * @param array  $content           The content of the pages.
     * @param array  $pagedata          The pagedata of the pages.
     * @param bool   $excludeHidden     Whether to exclude hidden pages.
     * @param string $defaultChangefreq Default sitemap changefreq.
     * @param float  $defaultPriority   Default sitemap priority.
     */
    public function __construct($defaultLang, $baseFolder, $content, $pagedata, $excludeHidden, $defaultChangefreq, $defaultPriority)
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
        $res = gmdate('Y-m-d\TH:i:s\Z', $timestamp);
        return $res;
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
        $res = (isset($pagedata['linked_to_menu'])
                && $pagedata['linked_to_menu'] == '0')
            || hide($index);
        return $res;
    }

    /**
     * @param int $index
     * @return bool
     */
    private function isPagePublished($index)
    {
        $pagedata = $this->pagedata[$index];
        $res = (!isset($pagedata['published']) || $pagedata['published'] != '0')
            && $this->content[$index] != '#CMSimple hide#';
        return $res;
    }

    /**
     * @param int $index
     * @return bool
     */
    public function isPageExcluded($index)
    {
        $res = !$this->isPagePublished($index)
            || $this->excludeHidden && $this->isPageHidden($index);
        return $res;
    }

    /**
     * @param int $index
     * @return string or false on failure
     */
    public function pageLastMod($index)
    {
        $res = $this->pagedata[$index]['last_edit'];
        $res = !empty($res) ? $this->sitemapDate($res) : false;
        return $res;
    }

    /**
     * @param int $index
     * @return string
     */
    public function pageChangefreq($index)
    {
        $pagedata = $this->pagedata[$index];
        $res = !empty($pagedata['sitemapper_changefreq'])
            ? $pagedata['sitemapper_changefreq']
            : $this->defaultChangefreq;
        return $res;
    }

    /**
     * @param int $index
     * @return float
     */
    public function pagePriority($index)
    {
        $pagedata = $this->pagedata[$index];
        $res = isset($pagedata['sitemapper_priority'])
            && $pagedata['sitemapper_priority'] != ''
            ? $pagedata['sitemapper_priority']
            : $this->defaultPriority;
        return $res;
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
