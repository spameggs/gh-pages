<!-- booking availability search tpl -->

{if !empty($listings)}

	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'grid_navbar.tpl'}
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'grid.tpl'}

	<!-- paging block -->
	{if $config.mod_rewrite}
		{paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page url=$category.Path var='listing'}
	{else}
		{paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page url=$category.ID var='category'}
	{/if}
	<!-- paging block end -->

{else}

	<div class="info">
		{if $config.mod_rewrite}
			{assign var='href' value=$rlBase|cat:$pages.add_listing|cat:'.html'}
		{else}
			{assign var='href' value=$rlBase|cat:'?page='|cat:$pages.add_listing}
		{/if}

		{assign var='link' value='<a href="'|cat:$href|cat:'">$1</a>'}
		{$lang.no_listings_found|regex_replace:'/\[(.+)\]/':$link}
	</div>

{/if}

<!-- booking availability search tpl end -->