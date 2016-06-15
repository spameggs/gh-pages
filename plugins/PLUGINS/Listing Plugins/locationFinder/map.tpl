<!-- location finder tpl -->

<div id="lf-container" {if $config.locationFinder_position != 'top'} class="hide" {if $config.locationFinder_position == 'bottom'}style="padding-top: 5px;"{/if}{/if}>
	{if $config.locationFinder_position == 'top' || $config.locationFinder_position == 'bottom'}
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='lf_fieldset' name=$lang.locationFinder_fieldset_caption}
	{/if}
	
	{if $config.listing_feilds_position == 1}
		<div class="name">{$lang.locationFinder_location}:</div>
	{elseif $config.listing_feilds_position == 2}
		<table class="submit">
		<tr>
			<td class="name">
				{$lang.locationFinder_location}:
			</td>
			<td class="field" id="sf_field_lfmap">
	{/if}
	
				<div id="lf_map" style="{if $config.locationFinder_map_width}width: {$config.locationFinder_map_width}px;{/if}height: {if $config.locationFinder_map_height}{$config.locationFinder_map_height}{else}250{/if}px;"></div>
				<table class="sTable" style="margin-top: 5px;">
				<tr>
					<td class="falign" style="-moz-user-select: none;-khtml-user-select: none;"><label><input id="lf_use" type="checkbox" name="f[lf][use]" value="1" {if $smarty.post.f.lf.use}checked="checked"{/if} /> {$lang.locationFinder_use_location}</label></td>
					<td class="ralign">
						<table align="right">
						<tr>
							<td>
								<img src="{$rlTplBase}img/blank.gif" class="qtip" title="{$lang.locationFinder_hint}" />
								<input style="margin: 0 5px;" type="text" id="lf_query" name="f[lf][query]" value="{if $smarty.post.f.lf.query}{$smarty.post.f.lf.query}{else}{if !$config.locationFinder_use_location}{$config.locationFinder_search}{/if}{/if}" />
							</td>
							<td><input id="lf_search" class="search" type="button" value="&nbsp;" /></td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
				<input id="lf_lat" name="f[lf][lat]" type="hidden" value="{$smarty.post.f.lf.lat}" />
				<input id="lf_lng" name="f[lf][lng]" type="hidden" value="{$smarty.post.f.lf.lng}" />
				<input id="lf_zoom" name="f[lf][zoom]" type="hidden" value="{$smarty.post.f.lf.zoom}" />
	
	{if $config.listing_feilds_position == 2}
			</td>
		</tr>
		</table>
	{/if}
	
	{if $config.locationFinder_position == 'top' || $config.locationFinder_position == 'bottom'}
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
	{/if}
</div>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false{if $smarty.const.RL_LANG_CODE != '' && $smarty.const.RL_LANG_CODE != 'en'}&amp;language={$smarty.const.RL_LANG_CODE}{/if}"></script>
<script type="text/javascript" src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&amp;key={$config.google_map_key}"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.flmap.js"></script>

<script type="text/javascript">//<![CDATA[
var lfConfig = new Array();
lfConfig['geoCoder'] = new google.maps.Geocoder();
lfConfig['containerPosition'] = '{$config.locationFinder_position}';
lfConfig['containerPositionType'] = '{$config.locationFinder_type}';
lfConfig['phrase_drag_hint'] = "{$lang.locationFinder_drag_notice}";
lfConfig['phrase_not_found'] = "{$lang.location_not_found}";
{if $smarty.session.GEOLocationData->Country_name && $config.locationFinder_use_location}
lfConfig['location'] = "{$smarty.session.GEOLocationData->Country_name}{if $smarty.session.GEOLocationData->Region}, {$smarty.session.GEOLocationData->Region}{/if}{if $smarty.session.GEOLocationData->City}, {$smarty.session.GEOLocationData->City}{/if}";
{else}
lfConfig['location'] = "{$config.locationFinder_search}";
{/if}
lfConfig['zoom'] = {if $config.locationFinder_map_zoom}{$config.locationFinder_map_zoom}{else}12{/if};
lfConfig['postLat'] = {if $smarty.post.f.lf.lat}{$smarty.post.f.lf.lat}{else}false{/if};
lfConfig['postLng'] = {if $smarty.post.f.lf.lng}{$smarty.post.f.lf.lng}{else}false{/if};
lfConfig['postZoom'] = {if $smarty.post.f.lf.zoom}{$smarty.post.f.lf.zoom}{else}false{/if};

{literal}

$(document).ready(function(){
	/* assign map container */
	if ( lfConfig['containerPosition'] == 'bottom' )
	{
		$('div#controller_area form table.submit:last').before($('#lf-container'));
	}
	else if ( lfConfig['containerPosition'] != 'top' )
	{
		if ( lfConfig['containerPositionType'] == 'prepend' )
		{
			$('div#fs_'+lfConfig['containerPosition']+' > div.body').prepend($('#lf-container'));
		}
		else
		{
			$('div#fs_'+lfConfig['containerPosition']+' > div.body').append($('#lf-container'));
		}
	}
	
	$('#lf-container').show();
	
	/* generate map */
	$('#lf_map').flMap({
		addresses: [
			[lfConfig['postLat'] && lfConfig['postLng'] ? lfConfig['postLat']+','+lfConfig['postLng'] : lfConfig['location'], lfConfig['location'], lfConfig['postLat'] && lfConfig['postLng'] ? 'direct' : 'geocoder']
		],
		phrases: {
			notFound: lfConfig['phrase_not_found']
		},
		scrollWheelZoom: false,
		zoom: lfConfig['postZoom'] ? lfConfig['postZoom'] : lfConfig['zoom'],
		ready: lfHandler
	});
});

{/literal}
//]]>
</script>
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}locationFinder/static/lib.js"></script>

<!-- location finder tpl end -->