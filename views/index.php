<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var list<array{loc:string,time:int}> $sitemaps
 */
?>
<!-- Sitemapper_XH: sitemap index -->
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"
              xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?foreach ($sitemaps as $sitemap):?>
  <sitemap>
    <loc><?=$this->esc($sitemap['loc'])?></loc>
    <lastmod><?=$sitemap['time']?></lastmod>
  </sitemap>
<?endforeach?>
</sitemapindex>
