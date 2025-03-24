<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<array{loc:string,lastmod:int,changefreq:string,priority:string}> $urls
 */
?>
<!-- Sitemapper_XH: sitemap -->
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?foreach ($urls as $url):?>
  <url>
    <loc><?=$this->esc($url['loc'])?></loc>
<?  if ($url['lastmod']):?>
    <lastmod><?=$url['lastmod']?></lastmod>
<?  endif?>
    <changefreq><?=$this->esc($url['changefreq'])?></changefreq>
    <priority><?=$this->esc($url['priority'])?></priority>
  </url>
<?endforeach?>
</urlset>
