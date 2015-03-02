<?php

/**
 * Testing the model.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Sitemapper
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2015 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */

require './vendor/autoload.php';
require './classes/model.php';

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

/**
 * Stub for hide().
 *
 * @param int $index A page index.
 *
 * @return bool
 */
function hide($index)
{
    global $c;

    return preg_match('/\\#CMSimple hide\\#/is', $c[$index]);
}

/**
 * Testing the info view.
 *
 * @category Testing
 * @package  Sitemapper
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Sitemapper_XH
 */
class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * The model object.
     *
     * @var Sitemapper_Model
     */
    protected $sitemapper;

    /**
     * Sets up the text fixture.
     *
     * @return void
     */
    public function setUp()
    {
        global $c;

        $content = array(
            'Lorem ipsum',
            'Lorem ipsum',
            'Lorem #cmsimple hide# ipsum',
            'Lorem ipsum',
            '#cmsimple hide#'
        );
        $c = $content;
        $pagedata = array(
            array('last_edit' => ''),
            array(
                'last_edit' => '1366639458',
                'linked_to_menu' => '0',
                'sitemapper_changefreq' => 'daily',
                'sitemapper_priority' => '0.3'
            ),
            array(
                'sitemapper_priority' => '1.0'
            ),
            array('published' => '0'),
            array()
        );
        $this->setUpVirtualFileSystem();
        $this->sitemapper = new Sitemapper_Model(
            'en', vfsStream::url('test/'), $content, $pagedata, true, 'monthly', '0.5'
        );
    }

    /**
     * Sets up the virtual file system fixture.
     *
     * @return void
     */
    protected function setUpVirtualFileSystem()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        mkdir(vfsStream::url('test/de'));
        mkdir(vfsStream::url('test/de/content'));
        touch(vfsStream::url('test/de/content/content.htm'));
        touch(vfsStream::url('test/de/content/pagedata.php'));
        mkdir(vfsStream::url('test/subsite'));
        touch(vfsStream::url('test/subsite/cmsimplesubsite.htm'));
        touch(vfsStream::url('test/ab'));
    }

    /**
     * Tests ::pageLastMod().
     *
     * @param int    $index    A page index.
     * @param string $expected Expected result.
     *
     * @return void
     *
     * @dataProvider dataForTestPageLastMod
     */
    public function testPageLastMod($index, $expected)
    {
        $actual = $this->sitemapper->pageLastMod($index);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Provides data for testPageLastMod().
     *
     * @return array
     */
    public function dataForTestPageLastMod()
    {
        return array(
            array(0, false),
            array(1, '2013-04-22T14:04:18Z')
        );
    }

    /**
     * Tests ::pageChangefreq().
     *
     * @param int    $index    A page index.
     * @param string $expected Expected result.
     *
     * @return void
     *
     * @dataProvider dataForTestPageChangefreq
     */
    public function testPageChangefreq($index, $expected)
    {
        $actual = $this->sitemapper->pageChangefreq($index);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Provides data for testPageChangefreq().
     *
     * @return array
     */
    public function dataForTestPageChangefreq()
    {
        return array(
            array(0, 'monthly'),
            array(1, 'daily')
        );
    }

    /**
     * Tests ::pagePriority().
     *
     * @param int    $index    A page index.
     * @param string $expected Expected result.
     *
     * @return void
     *
     * @dataProvider dataForTestPagePriority
     */
    public function testPagePriority($index, $expected)
    {
        $actual = $this->sitemapper->pagePriority($index);
        $this->assertSame($expected, $actual);
    }

    /**
     * Provides data for testPagePriority().
     *
     * @return array
     */
    public function dataForTestPagePriority()
    {
        return array(
            array(0, '0.5'),
            array(1, '0.3'),
            array(2, '1.0')
        );
    }

    /**
     * Tests ::isPageExcluded().
     *
     * @param int    $index    A page index.
     * @param string $expected Expected result.
     *
     * @return void
     *
     * @dataProvider provideDataForIsPageExcluded
     */
    public function testIsPageExcluded($index, $expected)
    {
        global $c;

        $actual = $this->sitemapper->isPageExcluded($index);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Provides data for testIsPageExcluded().
     *
     * @return array
     */
    public function provideDataForIsPageExcluded()
    {
        return array(
            array(0, false),
            array(1, true),
            array(2, true),
            array(3, true),
            array(4, true)
        );
    }

    /**
     * Tests the installed subsites.
     *
     * @return void
     */
    public function testInstalledSubsites()
    {
        $expected = array('de', 'en', 'subsite');
        $actual = $this->sitemapper->installedSubsites();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests the last modification time of a subsite.
     *
     * @return void
     */
    public function testSubsiteLastMod()
    {
        $filename = vfsStream::url('test/de/content/pagedata.php');
        touch($filename);
        $timestamp = filemtime($filename);
        $expected = gmdate('Y-m-d\TH:i:s\Z', $timestamp);
        $actual = $this->sitemapper->subsiteLastMod('de');
        $this->assertEquals($expected, $actual);
    }
}

?>
