<?php

/**
 * Copyright 2011-2021 Christoph M. Becker
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

use Plib\Request;
use Plib\View;

class PageDataController
{
    /** @var string */
    private $imageDir;

    /** @var Model */
    private $model;

     /** @var View */
    private $view;

    public function __construct(string $imageDir, Model $model, View $view)
    {
        $this->imageDir = $imageDir;
        $this->model = $model;
        $this->view = $view;
    }

    /** @param array<string,string> $pageData */
    public function execute(Request $request, array $pageData): string
    {
        $action = $request->url()->relative();
        $helpIcon = "$this->imageDir/help.png";
        $changefreqs = $this->model->changefreqs;
        array_unshift($changefreqs, '');
        $changefreqOptions = [];
        foreach ($changefreqs as $changefreq) {
            $changefreqOptions[] = [
                "name" => $changefreq,
                "selected" => $pageData['sitemapper_changefreq'] == $changefreq ? " selected" : "",
            ];
        }
        $priority = $pageData['sitemapper_priority'];
        $bag = compact('action', 'helpIcon', 'changefreqOptions', 'priority');
        return $this->view->render('pdtab', $bag);
    }
}
