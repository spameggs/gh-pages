<!-- PayTpv Plugin  -->
<li id="gateway_paytpv">
	<img alt="{$lang.paytpv_payment}" src="{$smarty.const.RL_PLUGINS_URL}paytpv/static/paytpv.png" />
	<p><input {if $smarty.post.gateway == 'paytpv'}checked="checked"{/if} type="radio" name="gateway" value="paytpv" /></p>
</li>
<!-- end PayTpv Plugin --> 