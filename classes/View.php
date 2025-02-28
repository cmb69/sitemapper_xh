<?php

/**
 * Copyright 2013-2021 Christoph M. Becker
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

class View
{
    /** @var string */
    private $templateDir;

    /** @var array<string,string> */
    private $lang;

    /** @var string */
    private $template;

    /** @var array<string,mixed> */
    private $data;

    /** @param array<string,string> $lang */
    public function __construct(string $templateDir, array $lang)
    {
        $this->templateDir = $templateDir;
        $this->lang = $lang;
    }

    /** @param array<string,mixed>  $data */
    public function render(string $template, array $data): string
    {
        $this->template = "$this->templateDir/$template.php";
        $this->data = $data;
        return $this->doRender();
    }

    private function doRender(): string
    {
        array_walk_recursive($this->data, function (&$value) {
            assert(is_null($value) || is_scalar($value) || $value instanceof HtmlString);
            if (is_string($value)) {
                $value = XH_hsc($value);
            } elseif ($value instanceof HtmlString) {
                $value = $value->value();
            }
        });
        extract($this->data);
        ob_start();
        include $this->template;
        return (string) ob_get_clean();
    }

    /** @param mixed $args */
    public function text(string $key, ...$args): string
    {
        return $this->esc(vsprintf($this->lang[$key], $args));
    }

    /** @param mixed $value */
    private function esc($value): string
    {
        if ($value instanceof HtmlString) {
            return $value->value();
        }
        return XH_hsc($value);
    }
}
