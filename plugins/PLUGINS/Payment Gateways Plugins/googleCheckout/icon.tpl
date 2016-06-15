<!-- google checkout icon tpl -->
{if $config.googleCheckout_module && $config.googleCheckout_currency && $config.googleCheckout_merchant_id}
	<li>
		<img alt="{$lang.googleCheckout_button_title}" title="{$lang.googleCheckout_button_title}" src="{$smarty.const.RL_PLUGINS_URL}googleCheckout/static/googleCheckout.png" />
		<p><input title="{$lang.googleCheckout_button_title}" {if $smarty.post.gateway == 'googleCheckout'}checked="checked"{/if} type="radio" name="gateway" value="googleCheckout" /></p>
	</li>
{/if}
<!-- google checkout icon tpl end -->