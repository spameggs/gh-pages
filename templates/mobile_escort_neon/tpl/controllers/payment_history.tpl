<!-- payment history tpl -->

{if $transactions}
	<div class="highlight">		
		<table class="list" id="saved_search">
		<tr class="header">
			<td>{$lang.item}</td>
			<td class="divider"></td>
			<td style="width: 150px;"><div title="{$lang.amount}" class="text-overflow">{$lang.info}</div></td>
		</tr>
		{foreach from=$transactions item='item' name='transactionF'}
		<tr class="body" id="item_{$item.ID}">
			<td>
				<span class="text">{$item.plan_info}</span>
				<div>
					{if $item.item_info}
						<a href="{$item.link}">{$item.item_info}</a>
					{else}
						<span class="red">{$lang.item_not_available}</span>
					{/if}
				</div>
			</td>
			<td class="divider"></td>
			<td>
				<table class="info">
				<tr>
					<td class="name">{$lang.amount}:</td>
					<td class="value">{if $config.system_currency_position == 'before'}{$config.system_currency}{/if} {$item.Total} {if $config.system_currency_position == 'after'}{$config.system_currency}{/if}</td>
				</tr>
				<tr>
					<td class="name">{$lang.payment_gateway}:</td>
					<td class="value">{$item.Gateway}</td>
				</tr>
				<tr>
					<td class="name">{$lang.txn_id}:</td>
					<td class="value">{$item.Txn_ID}</td>
				</tr>
				<tr>
					<td class="name">{$lang.date}:</td>
					<td class="value">{$item.Date|date_format:$smarty.const.RL_DATE_FORMAT}</td>
				</tr>
				</table>
			</td>
		</tr>
		{/foreach}
		</table>
		
		{paging calc=$pInfo.calc total=$transactions|@count current=$pInfo.current per_page=$config.transactions_per_page}
	</div>
{else}
	<div class="info padding">{$lang.no_account_transactions}</div>
{/if}
<!-- payment history tpl end -->