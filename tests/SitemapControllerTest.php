<?php

namespace Sitemapper;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;
use XH\Pages;
use XH\Publisher;

class SitemapControllerTest extends TestCase
{
    public function testSitemapIndex(): void
    {
        $model = $this->createStub(Model::class);
        $model->method("installedLanguages")->willReturn(["en", "de"]);
        $pages = $this->createStub(Pages::class);
        $publisher = $this->createStub(Publisher::class);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["sitemapper"]);
        $sut = new SitemapController(
            "./",
            "en",
            $model,
            $pages,
            $publisher,
            $view
        );
        $response = $sut->execute(new FakeRequest(), "sitemapper_index");
        $this->assertSame("application/xml; charset=utf-8", $response->contentType());
        Approvals::verifyHtml($response->output());
    }

    public function testLanguageSitemap(): void
    {
        $model = $this->createStub(Model::class);
        $model->method("pagePriority")->willReturnMap([
            [0, "1.0"],
            [1, "0.1"],
        ]);
        $model->method("pageLastMod")->willReturnMap([
            [0, 12345],
            [1, 23456],
        ]);
        $model->method("pageChangefreq")->willReturnMap([
            [0, "daily"],
            [1, "weekly"],
        ]);
        $pages = $this->createStub(Pages::class);
        $pages->method("getCount")->willReturn(2);
        $pages->method("url")->willReturnMap([
            [0, "One"],
            [1, "Two"],
        ]);
        $publisher = $this->createStub(Publisher::class);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["sitemapper"]);
        $sut = new SitemapController(
            "./",
            "en",
            $model,
            $pages,
            $publisher,
            $view
        );
        $response = $sut->execute(new FakeRequest(), "sitemapper_sitemap");
        $this->assertSame("application/xml; charset=utf-8", $response->contentType());
        Approvals::verifyHtml($response->output());
    }
}
