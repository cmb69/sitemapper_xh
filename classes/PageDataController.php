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

class PageDataController
{
    /** @var array<string,string> */
    private $pageData;

    /** @var Model */
    private $model;

     /** @var View */
    private $view;

    /**
     * @param array<string,string> $pageData
     */
    public function __construct(array $pageData, Model $model, View $view)
    {
        $this->pageData = $pageData;
        $this->model = $model;
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function execute()
    {
        global $sn, $su, $pth;

        $action = "$sn?$su";
        $helpIcon = $pth['folder']['plugins'] . 'sitemapper/images/help.png';
        $changefreqs = $this->model->changefreqs;
        array_unshift($changefreqs, '');
        $changefreqOptions = [];
        foreach ($changefreqs as $changefreq) {
            $changefreqOptions[] = (object) [
                "name" => $changefreq,
                "selected" => $this->pageData['sitemapper_changefreq'] == $changefreq ? " selected" : "",
            ];
        }
        $priority = $this->pageData['sitemapper_priority'];
        $bag = compact('action', 'helpIcon', 'changefreqOptions', 'priority');
        return $this->view->render('pdtab', $bag);
    }
}
