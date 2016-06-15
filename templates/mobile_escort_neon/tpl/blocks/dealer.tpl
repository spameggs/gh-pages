<!-- dealer block -->

<li>
	<table class="sTable">
	<tr>
		<td valign="top" class="image">			
			<div class="img">
				<a title="{$dealer.Full_name}" href="{$dealer.Personal_address}">
					<img style="width: 100px;" alt="" src="{if $dealer.Photo}{$smarty.const.RL_URL_HOME}files/{$dealer.Photo}{else}{$rlTplBase}img/account.gif{/if}" />
				</a>
				{if !empty($dealer.Listings_count)}				
					<div class="count"><a title="{$lang.account_listings}" href="{$dealer.Personal_address}#listings">{$dealer.Listings_count}</a></div>
				{/if}
			</div>
		</td>
		<td valign="top">
			<div class="fields listing_group">
				<a title="{$dealer.First_name} {$dealer.Last_name}" href="{$dealer.Personal_address}">
					{if !empty($dealer.First_name) || !empty($dealer.Last_name)}{$dealer.First_name} {$dealer.Last_name}{else}{$dealer.Username}{/if}
				</a>
			
				{*$lang.join_date}: {$dealer.Date|date_format:$smarty.const.RL_DATE_FORMAT*}
				
				<table>
				{foreach from=$dealer.fields item='item' key='field' name='fDealers'}
				{if !empty($item.value)}
					<tr>
						<td class="name">{$lang[$item.pName]}:</td>
						<td class="value">{$item.value}</td>
					</tr>
				{/if}
				{/foreach}
				</table>
				
			</div>
		</td>
	</tr>
	</table>
</li>

<!-- dealer block end -->