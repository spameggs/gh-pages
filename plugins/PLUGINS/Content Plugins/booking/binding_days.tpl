<div id="bindings_obj" style="margin: 10px 0">
	<form id="binding_days_form" method="post">
	<table class="sTable">
		<tr>
			<td align="center" class="static title_bg" style="width: 20px;">#</td>
			<td style="width: 5px;"></td>
			<td class="static title_bg"><div>{$lang.booking_checkin}</div></td>
			<td style="width: 5px;"></td>
			<td class="static title_bg"><div>{$lang.booking_checkout}</div></td>
			<td style="width: 5px;"></td>
			<td align="center" class="static title_bg" style="width: 70px;">{$lang.actions}</td>
		</tr>
		<tr id="bind_days_name">
			<td class="grey_line_1 grey_small" align="center">1</td>
			<td></td>
			<td class="grey_line_1  grey_small" style="padding: 8px;">
				<b>{if $binding_days.Checkin}{$binding_days.Checkin}{else}{$lang.booking_not_set}{/if}</b>
			</td>
			<td></td>
			<td class="grey_line_1  grey_small" style="padding: 8px;">
				<b>{if $binding_days.Checkout}{$binding_days.Checkout}{else}{$lang.booking_not_set}{/if}</b>
			</td>
			<td></td>
			<td class="grey_line_1" align="center">
				<img alt="{$lang.edit}" title="{$lang.edit}" src="{$smarty.const.RL_PLUGINS_URL}booking/img/edit.png" onclick="bind_edit()" style="cursor: pointer;" />
			</td>
		</tr>
		<tr id="bind_days_checkbox" class="hide">
			<td class="grey_line_1 grey_small" align="center"></td>
			<td></td>
			<td class="grey_line_1  grey_small" style="padding: 8px;">
			{foreach from=$mass_days key='kD' item='day'}
				<input type="checkbox" {if in_array($day,","|explode:$binding_days.Checkin)}checked="true" {/if}id="day_in_{$kD}" name="in" value="{$day}" /><label for="day_in_{$kD}"> {$day}</label><br />
			{/foreach}
			</td>
			<td></td>
			<td class="grey_line_1  grey_small" style="padding: 8px;">
			{foreach from=$mass_days key='kD' item='day'}
				<input type="checkbox" {if in_array($day,","|explode:$binding_days.Checkout)}checked="true" {/if}id="day_out_{$kD}" name="out" value="{$day}" /><label for="day_out_{$kD}"> {$day}</label><br />
			{/foreach}
			</td>
			<td></td>
			<td class="grey_line_1" align="center">
				<img class="save" alt="{$lang.save}" title="{$lang.save}" src="{$smarty.const.RL_PLUGINS_URL}booking/img/save.png" onclick="save_binding_days();" style="cursor: pointer;" />
			</td>
		</tr>
	</table>
	</form>
</div>
