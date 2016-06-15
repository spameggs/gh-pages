{if $config.ccbill_module}
	<li id="gateway_ccbill">
		<img alt="{$lang.ccbill_payment}" src="{$smarty.const.RL_PLUGINS_URL}ccbill/static/ccbill.png" />
		<p><input {if $smarty.post.gateway == 'ccbill'}checked="checked"{/if} type="radio" name="gateway" value="ccbill" /></p>
	</li>
	<script type="text/javascript">
		var plans_ccbill = Array();
		{foreach from=$plans_ccbill item='plan'}
			plans_ccbill[{$plan.ID}] = new Array();
			plans_ccbill[{$plan.ID}]['ccbill_allowedTypes'] = '{$plan.ccbill_allowedTypes}';
		{/foreach}
		var plan_ccbill_allowedTypes = '{$ccbill_allowedTypes}';
		{literal} 
		$(document).ready(function()
		{
			if(plan_ccbill_allowedTypes == '')
			{
				$('#gateway_ccbill').fadeOut('fast');
			}
			$('ul.plans>li').click(function()
			{
				selected_plan_id = $(this).find('input[name=plan]').attr('id').split('_')[1];
				var plan_ccbill = plans_ccbill[selected_plan_id];

				var ccbill_allowedTypes = plan_ccbill['ccbill_allowedTypes'];

				if(ccbill_allowedTypes == '')
				{
					$('#gateway_ccbill').fadeOut('fast');
				}
				else
				{ 
					$('#gateway_ccbill').fadeIn('normal');
				}
			});
			if(plan_ccbill_allowedTypes == '')
			{           
				var _selected_plan_id = $('ul.plans input[name=plan]:checked').attr('id').split('_')[1];
				var plan_ccbill = plans_ccbill[_selected_plan_id];
                                                           
				var ccbill_allowedTypes = plan_ccbill['ccbill_allowedTypes'];

				if(ccbill_allowedTypes == '')
				{
					$('#gateway_ccbill').fadeOut('fast');
				}
				else
				{ 
					$('#gateway_ccbill').fadeIn('normal');
				}
			}
		});
		{/literal} 
	</script>	
{/if}