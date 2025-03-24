<?php

/**
 * Copyright 2011-2025 Christoph M. Becker
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

use Plib\Request;
use Sitemapper\Dic;
use XH\PageDataRouter;

// phpcs:ignore
if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var PageDataRouter $pd_router
 * @var array<string,array<string,string>> $plugin_tx
 * @var array{folder:array<string,string>,file:array<string,string>} $pth
 * @var string $o
 * @var string $admin
 */

XH_registerStandardPluginMenuItems(false);

$pd_router->add_tab(
    $plugin_tx['sitemapper']['tab'],
    $pth['folder']['plugins'] . 'sitemapper/sitemapper_view.php'
);

if (XH_wantsPluginAdministration('sitemapper')) {
    $o .= print_plugin_admin('off');
    switch ($admin) {
        case '':
            $o .= Dic::makeInfoController()->execute(Request::current());
            break;
        default:
            $o .= plugin_admin_common();
    }
}
