<?php

require_once './vendor/autoload.php';

require_once '../../cmsimple/classes/PageDataRouter.php';
require_once '../../cmsimple/classes/Pages.php';
require_once '../../cmsimple/classes/Publisher.php';
require_once '../../cmsimple/functions.php';

require_once '../plib/classes/Request.php';
require_once '../plib/classes/Response.php';
require_once '../plib/classes/SystemChecker.php';
require_once '../plib/classes/Url.php';
require_once '../plib/classes/View.php';
require_once '../plib/classes/FakeRequest.php';
require_once '../plib/classes/FakeSystemChecker.php';

require_once './classes/Dic.php';
require_once './classes/InfoController.php';
require_once './classes/Model.php';
require_once './classes/PageDataController.php';
require_once './classes/SitemapController.php';

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const CMSIMPLE_ROOT = "/";
const CMSIMPLE_URL = "http://example.com/";
const SITEMAPPER_VERSION = "3.3-dev";
