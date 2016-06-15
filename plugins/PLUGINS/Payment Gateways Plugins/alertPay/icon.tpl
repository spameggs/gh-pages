<!-- alert pay icon tpl -->
<li>
	<img alt="{$lang.alertPay_title}" title="{$lang.alertPay_title}" src="{$smarty.const.RL_PLUGINS_URL}alertPay/static/alert-pay.png" />
	<p><input title="{$lang.alertPay_title}" {if $smarty.post.gateway == 'alertPay'}checked="checked"{/if} type="radio" name="gateway" value="alertPay" /></p>
</li>
<!-- alert pay icon tpl end -->