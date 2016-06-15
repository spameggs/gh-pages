<!-- featured boats block -->
{if !empty($listings_box)}
	{if $box_option.display_mode == 'grid'}
		{math assign='columns' equation='(100-x)/x' x=$box_option.box_columns format="%.1f"}
		{math assign='image_margin' equation='ceil((w/(x/60)-60)/2)' x=$config.pg_upload_thumbnail_height w=$config.pg_upload_thumbnail_width}
	{/if}
	{assign var='phrase_sale' value='listing_fields+name+sale_rent_1'}
	<ul id="listing_box_{$block.ID}" class="featured{if $box_option.display_mode == 'grid'} box_grid{else} with-pictures{/if}">
	{foreach from=$listings_box item='featured_listing' key='key'}
		{assign var='type' value=$featured_listing.Listing_type}
		{assign var='page_key' value=$listing_types.$type.Page_key}
		{if $box_option.display_mode == 'default'}
			<li class="item" id="fli_{$featured_listing.ID}">
				<div class="content">
					{if $featured_listing.Image_unlim != '0' || $featured_listing.Image !='0'}
						<a {if $config.featured_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.$page_key}/{$featured_listing.Path}/{str2path string=$featured_listing.listing_title}-{$featured_listing.ID}.html{else}?page={$pages.$page_key}&amp;id={$featured_listing.ID}{/if}">
							<img alt="{$featured_listing.listing_title}" title="{$featured_listing.listing_title}" src="{if empty($featured_listing.Main_photo)}{$rlTplBase}img/no-picture.jpg{else}{$smarty.const.RL_URL_HOME}files/{$featured_listing.Main_photo}{/if}" style="width: {$config.pg_upload_thumbnail_width}px;height: {$config.pg_upload_thumbnail_height}px;" />
						</a>
					{/if}
					
					{assign var='available_field' value=1}
					<ul>
					{foreach from=$featured_listing.fields item='item' key='field' name='fieldsF'}
						{if !empty($item.value) && $item.Details_page}
							<li id="flf_{$featured_listing.ID}_{$item.Key}" class="{if $available_field == 1}first{/if}{if $item.Key == 'price' && $tpl_settings.featured_price_tag} price_tag{/if}{if $item.Key == 'sale_rent' && $tpl_settings.featured_price_tag} sale-rent {if $item.value == $lang.$phrase_sale}type-sale{else}type-rent{/if}{/if}" {if $listing_types.$type.Photo}style="width: {$config.pg_upload_thumbnail_width+4}px;"{/if}>
								{if $available_field == 1}
									{if !$listing_types.$type.Photo}<img alt="" class="point" src="{$rlTplBase}img/blank.gif" />{/if}
									<a {if $config.featured_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.$page_key}/{$featured_listing.Path}/{str2path string=$featured_listing.listing_title}-{$featured_listing.ID}.html{else}?page={$pages.$page_key}&amp;id={$featured_listing.ID}{/if}">
										{$item.value}
									</a>
								{else}
									{$item.value}
								{/if}
								{if $item.Key == 'sale_rent' && $tpl_settings.featured_price_tag}<span></span><span></span>{/if}
							</li>
							{assign var='available_field' value=$available_field+1}
						{/if}
					{/foreach}
					{if $available_field == 1}<li></li>{/if}
					</ul>
					
				</div>
			</li>
		{elseif $box_option.display_mode == 'grid'}		
			<li class="item" id="fli_{$featured_listing.ID}" style="width: {$columns}%;">
				<div class="content" >
					{if $featured_listing.Image_unlim != '0' || $featured_listing.Image !='0'}
						<div class="photo">
							<a {if $config.featured_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.$page_key}/{$featured_listing.Path}/{str2path string=$featured_listing.listing_title}-{$featured_listing.ID}.html{else}?page={$pages.$page_key}&amp;id={$featured_listing.ID}{/if}">
								<img style="margin-{$text_dir}: -{$image_margin}px; " alt="{$featured_listing.listing_title}" title="{$featured_listing.listing_title}" src="{if empty($featured_listing.Main_photo)}{$rlTplBase}img/no-picture.jpg{else}{$smarty.const.RL_URL_HOME}files/{$featured_listing.Main_photo}{/if}"/>
							</a>
							{if $featured_listing.fields.sale_rent.Key == 'sale_rent' && $featured_listing.fields.sale_rent.value}<div title="{$featured_listing.fields.sale_rent.value}" class="sale-rent {if $featured_listing.fields.sale_rent.value == $lang.$phrase_sale}type-sale{else}type-rent{/if}"></div>{/if}
						</div>
					{/if}
					{assign var='available_field' value=1}
					<ul>
					{foreach from=$featured_listing.fields item='item' key='field' name='fieldsF'}
						{if !empty($item.value) && $item.Details_page && $item.Key != 'sale_rent' && $tpl_settings.featured_price_tag || !empty($item.value) && $item.Details_page && !$tpl_settings.featured_price_tag}
							<li id="flf_{$featured_listing.ID}_{$item.Key}" class="{if $available_field == 1}first{/if}{if $item.Key == 'price' && $tpl_settings.featured_price_tag} price_tag{/if}">
								{if $available_field == 1}
									{if !$listing_types.$type.Photo}<img alt="" class="point" src="{$rlTplBase}img/blank.gif" />{/if}
									<a {if $config.featured_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.$page_key}/{$featured_listing.Path}/{str2path string=$featured_listing.listing_title}-{$featured_listing.ID}.html{else}?page={$pages.$page_key}&amp;id={$featured_listing.ID}{/if}">
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
			</li>
		{/if}
	{/foreach}
	
	</ul>
	<div class="clear"></div>
{else}

	{if $config.mod_rewrite}
		{assign var='href' value=$rlBase|cat:$pages.add_listing|cat:'.html'}
	{else}
		{assign var='href' value=$rlBase|cat:'index.php?page='|cat:$pages.add_listing}
	{/if}
	{assign var='link' value='<a href="'|cat:$href|cat:'">$1</a>'}
	{$lang.no_listings_here|regex_replace:'/\[(.+)\]/':$link}
	
{/if}

<!-- featured boats block end -->