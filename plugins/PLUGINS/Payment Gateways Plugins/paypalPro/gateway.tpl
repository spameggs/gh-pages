<!-- paypalPro Plugin  -->

<li id="gateway_paypalPro">
	<img alt="{$lang.dpp_gateway}" src="{$smarty.const.RL_PLUGINS_URL}paypalPro/static/paypalPro.png" title="{$lang.dpp_gateway}" />
	<p><input {if $smarty.post.gateway == 'paypalPro'}checked="checked"{/if} type="radio" name="gateway" value="paypalPro" /></p>
</li>

<!-- end paypalPro Plugin --> 