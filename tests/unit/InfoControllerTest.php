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

class InfoControllerTest extends TestCase
{
    public function testExecute(): void
    {
        $model = $this->createStub(Model::class);
        $model->method("installedLanguages")->willReturn(["en", "de"]);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["sitemapper"]);
        $sut = new InfoController(
            "/",
            "en",
            "./plugins/sitemapper",
            "CMSimple_XH 1.8",
            $model,
            $view
        );
        Approvals::verifyHtml($sut->execute());
    }
}
