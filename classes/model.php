<?php

/**
 * Model of Sitemapper_XH.
 *
 * @package	Sitemapper
 * @copyright	Copyright (c) 2011-2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 * @link	http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */


/**
 * The model class.
 *
 * @package Sitemapper
 */
class Sitemapper_Model
{
    /**
     *
     * @access public
     *
     * @var array
     */
    var $changefreqs = array('always', 'hourly', 'daily', 'weekly', 'monthly',
                         'yearly', 'never');

    /**
     * The default language of CMSimple_XH.
     *
     * @access private
     *
     * @var string
     */
    var $defaultLang;

    /**
     * The path of the root folder of the CMSimple_XH installation.
     *
     * @access private
     *
     * @var string
     */
    var $baseFolder;

    /**
     * The content array.
     *
     * @access private
     *
     * @var array
     */
    var $content;

    /**
     * The pagedata array.
     *
     * @access private
     *
     * @var array
     */
    var $pagedata;

    var $excludeHidden = true; // TODO: config option

    var $defaultChangefreq = 'monthly'; // TODO: config option

    var $defaultPriority = 0.5; // TODO: config option

    /**
     * Constructs a sitemapper model object.
     *
     * @access public
     *
     * @param  string $defaultLang
     * @param  string $baseFolder
     * @param  array $content
     * @param  array $pagedata
     * @return void
     */
    function Sitemapper_Model($defaultLang, $baseFolder, $content, $pagedata)
    {
        $this->defaultLang = $defaultLang;
        $this->baseFolder = $baseFolder;
        $this->content = $content;
        $this->pagedata = $pagedata;
    }

    /**
     * Returns a sitemap.xml conforming timestamp.
     *
     * @param  int $timestamp
     * @return string
     */
    function _sitemapDate($timestamp)
    {
        $res = gmdate('Y-m-d\TH:i:s\Z', $timestamp);
        return $res;
    }

    /**
     * Returns the path of a subsites' content folder.
     *
     * @access private
     *
     * @param  string $subsite
     * @return string
     */
    function _subsiteContentFolder($subsite)
    {
        $res = $this->baseFolder;
        if ($subsite != $this->defaultLang) {
            $res .= $subsite . '/';
        }
        $res .= 'content/';
        return $res;
    }

    /**
     * Returns whether a page is hidden.
     *
     * @access private
     *
     * @param  int $index  The numeric index of the page.
     * @return bool
     */
    function _isPageHidden($index)
    {
        $pagedata = $this->pagedata[$index];
        $res = (isset($pagedata['linked_to_menu'])
                && $pagedata['linked_to_menu'] == '0')
            || hide($index);
        return $res;
    }

    /**
     * Returns whether a page is published.
     *
     * @access private
     *
     * @param  int $index  The numeric index of the page.
     * @return bool.
     */
    function _isPagePublished($index)
    {
        $pagedata = $this->pagedata[$index];
        $res = (!isset($pagedata['published']) || $pagedata['published'] != '0')
            && $this->content[$index] != '#CMSimple hide#';
        return $res;
    }

    /**
     * Returns whether a page shall be excluded from the sitemap,
     * either because it is unpublished, or because it is hidden
     * and hidden pages shall be excluded.
     *
     * @access public
     *
     * @param  int $index  The numeric index of the page.
     */
    function isPageExcluded($index)
    {
        $res = !$this->_isPagePublished($index)
            || $this->excludeHidden && $this->_isPageHidden($index);
        return $res;
    }

    /**
     * Returns the sitemap.xml timestamp of the last modification of a page.
     *
     * @access public
     *
     * @param  int $index  The numeric index of the page.
     * @return string
     */
    function pageLastMod($index)
    {
        $res = $this->pagedata[$index]['last_edit'];
        $res = $this->_sitemapDate($res);
        return $res;
    }

    /**
     * Returns the sitemap.xml changefreq of a page.
     *
     * @access public
     *
     * @param  int $index  The numeric index of the page.
     * @return string
     */
    function pageChangefreq($index)
    {
        $pagedata = $this->pagedata[$index];
        $res = !empty($pagedata['sitemapper_changefreq'])
            ? $pagedata['sitemapper_changefreq']
            : $this->defaultChangefreq;
        return $res;
    }

    /**
     * Returns the sitemap.xml priority of a page.
     *
     * @access public
     *
     * @param  int $index  The numeric index of the page.     *
     * @return float
     */
    function pagePriority($index)
    {
        $pagedata = $this->pagedata[$index];
        $res = isset($pagedata['sitemapper_priority'])
            && $pagedata['sitemapper_priority'] != ''
            ? $pagedata['sitemapper_priority']
            : $this->defaultPriority;
        return floatval($res);
    }

    /**
     * Returns the sitemap.xml timestamp of the last modification of a subsite.
     *
     * @access public
     *
     * @param  string $subsite  The name of the subsite.
     * @return string
     */
    function subsiteLastMod($subsite)
    {
        $contentFolder = $this->_subsiteContentFolder($subsite);
        $contentFile = $contentFolder . 'content.htm';
        $pagedataFile = $contentFolder . 'pagedata.php';
        $res = max(filemtime($contentFile), filemtime($pagedataFile));
        $res = $this->_sitemapDate($res);
        return $res;
    }

    /**
     * Returns whether a path points to a subsite.
     *
     * @access private
     *
     * @param  string $path  The path relative to the base folder.
     * @return bool
     */
    function _isSubsite($path)
    {
        $baseName = basename($path);
        $res = is_dir($path)
            && (strlen($baseName) == 2
                || ($baseName != '2site'
                    && file_exists($path . '/cmsimplesubsite.htm')));
        return $res;
    }

    /**
     * Returns all available subsites and languages.
     *
     * @return array
     */
    function installedSubsites()
    {
        $res = array($this->defaultLang);
        $dir = $this->baseFolder;
        $dh = opendir($dir);
        while (($fn = readdir($dh)) !== false) {
            if ($fn[0] != '.' && $this->_isSubsite($dir . $fn)) {
                $res[] = $fn;
            }
        }
        closedir($dh);
        sort($res);
        return $res;

    }
}

?>
