<?php

/**
 * @copyright 2013-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

namespace Sitemapper;

require './vendor/autoload.php';
require '../../cmsimple/functions.php';

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    protected $sitemapper;

    /**
     * @var object
     */
    protected $hideMock;
    
    /**
     * @var object
     */
    protected $secondLanguagesMock;

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
        $this->sitemapper = new Model('en', vfsStream::url('test/'), $content, $pagedata, true, 'monthly', '0.5');
        $this->hideMock = new \PHPUnit_Extensions_MockFunction('hide', $this->sitemapper);
        $this->hideMock->expects($this->any())->will(
            $this->returnCallback(
                function ($index) {
                    global $c;

                    return preg_match('/\\#CMSimple hide\\#/is', $c[$index]);
                }
            )
        );
        $this->secondLanguagesMock = new \PHPUnit_Extensions_MockFunction('XH_secondLanguages', $this->sitemapper);
        $this->secondLanguagesMock->expects($this->any())->willReturn(['de']);
    }

    protected function setUpVirtualFileSystem()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        mkdir(vfsStream::url('test/content'));
        touch(vfsStream::url('test/content/content.htm'));
        mkdir(vfsStream::url('test/content/de'));
        touch(vfsStream::url('test/content/de/content.htm'));
        mkdir(vfsStream::url('test/de'));
        touch(vfsStream::url('test/de/.2lang'));
        touch(vfsStream::url('test/ab'));
    }

    /**
     * @param int $index
     * @param string $expected
     *
     * @dataProvider dataForTestPageLastMod
     */
    public function testPageLastMod($index, $expected)
    {
        $actual = $this->sitemapper->pageLastMod($index);
        $this->assertEquals($expected, $actual);
    }

    /**
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
     * @param int $index
     * @param string $expected
     *
     * @dataProvider dataForTestPageChangefreq
     */
    public function testPageChangefreq($index, $expected)
    {
        $actual = $this->sitemapper->pageChangefreq($index);
        $this->assertEquals($expected, $actual);
    }

    /**
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
     * @param int $index
     * @param string $expected
     *
     * @dataProvider dataForTestPagePriority
     */
    public function testPagePriority($index, $expected)
    {
        $actual = $this->sitemapper->pagePriority($index);
        $this->assertSame($expected, $actual);
    }

    /**
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
     * @param int $index
     * @param string $expected
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

    public function testInstalledLanguages()
    {
        $expected = array('en', 'de');
        $actual = $this->sitemapper->installedLanguages();
        $this->assertEquals($expected, $actual);
    }

    public function testMainSiteLastMod()
    {
        $filename = vfsStream::url('test/content/content.htm');
        touch($filename);
        $timestamp = filemtime($filename);
        $expected = gmdate('Y-m-d\TH:i:s\Z', $timestamp);
        $actual = $this->sitemapper->languageLastMod('en');
        $this->assertEquals($expected, $actual);
    }

    public function testSubsiteLastMod()
    {
        $filename = vfsStream::url('test/content/de/content.htm');
        touch($filename);
        $timestamp = filemtime($filename);
        $expected = gmdate('Y-m-d\TH:i:s\Z', $timestamp);
        $actual = $this->sitemapper->languageLastMod('de');
        $this->assertEquals($expected, $actual);
    }
}
