<!-- listing item -->

{if $listing.Listing_type}
	{assign var='listing_type' value=$listing_types[$listing.Listing_type]}
{/if}

<div class="item{if $listing.Featured} featured{/if}">
	<div class="b-layer">
		{if $listing_type.Photo}
			<div class="photo">
				{if $listing_type.Page}<a title="{$listing.listing_title}" {if $config.view_details_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$listing.Path}/{str2path string=$listing.listing_title}-{$listing.ID}.html{if $hl}?highlight{/if}{else}?page={$pages[$listing_type.Page_key]}&amp;id={$listing.ID}{if $hl}&amp;highlight{/if}{/if}">{/if}
					<img style="width: {$config.pg_upload_thumbnail_width}px;height: {$config.pg_upload_thumbnail_height}px;" alt="{$listing.listing_title}" class="{if empty($listing.Main_photo)}empty{/if}" src="{if empty($listing.Main_photo)}{$rlTplBase}img/blank.gif{else}{$smarty.const.RL_URL_HOME}files/{$listing.Main_photo}{/if}" />
				{if $listing_type.Page}</a>{/if}
				{if !empty($listing.Main_photo) && $config.grid_photos_count && $listing_type.Page}
					<div class="counter" title="{$lang.photos_count}"><a {if $config.view_details_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$listing.Path}/{str2path string=$listing.listing_title}-{$listing.ID}.html{else}?page={$pages[$listing_type.Page_key]}&amp;id={$listing.ID}{/if}">{$listing.Photos_count}</a></div>
				{/if}
				
				{if $listing.Featured}<div class="f-label" title="{$lang.featured}">{$lang.featured[0]}</div>{/if}
				
				<div class="favorite_circle">
					<a id="fav_{$listing.ID}" title="{$lang.add_to_favorites}" href="javascript:void(0)" class="icon add_favorite"><span>&nbsp;</span></a>
				</div>
			</div>
		{/if}
		
		<ul class="fields">
			{if $listing.fields}
				{assign var='f_first' value=true}
				{foreach from=$listing.fields item='item' key='field' name='fListings'}
					{if !empty($item.value) && $item.Details_page}
						<li style="width: {$config.pg_upload_thumbnail_width}px;" {if $smarty.foreach.fListings.iteration > 2}class="hide nav"{/if}>
						{*if $config.sf_display_fields}{$item.name}:{/if*}
						
						{if $f_first && $listing_type.Page}
							<a title="{$item.value}" {if $config.view_details_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$listing.Path}/{str2path string=$listing.listing_title}-{$listing.ID}.html{if $hl}?highlight{/if}{else}?page={$pages[$listing_type.Page_key]}&amp;id={$listing.ID}{if $hl}&amp;highlight{/if}{/if}">{$item.value}</a>
						{else}
							{$item.value}
						{/if}
						{assign var='f_first' value=false}	
						</li>
					{/if}
				{/foreach}
				
				{rlHook name='listingAfterFields'}
			{/if}
		</ul>
		
		<div class="nav" style="width: {$config.pg_upload_thumbnail_width}px;">
			{rlHook name='listingBeforeStats'}
			
			<div>
			{if $config.count_listing_visits}<span class="shows icon" title="{$lang.shows}">{$listing.Shows}</span>{/if}
			{if $config.display_posted_date}<span class="date icon" title="{$lang.posted_date}">{$listing.Date|date_format:$smarty.const.RL_DATE_FORMAT}</span>{/if}
			</div>
			
			{rlHook name='listingAfterStats'}
			
			<table class="nav"><tr><td>{rlHook name='listingNavIcons'}</td></tr></table>
		</div>
	</div>
</div>

<!-- listing item end -->