<?php

use Sitemapper\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $action
 * @var string $helpIcon
 * @var array<int,stdClass> $changefreqOptions
 * @var string $priority
 */
?>
<!-- Sitemapper: page data tab -->
<form id="sitemapper" action="<?=$this->esc($action)?>" method="post">
    <p><strong>Sitemap</strong></p>
    <p>
        <a class="pl_tooltip" onclick="return false">
            <img src="<?=$this->esc($helpIcon)?>" alt="help">
            <span><?=$this->text('cf_changefreq')?></span>
        </a>
        <label for="sitemapper_changefreq"><span>Changefreq:</span></label>
        <select id="sitemapper_changefreq" name="sitemapper_changefreq"
                style="display: block">
<?php foreach ($changefreqOptions as $changefreqOption):?>
            <option value="<?=$this->esc($changefreqOption->name)?>"<?=$changefreqOption->selected?>><?=$this->esc($changefreqOption->name)?></option>
<?php endforeach?>
        </select>
    </p>
    <p>
        <a class="pl_tooltip" onclick="return false">
            <img src="<?=$this->esc($helpIcon)?>" alt="help">
            <span><?=$this->text('cf_priority')?></span>
        </a>
        <label for="sitemapper_priority"><span>Priority:</span></label>
        <input type="text" id="sitemapper_priority" name="sitemapper_priority"
               style="display: block"
               value="<?=$this->esc($priority)?>">
    </p>
    <p style="text-align: right">
        <input type="submit" class="submit" name="save_page_data">
    </p>
</form>
