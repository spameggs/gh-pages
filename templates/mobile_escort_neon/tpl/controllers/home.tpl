<!-- home tpl -->

<script type="text/javascript" src="{$rlTplBase}js/jquery.mousewheel.js"></script>

<!-- quick search -->
<div id="qucik_search">
	<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pages.search}.html{else}?page={$pages.search}{/if}">
		<input type="hidden" name="form" value="keyword_search" />
		<input type="text" name="f[keyword_search]" title="{$lang.keyword_search}" maxlength="255" {if $smarty.post.f.keyword_search}value="{$smarty.post.f.keyword_search}"{/if} />
	</form>
</div>
<!-- quick search end -->

{if $mobile_featured}
<!-- featured carousel -->
<div id="width_tracker"></div>
<div id="carousel">
	<div class="left_nav"></div>
	<div class="visible">
		<ul>
		{foreach from=$mobile_featured item='listing' key='key'}
			{if $listing.Listing_type}
				{assign var='featured_listing_type' value=$listing_types[$listing.Listing_type]}
			{/if}
			<li class="item" style="width: {if $config.pg_upload_thumbnail_width > 138}144{else}{math equation='x + y' x=$config.pg_upload_thumbnail_width y=6}{/if}px;">	
				<div class="img_border">
					<a href="{$rlBase}{if $config.mod_rewrite}{$pages[$featured_listing_type.Page_key]}/{$listing.Path}/{str2path string=$listing.listing_title}-{$listing.ID}.html{else}?page={$pages[$featured_listing_type.Page_key]}&amp;id={$listing.ID}{/if}">
						<img style="width: {if $config.pg_upload_thumbnail_width > 138}138{else}{$config.pg_upload_thumbnail_width}{/if}px;" alt="{$listing.fields.0.value}" title="{$listing.fields.0.value}" src="{if empty($listing.Main_photo)}{$rlTplBase}img/no_photo.gif{else}{$smarty.const.RL_URL_HOME}files/{$listing.Main_photo}{/if}" />
					</a>
				</div>
				
				{foreach from=$listing.fields item='item' key='field' name='fListings'}
					{if !empty($item.value) && $item.Details_page}
					<div class="fField" style="width: {if $config.pg_upload_thumbnail_width > 138}144{else}{math equation='x + y' x=$config.pg_upload_thumbnail_width y=6}{/if}px;">
						{if $smarty.foreach.fListings.first}
							<a href="{$rlBase}{if $config.mod_rewrite}{$pages[$featured_listing_type.Page_key]}/{$listing.Path}/{str2path string=$listing.listing_title}-{$listing.ID}.html{else}?page={$pages[$featured_listing_type.Page_key]}&amp;id={$listing.ID}{/if}">{$item.value}</a>
						{else}
							{$item.value}
						{/if}
					</div>
					{/if}
				{/foreach}
			</li>
		{/foreach}
		</ul>
	</div>
	<div class="right_nav"></div>
</div>
<!-- featured carousel -->
{/if}

<!-- user menu -->
<ul id="user_menu">
	{foreach from=$user_menu item=muser}
		<li>
			<a href="{$rlBase}{if $config.mod_rewrite}{$pages[$muser.Key]}.html{else}?page={$pages[$muser.Key]}{/if}">{$muser.name}<img src="{$rlTplBase}img/blank.gif" alt="" /></a>
		</li>
	{/foreach}
</ul>
<!-- user menu end -->

<!-- home tpl end -->