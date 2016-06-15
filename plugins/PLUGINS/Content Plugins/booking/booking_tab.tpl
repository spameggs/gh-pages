<!-- booking tab -->

<div id="area_booking" class="tab_area hide">
	<div class="highlight">
		{assign var='unColors' value="|"|explode:$config.booking_colors}
		<style type="text/css">
		@import url('{$smarty.const.RL_PLUGINS_URL}booking/booking.css');
		{literal}
		.dfg { font-size:9px;}
		.daySelect{ background:{/literal}{$unColors.0}{literal} !important; }
		.available{ background:{/literal}{$unColors.1}{literal}; }
		.booked{ background:{/literal}{$unColors.2}{literal}; }
		.checkin{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/booked.png'); 0 0 repeat-y; }
		.checkout{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/booked.png') -20px 0 repeat-y; }

		.prbooked{ background:{/literal}{$unColors.3}{literal}; }
		.prcheckin{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/processed.png') 0 0 repeat-y; }
		.prcheckout{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/processed.png') -20px 0 repeat-y;}

		.bprcheckin{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/booked_processed.png') 0 0 repeat-y; }
		.bprcheckout{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/booked_processed.png') -20px 0 repeat-y;}

		.closed{ background:{/literal}{$unColors.4}{literal}; }
		.closein{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/available_closed.png') 0 0 repeat-y; }
		.closeout{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/available_closed.png') -20px 0 repeat-y;}

		.bclosein{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/booked_closed.png') 0 0 repeat-y; }
		.bcloseout{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/booked_closed.png') -20px 0 repeat-y;}

		.pclosein{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/processed_closed.png') 0 0 repeat-y; }
		.pcloseout{ background:url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}booking/img/processed_closed.png') -20px 0 repeat-y;}
		{/literal}
		</style>
		<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.qtip.js"></script>
		<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}booking/js/jquery.form.js"></script>
		<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}booking/js/jquery.ufvalidator.js"></script>
		<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}booking/js/booking.js"></script>
		<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}booking/js/date.js"></script>
		<script type="text/javascript">
			var booking_debug = true;
			var listing_id = {if $config.mod_rewrite}{$smarty.get.listing_id}{else}{$smarty.get.id}{/if};
			var selected = new Array();
			var usRange = new Array();
			var closeRange = new Array();
			var defPrice = '{$defPrice.value}';
			var defCurrency = '{$defPrice.currency}';
			var total_cost = 0;
			var cur_id=0;
			var s_id = 0;
			var db_start = 0;
			var db_end = 0;
			var first=0;
			var index = 0;
			var min_bl = {$config.booking_min_book_day};
			var max_bl = {$config.booking_max_book_day};
			var bind_in_out = {$config.booking_bind_checkin_checkout};
			var fixed_range = {$config.booking_fixed_range};
			var price_delimiter = '{$config.price_delimiter}';
			var bind_checkin = '';
			var bind_checkout = '';
			var deny_text = '{$lang.booking_deny_guests}';
			var min_bl_text = '{$lang.booking_min_booking}';
			var max_bl_text = '{$lang.booking_max_booking}';
			var closed_day_text = '{$lang.booking_day_closed}';
			var booked_day_text = '{$lang.booking_day_booked}';
			var check_in_only_text = '{$lang.booking_check_in_only}';
			var check_out_only_text = '{$lang.booking_check_out_only}';
			var booking_next_step = '{$lang.booking_next_step}';
			var deny = {if $config.booking_deny_guest && !$isLogin}0{else}1{/if};
			var message_obj = '#booking_message';
			var book_btn_obj = '#nextBtn';
			var book_btp_obj = '#prevBtn';
			var day_prefix = '#day_';
			var book_display = 'none';
			var usBook = new Array();
			var bookingDateFormat = '{$smarty.const.RL_DATE_FORMAT}';

			/* phrases */
			var already_booked_text = '{$lang.booking_already_booked}';
			var booking_checkin = '{$lang.booking_checkin}';
			var booking_checkout = '{$lang.booking_checkout}';
			var booking_amount = '{$lang.booking_amount}';
			var booking_nights = '{$lang.booking_nights}';

			//
			var nextStep;

			{literal}
				$(document).ready(function(){
					xajax_getDates(listing_id);

					$('#checkValid').formValidator({
						onSuccess: function() {
							var formData = $('#ufvalid').formToArray();
							xajax_bookNow(listing_id, db_start, db_end, formData, total_cost);

							selected = [];
							index = 0;

							$(message_obj).html('');
							$('#booking_message_obj').hide();
							$('#ufvalid').resetForm();

							$('div#step_2').hide();
						},
						scope: '#ufvalid'
					});

					nextStep = function(obj) {
						$('div#booking_tab').hide();
						$('div#step_2').show();
						$(obj).hide();
					}

					$('div#step_2 span.cancel').click(function() {
						$('div#step_2').hide();
						$('div#booking_message_obj').hide();
						$('div#booking_tab').show();
						book_color(true);

						if ( typeof closeMessage == 'function' ) {
							closeMessage();
						}
					});

					$('#step_2 input, #step_2 textarea').click(function() {
						$(this).removeClass('error-input');
					}).keydown(function() {
						$(this).removeClass('error-input');
					});

					flynax.qtip();
				});
			{/literal}
		</script>

		<table class="sTable" id="booking_legend" style="table-layout: fixed;">
		<tr>
			<td valign="top">
				{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='booking_legend' name=$lang.booking_legend}
				<table class="submit">
				<tr>
					<td class="name">{$lang.booking_legend_available}:</td>
					<td class="field"><div class="available image">&nbsp;</div></td>
					<td style="width: 5px;"></td>
					<td class="name">{$lang.booking_legend_booked}:</td>
					<td class="field"><div class="booked image">&nbsp;</div></td>
				</tr>
				<tr>
					<td class="name">{$lang.booking_legend_checkin}:</td>
					<td class="field"><div class="checkin image">&nbsp;</div></td>
					<td style="width: 5px;"></td>
					<td class="name">{$lang.booking_legend_checkout}:</td>
					<td class="field"><div class="checkout image">&nbsp;</div></td>
				</tr>
				<tr>
					<td class="name">{$lang.booking_legend_precheckin}:</td>
					<td class="field"><div class="prcheckin image">&nbsp;</div></td>
					<td style="width: 5px;"></td>
					<td class="name">{$lang.booking_legend_precheckout}:</td>
					<td class="field"><div class="prcheckout image">&nbsp;</div></td>
				</tr>
				<tr>
					<td class="name">{$lang.booking_legend_arrived}:</td>
					<td class="field"><div class="bprcheckin image">&nbsp;</div></td>
					<td style="width: 5px;"></td>
					<td class="name">{$lang.booking_legend_departure}:</td>
					<td class="field"><div class="bprcheckout image">&nbsp;</div></td>
				</tr>
				<tr>
					<td class="name">{$lang.booking_legend_prebooked}:</td>
					<td class="field"><div class="prbooked image">&nbsp;</div></td>
					<td style="width: 5px;"></td>
					<td class="name">{$lang.booking_legend_selected}:</td>
					<td class="field"><div class="daySelect image">&nbsp;</div></td>
				</tr>
				</table>
				{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
			</td>
			<td style="width: 65px;"></td>
			<td valign="top">
				<!-- rate range -->
				{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='rate_range' name=$lang.booking_rate_range}

					{rlHook name='bookingPreRateRangeTpl'}

					<table id="booking_rate_range" class="submit">
					{foreach from=$rate_range item='rRange'}
					<tr>
						<td class="field">{$rRange.From|date_format:$smarty.const.RL_DATE_FORMAT} - {$rRange.To|date_format:$smarty.const.RL_DATE_FORMAT}</td>
						<td class="name"{if $aHooks.currencyConverter} style="width: 100px;"{/if}>
						    <b>{if $rRange.Price == 0}{$lang.booking_close_days}{else}{$defPrice.currency} {$rRange.Price}{/if}</b>
						</td>
						{if !empty($rRange.desc)}
						<td class="value">
							<img class="qtip" alt="" title="{$rRange.desc}" id="fd_{$smarty.foreach.rate_rageF.iteration}" src="{$rlTplBase}img/blank.gif" />
						</td>
						{/if}
					</tr>
					{/foreach}

					{if $use_time_frame}
					<tr>
						<td class="field"><div class="grey_small">{$lang.booking_rate_price_per_day}</div></td>
						<td class="name"><b>{$defPrice.name}</b></td>
						{if $range_regular_desc}
						<td class="value">
							<img class="qtip" alt="" title="{$range_regular_desc}" id="fd_regular" src="{$rlTplBase}img/blank.gif" />
						</td>
						{/if}
					</tr>
					{/if}
					</table>
				{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
				<!-- rate range end -->
			</td>
		</tr>
		</table>

		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='calendar_fieldset' name=$lang.booking_calendar}
			<div id="booking_calendar"></div>
			<div id="calendar_load"></div>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}

		<div class="hide" id="booking_message_obj">
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='booking_mes' name=$lang.booking_details}
			<div id="booking_message"></div>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
		</div>

		<div class="hide" id="step_2">
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='booking_mes' name=$lang.booking_step2}
			<form onsubmit="return false;" id="ufvalid" class="ufvalid" action="#" method="post">
				{include file=$smarty.const.RL_PLUGINS|cat:'booking'|cat:$smarty.const.RL_DS|cat:'booking_fields.tpl'}
			</form>
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
		</div>
	</div>
</div>
<!-- booking tab end -->
