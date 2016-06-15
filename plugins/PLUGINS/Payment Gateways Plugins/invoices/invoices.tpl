<!-- invoice tpl -->
{if !empty($invoice_info)}
	<div class="highlight">
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='gateways' name=$lang.invoice_info}
			<table class="table">
				<tr>
					<td class="name">{$lang.invoice_txn_id}:</td>
					<td class="value">{$invoice_info.Txn_ID}</td>
				</tr>
				<tr>
					<td class="name">{$lang.invoice_subject}:</td>
					<td class="value">{$invoice_info.Subject}</td>
				</tr>
				<tr>
					<td class="name">{$lang.invoice_total}:</td>
					<td class="value"><b>{if $config.system_currency_position == 'before'}{$config.system_currency}{/if} {$invoice_info.Total} {if $config.system_currency_position == 'after'}{$config.system_currency}{/if}</b></td>
				</tr>
				<tr>
					<td class="name">{$lang.invoice_description}:</td>
					<td class="value">{$invoice_info.Description}</td>
				</tr>
			</table>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
		<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pages.invoices}/{$invoice_info.Txn_ID}.html{else}?page={$pages.invoices}&amp;item={$smarty.get.item}{/if}">
			<input type="hidden" name="submit" value="true" />
			<!-- select a payment gateway -->
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='gateways' name=$lang.payment_gateways}
				<ul id="payment_gateways">
					{if $config.use_paypal}
					<li>
						<img alt="" src="{$smarty.const.RL_LIBS_URL}payment/paypal/paypal.png" />
						<p><input {if $smarty.post.gateway == 'paypal' || !$smarty.post.gateway}checked="checked"{/if} type="radio" name="gateway" value="paypal" /></p>
					</li>
					{/if}
					{if $config.use_2co}
					<li>
						<img alt="" src="{$smarty.const.RL_LIBS_URL}payment/2co/2co.png" />
						<p><input {if $smarty.post.gateway == '2co'}checked="checked"{/if} type="radio" name="gateway" value="2co" /></p>
					</li>
					{/if}
					{rlHook name='paymentGateway'}
				</ul>
				<script type="text/javascript">
					flynax.paymentGateway();
				</script>
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
			<!-- select a payment gateway end -->
			<input type="submit" value="{$lang.invoice_pay}" />
		</form>
	</div>
{else}
	{if $invoices}
		<div class="highlight">
			<table class="list">
				<tr class="header">
					<td align="center" class="no_padding" style="width: 15px;">#</td>
					<td class="divider"></td>
					<td>{$lang.invoice_subject}</td>
					<td class="divider"></td>
					<td style="width: 50px;"><div title="{$lang.amount}" class="text-overflow">{$lang.invoice_total}</div></td>
					<td class="divider"></td>
					<td style="width: 90px;"><div title="{$lang.txn_id}" class="text-overflow">{$lang.invoice_txn_id}</div></td>
					<td class="divider"></td>
					<td style="width: 70px;">{$lang.date}</td>
					<td class="divider"></td>
					<td style="width: 100px;">{$lang.status}</td>
					<td class="divider"></td>
					<td style="width: 70px;"></td>
				</tr>
				{foreach from=$invoices item='item' name='invoiceF'}
				<tr class="body" id="item_{$item.ID}">
					<td class="no_padding" align="center"><span class="text">{$smarty.foreach.invoiceF.iteration}</span></td>
					<td class="divider"></td>
					<td class="text-overflow">
						{if $item.pStatus == 'unpaid'}
							<span class="text">{$item.Subject}</span>
						{else}
							<a href="#" id="{$item.Txn_ID}" onClick="initFlModal(this, 'txn_{$item.Txn_ID}');" ref="nofollow">{$item.Subject}</a>
						{/if}
					</td>
					<td class="divider"></td>
					<td><b>{if $config.system_currency_position == 'before'}{$config.system_currency}{/if} {$item.Total} {if $config.system_currency_position == 'after'}{$config.system_currency}{/if}</b></td>
					<td class="divider"></td>
					<td><span class="text">{$item.Txn_ID}</span></td>
					<td class="divider"></td>
					<td><span class="text">{$item.Date|date_format:$smarty.const.RL_DATE_FORMAT}</span></td>
					<td class="divider"></td>
					<td class="text-overflow"><span class="invoice_{$item.pStatus}">{$lang[$item.pStatus]}</span></td>
					<td class="divider"></td>
					<td>{if $item.pStatus == 'unpaid'}<a href="{$rlBase}{if $config.mod_rewrite}{$pages.invoices}/{$item.Txn_ID}.html{else}?page={$pages.invoices}&amp;item={$item.Txn_ID}{/if}" class="a_link">{$lang.invoice_pay}</a>{/if}</td>
				</tr>
				{/foreach}
			</table>
			{paging calc=$pInfo.calc total=$invoices|@count current=$pInfo.current per_page=$config.invoices_per_page}
		</div>
	{else}
		<div class="info">{$lang.no_account_invoices}</div>
	{/if}
{/if}
<!-- invoice tpl end -->