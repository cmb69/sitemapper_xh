<?php

/**
 * @var string $action
 * @var string $helpIcon
 * @var array<string,bool> $changefreqOptions
 * @var string $priority
 */

if (!isset($this)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}
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
<?php foreach ($changefreqOptions as $name => $default):?>
            <option value="<?=$this->esc($name)?>" <?=$default ? 'selected="selected"' : ''?>><?=$this->esc($name)?></option>
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
