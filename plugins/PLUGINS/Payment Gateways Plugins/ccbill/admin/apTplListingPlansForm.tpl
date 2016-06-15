<table class="form">
	<tr>       
		<td class="name">{$lang.ccbill_allowed_yypes_name}</td>
		<td class="field">
			<input name="ccbill_allowedTypes" type="text" value="{if !empty($sPost.ccbill_allowedTypes)}{$sPost.ccbill_allowedTypes}{else}{$plan_info.ccbill_allowedTypes}{/if}" maxlength="255" />
		</td>
	</tr>
</table>