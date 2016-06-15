<!-- browser map, listing icon -->

{if $listing.Loc_latitude && $listing.Loc_longitude && $pageInfo.Key != 'search_by_distance'}
	{if !$bm_letter}
		{assign var='bm_letter' value=0}
	{/if}
	<span id="bm-listing_{$listing.ID}" class="icon bm_icon" title="{$lang.browseMap_show_on_map}" style="cursor: pointer;background: url('{$smarty.const.RL_PLUGINS_URL}browse_map/markers/marker{if $config.browse_map_letters}{$bm_alphabet.$bm_letter}{/if}.png') 0 3px no-repeat;padding: 0 5px;">&nbsp;</span>
{/if}

<!-- browser map, listing icon end -->