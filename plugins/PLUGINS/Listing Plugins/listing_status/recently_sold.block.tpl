<!-- recently sold listings block -->
{if !empty($ls_listings)}
	<ul class="featured">
	{foreach from=$ls_listings item='rsold_listing' key='key'}
		{assign var='type' value=$rsold_listing.Listing_type}
		{assign var='page_key' value=$listing_types.$type.Page_key}
		<li class="item" id="fli_{$rsold_listing.ID}">
			<div class="content">
				{if $rsold_listing.Image_unlim != '0' || $rsold_listing.Image !='0'}
					<a {if $config.featured_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.$page_key}/{$rsold_listing.Path}/{str2path string=$rsold_listing.listing_title}-{$rsold_listing.ID}.html{else}?page={$pages.$page_key}&amp;id={$rsold_listing.ID}{/if}">
						<img alt="{$rsold_listing.listing_title}" title="{$rsold_listing.listing_title}" src="{if empty($rsold_listing.Main_photo)}{$rlTplBase}img/no-picture.jpg{else}{$smarty.const.RL_URL_HOME}files/{$rsold_listing.Main_photo}{/if}" style="width: {$config.pg_upload_thumbnail_width}px;height: {$config.pg_upload_thumbnail_height}px;" />
					</a>
				{/if}
					
				{assign var='available_field' value=1}
				<ul>
				{foreach from=$rsold_listing.fields item='item' key='field' name='fieldsF'}
					{if !empty($item.value) && $item.Details_page}
						<li id="flf_{$rsold_listing.ID}_{$item.Key}" {if $available_field == 1}class="first"{/if} {if $listing_types.$type.Photo}style="width: {$config.pg_upload_thumbnail_width+4}px;"{/if}>
							{if $available_field == 1}
								<a {if $config.featured_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.$page_key}/{$rsold_listing.Path}/{str2path string=$rsold_listing.listing_title}-{$rsold_listing.ID}.html{else}?page={$pages.$page_key}&amp;id={$rsold_listing.ID}{/if}">
									{$item.value}
								</a>
							{else}
								{$item.value}
							{/if}
						</li>
						{assign var='available_field' value=$available_field+1}
					{/if}
				{/foreach}
				{if $available_field == 1}<li></li>{/if}
				</ul>
			</div>
	{/foreach}
	</ul>
{else}
	{assign var='ls_lang' value='lsl_'|cat:$ls_key}
	<div style="margin: 10px;" class="grey_middle">{$lang.rsold_no_listings|replace:"[days]":$ls_days|replace:"[label]":$lang.$ls_lang}</div>
{/if}
<!--  recently sold listings block -->

