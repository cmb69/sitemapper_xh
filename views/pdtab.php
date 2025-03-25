<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $action
 * @var string $helpIcon
 * @var list<array{name:string,selected:string}> $changefreqOptions
 * @var string $priority
 * @var list<array{name:string,label:string,selected:string}> $includeOptions
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
    <select id="sitemapper_changefreq" name="sitemapper_changefreq" style="display: block">
<?foreach ($changefreqOptions as $changefreqOption):?>
      <option value="<?=$this->esc($changefreqOption['name'])?>"<?=$this->esc($changefreqOption['selected'])?>><?=$this->esc($changefreqOption['name'])?></option>
<?endforeach?>
    </select>
  </p>
  <p>
    <a class="pl_tooltip" onclick="return false">
      <img src="<?=$helpIcon?>" alt="help">
      <span><?=$this->text('cf_priority')?></span>
    </a>
    <label for="sitemapper_priority"><span>Priority:</span></label>
    <input type="text" id="sitemapper_priority" name="sitemapper_priority" style="display: block" value="<?=$this->esc($priority)?>">
  </p>
  <p>
    <a class="pl_tooltip" onclick="return false">
      <img src="<?=$helpIcon?>" alt="help">
      <span><?=$this->text('cf_include')?></span>
    </a>
    <label for="sitemapper_include"><span><?=$this->text('label_include')?></span></label>
    <select id="sitemapper_include" name="sitemapper_include" style="display: block">
<?foreach ($includeOptions as $includeOption):?>
      <option value="<?=$this->esc($includeOption['name'])?>"<?=$this->esc($includeOption['selected'])?>><?=$this->text($includeOption['label'])?></option>
<?endforeach?>
    </select>
  </p>
  <p style="text-align: right">
    <input type="submit" class="submit" name="save_page_data">
  </p>
</form>
