<!-- renew tpl -->

{assign var='sPost' value=$smarty.post}
<div class="highlight clear">

	<!-- checkout -->
	<div class="area_checkout step_area">

		{if isset($smarty.get.canceled)}
			<script type="text/javascript">
				printMessage('error', '{$lang.bannersNoticePaymentCanceled}', 0, 1);
			</script>
		{/if}

		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='plans' name=$lang.select_plan tall=true}
			{include file=$smarty.const.RL_PLUGINS|cat:'banners'|cat:$smarty.const.RL_DS|cat:'banner_plans.tpl' plans=$planInfo}
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}

		<!-- select a payment gateway -->
		{if $planInfo[$sPost.plan].Price}{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='gateways' name=$lang.payment_gateways}{/if}
		<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?id={$bannerInfo.ID}{else}?page={$pageInfo.Path}&amp;{$bannerInfo.ID}{/if}">
			<input type="hidden" name="go" value="1" />

			{if $planInfo[$sPost.plan].Price}
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
			{/if}

			<table class="form">
			<tr>
				<td class="name">
					<input type="submit" name="submit" value="{$lang.upgrade}" id="checkout_submit" />
				</td>
			</tr>
			</table>
		</form>
		{if $planInfo[$sPost.plan].Price}{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}{/if}
		<!-- select a payment gateway end -->

		<script type="text/javascript">
			flynax.paymentGateway();
		</script>
	</div>
	<!-- checkout end -->

</div>

<!-- renew tpl end -->