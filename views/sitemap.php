<?php
if (!isset($this)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}
?>
<!-- Sitemapper_XH: sitemap -->
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url):?>
    <url>
        <loc><?=$url['loc']?></loc>
<?php if (!empty($url['lastmod'])):?>
        <lastmod><?=$url['lastmod']?></lastmod>
<?php endif?>
        <changefreq><?=$url['changefreq']?></changefreq>
        <priority><?=$url['priority']?></priority>
    </url>
<?php endforeach?>
</urlset>
