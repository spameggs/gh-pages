<!-- contact us -->

<div class="padding">
	{if $smarty.get.sending == 'complete'}
		{$lang.contact_sent}
	{else}
		<form action="" method="post">
		<input type="hidden" name="action" value="contact_us" />
		
		<table class="submit">
		<tr>
			<td class="name">{$lang.your_name} <span class="red">*</span></td>
			<td class="field"><input type="text" name="your_name" maxlength="30" value="{if $smarty.post.your_name}{$smarty.post.your_name}{elseif !empty($account_info.First_name) || !empty($account_info.Last_name)}{$account_info.First_name} {$account_info.Last_name}{/if}" /></td>
		</tr>
		<tr>
			<td class="name">{$lang.your_email} <span class="red">*</span></td>
			<td class="field"><input type="text" name="your_email" maxlength="30" value="{if $smarty.post.your_email}{$smarty.post.your_email}{else}{$account_info.Mail}{/if}" /></td>
		</tr>
		
		{rlHook name='contactFields'}
		
		<tr>
			<td colspan="2" class="name">
				<div class="nae">{$lang.message} <span class="red">*</span></div>
				<textarea name="message" rows="6" cols="30">{$smarty.post.message}</textarea>
			</td>
		</tr>
		{if $config.security_img_contact_us}
		<tr>
			<td>
				<span class="field">{$lang.security_code} <span class="red">*</span></span>
			</td>
			<td>
				{include file='captcha.tpl' no_caption=true}
			</td>
		</tr>
		{/if}
		<tr>
			<td></td>
			<td>
				<input onclick="$('#c_loading').fadeIn('normal');" class="tall" type="submit" name="finish" value="{$lang.send}" />
				<span class="loading" id="c_loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
			</td>
		</tr>
		</table>
		</form>
	{/if}
</div>

<!-- contact us end -->