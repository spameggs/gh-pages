<!-- compare table tpl -->
{if $save_mode}
	{if $isLogin}
		<div class="highlight">
			<form action="{$rlBase}{if $config.mod_rewrite}{$pages.compare_listings}/save.html{else}?page={$pages.compare_listings}&amp;save{/if}" method="POST">
				<input type="hidden" name="action" value="save" />
				<table class="submit">
				<tr>
					<td class="name">{$lang.name} <span class="red">*</span></td>
					<td class="field"><input type="text" name="name" value="{$smarty.post.name}" /></td>
				</tr>
				<tr>
					<td class="name">{$lang.compare_save_as}</td>
					<td class="field">
						<label><input type="radio" name="type" value="private" {if $smarty.post.type == 'private' || !$smarty.post.type}checked="checked"{/if} /> {$lang.compare_private}</label>
						<label><input type="radio" name="type" value="public" {if $smarty.post.type == 'public'}checked="checked"{/if} /> {$lang.compare_public}</label>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="field"><input type="submit" value="{$lang.save}" /></td>
				</tr>
				</table>
			</form>
		</div>
	{else}
		<div class="highlight">
			{include file='menus'|cat:$smarty.const.RL_DS|cat:'account_menu.tpl'}
		</div>
	{/if}
{else}
	{if $fields_out && $compare_listings}
		<div class="highlight">
			<!-- fields column -->
			<table class="compare">
			<tr {if $saved_list}class="deny"{/if}>
				<td valign="top" class="fields-column">
					<table class="list table">
					{foreach from=$fields_out item='c_field'}
					<tr class="header">
						<td class="item name">{$c_field.name}</td>
					</tr>
					<tr>
						<td class="divider"></td>
					</tr>
					{/foreach}
					</table>
				</td>
				<td valign="top" class="fields-content">
					<div class="scroll">
					<table class="table">
					{foreach from=$fields_out item='c_field'}
						<tr class="in">
						{foreach from=$compare_listings item='compare_listing'}
							{if $c_field.Key == 'Main_photo'}
								<td class="value item side_bar listing_{$compare_listing.ID}">
									<div class="preview">
										<img style="width: 120px;height: 90px;" title="{$compare_listing.listing_title}" alt="{$compare_listing.listing_title}" src="{$compare_listing[$c_field.Key]}" />
										<div class="remove" id="remove_from_compare_{$compare_listing.ID}"></div>
									</div>
								</td>
							{elseif $c_field.Key == 'listing_title'}
								<td class="value item listing_{$compare_listing.ID}" valign="top">
									<a href="{$compare_listing.listing_link}">{$compare_listing.fields[$c_field.Key].value}</a>
								</td>						
							{else}
								<td class="value item listing_{$compare_listing.ID}">
									{if $compare_listing.fields[$c_field.Key].value != ''}
										{$compare_listing.fields[$c_field.Key].value}
									{else}
										-
									{/if}
								</td>
							{/if}
						{/foreach}
						</tr>
						<tr>
							<td class="divider"></td>
						</tr>
					{/foreach}
					</table>	
					</div>
				</td>
			</tr>
			</table>
			<!-- fields column end -->
		</div>
		<span class="info hide" id="compare_no_data">{$lang.compare_no_listings_to_compare}</span>
		<script type="text/javascript">
		{literal}
		$(document).ready(function(){
			flCompare.fixSizes();
			flCompare.fieldHover();
			flCompare.removeFromTable();
			flCompare.modeSwitcher();
		});
		{/literal}
		</script>
	{else}
		{if !$errors}
			<span class="info">{$lang.compare_no_listings_to_compare}</span>
		{/if}
	{/if}
{/if}
<!-- compare table tpl end -->