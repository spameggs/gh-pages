<div id="rate_range_obj">
	{if $smarty.const.REALM}
		{assign var='rlTplBase' value=$smarty.const.RL_URL_HOME|cat:'templates/'|cat:$config.template|cat:'/'}
	{/if}

	<form id="valid_rate_range" class="ufvalid" action="#" method="post">
		<table class="list" id="rate_ranges_table">
		<tr class="header">
			<td align="center" class="no_padding" style="width: 15px;">#</td>
			<td class="divider"></td>
			<td>{$lang.from}</td>
			<td class="divider"></td>
			<td>{$lang.to}</td>
			<td class="divider"></td>
			<td style="width: 100px;">{$lang.price}</td>
			<td class="divider"></td>
			<td style="width: 50px;">{$lang.booking_desc}</td>
			<td class="divider"></td>
			<td style="width: 65px;">{$lang.actions}</td>
		</tr>
		{foreach from=$rate_range item='rRange' name='rate_rageF'}
		<tr class="body">
			<td class="no_padding" align="center"><span class="text">{$smarty.foreach.rate_rageF.iteration}</span></td>
			<td class="divider"></td>
			<td><span class="text">{$rRange.From|date_format:$smarty.const.RL_DATE_FORMAT}</span></td>
			<td class="divider"></td>
			<td><span class="text">{$rRange.To|date_format:$smarty.const.RL_DATE_FORMAT}</span></td>
			<td class="divider"></td>
			<td>
				<span class="text">
					{if $rRange.Price == 0}{$lang.booking_close_days}{else}{$defPrice.currency} {str2money string=$rRange.Price}{/if}
				</span>
			</td>
			<td class="divider"></td>
			<td align="center">
				<span class="text">
					{assign var='qtip_e' value=" <a href='#' onclick='edit_desc("|cat:$smarty.foreach.rate_rageF.iteration|cat:")'>"|cat:$lang.edit|cat:"</a>"}
					<img class="qtip" alt="" title="{if !empty($rRange.desc)}{$rRange.desc}{else}{$lang.not_available}{/if}{$qtip_e}" id="desc_ico_{$smarty.foreach.rate_rageF.iteration}" src="{$rlTplBase}img/blank.gif" />
				</span>
			</td>
			<td class="divider"></td>
			<td>
				<span class="text">
					<img class="remove" onclick="rlConfirm( '{$lang.booking_remove_confirm}', 'xajax_deleteRateRange', Array('{$rRange.ID}'), 'listing_loading' );" src="{$rlTplBase}img/blank.gif" />
				</span>
			</td>
		</tr>

		<tr class="hide" id="rate_desc_{$smarty.foreach.rate_rageF.iteration}">
			<td class="name"></td>
			<td class="field" colspan="8">
				<textarea id="save_desc_{$rRange.ID}" cols="30" rows="2">{$rRange.desc}</textarea>
			</td>
			<td class="divider"></td>
			<td>
				<input type="button" onclick="save_desc({$rRange.ID});" value="{$lang.save}" />
			</td>
		</tr>
		{/foreach}
		<tr class="body hide" id="rate_range_before"></tr>

		{if $use_time_frame}
		<tr class="body">
			<td class="no_padding" align="center"><span class="text">{math equation="x + 1" x=$smarty.foreach.rate_rageF.iteration}</span></td>
			<td class="divider"></td>
			<td colspan="3"><span class="text">{$lang.booking_rate_price_per_day}</span></td>
			<td class="divider"></td>
			<td><span class="text">{$defPrice.name}</span></td>
			<td class="divider"></td>
			<td align="center">
				<span class="text">
					{assign var='qtip_e_regular' value=" <a href='#' onclick='edit_desc(0, 1);'>"|cat:$lang.edit|cat:"</a>"}
					<img class="qtip" alt="" title="{if !empty($range_regular_desc)}{$range_regular_desc}{else}{$lang.not_available}{/if}{$qtip_e_regular}" id="desc_ico_regular" src="{$rlTplBase}img/blank.gif" />
				</span>
			</td>
			<td class="divider"></td>
			<td><span class="text">{$lang.not_available}</span></td>
		</tr>

		<tr class="hide" id="rate_desc_regular">
			<td class="name"></td>
			<td class="field" colspan="8">
				<textarea id="save_desc_regular" cols="30" rows="2">{$rRange.desc}</textarea>
			</td>
			<td class="divider"></td>
			<td>
				<input type="button" onclick="save_desc(0,1);" value="{$lang.save}" />
			</td>
		</tr>
		{/if}

		</table>

		<table class="sTable">
			<tr>
				<td align="left"><input class="hide button" type="button" value="{$lang.save}" id="label_save_range" /></td>
				<td align="right"><div style="margin: 5px 10px;"><a class="static" id="label_range" href="javascript:void(0);" onclick="add_rate_range();">{$lang.booking_rate_add}</a></div></td>
			</tr>
		</table>
	</form>
</div>
