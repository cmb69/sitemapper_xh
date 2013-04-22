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
    var $_defaultLang;

    /**
     * The path of the root folder of the CMSimple_XH installation.
     *
     * @access private
     *
     * @var string
     */
    var $_baseFolder;

    /**
     * The content array.
     *
     * @access private
     *
     * @var array
     */
    var $_content;

    /**
     * The pagedata array.
     *
     * @access private
     *
     * @var array
     */
    var $_pagedata;

    /**
     * Whether hidden pages shall be excluded from the sitemap.
     *
     * @access private
     *
     * @var bool
     */
    var $_excludeHidden;

    /**
     * The default sitemap changefreq.
     *
     * @access private
     *
     * @var string
     */
    var $_defaultChangefreq;

    /**
     * The default sitemap priority.
     *
     * @access private
     *
     * @var float
     */
    var $_defaultPriority;

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
    function Sitemapper_Model($defaultLang, $baseFolder, $content, $pagedata,
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
        $res = $this->_baseFolder;
        if ($subsite != $this->_defaultLang) {
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
        $pagedata = $this->_pagedata[$index];
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
     * @access public
     *
     * @param  int $index  The numeric index of the page.
     */
    function isPageExcluded($index)
    {
        $res = !$this->_isPagePublished($index)
            || $this->_excludeHidden && $this->_isPageHidden($index);
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
        $res = $this->_pagedata[$index]['last_edit'];
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
        $pagedata = $this->_pagedata[$index];
        $res = !empty($pagedata['sitemapper_changefreq'])
            ? $pagedata['sitemapper_changefreq']
            : $this->_defaultChangefreq;
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
        $pagedata = $this->_pagedata[$index];
        $res = isset($pagedata['sitemapper_priority'])
            && $pagedata['sitemapper_priority'] != ''
            ? $pagedata['sitemapper_priority']
            : $this->_defaultPriority;
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
        $res = array($this->_defaultLang);
        $dir = $this->_baseFolder;
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
