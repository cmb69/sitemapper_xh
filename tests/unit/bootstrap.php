<?php

/**
 * Copyright 2021 Christoph M. Becker
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

require_once './vendor/autoload.php';
require_once '../../cmsimple/classes/PageDataRouter.php';
require_once '../../cmsimple/classes/Pages.php';
require_once '../../cmsimple/classes/Publisher.php';
require_once '../../cmsimple/functions.php';
require_once './classes/Dic.php';
require_once './classes/HtmlString.php';
require_once './classes/InfoController.php';
require_once './classes/Model.php';
require_once './classes/PageDataController.php';
require_once './classes/SitemapController.php';
require_once './classes/View.php';

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const CMSIMPLE_ROOT = "/";
const CMSIMPLE_URL = "http://example.com/";
const SITEMAPPER_VERSION = "3.1-dev";
