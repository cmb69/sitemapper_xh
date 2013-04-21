<?php


class Sitemapper_Model
{
    var $defaultLang;

    var $baseFolder;

    var $content;

    var $pagedata;

    var $excludeHidden = true; // TODO: config option

    var $defaultChangefreq = 'monthly'; // TODO: config option

    var $defaultPriority = 0.5; // TODO: config option

    function Sitemapper_Model($defaultLang, $baseFolder, $content, $pagedata)
    {
        $this->defaultLang = $defaultLang;
        $this->baseFolder = $baseFolder;
        $this->content = $content;
        $this->pagedata = $pagedata;
    }

    /**
     * Returns sitemap conforming timestamp.
     *
     * @param  int $timestamp
     * @return string
     */
    function _sitemapDate($timestamp)
    {
        $res = date('Y-m-d\TH:i:sO', $timestamp);
        $res = substr($res, 0, strlen($res)-2) . ':' . substr($res, -2);
        return $res;
    }

    function _subsiteContentFolder($subsite)
    {
        $res = $this->baseFolder;
        if ($subsite != $this->defaultLang) {
            $res .= $subsite . '/';
        }
        $res .= 'content/';
        return $res;
    }

    function _isPageHidden($index)
    {
        $res = $this->pagedata[$index]['linked_to_menu'] == '0'
            || hide($index);
        return $res;
    }

    function _isPagePublished($index)
    {
        $res = $this->pagedata[$index]['published'] != '0'
            && $this->content[$index] != '#CMSimple hide#';
        return $res;
    }

    /**
     *
     */
    function isPageExcluded($index)
    {
        $res = !$this->_isPagePublished($index)
            || $this->excludeHidden && $this->_isPageHidden($index);
        return $res;
    }

    function pageLastMod($index)
    {
        $res = $this->pagedata[$index]['last_edit'];
        $res = $this->_sitemapDate($res);
        return $res;
    }

    function pageChangefreq($index)
    {
        $res = $this->pagedata[$index]['sitemapper_changefreq'];
        if (empty($res)) {
            $res = $this->defaultChangefreq;
        }
        return $res;
    }

    function pagePriority($index)
    {
        $res = $this->pagedata[$index]['sitemapper_priority'];

    }

    function subsiteLastMod($subsite)
    {
        $contentFolder = $this->_subsiteContentFolder($subsite);
        $contentFile = $contentFolder . 'content.htm';
        $pagedataFile = $contentFolder . 'pagedata.php';
        $res = max(filemtime($contentFile), filemtime($pagedataFile));
        $res = $this->_sitemapDate($res);
        return $res;
    }

    function _isSubsite($path)
    {
        $baseName = basename($path);
        $res = strlen($baseName) == 2
            || ($baseName != '2site' && is_dir($path)
                && file_exists($path . '/cmsimplesubsite.htm'));
        return $res;
    }

    /**
     * Returns all installed subsites (incl. languages).
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
        return $res;

    }
}
