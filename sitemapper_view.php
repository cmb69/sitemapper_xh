<?php

/**
 * Pagedata view of Sitemapper_XH.
 * Copyright (c) 2011-2013 Christoph M. Becker (see license.txt)
 */


// utf-8-marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns the pagedata form.
 *
 * @param  array $page	The page's data.
 * @return string
 */
function sitemapper_view($page) {
    global $tx, $sn, $su, $pth, $plugin_tx;

    $sitemapper_tx =& $plugin_tx['sitemapper'];
    $help_icon = tag('img src="'.$pth['folder']['plugins'].'sitemapper/images/help.png " alt="help"');
    $res = '<form id="sitemapper" action="'.$sn.'?'.$su.'" method="post">'."\n"
	    .'<p><strong>Sitemap</strong></p>'."\n";

    $res .= '<a class="pl_tooltip" href="javascript:return false">'.$help_icon.'<span>'.$sitemapper_tx['cf_changefreq'].'</span></a>&nbsp;'
	    .'<label for="sitemapper_changefreq"><span>Changefreq:</span></label>'.tag('br')."\n";
    $res .= '<select id="sitemapper_changefreq" name="sitemapper_changefreq" style="width: 10em;">'."\n";
    foreach (array('', 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never') as $opt) {
	$selected = $page['sitemapper_changefreq'] == $opt ? ' selected="selected"' : '';
	$res .= '<option'.$selected.'>'.$opt.'</option>'."\n";
    }
    $res .= '</select>'.tag('br').tag('hr style="margin: 6px 0; visibility: hidden"')."\n";

    $res .= '<a class="pl_tooltip" href="javascript:return false">'.$help_icon.'<span>'.$sitemapper_tx['cf_priority'].'</span></a>&nbsp;'
	.'<label for="sitemapper_priority"><span>Priority:</span></label>'.tag('br')."\n";
    $res .= tag('input type="text" id="sitemapper_priority" name="sitemapper_priority" size="16"'
	    .' value="'.$page['sitemapper_priority'].'"').tag('br')."\n";

    $res .= tag('input type="hidden" name="save_page_data"')."\n"
	    .'<div style="text-align: right">'."\n"
	    .tag('input type="submit" value="'.ucfirst($tx['action']['save']).'"')."\n"
	    .'</div></form>'."\n";
    return $res;
}

?>
