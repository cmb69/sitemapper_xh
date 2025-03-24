<?php

namespace Sitemapper;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoControllerTest extends TestCase
{
    public function testExecute(): void
    {
        $model = $this->createStub(Model::class);
        $model->method("installedLanguages")->willReturn(["en", "de"]);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["sitemapper"]);
        $sut = new InfoController(
            "./",
            "en",
            "./plugins/sitemapper",
            $model,
            new FakeSystemChecker(),
            $view
        );
        Approvals::verifyHtml($sut->execute(new FakeRequest()));
    }
}
