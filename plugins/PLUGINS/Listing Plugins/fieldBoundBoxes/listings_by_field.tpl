{if !empty($listings)}
	{if !empty($description)}
		<div class="highlight" style="margin: 0 0 15px;">
			{$description}
		</div>
	{/if}
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'grid_navbar.tpl'}
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'grid.tpl' hl=true}
	<script type="text/javascript">flynax.highlightSRGrid($('div.refine input[name="f\[keyword_search\]"]').val());</script>
	<!-- paging block -->
	{paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page url=$smarty.get.nvar_2 controller=$smarty.get.nvar_1 method=$listing_type.Submit_method}
	<!-- paging block end -->
{elseif $options}
	<div class="field_bound_box categories">
		<ul>
			<li>
			<table class="fixed">
			<tr>
			{foreach from=$options item='option' name='fCats'}
				<td valign="top">
					<div class="item">
						{if ($icons_position == 'left' || $icons_position == 'top') && $option.Icon}
							<div style="{if $icons_position == 'left'}display: inline;{else}display: block;{/if}">
								<a class="category cat_icon" title="{$lang[$option.pName]}" href="{$rlBase}{if $config.mod_rewrite}{$path}/{$option.Key}{if $html_postfix}.html{else}/{/if}{else}?page={$pages.listings_by_field}&amp;{$path}={$option.Key}{/if}">
									<img src="{$smarty.const.RL_URL_HOME}files/{$option.Icon}" title="{$lang[$option.pName]}" alt="{$lang[$option.pName]}" />
								</a>
							</div>
						{/if}

						<a class="category" title="{$lang[$option.pName]}" href="{$rlBase}{if $config.mod_rewrite}{$path}/{$option.Key}{if $html_postfix}.html{else}/{/if}{else}?page={$pages.listings_by_field}&amp;{$path}={$option.Key}{/if}">{$lang[$option.pName]}</a>
						{if $show_count}<span>(<b>{$option.Count}</b>)</span>{/if}

						{if ($icons_position == 'right' || $icons_position == 'bottom') && $option.Icon}
							<div style="{if $icons_position == 'right'}display: inline;{else}display: block;{/if}">
								<a class="category cat_icon" title="{$lang[$option.pName]}" href="{$rlBase}{if $config.mod_rewrite}{$path}/{$option.Key}{if $html_postfix}.html{else}/{/if}{else}?page={$pages.listings_by_field}&amp;{$path}={$option.Key}{/if}">
									<img src="{$smarty.const.RL_URL_HOME}files/{$option.Icon}" title="{$lang[$option.pName]}" alt="{$lang[$option.pName]}" />
								</a>
							</div>
						{/if}
					</div>
				</td>
				{if $smarty.foreach.fCats.iteration%$columns_number == 0 && !$smarty.foreach.fCats.last}
				</tr>
				<tr>
				{/if}
			{/foreach}
			</tr>
			</table>
			</li>
		</ul>
		<div class="clear"></div>
	</div>
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