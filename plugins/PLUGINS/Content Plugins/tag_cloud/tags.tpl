<!-- tags tpl -->

{if $tag_info}
	{if !empty($tag_info.des)}
		<div class="highlight" style="margin: 0 0 15px;">
			{$tag_info.des}
		</div>
	{/if}

	<div class="listings_area">
		{if !empty($listings)}
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'grid_navbar.tpl'}
							
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'grid.tpl' hl=true}

			{if $config.tc_highlight_tag}
				<script type="text/javascript">flynax.highlightSRGrid('{$smarty.session.keyword_search_data.keyword_search}');</script>
			{/if}
			
			<!-- paging block -->
			{if $config.mod_rewrite}
				{paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page url=$tag_info.Path}
			{else}
				{paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page url=$tag_info.Path var='tag'}
			{/if}
			<!-- paging block end -->
		{else}
			{if $keyword_search}
				{if $config.mod_rewrite}
					{assign var='href' value=$rlBase|cat:$pages.add_listing|cat:'.html'}
				{else}
					{assign var='href' value=$rlBase|cat:'index.php?page='|cat:$pages.add_listing}
				{/if}
				
				{assign var='link' value='<a href="'|cat:$href|cat:'">$1</a>'}
				<div class="info">{$lang.no_listings_found|regex_replace:'/\[(.+)\]/':$link}</div>
			{/if}
		{/if}
	</div>

{else}
	{include file=$smarty.const.RL_PLUGINS|cat:"tag_cloud"|cat:$smarty.const.RL_DS|cat:"tag_cloud.tpl"}
{/if}
<!-- tags tpl end -->
