<!-- search by distance block -->
{if $config.sbd_geonames_user}
<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pages.search_by_distance}.html{else}?page={$pages.search_by_distance}{/if}">
	<input type="hidden" name="sbd_block" value="1" />
	<input type="hidden" name="sbd_geouser" value="{$config.sbd_geonames_user}" />
	{if $sbd_countries|@count > 1}
		<select name="block_country" style="width: 170px;">
			<option value="">{$lang.sbd_select_country}</option>
			{foreach from=$sbd_countries item='country'}
				<option value="{$country.Code}" {if $smarty.post.block_country == $country.Code || (!$smarty.post.block_country && $country.Code == $config.sbd_default_country)}selected="selected"{elseif !$smarty.post.block_country && $smarty.session.GEOLocationData->Country_code && $smarty.session.GEOLocationData->Country_code == $country.Code}selected="selected"{/if}>{$lang[$country.pName]}</option>
			{/foreach}
		</select>
	{else}
		<input type="hidden" name="block_country" value="{foreach from=$sbd_countries item='country'}{$country.Code}{/foreach}" />
	{/if}
	
	<div style="padding: 10px 0">
		<input maxlength="10" name="block_zip" type="text" value="{if $smarty.post.block_zip}{$smarty.post.block_zip}{else}{$lang.sbd_zipcode}{/if}" style="width: 55px;text-align: center;" />
		
		{$lang.sbd_within}
		
		<select name="block_distance" class="w50">
			{foreach from=','|explode:$config.sbd_distance_items item='distance'}
				<option {if $smarty.post.block_distance == $distance}selected="selected"{elseif $distance == $config.sbd_default_distance}selected="selected"{/if} value="{$distance}">{$distance}</option>
			{/foreach}
		</select>
		
		{if $config.sbd_units == 'miles/kilometres'}
			<select name="block_distance_unit" style="width: 50px;">
				{if $config.sbd_default_units == 'miles'}
					<option value="mi" title="{$lang.sbd_mi}">{$lang.sbd_mi_short}</option>
					<option {if $smarty.post.block_distance_unit == 'km'}selected="selected"{/if} value="km" title="{$lang.sbd_km}">{$lang.sbd_km_short}</option>
				{else}
					<option value="km" title="{$lang.sbd_km}">{$lang.sbd_km_short}</option>
					<option {if $smarty.post.block_distance_unit == 'mi'}selected="selected"{/if} value="mi" title="{$lang.sbd_mi}">{$lang.sbd_mi_short}</option>
				{/if}
			</select>
		{else}
			<input name="block_distance_unit" type="hidden" value="{if $config.sbd_units == 'miles'}mi{else}km{/if}" />
			{if $config.sbd_units == 'miles'}{$lang.sbd_mi}{else}{$lang.sbd_km}{/if},
		{/if}
	</div>
	
	<input type="submit" value="{$lang.search}" />
</form>
<script type="text/javascript">
var sbd_zip_phrase = '{$lang.sbd_zipcode}';
{literal}

	$(document).ready(function(){
		$('input[name=block_zip]').focus(function(){
			if ( $(this).val() == sbd_zip_phrase )
			{
				$(this).val('');
			}
		}).blur(function(){
			if ( $(this).val() == '' )
			{
				$(this).val(sbd_zip_phrase);
			}
		});
	});
	
{/literal}
</script>
{else}
<p>{$lang.sbd_no_geoaccount}</p>
{/if}
<!-- search by distance block end -->