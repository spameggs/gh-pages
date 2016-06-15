<!-- Invoice Details -->
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'} 
	<fieldset class="light">
		<legend id="legend_search_settings" class="up" onclick="fieldset_action('search_settings');">{$lang.invoice_details}</legend>
		<table class="list">
		<tr>
			<td class="name">{$lang.invoice_txn_id}:</td>
			<td class="value"><b>{$invoice_info.Txn_ID}</b></td>
		</tr>
		<tr>
			<td class="name">{$lang.username}:</td>
			<td class="value"><a href="{$rlBase}index.php?controller=accounts&amp;action=view&amp;id={$invoice_info.Account_ID}">{$invoice_info.Username}</a></td>
		</tr>
		<tr>
			<td class="name">{$lang.date}:</td>
			<td class="value">{$invoice_info.Date|date_format:$smarty.const.RL_DATE_FORMAT}</td>
		</tr>
		{if !empty($invoice_info.Total)}
		<tr>
			<td class="name">{$lang.price}:</td>
			<td class="value"><b>{$invoice_info.Total} {$config.system_currency}</b></td>
		</tr>
		{/if}
		<tr>
			<td class="name">{$lang.invoice_subject}:</td>
			<td class="value">{$invoice_info.Subject}</td>
		</tr>
		<tr>
			<td class="name">{$lang.invoice_description}:</td>
			<td class="value">{$invoice_info.Description}</td>
		</tr>
		<tr>
			<td class="name">{$lang.status}:</td>
			<td class="value">{$lang[$invoice_info.pStatus]}</td>
		</tr>
	</table>
	</fieldset>
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
<!-- ens Invoice Details -->