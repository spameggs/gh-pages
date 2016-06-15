{if $pageInfo.Controller == 'invoices'}
	<script type="text/javascript"> 
		{literal}
		function initFlModal(obj, element)
		{
			$(obj).flModal({
				width: 450,
				height: 'auto',
				source: '#' + element,
				click: false
			});
		}
		{/literal}
	</script>
	{foreach from=$invoices item='item'}
		<div class="hide" id="txn_{$item.Txn_ID}">
			<div class="caption_padding">{$lang.invoice_description}</div>	
			<div class="value">{$lang.invoice_txn_id} <b>{$item.Txn_ID}</b></div>
			<div class="value">{$lang.invoice_subject} <b>{$item.Subject}</b></div>
			<div class="value">{$item.Description}</div>
		</div>
	{/foreach}
{/if}
{if $pageInfo.Key != 'home' && $pageInfo.Key != 'invoices'}
<script type="text/javascript">
//<![CDATA[
	var unpaid_invoices = {$unpaid_invoices};
	var unpaid_invoices_message = '{$lang.unpaid_invoices_message|replace:"[here]":$invoice_link}';
	{literal}
	$(document).ready(function()
	{ 
	    if(unpaid_invoices > 0)
		{
			printMessage('warning', unpaid_invoices_message);
		}
	});
	{/literal}
//]]>
</script>
{/if}