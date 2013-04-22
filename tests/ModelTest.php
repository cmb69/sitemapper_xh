<?php

/**
 * Test case for the model of Sitemapper_XH.
 *
 * @package	SitemapperTests
 * @copyright	Copyright (c) 2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 */


require './classes/model.php';


/*
 * functional dependencies
 */

function hide($index)
{
    global $c;

    return preg_match('/\\#CMSimple hide\\#/is', $c[$index]);
}


class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * The model object.
     *
     * @var object
     */
    private $sitemapper;

    /**
     * Initializes the tests.
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
            array('last_edit' => '0'),
            array(
                'last_edit' => '1366639458',
                'linked_to_menu' => '0',
                'sitemapper_changefreq' => 'daily',
                'sitemapper_priority' => '0.3'
            ),
            array(),
            array('published' => '0'),
            array()
        );
        $this->sitemapper = new Sitemapper_Model(
            'en', './tests/data/', $content, $pagedata, true, 'monthly', '0.5'
        );
    }

    /**
     * Provides data for testPageLastMod().
     *
     * @return array
     */
    public function dataForTestPageLastMod()
    {
        return array(
            array(0, '1970-01-01T00:00:00Z'),
            array(1, '2013-04-22T14:04:18Z')
        );
    }

    /**
     * @dataProvider dataForTestPageLastMod
     */
    public function testPageLastMod($index, $expected)
    {
        $actual = $this->sitemapper->pageLastMod($index);
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
     * @dataProvider dataForTestPageChangefreq
     */
    public function testPageChangefreq($index, $expected)
    {
        $actual = $this->sitemapper->pageChangefreq($index);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Provides data for testPagePriority().
     *
     * @return array
     */
    public function dataForTestPagePriority()
    {
        return array(
            array(0, 0.5),
            array(1, 0.3)
        );
    }

    /**
     * @dataProvider dataForTestPagePriority
     */
    public function testPagePriority($index, $expected)
    {
        $actual = $this->sitemapper->pagePriority($index);
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
     * @dataProvider provideDataForIsPageExcluded
     */
    public function testIsPageExcluded($index, $expected)
    {
        global $c;

        $actual = $this->sitemapper->isPageExcluded($index);
        $this->assertEquals($expected, $actual);
    }

    public function testInstalledSubsites()
    {
        $expected = array('de', 'en', 'subsite');
        $actual = $this->sitemapper->installedSubsites();
        $this->assertEquals($expected, $actual);
    }

    public function testSubsiteLastMod()
    {
        $expected = '2013-04-21T17:10:10Z';
        $actual = $this->sitemapper->subsiteLastMod('de');
        $this->assertEquals($expected, $actual);
    }
}

?>
