<?php

/**
 * Controller of Sitemapper_XH.
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

/**
 * The fully qualified absolute URL of the current (sub)site.
 */
define(
    'SITEMAPPER_URL',
    'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://'
    . (empty($plugin_cf['sitemapper']['canonical_hostname'])
        ? $_SERVER['HTTP_HOST']
        : $plugin_cf['sitemapper']['canonical_hostname']
        . ($_SERVER['SERVER_PORT'] < 1024 ? '' : ':' . $_SERVER['SERVER_PORT']))
    . preg_replace('/index.php$/', '', $sn)
);

/**
 * The controller class.
 *
 * @category CMSimple_XH
 * @package  Sitemapper
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */
class Sitemapper_Controller
{
    /**
     * The model.
     *
     * @access private
     *
     * @var object
     */
    var $_model;

    /**
     * Initializes a controller.
     *
     * @return void
     *
     * @global array The content of the pages.
     * @global array The paths of system files and folders.
     * @global array The configuration of the core.
     * @global array The configuration of the plugins.
     * @global array The page data router.
     *
     * @access public
     */
    function Sitemapper_Controller()
    {
        global $c, $pth, $cf, $plugin_cf, $pd_router;

        $this->_model = new Sitemapper_Model(
            $cf['language']['default'], $pth['folder']['base'],
            $c, $pd_router->find_all(),
            $plugin_cf['sitemapper']['ignore_hidden_pages'],
            $plugin_cf['sitemapper']['changefreq'],
            $plugin_cf['sitemapper']['priority']
        );
    }

    /**
     * Returns a string with special HTML characters escaped.
     *
     * @param string $str A string.
     *
     * @return string
     *
     * @access private
     */
    function _hsc($str)
    {
        return htmlspecialchars($str, ENT_COMPAT, 'UTF_8');
    }

    /**
     * Renders a template.
     *
     * @param string $_template The name of the template.
     * @param array  $_bag      Variables available in the template.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the core.
     *
     * @access private
     */
    function _render($_template, $_bag)
    {
        global $pth, $cf;

        $_template = $pth['folder']['plugins'] . 'sitemapper/views/'
            . $_template . '.htm';
        $_xhtml = $cf['xhtml']['endtags'];
        unset($pth, $cf);
        extract($_bag);
        ob_start();
        include $_template;
        $o = ob_get_clean();
        if (!$_xhtml) {
            $o = str_replace('/>', '>', $o);
        }
        return $o;
    }

    /**
     * Renders an XML template.
     *
     * @param string $_template The name of the template.
     * @param array  $_bag      Variables available in the template.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     *
     * @access private
     */
    function _renderXML($_template, $_bag)
    {
        global $pth;

        $_template = $pth['folder']['plugins'] . 'sitemapper/views/'
            . $_template . '.xml';
        unset($pth, $cf);
        extract($_bag);
        ob_start();
        include $_template;
        $o = ob_get_clean();
        return $o;
    }

    /**
     * Returns the sitemap index.
     *
     * @return string XML.
     *
     * @global array The configuration of the core.
     *
     * @access private
     */
    function _sitemapIndex()
    {
        global $cf;

        $sitemaps = array();
        foreach ($this->_model->installedSubsites() as $ss) {
            $base = SITEMAPPER_URL;
            if ($ss != $cf['language']['default']) {
                $base .= $ss . '/';
            }
            $sitemap = array(
                'loc' => $base . '?sitemapper_sitemap',
                'time' => $this->_model->subsiteLastMod($ss)
            );
            array_walk($sitemap, array($this, '_hsc'));
            $sitemaps[] = $sitemap;
        }
        return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
            . $this->_renderXML('index', array('sitemaps' => $sitemaps));
    }

    /**
     * Returns the sitemap of the current subsite/language.
     *
     * @return string XML.
     *
     * @global array The "URLs" of the pages.
     * @global int   The number of pages.
     * @global array The configuration of the plugins.
     *
     * @access private
     */
    function _subsiteSitemap()
    {
        global $u, $cl, $plugin_cf;

        $urls = array();
        for ($i = 0; $i < $cl; $i++) {
            if (!$this->_model->isPageExcluded($i)) {
                $seperator = $plugin_cf['sitemapper']['clean_urls'] ? '' : '?';
                $priority = $this->_model->pagePriority($i);
                $url = array(
                    'loc' => SITEMAPPER_URL . $seperator . $u[$i],
                    'lastmod' => $this->_model->pageLastMod($i),
                    'changefreq' => $this->_model->pageChangefreq($i),
                    'priority' => $priority
                );
                array_walk($url, array($this, '_hsc'));
                $urls[] = $url;
            }
        }
        return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
            . $this->_renderXML('sitemap', array('urls' => $urls));
    }

    /**
     * Returns the sitemaps.
     *
     * @return array
     *
     * @global array The configuration of the core.
     *
     * @access private
     */
    function _sitemaps()
    {
        global $cf;

        $sitemap = array(
            'name' => 'index',
            'href' => CMSIMPLE_ROOT . '?sitemapper_index'
        );
        $sitemaps = array($sitemap);
        foreach ($this->_model->installedSubsites() as $ss) {
            $subdir = $ss != $cf['language']['default'] ? $ss.'/' : '';
            $sitemap = array(
                'name' => $ss,
                'href' => CMSIMPLE_ROOT . $subdir . '?sitemapper_sitemap'
            );
            $sitemaps[] = $sitemap;
        }
        return $sitemaps;
    }

