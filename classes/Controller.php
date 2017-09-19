<?php

/**
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

namespace Sitemapper;

class Controller
{
    /**
     * @var Model
     */
    private $model;

    public function __construct()
    {
        global $c, $pth, $cf, $plugin_cf, $pd_router;

        $this->model = new Model(
            $cf['language']['default'],
            $pth['folder']['base'],
            $c,
            $pd_router->find_all(),
            $plugin_cf['sitemapper']['ignore_hidden_pages'],
            $plugin_cf['sitemapper']['changefreq'],
            $plugin_cf['sitemapper']['priority']
        );
    }

    /**
     * @param string $_template
     * @return string
     */
    private function render($_template, array $_bag)
    {
        global $pth, $cf;

        $_template = "{$pth['folder']['plugins']}sitemapper/views/$_template.php";
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
     * @param string $_template
     * @return string
     */
    private function renderXML($_template, array $_bag)
    {
        global $pth;

        $_template = "{$pth['folder']['plugins']}sitemapper/views/$_template.php";
        unset($pth);
        extract($_bag);
        ob_start();
        include $_template;
        $o = ob_get_clean();
        return $o;
    }

    /**
     * @return string
     */
    private function sitemapIndex()
    {
        global $cf;

        $sitemaps = array();
        foreach ($this->model->installedLanguages() as $lang) {
            $base = CMSIMPLE_URL;
            if ($lang != $cf['language']['default']) {
                $base .= $lang . '/';
            }
            $sitemap = array(
                'loc' => $base . '?sitemapper_sitemap',
                'time' => $this->model->languageLastMod($lang)
            );
            array_walk($sitemap, 'XH_hsc');
            $sitemaps[] = $sitemap;
        }
        return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
            . $this->renderXML('index', array('sitemaps' => $sitemaps));
    }

    /**
     * @return string
     */
    private function languageSitemap()
    {
        global $u, $cl, $plugin_cf, $_XH_firstPublishedPage;

        $startpage = isset($_XH_firstPublishedPage) ? $_XH_firstPublishedPage : 0;
        $urls = array();
        for ($i = 0; $i < $cl; $i++) {
            if (!$this->model->isPageExcluded($i)) {
                $separator = $plugin_cf['sitemapper']['clean_urls'] ? '' : '?';
                $priority = $this->model->pagePriority($i);
                $url = array(
                    'loc' => CMSIMPLE_URL
                        . ($i == $startpage ? '' : ($separator . $u[$i])),
                    'lastmod' => $this->model->pageLastMod($i),
                    'changefreq' => $this->model->pageChangefreq($i),
                    'priority' => $priority
                );
                array_walk($url, 'XH_hsc');
                $urls[] = $url;
            }
        }
        return '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
            . $this->renderXML('sitemap', array('urls' => $urls));
    }

    /**
     * @return array
     */
    private function sitemaps()
    {
        global $cf;

        $sitemap = array(
            'name' => 'index',
            'href' => CMSIMPLE_ROOT . '?sitemapper_index'
        );
        $sitemaps = array($sitemap);
        foreach ($this->model->installedLanguages() as $lang) {
            $subdir = $lang != $cf['language']['default'] ? "$lang/" : '';
            $sitemap = array(
                'name' => $lang,
                'href' => CMSIMPLE_ROOT . $subdir . '?sitemapper_sitemap'
            );
            $sitemaps[] = $sitemap;
        }
        return $sitemaps;
    }

    /**
     * @return array
     */
    private function systemChecks()
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['sitemapper'];
        $phpVersion = '5.4.0';
        $xhVersion = '1.6.3';
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        $checks[sprintf($ptx['syscheck_xhversion'], $xhVersion)]
            = version_compare(substr(CMSIMPLE_XH_VERSION, 12), $xhVersion) >= 0 ? 'ok' : 'fail';
        $folders = array();
        foreach (array('config/', 'languages/') as $folder) {
            $folders[] = "{$pth['folder']['plugins']}sitemapper/$folder";
        }
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        return $checks;
    }

    /**
     * @return string
     */
    private function info()
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['sitemapper'];
        $labels = array(
            'sitemaps' => $ptx['sitemaps'],
            'syscheck' => $ptx['syscheck_title'],
            'about' => $ptx['about']
        );
        $sitemaps = $this->sitemaps();
        foreach (array('ok', 'warn', 'fail') as $state) {
            $images[$state] = "{$pth['folder']['plugins']}sitemapper/images/$state.png";
        }
        $checks = $this->systemChecks();
        $icon = $pth['folder']['plugins'] . 'sitemapper/sitemapper.png';
        $version = SITEMAPPER_VERSION;
        $bag = compact('labels', 'sitemaps', 'images', 'checks', 'icon', 'version');
        return $this->render('info', $bag);
    }

    /**
     * @param string $body
     */
    private function respondWithSitemap($body)
    {
        header('HTTP/1.0 200 OK');
        header('Content-Type: application/xml; charset=utf-8');
        echo $body;
        exit;
    }

    private function dispatch()
    {
        global $admin, $action, $plugin, $o, $sitemapper, $f, $sl, $cf;

        if (XH_ADM && XH_wantsPluginAdministration('sitemapper')) {
            $o .= print_plugin_admin('off');
            switch ($admin) {
                case '':
                    $o .= $this->info();
                    break;
                default:
                    $o .= plugin_admin_common($action, $admin, $plugin);
            }
        } elseif (isset($_GET['sitemapper_index']) && $sl == $cf['language']['default']) {
            $f = 'sitemapper_index';
        } elseif (isset($_GET['sitemapper_sitemap'])) {
            $f = 'sitemapper_sitemap';
        }
    }

    public function init()
    {
        global $pth, $plugin_tx, $pd_router;

        $pd_router->add_interest('sitemapper_changefreq');
        $pd_router->add_interest('sitemapper_priority');
        XH_afterPluginLoading(array($this, 'dispatchAfterPluginLoading'));
        if (XH_ADM) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(false);
            }
            $pd_router->add_tab(
                $plugin_tx['sitemapper']['tab'],
                $pth['folder']['plugins'] . 'sitemapper/sitemapper_view.php'
            );
        }
        $this->dispatch();
    }

    /**
     * @return string
     */
    public function pageDataTab(array $pageData)
    {
        global $sn, $su, $pth, $plugin_tx;

        $ptx = $plugin_tx['sitemapper'];
        $action = "$sn?$su";
        $help = array(
            'changefreq' => $ptx['cf_changefreq'],
            'priority' => $ptx['cf_priority']
        );
        $helpIcon = $pth['folder']['plugins'] . 'sitemapper/images/help.png';
        $changefreqOptions = $this->model->changefreqs;
        array_unshift($changefreqOptions, '');
        $changefreqOptions = array_flip($changefreqOptions);
        foreach (array_keys($changefreqOptions) as $opt) {
            $changefreqOptions[$opt]
                = $pageData['sitemapper_changefreq'] == $opt;
        }
        $priority = $pageData['sitemapper_priority'];
        $bag = compact('action', 'helpIcon', 'help', 'changefreqOptions', 'priority');
        return $this->render('pdtab', $bag);
    }

    public function dispatchAfterPluginLoading()
    {
        global $f;

        switch ($f) {
            case 'sitemapper_index':
                $this->respondWithSitemap($this->sitemapIndex());
                break;
            case 'sitemapper_sitemap':
                $this->respondWithSitemap($this->languageSitemap());
                break;
        }
    }
}
