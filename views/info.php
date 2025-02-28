<?php

use Sitemapper\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this;
 * @var array<int,stdClass> $sitemaps
 * @var array<int,stdClass> $checks
 * @var string $version
 */
?>
<h1>Sitemapper <?=$this->esc($version)?></h1>
<h2><?=$this->text('sitemaps')?></h2>
<ul>
<?php foreach ($sitemaps as $sitemap):?>
    <li>
        <a href="<?=$this->esc($sitemap->href)?>" target="_blank"><?=$this->esc($sitemap->name)?></a>
    </li>
<?php endforeach?>
</ul>
<h2><?=$this->text('syscheck_title')?></h2>
<?php foreach ($checks as $check):?>
    <p class="<?=$this->esc($check->class)?>"><?=$this->esc($check->label)?></p>
<?php endforeach?>
