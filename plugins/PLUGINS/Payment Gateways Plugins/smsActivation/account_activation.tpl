<!-- smsActivation controller -->
{if $notice}
	<div class="info">{$notice}</div>
	
	{if $login_form}
		{assign var='account_area_phrase' value='blocks+name+account_area'}
		{include file='controllers'|cat:$smarty.const.RL_DS|cat:'login.tpl'}
	{/if}
{else}
	<div id="smsActivation_container"></div>
	<div class="highlight">
		<div class="info" style="padding: 0 0 10px;">{$success_code}</div>
		
		<form action="" method="post" onsubmit="return smsCheck();">
			<table class="submit">
			<tr>
				<td class="name">{$lang.smsActivation_code}</td>
				<td class="field"><input class="w120" type="text" id="sms_code" name="sms_code" maxlength="{$config.sms_activation_code_length}" /></td>
			</tr>
			<tr>
				<td class="name"></td>
				<td class="field"><input type="submit" name="sms_submit" value="{$lang.smsActivation_confirm}" /></td>
			</tr>
			</table>
		</form>
	</div>
	
	<script type="text/javascript">
	{literal}
	var smsCheck = function(){
		$('input[name=sms_submit]').val(lang['loading']);
		xajax_smsActivationCheck($('#sms_code').val());
		return false;
	}
	{/literal}
	</script>
{/if}
<!-- smsActivation controller end -->