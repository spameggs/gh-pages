{if $config.smscoin_module}
	<li id="gateway_smscoin">
		<img alt="{$lang.smscoin_payment}" src="{$smarty.const.RL_PLUGINS_URL}smsCoin/img/smscoin.png" />
		<p><input {if $smarty.post.gateway == 'smsCoin'}checked="checked"{/if} type="radio" name="gateway" value="smsCoin" /></p>
	</li>
	<script type="text/javascript">
	var country_price = parseFloat({$country_price});
	var plan_price = parseFloat({$plan_price});
	{literal} 
	$(document).ready(function()
	{
		if(plan_price > country_price)
		{
			$('#gateway_smscoin').fadeOut('fast');
		}

		$('ul.plans>li').click(function()
		{
			selected_plan_id = $(this).find('input[name=plan]').attr('id').split('_')[1];
			var plan = plans[selected_plan_id];

			plan['Price'] = parseFloat(plan['Price']);
			if(plan['Price'] > country_price || country_price <= 0)
			{
				$('#gateway_smscoin').fadeOut('fast');
			}
			else
			{ 
				$('#gateway_smscoin').fadeIn('normal');
			}
		});  
		if(!plan_price)
		{           
			var _selected_plan_id = $('ul.plans input[name=plan]:checked').attr('id').split('_')[1];
			var plan = plans[_selected_plan_id];

			plan['Price'] = parseFloat(plan['Price']);

			if(plan['Price'] > country_price || country_price <= 0)
			{
				$('#gateway_smscoin').fadeOut('fast');
			}
			else
			{ 
				$('#gateway_smscoin').fadeIn('normal');
			}
		}
	});
	{/literal} 
</script>
{/if}