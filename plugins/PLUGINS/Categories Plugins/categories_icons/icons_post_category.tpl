{if $cat.Icon && ($config.categories_icons_position == 'right' || $config.categories_icons_position == 'bottom')} 
	{if $pageInfo.Key == 'home'}
		{assign var='icon_path' value=$pages.browse}
	{else}
		{assign var='icon_path' value=$pageInfo.Path}
	{/if}
	<div style="{if $config.categories_icons_position == 'right'}display: inline;{else}display: block;{/if}">
		<a class="category cat_icon" title="{$cat.name}" href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$cat.Path}{if $listing_type.Cat_postfix}.html{else}/{/if}{else}?page={$pages[$listing_type.Page_key]}&amp;category={$cat.ID}{/if}">
			<img src="{$smarty.const.RL_URL_HOME}files/{$cat.Icon}" title="{$cat.name}" alt="{$cat.name}" />
		</a>
	</div>
{/if}