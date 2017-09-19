<!-- Sitemapper_XH: sitemap index -->
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"
              xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($sitemaps as $sitemap):?>
    <sitemap>
        <loc><?=$sitemap['loc']?></loc>
        <lastmod><?=$sitemap['time']?></lastmod>
    </sitemap>
<?php endforeach?>
</sitemapindex>
