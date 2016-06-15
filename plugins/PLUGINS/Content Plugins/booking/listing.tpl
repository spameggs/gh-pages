<!-- listing block -->

<fieldset class="item {if $listing.Featured}featured{else}regular{/if}" id="listing_{$listing.ID}">
{if $listing.Featured}
<legend class="blue_bright" align="{$text_dir_rev}">{$lang.featured}</legend>
{/if}
	<table class="sTable">
	<tr>
		<td rowspan="2" style="width: {if $config.pg_upload_thumbnail_width}{$config.pg_upload_thumbnail_width}{else}100{/if}px;" align="center" valign="top">
		<a {if $config.view_details_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.browse}/{$listing.Path}/{str2path string=$listing.listing_title}-l{$listing.ID}.html{else}?page={$pages.browse}&amp;id={$listing.ID}{/if}">
			<img alt="{$listing.listing_title}" title="{$listing.listing_title}" src="{if empty($listing.Main_photo)}{$rlTplBase}img/no_photo.gif{else}{$smarty.const.RL_URL_HOME}files/{$listing.Main_photo}{/if}" />
		</a>
		{if !empty($listing.Main_photo) && $config.grid_photos_count}
			<div style="width: {if $config.pg_upload_thumbnail_width}{$config.pg_upload_thumbnail_width}{else}100{/if}px;" class="photos_count"><div><a {if $config.view_details_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.browse}/{$listing.Path}/{str2path string=$listing.listing_title}-l{$listing.ID}.html{else}?page={$pages.browse}&amp;id={$listing.ID}{/if}"><b>{$listing.Photos_count}</b> {$lang.photos_count}</a></div></div>
		{/if}
		</td>
		<td class="spliter" rowspan="2"></td>
		<td align="{$text_dir}" valign="top" style="height: 65px">
			<table>
			{assign var='f_first' value=true}
			{foreach from=$listing.fields item='item' key='field' name='fListings'}
			{if !empty($item.value) && $item.Details_page}
			<tr id="sf_field_{$listing.ID}_{$item.Key}">
				{if $config.sf_display_fields}
				<td valign="top">
					<div class="field">{$item.name}:</div>
				</td>
				<td style="width: 3px;"></td>
				{/if}
				<td valign="top">
					{if $f_first}
						<a class="static" {if $config.view_details_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.browse}/{$listing.Path}/{str2path string=$listing.listing_title}-l{$listing.ID}.html{else}?page={$pages.browse}&amp;id={$listing.ID}{/if}">
							<b>{$item.value}</b>
						</a>
					{else}
						<div class="value">
							{$item.value}
						</div>
					{/if}
				</td>
			</tr>
			{assign var='f_first' value=false}
			{/if}
			{/foreach}
			</table>
		</td>
		<td align="{$text_dir_rev}" valign="top">
			<table>
			{if $listing.Type && $config.listing_for}
			<tr>
				<td align="{$text_dir_rev}"><span class="field">{assign var='type_f_name' value='listing_fields+name+Type'}<small>{$lang.$type_f_name}:</small></span></td>
				<td style="width: 90px;" align="{$text_dir}">
					{assign var='l_type' value='listing_fields+name+Type_'|cat:$listing.Type}
					<span class="value"><small><b>{$lang.$l_type}</b></small></span>
				</td>
			</tr>
			{/if}
			<tr>
				<td align="{$text_dir_rev}"><span class="field"><small>{$lang.category}:</small></span></td>
				<td align="{$text_dir}">
					<a title="{$listing.name}" class="static_small" href="{$rlBase}{if $config.mod_rewrite}{$pages.browse}/{$listing.Path}{if $config.display_cat_html}.html{else}/{/if}{else}?page={$pages.browse}&amp;category={$listing.Kind_ID}{/if}">{$listing.name}</a>
				</td>
			</tr>

			{rlHook name='listingAfterFields'}

			</table>
		</td>
	</tr>
	<tr>
		<td align="{$text_dir}" valign="bottom">
			{if $config.count_listing_visits}<span class="grey_small">{$lang.shows}: <b>{$listing.Shows}</b></span>{/if}

			{rlHook name='listingAfterStats'}
		</td>
		<td valign="bottom" class="icon" align="{$text_dir_rev}">

			{rlHook name='listingNavIcons'}

			{if $pageInfo.Controller == 'my_favorite'}
			<a onclick="rlConfirm( '{$lang.remove_favorite_confirm|escape:quotes}', 'xajax_removeFavorite', Array('{$listing.ID}'), 'listing_loading' );" href="javascript:void(0)">
				<img title="{$lang.remove_from_favorites}" alt="{$lang.remove_to_favorites}" src="{$rlTplBase}img/star_del.gif" /></a>
			{else}
				<span id="favorite_{$listing.ID}">
				{if isset($smarty.session.id) && $listing.Favorite == $smarty.session.id}
					<a onclick="rlConfirm( '{$lang.remove_favorite_confirm|escape:quotes}', 'xajax_restoreFavorite', Array('{$listing.ID}'), 'listing_loading' );" href="javascript:void(0)">
					<img title="{$lang.remove_from_favorites}" alt="{$lang.add_to_favorites}" src="{$rlTplBase}img/star_del.gif" /></a>
				{else}
					<a onclick="rlConfirm( '{$lang.add_favorite_confirm|escape:quotes}', 'xajax_addToFavorite', Array('{$listing.ID}'), 'listing_loading' );" href="javascript:void(0)">
					<img title="{$lang.add_to_favorites}" alt="{$lang.add_to_favorites}" src="{$rlTplBase}img/star.gif" /></a>
				{/if}
				</span>
			{/if}
			<a {if $config.view_details_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.browse}/{$listing.Path}/{str2path string=$listing.listing_title}-l{$listing.ID}.html{else}?page={$pages.browse}&amp;id={$listing.ID}{/if}">
				<img title="{$lang.view_details}" alt="{$lang.view_details}" src="{$rlTplBase}img/view_details_icon.gif" /></a>
		</td>
	</tr>
	</table>
</fieldset>

<!-- listing block end -->