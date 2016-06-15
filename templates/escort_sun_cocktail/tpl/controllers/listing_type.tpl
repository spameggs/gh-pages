<!-- listing type -->

{rlHook name='browseTop'}

<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/numeric.js"></script>

<!-- search results -->
{if $search_results}
	
	{if !empty($listings)}
	
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'grid_navbar.tpl'}
	
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'grid.tpl' hl=true}
		<script type="text/javascript">flynax.highlightSRGrid($('div.refine input[name="f\[keyword_search\]"]').val());</script>
		
		<!-- paging block -->
		{paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page url=$search_results_url method=$listing_type.Submit_method}
		<!-- paging block end -->
	
	{else}
		
		<div class="info">
			{if $listing_type.Admin_only}
				{$lang.no_listings_found_deny_posting}
			{else}
				{if $config.mod_rewrite}
					{assign var='href' value=$rlBase|cat:$pages.add_listing|cat:'.html'}
				{else}
					{assign var='href' value=$rlBase|cat:'?page='|cat:$pages.add_listing}
				{/if}
				
				{assign var='link' value='<a href="'|cat:$href|cat:'">$1</a>'}
				{$lang.no_listings_found|regex_replace:'/\[(.+)\]/':$link}
			{/if}
		</div>
		
	{/if}
	
	<script type="text/javascript">
	var save_search_notice = "{$lang.save_search_confirm}";
	flynax.saveSearch();
	flynax.multiCatsHandler();
	</script>

<!-- search results end -->
{else}
<!-- browse/search forms mode -->

	{if $advanced_search}
	
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'advanced_search.tpl'}

	{else}
	
		{if !empty($category.des)}
			<div class="highlight" style="margin: 0 0 15px;">
				{$category.des}
			</div>
		{/if}
	
		{if $listing_type.Cat_position == 'top' || $listing_type.Cat_position == 'bottom'}
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'categories.tpl' no_margin=false}
		{/if}
		
		{if $category.ID}
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
			
				{if $category.Lock}
					{assign var='br_count' value=$bread_crumbs|@count}
					{assign var='br_count' value=$br_count-2}
					
					{if $config.mod_rewrite}
						{assign var='lock_link' value=$rlBase|cat:$bread_crumbs.$br_count.path}
						{if $listing_type.Cat_postfix}
							{assign var='lock_link' value=$lock_link|cat:'.html'}
						{else}
							{assign var='lock_link' value=$lock_link|cat:'/'}
						{/if}
					{else}
						{assign var='lock_link' value=$rlBase|cat:'?page='|cat:$bread_crumbs.$br_count.path}
					{/if}
					
					{assign var='replace' value='<a title="'|cat:$lang.back_to_category|replace:'[name]':$bread_crumbs.$br_count.name|cat:'" href="'|cat:$lock_link|cat:'">'|cat:$lang.click_here|cat:'</a>'}
					<div class="info">{$lang.browse_category_locked|regex_replace:'/\[(.+)\]/':$replace}</div>
				{else}
					<div class="info">
						{if $listing_type.Admin_only}
							{$lang.no_listings_here_submit_deny}
						{else}
							{assign var='link' value='<a href="'|cat:$add_listing_href|cat:'">$1</a>'}
							{$lang.no_listings_here|regex_replace:'/\[(.+)\]/':$link}
						{/if}
					</div>
				{/if}
				
			{/if}
		{/if}
		
	{/if}
	
	<script type="text/javascript">$("input.numeric").numeric();</script>
	
{/if}
<!-- browse mode -->

{rlHook name='browseBottom'}

<!-- listing type end -->