    /**
     * Returns the system checks.
     *
     * @return array
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the core.
     * @global array The localization of the plugins.
     *
     * @access private
     */
    function _systemChecks()
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['sitemapper'];
        $phpVersion = '4.3.10';
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (array('pcre') as $ext) {
            $checks[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($ext) ? 'ok' : 'fail';
        }
        $checks[$ptx['syscheck_magic_quotes']]
            = !get_magic_quotes_runtime() ? 'ok' : 'fail';
        $checks[$ptx['syscheck_encoding']]
            = strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
        $folders = array();
        foreach (array('config/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'sitemapper/' . $folder;
        }
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        return $checks;
    }

    /**
     * Returns the plugin information view.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     *
     * @access private
     */
    function _info()
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['sitemapper'];
        $labels = array(
            'sitemaps' => $ptx['sitemaps'],
            'syscheck' => $ptx['syscheck_title'],
            'about' => $ptx['about']
        );
        $sitemaps = $this->_sitemaps();
        foreach (array('ok', 'warn', 'fail') as $state) {
            $images[$state] = $pth['folder']['plugins']
                . 'sitemapper/images/' . $state . '.png';
        }
        $checks = $this->_systemChecks();
        $icon = $pth['folder']['plugins'] . 'sitemapper/sitemapper.png';
        $version = SITEMAPPER_VERSION;
        $bag = compact(
            'labels', 'sitemaps', 'images', 'checks', 'icon', 'version'
        );
        return $this->_render('info', $bag);
    }

    /**
     * Sends a sitemap as HTTP response.
     *
     * @param string $body The response body.
     *
     * @return void
     *
     * @access private
     */
    function _respondWithSitemap($body)
    {
        header('HTTP/1.0 200 OK');
        header('Content-Type: application/xml; charset=utf-8');
        echo $body;
        exit;
    }

    /**
     * Dispatches on Sitemapper related requests.
     *
     * @return void
     *
     * @global string The value of the "admin" GET or POST parameter.
     * @global string The value of the "action" GET or POST parameter.
     * @global string The name of the plugin.
     * @global string The (X)HTML to be placed in the contents area.
     * @global string Whether the plugin administration is requested.
     * @global string The requested special functionality.
     * @global string The current language.
     * @global array  The configuration of the core.
     *
     * @access private
     */
    function _dispatch()
    {
        global $admin, $action, $plugin, $o, $sitemapper, $f, $sl, $cf;

        if (XH_ADM && isset($sitemapper) && $sitemapper == 'true') {
            $o .= print_plugin_admin('off');
            switch ($admin) {
            case '':
                $o .= $this->_info();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, $plugin);
            }
        } elseif (isset($_GET['sitemapper_index'])
            && $sl == $cf['language']['default']
        ) {
            $f = 'sitemapper_index';
        } elseif (isset($_GET['sitemapper_sitemap'])) {
            $f = 'sitemapper_sitemap';
        }
    }

    /**
     * Initializes the controller object.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     * @global array  The localization of the plugins.
     * @global object The page data router.
     *
     * @access public
     */
    function init()
    {
        global $pth, $plugin_tx, $pd_router;

        $pd_router->add_interest('sitemapper_changefreq');
        $pd_router->add_interest('sitemapper_priority');
        if (function_exists('XH_afterPluginLoading')) {
            XH_afterPluginLoading(array($this, 'dispatchAfterPluginLoading'));
        }
        if (XH_ADM) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(false);
            }
            $pd_router->add_tab(
                $plugin_tx['sitemapper']['tab'],
                $pth['folder']['plugins'] . 'sitemapper/sitemapper_view.php'
            );
        }
        $this->_dispatch();
    }

    /**
     * Returns the page data tab view.
     *
     * @param array $pageData The page's data.
     *
     * @return string (X)HTML.
     *
     * @global string The "URL" of the currently selected page.
     * @global array  The paths of system files and folders.
     * @global array  The localization of the plugins.
     *
     * @access public
     */
    function pageDataTab($pageData)
    {
        global $sn, $su, $pth, $plugin_tx;

        $ptx = $plugin_tx['sitemapper'];
        $action = $sn . '?' . $su;
        $help = array(
            'changefreq' => $ptx['cf_changefreq'],
            'priority' => $ptx['cf_priority']
        );
        $helpIcon = $pth['folder']['plugins'] . 'sitemapper/images/help.png';
        $changefreqOptions = $this->_model->changefreqs;
        array_unshift($changefreqOptions, '');
        $changefreqOptions = array_flip($changefreqOptions);
        foreach ($changefreqOptions as $opt => $dummy) {
            $changefreqOptions[$opt]
                = $pageData['sitemapper_changefreq'] == $opt;
        }
        $priority = $pageData['sitemapper_priority'];
        $bag = compact(
            'action', 'helpIcon', 'help', 'changefreqOptions', 'priority'
        );
        return $this->_render('pdtab', $bag);
    }

    /**
     * Dispatches to sitemap requests.
     *
     * @return void
     *
     * @global string The requested special functionality.
     *
     * @access public
     */
    function dispatchAfterPluginLoading()
    {
        global $f;

        switch ($f) {
        case 'sitemapper_index':
            $this->_respondWithSitemap($this->_sitemapIndex());
            break;
        case 'sitemapper_sitemap':
            $this->_respondWithSitemap($this->_subsiteSitemap());
            break;
        }
    }
}

?>
