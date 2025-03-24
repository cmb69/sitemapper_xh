<?php

namespace Sitemapper;

use PHPUnit\Framework\TestCase;
use XH\PageDataRouter;
use XH\Publisher;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pd_router, $xh_publisher, $cf, $plugin_cf, $plugin_tx, $pth, $c;

        $pd_router = $this->createStub(PageDataRouter::class);
        $xh_publisher = $this->createStub(Publisher::class);
        $cf = ["language" => ["default" => "en"]];
        $plugin_cf = ["sitemapper" => ["ignore_hidden_pages" => "", "changefreq" => "daily", "priority" => "0.5"]];
        $plugin_tx = ["sitemapper" => []];
        $pth = ["folder" => ["base" => "./", "plugins" => "./plugins/"]];
        $c = [];
    }

    public function testMakeInfoController(): void
    {
        $this->assertInstanceOf(InfoController::class, Dic::makeInfoController());
    }

    public function testMakePageDataController(): void
    {
        $this->assertInstanceOf(PageDataController::class, Dic::makePageDataController());
    }

    public function testSitemapController(): void
    {
        $this->assertInstanceOf(SitemapController::class, Dic::makeSitemapController());
    }
}
