<?php

namespace Sitemapper;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

use XH\PageDataRouter;
use XH\Publisher;

class ModelTest extends TestCase
{
    /**
     * @var Model
     */
    protected $sitemapper;

    public function setUp(): void
    {
        $this->setUpVirtualFileSystem();
        $pageDataRouter = $this->createStub(PageDataRouter::class);
        $pageDataRouter->method("find_page")->willReturnMap([
            [0, ['last_edit' => '']],
            [1, [
                'last_edit' => '1366639458',
                'linked_to_menu' => '0',
                'sitemapper_changefreq' => 'daily',
                'sitemapper_priority' => '0.3',
                'sitemapper_include' => 'yes',
            ]],
            [2, [
                'sitemapper_priority' => '1.0'
            ]],
            [3, ['published' => '0']],
            [4, [
                'sitemapper_include' => 'no',
            ]],
        ]);
        $publisher = $this->createStub(Publisher::class);
        $publisher->method("isHidden")->willReturnMap([[0, false], [1, true], [2, true], [3, false], [4, false]]);
        $publisher->method("isPublished")->willReturnMap([[0, true], [1, true], [2, true], [3, false], [4, true]]);
        $this->sitemapper = new Model('en', ['de'], vfsStream::url('test/'), $pageDataRouter, $publisher, true, 'monthly', '0.5');
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
            array(1, false),
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
