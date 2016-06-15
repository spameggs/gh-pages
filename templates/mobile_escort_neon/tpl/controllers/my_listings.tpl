<!-- my listings -->

{if !empty($listings)}

	<!-- sorting -->
	<form method="get" action="">
		<div class="sorting">
			<select name="sort_by" class="default w90">
				<option value="">{$lang.select}</option>
				{foreach from=$sorting item='field_item' key='sort_key' name='fSorting'}
					<option value="{$sort_key}" {if $sort_by == $sort_key}selected="selected"{/if}>{$field_item.name}</option>
				{/foreach}
			</select>
			<select name="sort_type" class="default w110">
				<option value="asc">{$lang.ascending}</option>
				<option value="desc" {if $smarty.get.sort_type == 'desc'}selected="selected"{/if}>{$lang.descending}</option>
			</select>
			<input class="default" type="submit" name="submit" value="{$lang.sort}" />
		</div>
	</form>
	<!-- sorting end -->
	
	{rlHook name='myListingsBeforeListings'}
	
	<div id="listings" class="my_listings">
	{foreach from=$listings item='listing' key='key' name='listingsF'}
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'my_listing.tpl'}
	{/foreach}
	</div>
	
	<script type="text/javascript">
	{literal}
	
	$(document).ready(function(){
		$('div#listings img.delete_highlight').each(function(){
			$(this).flModal({
				caption: '{/literal}{$lang.warning}{literal}',
				content: '{/literal}{$lang.notice_delete_listing}{literal}',
				prompt: 'xajax_deleteListing('+ $(this).attr('id').split('_')[2] +')',
				width: 'auto',
				height: 'auto'
			});
		});
	});
	
	{/literal}
	</script>

	<!-- paging block -->
	{paging calc=$pInfo.calc total=$listings current=$pInfo.current per_page=$config.listings_per_page}
	<!-- paging block end -->

{else}
	<div class="info padding">
		{assign var='link' value='<a href="'|cat:$add_listing_href|cat:'">$1</a>'}
		{$lang.no_listings_here|regex_replace:'/\[(.+)\]/':$link}
	</div>
{/if}

{rlHook name='myListingsBottom'}

<!-- my listings end -->
