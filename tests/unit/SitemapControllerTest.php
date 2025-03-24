<?php

/**
 * Copyright 2025 Christoph M. Becker
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

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
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
            "http://example.com/",
            "en",
            XH_includeVar("./config/config.php", "plugin_cf")["sitemapper"],
            $model,
            $pages,
            $publisher,
            $view,
            function ($body) {
                Approvals::verifyHtml($body);
            }
        );
        $sut->execute("sitemapper_index");
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
            "http://example.com/",
            "en",
            XH_includeVar("./config/config.php", "plugin_cf")["sitemapper"],
            $model,
            $pages,
            $publisher,
            $view,
            function ($body) {
                Approvals::verifyHtml($body);
            }
        );
        $sut->execute("sitemapper_sitemap");
    }
}
