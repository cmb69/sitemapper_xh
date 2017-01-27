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
