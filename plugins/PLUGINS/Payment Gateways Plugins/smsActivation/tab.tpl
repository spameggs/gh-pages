<!-- sms activation tab -->
<div id="area_smsActivation" class="tab_area">
	<div class="highlight">
		<div class="info">{$success_code}</div>
		<table style="table-layout: fixed;margin-top: 10px;" class="sTable">
		<tr>
			<td>					
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
			</td>
			<td style="width: 20px;">
				<span class="grey_middle"><b>{$lang.smsActivation_or}</b></span>
			</td>
			<td align="center">
				<input name="new_code" type="button" value="{$lang.smsActivation_get_code}"  />
			</td>
		</tr>
		</table>
	</div>
	
	<script type="text/javascript">
	{literal}
	
	$('input[name=new_code]').flModal({
		caption: '{/literal}{$lang.warning}{literal}',
		content: '{/literal}{$lang.smsActivation_get_code_confirm}{literal}',
		prompt: '$("input[name=new_code]").val(lang[\'loading\']);xajax_smsActivationSendCode()',
		width: 'auto',
		height: 'auto'
	});
	
	$(document).ready(function(){
		$('div.tab_area:not(##area_smsActivation)').hide();
	});
	
	var smsCheck = function(){
		$('input[name=sms_submit]').val(lang['loading']);
		xajax_smsActivationCheckp($('#sms_code').val());
		return false;
	}
	{/literal}
	</script>
</div>
<!-- sms activation tab end -->