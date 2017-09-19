<!-- Sitemapper: page data tab -->
<form id="sitemapper" action="<?=$action?>" method="post">
    <p><strong>Sitemap</strong></p>
    <p>
        <a class="pl_tooltip" onclick="return false">
            <img src="<?=$helpIcon?>" alt="help"/>
            <span><?=$help['changefreq']?></span>
        </a>
        <label for="sitemapper_changefreq"><span>Changefreq:</span></label>
        <select id="sitemapper_changefreq" name="sitemapper_changefreq"
                style="display: block">
<?php foreach ($changefreqOptions as $name => $default):?>
            <option value="<?=$name?>" <?=$default ? 'selected="selected"' : ''?>><?=$name?></option>
<?php endforeach?>
        </select>
    </p>
    <p>
        <a class="pl_tooltip" onclick="return false">
            <img src="<?=$helpIcon?>" alt="help"/>
            <span><?=$help['priority']?></span>
        </a>
        <label for="sitemapper_priority"><span>Priority:</span></label>
        <input type="text" id="sitemapper_priority" name="sitemapper_priority"
               style="display: block"
               value="<?=$priority?>"/>
    </p>
    <p style="text-align: right">
        <input type="submit" class="submit" name="save_page_data"/>
    </p>
</form>
