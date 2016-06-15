<!-- report broken listing form -->
<div class="hide" id="reportBrokenListing_form">
	<div class="caption">{$lang.reportbroken_add_comment}</div>		
	<table class="submit_modal">			
		<tr>
			<td colspan="2">
				<div>{$lang.message} <span class="red">*</span></div>
				<textarea id="message_text" rows="6" cols="" style="width: 97%;"></textarea>
			</td>
		</tr>
	<tr>
		<td colspan="2" {if !$isLogin}class="button"{/if}>
			<input type="submit" name="send" value="{$lang.send}" onclick="xajax_reportBrokenListing( reportBrokenLisitngID, $('#message_text').val())"/>
			<input type="button" name="close" value="{$lang.cancel}" />
		</td>
	</tr>
	</table>
</div>
<script type="text/javascript">
lang['reportbroken_remove_in'] = "{$lang.reportbroken_remove_in}";
lang['reportbroken_add_in'] = "{$lang.reportbroken_add_in}";
lang['reportbroken_do_you_want_to_delete_list'] = "{$lang.reportbroken_do_you_want_to_delete_list}";
rlConfig['reportBroken_message_length'] = {if $config.reportBroken_message_length}{$config.reportBroken_message_length}{else}300{/if};
</script>
<!-- report broken listing form end -->