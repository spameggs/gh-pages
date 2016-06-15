<!-- browser map tpl -->

{if $config.browse_map_module}
	<div class="bm_maps{if $requested_type != 'all'} hide{/if}" {if !$block.Tpl}style="padding: 0 0 10px;"{/if}>
		<div class="{if !$block.Tpl}highlight highlight_loading{/if}" id="bm_map_all" style="{if $config.browse_map_width}width: {$config.browse_map_width}px;{/if}height: {if $config.browse_map_height}{$config.browse_map_height}{else}250{/if}px;"></div>
	</div>
	{foreach from=$home_account_types item='hTab' key='lt_key' name='tabsF'}
		{if $requested_type == $hTab.Key}
			{assign var='bmFirstKey' value=$hTab.Key|replace:'_':''}
		{/if}
		<div class="bm_maps{if $requested_type != $hTab.Key} hide{/if}" {if !$block.Tpl}style="padding: 0 0 10px;"{/if}>
			<div class="{if !$block.Tpl}highlight highlight_loading{/if}" id="bm_map_{$hTab.Key|replace:'_':''}" style="{if $config.browse_map_width}width: {$config.browse_map_width}px;{/if}height: {if $config.browse_map_height}{$config.browse_map_height}{else}250{/if}px;"></div>
		</div>
	{/foreach}

	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false{if $smarty.const.RL_LANG_CODE != '' && $smarty.const.RL_LANG_CODE != 'en'}&amp;language={$smarty.const.RL_LANG_CODE}{/if}"></script>
	<script type="text/javascript" src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&amp;key={$config.google_map_key}"></script>
	<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.flmap.js"></script>
	<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}browse_map/static/lib.js"></script>
	<script type="text/javascript">//<![CDATA[

	/* populate markers */
	{assign var='bm_index' value=0}
	{foreach from=$listings item='bm_listing'}
		{if $bm_listing.Loc_latitude && $bm_listing.Loc_longitude}
			browseMap.markers.push(new Array('{$bm_listing.Loc_latitude},{$bm_listing.Loc_longitude}', lang['loading'], 'direct', '{$bm_listing.ID}'));
			browseMap.mapping[{$bm_listing.ID}] = {$bm_index};
			{assign var='bm_index' value=$bm_index+1}
		{/if}
	{/foreach}
	
	/* populate phrases */
	browseMap.phrases['hide'] = '{$lang.hide}';
	browseMap.phrases['show'] = '{$lang.show}';
	browseMap.phrases['notFound'] = '{$lang.location_not_found}';
	
	/* populate configs */
	browseMap.config['use_letters'] = {if $config.browse_map_letters}true{else}false{/if};
	
	{literal}

	/* populate local search data */
	browseMap.localSearch = {/literal}{if $config.browse_map_amenities}{literal}{
		caption: '{/literal}{$lang.local_amenity}{literal}',
		services: [{/literal}
			{foreach from=$amenities item='amenity' name='amenityF'}
			['{$amenity.Key}', '{$amenity.name}', {if $amenity.Default}'checked'{else}false{/if}]{if !$smarty.foreach.amenityF.last},{/if}
			{/foreach}
		{literal}]
	}{/literal}{else}{literal}false{/literal}{/if}{literal};
	
	$(document).ready(function(){
		/* load map */
	//	browseMap.recentlyReload(flynax.getHash() ? flynax.getHash().replace(/_/g, '') : '{/literal}{$bmFirstKey}{literal}');
		browseMap.recentlyReload('{/literal}{$bmFirstKey}{literal}');
	});
	{/literal}
	
	//]]>
	</script>

{else}

	{$lang.browseMap_module_disabled}

{/if}

<script type="text/javascript">
{literal}

$(document).ready(function(){
	$('div.tabs ul li').click(function(){
		var key = $(this).attr('lang').replace(/_/g, '');
		$('div.bm_maps').hide();
		$('div#bm_map_'+key).parent().show();
		
		if ( $('div#area_'+key).find('div#listings').length <= 0 )
		{
			$('div.bm_maps:first').parent().hide();
		}
		else
		{
			$('div.bm_maps:first').parent().show();
		}
	});
});

{/literal}
</script>

<!-- browser map tpl end -->
