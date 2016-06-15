{if $sub_cat.Icon}
	{if $pageInfo.Key == 'home'}
		{assign var='icon_path' value=$pages.browse}
	{else}
		{assign var='icon_path' value=$pageInfo.Path}
	{/if}
	<a class="category" title="{$sub_cat.name}" href="{$rlBase}{if $config.mod_rewrite}{$icon_path}/{$sub_cat.Path}{if $config.display_cat_html}.html{else}/{/if}{else}?page={$icon_path}&amp;category={$sub_cat.ID}{/if}">
		<img src="{$smarty.const.RL_URL_HOME}files/{$sub_cat.Icon}" title="{$sub_cat.name}" alt="{$sub_cat.name}" />
	</a>
{/if}