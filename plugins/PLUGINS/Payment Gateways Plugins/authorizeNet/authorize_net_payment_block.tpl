{if $config.aNet_module && $config.aNet_transaction_key && $config.aNet_account_id}
	<li id="gateway_authorizeNet">
		<img alt="{$lang.aNet_payment}" src="{$smarty.const.RL_PLUGINS_URL}authorizeNet/static/authorizeNet.png" />
		<p><input {if $smarty.post.gateway == 'authorizeNet'}checked="checked"{/if} type="radio" name="gateway" value="authorizeNet" /></p>
	</li>
{/if}