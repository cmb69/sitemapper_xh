<?php

/**
 * Model of Sitemapper_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Sitemapper
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */

namespace Sitemapper;

/**
 * The model class.
 *
 * @category CMSimple_XH
 * @package  Sitemapper
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */
class Model
{
    /**
     * The possible change frequencies.
     *
     * @var array
     */
    public $changefreqs = array(
        'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'
    );

    /**
     * The default language of CMSimple_XH.
     *
     * @var string
     */
    private $_defaultLang;

    /**
     * The path of the root folder of the CMSimple_XH installation.
     *
     * @var string
     */
    private $_baseFolder;

    /**
     * The content array.
     *
     * @var array
     */
    private $_content;

    /**
     * The pagedata array.
     *
     * @var array
     */
    private $_pagedata;

    /**
     * Whether hidden pages shall be excluded from the sitemap.
     *
     * @var bool
     */
    private $_excludeHidden;

    /**
     * The default sitemap changefreq.
     *
     * @var string
     */
    private $_defaultChangefreq;

    /**
     * The default sitemap priority.
     *
     * @var string
     */
    private $_defaultPriority;

    /**
     * Constructs a sitemapper model object.
     *
     * @param string $defaultLang       Default language.
     * @param string $baseFolder        Path of the root folder.
     * @param array  $content           The content of the pages.
     * @param array  $pagedata          The pagedata of the pages.
     * @param bool   $excludeHidden     Whether to exclude hidden pages.
     * @param string $defaultChangefreq Default sitemap changefreq.
     * @param float  $defaultPriority   Default sitemap priority.
     *
     * @return void
     */
    public function __construct($defaultLang, $baseFolder, $content, $pagedata,
        $excludeHidden, $defaultChangefreq, $defaultPriority
    ) {
        $this->_defaultLang = $defaultLang;
        $this->_baseFolder = $baseFolder;
        $this->_content = $content;
        $this->_pagedata = $pagedata;
        $this->_excludeHidden = $excludeHidden;
        $this->_defaultChangefreq = $defaultChangefreq;
        $this->_defaultPriority = $defaultPriority;
    }

    /**
     * Returns a sitemap.xml conforming timestamp.
     *
     * @param int $timestamp A UNIX timestamp.
     *
     * @return string
     */
    private function _sitemapDate($timestamp)
    {
        $res = gmdate('Y-m-d\TH:i:s\Z', $timestamp);
        return $res;
    }

    /**
     * Returns the path of a subsites' content folder.
     *
     * @param string $subsite Name of a subsite.
     *
     * @return string
     */
    private function _subsiteContentFolder($subsite)
    {
        if ($subsite != $this->_defaultLang) {
            $res = $this->_baseFolder . 'content/' . $subsite . '/'; // XH >= 1.6
            if (!file_exists($res)) {
                $res = $this->_baseFolder . $subsite . '/content/'; // XH < 1.6
            }
        } else {
            $res = $this->_baseFolder . 'content/';
        }
        return $res;
    }

    /**
     * Returns whether a page is hidden.
     *
     * @param int $index The numeric index of the page.
     *
     * @return bool
     */
    private function _isPageHidden($index)
    {
        $pagedata = $this->_pagedata[$index];
        $res = (isset($pagedata['linked_to_menu'])
                && $pagedata['linked_to_menu'] == '0')
            || hide($index);
        return $res;
    }

    /**
     * Returns whether a page is published.
     *
     * @param int $index The numeric index of the page.
     *
     * @return bool
     */
    private function _isPagePublished($index)
    {
        $pagedata = $this->_pagedata[$index];
        $res = (!isset($pagedata['published']) || $pagedata['published'] != '0')
            && $this->_content[$index] != '#CMSimple hide#';
        return $res;
    }

    /**
     * Returns whether a page shall be excluded from the sitemap,
     * either because it is unpublished, or because it is hidden
     * and hidden pages shall be excluded.
     *
     * @param int $index The numeric index of the page.
     *
     * @return bool
     */
    public function isPageExcluded($index)
    {
        $res = !$this->_isPagePublished($index)
            || $this->_excludeHidden && $this->_isPageHidden($index);
        return $res;
    }

    /**
     * Returns the sitemap.xml timestamp of the last modification of a page.
     * Returns false, if the last modification time is not available.
     *
     * @param int $index The numeric index of the page.
     *
     * @return string
     */
    public function pageLastMod($index)
    {
        $res = $this->_pagedata[$index]['last_edit'];
        $res = !empty($res) ? $this->_sitemapDate($res) : false;
        return $res;
    }

    /**
     * Returns the sitemap.xml changefreq of a page.
     *
     * @param int $index The numeric index of the page.
     *
     * @return string
     */
    public function pageChangefreq($index)
    {
        $pagedata = $this->_pagedata[$index];
        $res = !empty($pagedata['sitemapper_changefreq'])
            ? $pagedata['sitemapper_changefreq']
            : $this->_defaultChangefreq;
        return $res;
    }

    /**
     * Returns the sitemap.xml priority of a page.
     *
     * @param int $index The numeric index of the page.
     *
     * @return float
     */
    public function pagePriority($index)
    {
        $pagedata = $this->_pagedata[$index];
        $res = isset($pagedata['sitemapper_priority'])
            && $pagedata['sitemapper_priority'] != ''
            ? $pagedata['sitemapper_priority']
            : $this->_defaultPriority;
        return $res;
    }

    /**
     * Returns the sitemap.xml timestamp of the last modification of a subsite.
     *
     * @param string $subsite The name of the subsite.
     *
     * @return string
     */
    public function subsiteLastMod($subsite)
    {
        $contentFolder = $this->_subsiteContentFolder($subsite);
        $contentFile = $contentFolder . 'content.htm';
        $pagedataFile = $contentFolder . 'pagedata.php';
        if (file_exists($pagedataFile)) {
            $res = max(filemtime($contentFile), filemtime($pagedataFile));
        } else {
            $res = filemtime($contentFile);
        }
        $res = $this->_sitemapDate($res);
        return $res;
    }

    /**
     * Returns whether a path points to a subsite.
     *
     * @param string $path The path relative to the base folder.
     *
     * @return bool
     */
    private function _isSubsite($path)
    {
        $baseName = basename($path);
        $res = is_dir($path)
            && (strlen($baseName) == 2
                && (file_exists("$path/.2lang") || is_dir("$path/content"))
                || ($baseName != '2site'
                    && file_exists($path . '/cmsimplesubsite.htm')));
        return $res;
    }

    /**
     * Returns all available subsites and languages.
     *
     * @return array
     */
    public function installedSubsites()
    {
        $res = array($this->_defaultLang);
        $dir = $this->_baseFolder;
        $dh = opendir($dir);
        if ($dh) {
            while (($fn = readdir($dh)) !== false) {
                if ($fn[0] != '.' && $this->_isSubsite($dir . $fn)) {
                    $res[] = $fn;
                }
            }
            closedir($dh);
        }
        sort($res);
        return $res;
    }
}

?>
