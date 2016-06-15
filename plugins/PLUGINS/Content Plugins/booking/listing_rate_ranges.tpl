<!-- listing_rate_range tpl -->
<div class="hide" id="booking_rate_ranges_list">
	<table class="list">
	<tr class="header">
		<td>{$lang.from}</td>
		<td class="divider"></td>
		<td>{$lang.to}</td>
		<td class="divider"></td>
		<td>{$lang.price}</td>
		<td class="divider"></td>
		<td style="width: 65px;">{$lang.actions}</td>
	</tr>
	<tr id="loading_ranges" class="body">
		<td colspan="7">{$lang.loading}</td>
	</tr>
	<tr class="hide"id="rate_range_before"></tr>
	</table>

	<table class="sTable hide" id="add_ranges_table">
	<tr>
		<td align="right">
			<div style="margin: 5px 10px;">
				<a href="javascript:void(0)" onclick="add_rate_range();">{$lang.booking_rate_add}</a>
			</div>
		</td>
	</tr>
	</table>
</div>

<script type="text/javascript">
lang['booking_addEditListingErrorEmptyRanges'] = '{$lang.booking_addEditListingErrorEmptyRanges}';
var rates_post = [];
{if $smarty.post.b}
	{foreach from=$smarty.post.b item='rPost'}
		rates_post.push(['{$rPost.from}', '{$rPost.to}', '{$rPost.price}']);
	{/foreach}
{/if}

var listing_id = {if $smarty.session.add_listing.listing_id}{$smarty.session.add_listing.listing_id}{else}{if $smarty.get.id}{$smarty.get.id}{else}false{/if}{/if};
var current_field = 1;
var lang_delete = '{$lang.delete}';
var src_delete_img = '{$rlTplBase}img/blank.gif';
var ranges_loaded = false;

{literal}

	$(document).ready(function() {
		if ( $('input[name="f[booking_module]"]').length > 0 ) {

			var rate_ranges = ' \
				<tr id="booking_rate_ranges" class="hide"> \
					<td class="name">{/literal}{$lang.booking_rate_range}{literal}:<td class="field" id="booking_rate_ranges_obj"></td> \
				</tr>';

			$('input[name="f[booking_module]"]').parent().parent().parent().after(rate_ranges);
			$('#booking_rate_ranges_list').moveTo('#booking_rate_ranges_obj');
			$('#booking_rate_ranges_list').removeClass('hide');

			if ( $('input[name="f[booking_module]"][value="1"]').is(':checked') ) {
				$('#booking_rate_ranges').fadeIn('normal', loadingRangesHandler);
			}

			$('input[name="f[booking_module]"]').click(function() {
				if ( $(this).is('[value="1"]:checked') ) {
					$('#booking_rate_ranges').fadeIn('normal', loadingRangesHandler);
				}
				else {
					$('#booking_rate_ranges').fadeOut('normal', loadingRangesHandler);
				}
			});

			var fForm = $('input[name="f[booking_module]"]').prop('form');
			$(fForm).bind('submit', function() {
				var res = true;
				var empty_fields = [];

				$('tr[id^=add_rate_] input').each(function(index, value) {
					if ( $(this).val() == '' && $('input[name="f[booking_module]"]').is('[value="1"]:checked') ) {
						empty_fields.push('#'+ $(this).attr('id'));
						res = false
					}
				});

				if ( !res ) {
					printMessage('error', lang['booking_addEditListingErrorEmptyRanges'], empty_fields);
				}
				return res;
			});
		}
	});

	function loadingRangesHandler() {
		if ( ranges_loaded === false ) {
			if ( rates_post.length > 0 ) {
				for(var j=0; j<rates_post.length; j++) {
					add_rate_range(rates_post[j]);
				}
			}

			$.ajax({
				url: '{/literal}{$smarty.const.RL_URL_HOME}{literal}plugins/booking/ranges.inc.php?id='+ listing_id,
				success: function(data, textStatus, jqXHR) {
					if ( data.ranges.length > 0) {
						var field = function(id, from, to, price) {
							var lCurrency = $('select[name="f[price][currency]"] option:selected').text();
							return ' \
								<tr class="body" id="rrange_'+ id +'"> \
									<td><span class="text">'+ from +'</span></td> \
									<td class="divider"></td> \
									<td><span class="text">'+ to +'</span></td> \
									<td class="divider"></td> \
									<td><span class="text">'+ lCurrency +' '+ price +'</span></td> \
									<td class="divider"></td> \
									<td><span class="text"><img class="remove" onclick="rlConfirm(\'{/literal}{$lang.booking_remove_confirm}{literal}\', \'xajax_deleteRateRange\', Array(\''+ id +'\'), \'listing_loading\');" title="'+ lang_delete +'" alt="'+ lang_delete +'" src="'+ src_delete_img +'" /></span></td> \
								</tr>';
						}

						for(var i=0; i<data.ranges.length; i++) {
							var item = data.ranges[i];
							$('#rate_range_before').before(field(item.ID, item.From, item.To, item.Price));
						}
					}

					$('#loading_ranges').remove();
					$('#add_ranges_table').fadeIn('normal');
					ranges_loaded = true;
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$('#loading_ranges td:first').html('Problems');
				}
			});
		}
	}

	function add_rate_range(rates_post) {
		var previous_field = current_field - 1;
		rates_post = rates_post ? rates_post : ['','',''];

		var field = ' \
		<tr class="body tmp" id="add_rate_'+ current_field +'"> \
			<td><span class="text"><input type="text" class="brr" name="b['+ current_field +'][from]" id="brr_from_'+ current_field +'" value="'+ rates_post[0] +'" style="width:100px;" /></span></td> \
			<td class="divider"></td> \
			<td><span class="text"><input type="text" class="brr" name="b['+ current_field +'][to]" id="brr_to_'+ current_field +'" value="'+ rates_post[1] +'" style="width:100px;" /></span></td> \
			<td class="divider"></td> \
			<td><span class="text"><input type="text" class="numeric w80" name="b['+ current_field +'][price]" id="price_'+ current_field +'" value="'+ rates_post[2] +'" style="width:50px;" /></span></td> \
			<td class="divider"></td> \
			<td><span class="text"><img class="remove" onclick="removeRate('+ current_field +')" title="'+ lang_delete +'" alt="'+ lang_delete +'" src="'+ src_delete_img +'" /></span></td> \
		</tr>';

		$('#rate_range_before').before(field);
		$('.numeric').numeric();

		var dp_regional = rlLang == 'en' ? 'en-GB' : rlLang;
		var dates = $("#brr_from_"+ current_field +", #brr_to_"+ current_field).datepicker({
			showOn: 'both',
			buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif',
			buttonImageOnly: true,
			dateFormat: 'dd-mm-yy',
			minDate: new Date(),
			onSelect: function(selectedDate) {
				if ( this.id.indexOf('from') !== -1 ) {
					var instance = $(this).data("datepicker"),
					date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
					dates.not(this).datepicker("option", "minDate", date);
					dates.not(this).val(selectedDate);

					if ( dates.not(this).hasClass('error') ) {
						dates.not(this).removeClass('error');
					}
				}
			}
		}).datepicker($.datepicker.regional[dp_regional]);

		current_field++;
	}

	function removeRate(rate_id) {
		$('#add_rate_'+ rate_id).remove();
	}

	(function($) {
		$.fn.moveTo = function(selector) {
			return this.each(function() {
				var cl = $(this).clone();
				$(cl).appendTo(selector);
				$(this).remove();
			});
		};
	})(jQuery);

{/literal}
</script>
<!-- listing_rate_range tpl end -->