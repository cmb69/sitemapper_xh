<?php
if (!isset($this)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}
?>
<!-- Sitemapper_XH: info -->
<h4><?=$this->text('sitemaps')?></h4>
<ul>
<?php foreach ($sitemaps as $sitemap):?>
    <li>
        <a href="<?=$sitemap['href']?>" target="_blank"><?=$sitemap['name']?></a>
    </li>
<?php endforeach?>
</ul>
<h4><?=$this->text('syscheck_title')?></h4>
<ul style="list-style: none">
<?php foreach ($checks as $check => $state):?>
    <li>
        <img src="<?=$images[$state]?>" alt="<?=$images[$state]?>"
            style="margin: 0; height: 1em; padding-right: 1em">
        <span><?=$check?></span>
    </li>
<?php endforeach?>
</ul>
<h4><?=$this->text('about')?></h4>
<img src="<?=$icon?>" style="float: left; width: 128px; height: 128px;
margin-right: 16px" alt="XML folder">
<p>Version: <?=$version?></p>
<p>Copyright &copy; 2011-2021 <a href="http://3-magi.net/">Christoph M. Becker</a></p>
<p style="text-align: justify">This program is free software: you can
redistribute it and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.</p>
<p style="text-align: justify">This program is distributed in the hope that it
will be useful, but <em>without any warranty</em>; without even the implied
warranty of <em>merchantability</em> or <em>fitness for a particular
purpose</em>. See the GNU General Public License for more details.</p>
<p style="text-align: justify">You should have received a copy of the GNU
General Public License along with this program. If not, see <a
href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>
