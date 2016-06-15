<!-- carousel block -->
{if $listings}
	{assign var='carousel_option' value=$carousel_options[$block.ID]}
	{assign var='phrase_sale' value='listing_fields+name+sale_rent_1'}
	<div class="carousel {$carousel_option.Direction}{if $carousel_option.Direction == 'vertical'} cVertical{else} cHorizontal{/if}">		
		<div id="carousel_{$block.Key}" class="carousel_block">
			<ul class="featured{if $listing_types.$type.Photo} with-pictures{else} list{/if}">
				{foreach from=$listings item='featured_listing' key='key'}
					{assign var='type' value=$featured_listing.Listing_type}
					{assign var='page_key' value=$listing_types.$type.Page_key}
					<li id="{$block.Key}_{$featured_listing.ID}" class="item">
						<div class="content">
							{if $listing_types.$type.Photo}
								{if $listing_types.$type.Page}<a {if $config.featured_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.$page_key}/{$featured_listing.Path}/{str2path string=$featured_listing.listing_title}-{$featured_listing.ID}.html{else}?page={$pages.$page_key}&amp;id={$featured_listing.ID}{/if}">{/if}
									<img alt="{$featured_listing.listing_title}" title="{$featured_listing.listing_title}" src="{if empty($featured_listing.Main_photo)}{$rlTplBase}img/no-picture.jpg{else}{$smarty.const.RL_URL_HOME}files/{$featured_listing.Main_photo}{/if}" style="width: {$config.pg_upload_thumbnail_width}px;height: {$config.pg_upload_thumbnail_height}px;" />
								{if $listing_types.$type.Page}</a>{/if}
							{/if}
							{assign var='available_field' value=1}
							<ul>
							{foreach from=$featured_listing.fields item='item' key='field' name='fieldsF'}
								{if !empty($item.value) && $item.Details_page}
									<li class="{if $available_field == 1}first{/if}{if $item.Key == 'price' && $tpl_settings.featured_price_tag} price_tag{/if}{if $item.Key == 'sale_rent' && $tpl_settings.featured_price_tag} sale-rent {if $item.value == $lang.$phrase_sale}type-sale{else}type-rent{/if}{/if}" id="flf_{$featured_listing.ID}_{$item.Key}" {if $listing_types.$type.Photo}style="width: {$config.pg_upload_thumbnail_width+4}px;"{/if}>
										{if $available_field == 1 || $item.Key == 'price'}
											{if !$listing_types.$type.Photo}<img alt="" class="point" src="{$rlTplBase}img/blank.gif" />{/if}
											{if $listing_types.$type.Page}<a {if $config.featured_new_window}target="_blank"{/if} href="{$rlBase}{if $config.mod_rewrite}{$pages.$page_key}/{$featured_listing.Path}/{str2path string=$featured_listing.listing_title}-{$featured_listing.ID}.html{else}?page={$pages.$page_key}&amp;id={$featured_listing.ID}{/if}">{else}<b>{/if}
												{$item.value}{if $item.Key == 'price'}<span></span>{/if}
											{if $listing_types.$type.Page}</a>{else}</b>{/if}
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
				{/foreach}
			</ul>
		</div>
	</div>	
	{if $listings|@count >= $carousel_option.Visible}
		<script type="text/javascript">
			rlCarousel['carousel_{$block.Key}'] = {$carousel_option.Number}-{$listings|@count};
			{literal}
			$(document).ready(function(){
				$("#{/literal}carousel_{$block.Key}{literal}").carousel({
					options: {/literal}'{$block.options}'{literal},
					priceTag: {/literal}{if $tpl_settings.featured_price_tag}true{else}false{/if}{literal},
					btnNext: ".next_{/literal}carousel_{$block.Key}{literal}",
					btnPrev: ".prev_{/literal}carousel_{$block.Key}{literal}",
					vertical: {/literal}{if $carousel_option.Direction == 'vertical'}true{else}false{/if}{literal},
					circular: {/literal}{if $carousel_option.Round == 1}true{else}false{/if}{literal},
					diraction: {/literal}'{$smarty.const.RL_LANG_DIR}'{literal},					
					visible: {/literal}{$carousel_option.Visible}{literal},
					scroll: {/literal}{$carousel_option.Per_slide}{literal},
					number: {/literal}{$carousel_option.Number}{literal},
					count: {/literal}{$listings|@count}{literal},
					auto: {/literal}{$carousel_option.Delay}{literal}000
					//auto: 0
				});
			});
			{/literal}
			
		</script>
	{/if}
{else}

	{if $listing_types.$type.Page}
		{if $config.mod_rewrite}
			{assign var='href' value=$rlBase|cat:$pages.add_listing|cat:'.html'}
		{else}
			{assign var='href' value=$rlBase|cat:'?page='|cat:$pages.add_listing}
		{/if}
		{assign var='link' value='<a href="'|cat:$href|cat:'">$1</a>'}
		{$lang.no_listings_here|regex_replace:'/\[(.+)\]/':$link}
	{else}
		{$lang.no_listings_here_submit_deny}
	{/if}
{/if}

<!-- carousel block end -->