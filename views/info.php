<?php

use Sitemapper\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this;
 * @var list<array{name:string,href:string}> $sitemaps
 * @var list<array{label:string,class:string}> $checks
 * @var string $version
 */
?>
<div>
  <h1>Sitemapper <?=$version?></h1>
  <h2><?=$this->text('sitemaps')?></h2>
  <ul>
<?foreach ($sitemaps as $sitemap):?>
    <li>
      <a href="<?=$sitemap['href']?>" target="_blank"><?=$sitemap['name']?></a>
    </li>
<?endforeach?>
  </ul>
  <h2><?=$this->text('syscheck_title')?></h2>
<?foreach ($checks as $check):?>
  <p class="<?=$check['class']?>"><?=$check['label']?></p>
<?endforeach?>
</div>
