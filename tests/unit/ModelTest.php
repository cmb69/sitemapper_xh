<?php

/**
 * Copyright 2013-2017 Christoph M. Becker
 *
 * This file is part of Sitemapper_XH.
 *
 * Sitemapper_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Sitemapper_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Sitemapper_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Sitemapper;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

class ModelTest extends TestCase
{
    /**
     * @var Model
     */
    protected $sitemapper;

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
        uopz_set_return('hide', function ($index) {
            global $c;
        
        
            return preg_match('/\\#CMSimple hide\\#/is', $c[$index]);
        }, true);
        uopz_set_return('XH_secondLanguages', ['de']);
    }

    protected function tearDown()
    {
        uopz_unset_return('hide');
        uopz_unset_return('XH_secondLanguages');
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
