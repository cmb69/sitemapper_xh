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
