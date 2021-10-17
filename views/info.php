<?php

/**
 * @var array<string,array> $sitemaps
 * @var array<string,string> $checks
 * @var array<string,string> $images
 * @var string $version
 */

if (!isset($this)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}
?>
<h1>Sitemapper <?=$version?></h1>
<h2><?=$this->text('sitemaps')?></h2>
<ul>
<?php foreach ($sitemaps as $sitemap):?>
    <li>
        <a href="<?=$sitemap['href']?>" target="_blank"><?=$sitemap['name']?></a>
    </li>
<?php endforeach?>
</ul>
<h2><?=$this->text('syscheck_title')?></h2>
<ul style="list-style: none">
<?php foreach ($checks as $check => $state):?>
    <li>
        <img src="<?=$images[$state]?>" alt="<?=$images[$state]?>"
            style="margin: 0; height: 1em; padding-right: 1em">
        <span><?=$check?></span>
    </li>
<?php endforeach?>
</ul>
