<!-- search by distance tpl -->
<div class="highlight">
	<div id="map" style="width: {if empty($config.sbd_map_width)}100%{else}{$config.sbd_map_width}px{/if}; height: {if empty($config.sbd_map_height)}300px{else}{$config.sbd_map_height}px{/if}"></div>
	
	<table class="sTable" style="margin: 10px 0 0 0;">
	<tr>
		<td class="lalign">
			{if $sbd_countries|@count > 1}
				<select class="sbd_control" name="country" style="width: 120px;">
					<option value="">{$lang.sbd_select_country}</option>
					{foreach from=$sbd_countries item='country'}
						<option value="{$country.Code}" {if $smarty.post.block_country == $country.Code || (!$smarty.post.block_country && $country.Code == $config.sbd_default_country)}selected="selected"{elseif !$smarty.post.block_country && $smarty.session.GEOLocationData->Country_code && $smarty.session.GEOLocationData->Country_code == $country.Code}selected="selected"{/if}>{$lang[$country.pName]}</option>
					{/foreach}
				</select>
			{else}
				<input type="hidden" name="country" value="{foreach from=$sbd_countries item='country'}{$country.Code}{/foreach}" />
			{/if}
		
			<input maxlength="10" id="sbd_zip" type="text" value="{if $smarty.post.block_zip && $smarty.post.block_zip != $lang.sbd_zipcode}{$smarty.post.block_zip}{else}{$lang.sbd_zipcode}{/if}" style="width: 60px;text-align: center;" />
			
			{$lang.sbd_within}
			
			<select id="sbd_radius" name="distance" class="w50">
				{foreach from=','|explode:$config.sbd_distance_items item='distance'}
					<option {if $smarty.post.block_distance == $distance}selected="selected"{elseif $distance == $config.sbd_default_distance}selected="selected"{/if} value="{$distance}">{$distance}</option>
				{/foreach}
			</select>
			
			{if $config.sbd_units == 'miles/kilometres'}
				<select name="distance_unit" style="width: 50px;">
					{if $config.sbd_default_units == 'miles'}
						<option value="mi" title="{$lang.sbd_mi}">{$lang.sbd_mi_short}</option>
						<option {if $smarty.post.block_distance_unit == 'km'}selected="selected"{/if} value="km" title="{$lang.sbd_km}">{$lang.sbd_km_short}</option>
					{else}
						<option value="km" title="{$lang.sbd_km}">{$lang.sbd_km_short}</option>
						<option {if $smarty.post.block_distance_unit == 'mi'}selected="selected"{/if} value="mi" title="{$lang.sbd_mi}">{$lang.sbd_mi_short}</option>
					{/if}
				</select>
			{else}
				<input name="distance_unit" type="hidden" value="{if $config.sbd_units == 'miles'}mi{else}km{/if}" />
				{if $config.sbd_units == 'miles'}{$lang.sbd_mi}{else}{$lang.sbd_km}{/if},
			{/if}
			
			<a class="sbd_control nav_icon" href="javascript:void(0)">
				<span class="left">&nbsp;</span><span class="center">{$lang.go}</span><span class="right">&nbsp;</span>
			</a>
		</td>
		<td class="ralign" id="sbd_count"></td>
	</tr>
	</table>
</div>

<div style="padding: 20px 0 0 0;" id="sbd_dom" class="hide">
	{*<table class="grid_navbar">
	<tr>
		<td class="switcher">
			<div class="table"><div {if $smarty.cookies.grid_mode == 'table' || !isset($smarty.cookies.grid_mode)}class="active"{/if}></div></div>
			<div class="list"><div {if $smarty.cookies.grid_mode == 'list'}class="active"{/if}></div></div>
		</td>
		{if $sorting}
		<td class="sorting">
			<span class="caption">{$lang.sort_listings_by}:</span>
			{foreach from=$sorting item='field_item' key='sort_key' name='fSorting'}
				<a accesskey="{$sort_key}" title="{$lang.sort_listings_by} {$field_item.name}" href="javascript:void(0)">{$field_item.name}</a>
				{if !$smarty.foreach.fSorting.last}<span class="divider">|</span>{/if}
			{/foreach}
			
			{rlHook name='browseAfterSorting'}
		</td>
		{/if}
		<td class="custom">{rlHook name='browseGridNavBar'}</td>
	</tr>
	</table>*}
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'grid_navbar.tpl'}
	
	<div id="sbd_listings"></div>
	<div id="sbd_paging"></div>
</div>

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false{if $smarty.const.RL_LANG_CODE != '' && $smarty.const.RL_LANG_CODE != 'en'}&amp;language={$smarty.const.RL_LANG_CODE}{/if}"></script>
<script type="text/javascript" src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&amp;key={$config.google_map_key}"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.flmap.js"></script>
<script type="text/javascript">//<![CDATA[
{literal}

var sbd_set_zoom = 10;
var sbdBase;
var sbdConfig = new Object({
	requestUrl: '{/literal}{$seoBase}{if $config.mod_rewrite}{$pages.search_by_distance}.html{else}index.php?page={$pages.search_by_distance}{/if}{literal}',
	circle: false,
	label: false,
	page: 1,
	unit: 'mi',
	markers: new Array(),
	idsOnMap: new Array(),
	windowinfo: new Array(),
	defaultDistance: {/literal}{if $config.sbd_default_distance}{$config.sbd_default_distance}*1000{if $config.sbd_default_units == 'miles' || $smarty.post.block_distance_unit == 'mi'}*1.609344{/if}{else}10000{/if}{literal},
	sortField: '',
	sortType: '',
	prZipNotFound: "{/literal}{$lang.sbd_zip_not_found}{literal}",
	prLocationNotFound: "{/literal}{$lang.sbd_location_not_found}{literal}",
	prKmShort: "{/literal}{$lang.sbd_km_short}{literal}",
	prMiShort: "{/literal}{$lang.sbd_mi_short}{literal}",
	go: "{/literal}{$lang.go}{literal}",
	zip_code: "{/literal}{$lang.sbd_zipcode}{literal}",
	start: "{/literal}{if !$smarty.post.sbd_block}{$geoData->Country_name}{if $geoData->Region}, {$geoData->Region}{/if}{if $geoData->City}, {$geoData->City}{/if}{/if}{literal}"
});

{/literal}
//]]>
</script>

<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}search_by_distance/static/lib.js"></script>
<!-- search by distance tpl end -->