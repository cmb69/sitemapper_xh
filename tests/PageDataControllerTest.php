<?php

namespace Sitemapper;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\View;

class PageDataControllerTest extends TestCase
{
    public function testExecute(): void
    {
        $model = $this->createStub(Model::class);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["sitemapper"]);
        $sut = new PageDataController(
            "./plugins/sitemapper/images",
            $model,
            $view
        );
        $pd = [
            "sitemapper_changefreq" => "monthly",
            "sitemapper_priority" => "0.5",
            "sitemapper_include" => "",
        ];
        Approvals::verifyHtml($sut->execute(new FakeRequest(["url" => "http://example.com/?Start"]), $pd));
    }
}
