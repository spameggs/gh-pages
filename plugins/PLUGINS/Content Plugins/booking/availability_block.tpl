<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.ui.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/datePicker/i18n/ui.datepicker-{$smarty.const.RL_LANG_CODE}.js"></script>

<form onsubmit="return bookingCheckAvailability();" method="post" action="{$rlBase}{if $config.mod_rewrite}{$pages.availability_listings}.html{else}?page={$pages.available_listings}{/if}">
	<input type="hidden" name="booking_submit" value="1" />

	<div class="name">{$lang.booking_checkin}:</div>
	<div class="value">
		<input class="date" style="width: 130px;" type="text" id="booking_check_in" name="availability[from]" maxlength="10" value="{$smarty.post.availability.from}" />
		<div class="clear"></div>
	</div>

	<div class="name">{$lang.booking_checkout}:</div>
	<div class="value">
		<input class="date" style="width: 130px;" type="text" id="booking_check_out" name="availability[to]" maxlength="10" value="{$smarty.post.availability.to}" />
		<div class="clear"></div>
	</div>

	<table>
	<tr>
		<td style="padding-top: 10px;">
			<input class="button" type="submit" value="{$lang.search}" />
		</td>
	</tr>
	</table>
</form>

<script type="text/javascript">
//<![CDATA[
var booking_min_book_day = parseInt('{$config.booking_min_book_day}');
{literal}
	function bookingCheckAvailability() {
		if ( $('#booking_check_in').val() == '' || $('#booking_check_out').val() == '' ) {
			printMessage('error', "{/literal}{$lang.booking_availability_error}{literal}");
			highlightFields();
			return false;
		}
		return true;
	}

	function highlightFields() {
		if ( $('#booking_check_in').val() == '' ) {
			$('#booking_check_in').addClass('error');
			$('#booking_check_in').unbind('click').click(function() {
				$(this).removeClass('error');
			});
		}

		if ( $('#booking_check_out').val() == '' ) {
			$('#booking_check_out').addClass('error');
			$('#booking_check_out').unbind('click').click(function() {
				$(this).removeClass('error');
			});
		}
	}

	$(document).ready(function() {
		var dp_regional = rlLang == 'en' ? 'en-GB' : rlLang;
		var dates = $("#booking_check_in, #booking_check_out").datepicker({
			showOn: 'both',
			buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif',
			buttonImageOnly: true,
			dateFormat: 'dd-mm-yy',
			minDate: new Date(),
			onSelect: function(selectedDate) {
				if ( this.id == "booking_check_in" ) {
					var instance = $(this).data("datepicker"),
					date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
					date.setDate(date.getDate() + booking_min_book_day);
					dates.not(this).datepicker("option", "minDate", date);

					var mMonth = date.getMonth() + 1;
					var mDay = date.getDate();
					mMonth = mMonth < 10 ? '0'+ mMonth : mMonth;
					mDay = mDay < 10 ? '0'+ mDay : mDay;

					$('#booking_check_out').val( mDay +'-'+ mMonth +'-'+ date.getFullYear() );
				}
			}
		}).datepicker($.datepicker.regional[dp_regional]);
	});
{/literal}
//]]>
</script>