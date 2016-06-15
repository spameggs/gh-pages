<!-- loan mortgage calculator option -->
<tr>
	<td class="name">{$lang.loanMortgage_option_name}</td>
	<td class="field">
		{assign var='checkbox_field' value='loan_calc'}
		{if $sPost.$checkbox_field == '1'}
			{assign var=$checkbox_field|cat:'_yes' value='checked="checked"'}
		{elseif $sPost.$checkbox_field == '0'}
			{assign var=$checkbox_field|cat:'_no' value='checked="checked"'}
		{else}
			{assign var=$checkbox_field|cat:'_no' value='checked="checked"'}
		{/if}
		<table>
		<tr>
			<td>
				<input {$loan_calc_yes} type="radio" id="{$checkbox_field}_yes" name="{$checkbox_field}" value="1" /> <label for="{$checkbox_field}_yes">{$lang.yes}</label>
				<input {$loan_calc_no} type="radio" id="{$checkbox_field}_no" name="{$checkbox_field}" value="0" /> <label for="{$checkbox_field}_no">{$lang.no}</label>
			</td>
		</tr>
		</table>
	</td>
</tr>
<!-- loan mortgage calculator option end -->