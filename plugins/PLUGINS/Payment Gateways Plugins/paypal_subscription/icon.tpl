<!-- paypal subscription icon tpl -->
<li>
	<img alt="{$lang.paypal_subscription_title}" title="{$lang.paypal_subscription_title}" src="{$smarty.const.RL_PLUGINS_URL}paypal_subscription/static/paypal-subscription.png" />
	<p><input title="{$lang.paypal_subscription_title}" {if $smarty.post.gateway == 'paypal_subscription'}checked="checked"{/if} type="radio" name="gateway" value="paypal_subscription" /></p>
</li>
<!-- paypal subscription icon tpl end -->