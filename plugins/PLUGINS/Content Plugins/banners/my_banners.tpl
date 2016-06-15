<!-- my banners -->

{if $myBanners}
	<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.fancybox.js"></script>
	<table class="grid_navbar my_listings">
	<tr>
		<td class="sorting">
			<span class="caption">{$lang.banners_sortBy}:</span>
			{foreach from=$sorting item='field_item' key='sort_key' name='fSorting'}
				<a {if $sort_by == $sort_key}class="active {if $sort_type == 'asc' || empty($sort_type)}asc{else}desc{/if}"{/if} title="{$lang.banners_sortBy} {$field_item.name}" href="{if $config.mod_rewrite}?{else}{$smarty.const.RL_URL_HOME}index.php?page={$pageInfo.Path}&amp;{/if}sort_by={$sort_key}{if $sort_by == $sort_key}&amp;sort_type={if $sort_type == 'asc' || !isset($sort_type)}desc{elseif !empty($sort_key) && empty($sort_type)}desc{else}asc{/if}{/if}">{$field_item.name}</a>
				{if !$smarty.foreach.fSorting.last}<span class="divider">|</span>{/if}
			{/foreach}
		</td>
	</tr>
	</table>

	<div id="listings" class="my_listings">
	{foreach from=$myBanners item='mBanner' name='transactionF'}
		{include file=$smarty.const.RL_PLUGINS|cat:'banners'|cat:$smarty.const.RL_DS|cat:'banner.tpl'}
	{/foreach}
	</div>

	<script type="text/javascript">
	{literal}
		$(document).ready(function(){
			$('div#listings img.delete_highlight').each(function() {
				$(this).flModal({
					caption: '{/literal}{$lang.warning}{literal}',
					content: '{/literal}{$lang.notice_delete_listing}{literal}',
					prompt: 'xajax_deleteBanner('+ $(this).attr('id').split('_')[2] +', 1)',
					width: 'auto',
					height: 'auto'
				});
			});

			$('td.photo a').fancybox({
				titlePosition: 'inside'
			});
		});
	{/literal}
	</script>

	<!-- paging block -->
	{paging calc=$pInfo.calc total=$myBanners current=$pInfo.current per_page=$config.listings_per_page}
	<!-- paging block end -->
{else}
	<div class="info">
		{assign var='link' value='<a href="'|cat:$add_banner_href|cat:'">$1</a>'}
		{$lang.banners_noBannersHere|regex_replace:'/\[(.+)\]/':$link}
	</div>
{/if}

<!-- my banners end -->