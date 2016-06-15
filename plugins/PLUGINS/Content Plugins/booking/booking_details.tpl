<!-- booking details -->

{if $config.template == 'default'}
	{assign var='block_class' value='grey_middle grey_line'}
{else}
	{assign var='block_class' value='block_caption'}
{/if}

<!-- tabs -->
<div class="tabs">
	<div class="left"></div>
	<ul>
		<li class="active first" id="tab_requests">
			<span class="left">&nbsp;</span>
			<span class="center"><span>{$lang.booking_booking_requests}</span></span>
			<span class="right">&nbsp;</span>
		</li>
		<li id="tab_raterange">
			<span class="left">&nbsp;</span>
			<span class="center"><span>{$lang.booking_rate_range}</span></span>
			<span class="right">&nbsp;</span>
		</li>
		{if $config.booking_bind_checkin_checkout}
		<li id="tab_binding">
			<span class="left">&nbsp;</span>
			<span class="center"><span>{$lang.booking_binding_days}</span></span>
			<span class="right">&nbsp;</span>
		</li>
		{/if}
	</ul>
	<div class="right"></div>
</div>
<div class="clear"></div>
<!-- tabs end -->

<!-- requests tab -->
<div id="area_requests" class="tab_area">
	<div class="highlight">
    	{if empty($requests)}
			<div class="info">{$lang.booking_no_requests}</div>
		{else}
	    	<table class="list" id="saved_search">
			<tr class="header">
				<td align="center" class="no_padding" style="width: 15px;">#</td>
				<td class="divider"></td>
				<td>{$lang.listing}</td>
				<td class="divider"></td>
				{if $aHooks.ref == 1}
				<td style="width: 80px;">{$lang.booking_ref_number}</td>
				<td class="divider"></td>
				{/if}
				<td style="width: 100px;">{$lang.booking_author}</td>
				<td class="divider"></td>
				<td style="width: 70px;">{$lang.status}</td>
				<td class="divider"></td>
				<td style="width: 65px;">{$lang.actions}</td>
			</tr>
			{foreach from=$requests item='request' name='requestsF' key='rKey'}
			{assign var='status_key' value=$request.status}
			<tr class="body" id="item_request_{$request.ID}">
				<td class="no_padding" align="center"><span class="text">{$smarty.foreach.requestsF.iteration}</span></td>
				<td class="divider"></td>
				<td>
					{assign var='ltype_key' value='lt_'|cat:$request.ltype}
					<a href="{$rlBase}{if $config.mod_rewrite}{$pages.$ltype_key}/{$request.Path}/{str2path string=$request.title}-l{$request.Listing_ID}.html{else}?page={$pages.$ltype_key}&amp;id={$request.Listing_ID}{/if}" title="{$request.booking_page_details}">{$request.title}</a>
				</td>
				<td class="divider"></td>
				{if $aHooks.ref == 1}
				<td><span class="text">{$request.ref}</span></td>
				<td class="divider"></td>
				{/if}
				<td><span class="text">{$request.Author}</span></td>
				<td class="divider"></td>
				<td id="status_{$request.ID}"><span class="{if $request.status == 'process'}active{elseif $request.status == 'refused'}red{else}deactive{/if}">{if $request.status == 'process'}{$lang.new}{elseif $request.status == 'booked'}{$lang.booking_legend_booked}{else}{$lang.booking_refused}{/if}</span></td>
				<td class="divider"></td>
				<td>
					<img class="search" onclick="location.href='{$rlBase}{if $config.mod_rewrite}{$pages.booking_requests}/{str2path string=$request.title}-r{$rKey}.html{else}?page={$pages.booking_requests}&amp;id={$rKey}{/if}';" title="{$lang.booking_page_details}" alt="" src="{$rlTplBase}img/blank.gif" />
					<img class="del" onclick="rlConfirm( '{$lang.ext_booking_remove_notice}', 'xajax_deleteRequest', Array('{$request.ID}'), 'request_loading' );" alt="{$lang.delete}" title="{$lang.delete}" src="{$rlTplBase}img/blank.gif" />
				</td>
			</tr>
			{/foreach}
			</table>
		{/if}
	</div>
</div>
<!-- requests tab end -->

<!-- rate range tab -->
<div id="area_raterange" class="tab_area hide">
	<div class="highlight">
		{include file=$smarty.const.RL_PLUGINS|cat:'booking'|cat:$smarty.const.RL_DS|cat:'rate_range.tpl'}
	</div>
</div>
<!-- rate range tab end -->

{if $config.booking_bind_checkin_checkout}
<!-- binding checkin / checkout tab -->
<div id="area_binding" class="tab_area hide">
	<div class="highlight">
		{include file=$smarty.const.RL_PLUGINS|cat:'booking'|cat:$smarty.const.RL_DS|cat:'binding_days.tpl'}
	</div>
</div>
<!-- binding checkin / checkout tab end -->
{/if}

<!-- additional javascripts -->
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.qtip.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.ui.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}booking/js/jquery.ufvalidator.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}booking/js/jquery.form.js"></script>
<script type="text/javascript">
//<![CDATA[
var listing_id = parseInt('{$smarty.get.id}');
var current_field = 1;
var bind_click = 0;
var lang_delete = '{$lang.delete}';
var src_delete_img = '{$rlTplBase}img/blank.gif';
var qtip_init;

{literal}

qtip_init = function() {
	$('.qtip').each(function(){
		$(this).qtip({
			content: $(this).attr('title') ? $(this).attr('title') : $(this).prev('div.qtip_cont').html(),
			show: 'mouseover',
			hide: {
					fixed: true,
					delay: 500
			},
			position: {
				corner: {
					target: 'topRight',
					tooltip: 'bottomLeft'
				}
			},
			style: qtip_style
		}).attr('title', '');
	});
}
qtip_init();

function bind_edit() {
	if ( bind_click == 0 ) {
		$('#bind_days_checkbox').fadeIn();
		bind_click = 1;
	}
	else {
		$('#bind_days_checkbox').fadeOut();
		bind_click = 0;
	}
}

function save_binding_days() {
	var formData = $('#binding_days_form').formToArray();
	xajax_saveBindingDays(listing_id, formData);
}

function add_rate_range() {
	var previous_field = current_field - 1;

	var field = ' \
	<tr class="body tmp" id="add_rate_'+ current_field +'"> \
		<td class="no_padding" align="center"><span class="text"></span></td> \
		<td class="divider"></td> \
		<td><span class="text"><input type="text" class="brr req-string req-date" name="from_'+ current_field +'" id="brr_from_'+ current_field +'" style="width:100px;" /></span></td> \
		<td class="divider"></td> \
		<td><span class="text"><input type="text" class="brr req-string req-date" name="to_'+ current_field +'" id="brr_to_'+ current_field +'" style="width:100px;" /></span></td> \
		<td class="divider"></td> \
		<td><span class="text"><input type="text" class="numeric w80 req-string req-numeric" name="price_'+ current_field +'" id="price_'+ previous_field +'" style="width:50px;" /></span></td> \
		<td class="divider"></td> \
		<td align="center"><span class="text"><input type="checkbox" onclick="add_desc('+ current_field +');" /></span></td> \
		<td class="divider"></td> \
		<td><span class="text"><img class="remove" onclick="removeRate('+ current_field +')" title="'+ lang_delete +'" alt="'+ lang_delete +'" src="'+ src_delete_img +'" /></span></td> \
	</tr> \
	<tr class="tmp hide" id="add_rate_desc_'+ current_field +'"> \
		<td class="no_padding" align="center"><span class="text"></span></td> \
		<td colspan="6"><span class="text"><textarea name="desc_'+ current_field +'" cols="30" rows="2"></textarea></span></td> \
	</tr>';

	$('#rate_range_before').before(field);
	$('#booking_rate_range').fadeIn();
	$('#label_save_range').fadeIn('fast');

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
			}
		}
	}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);

	if ( current_field == 1 ) {
		$('#label_save_range').formValidator({
			onSuccess: function() {
				xajax_saveRateRange(listing_id, $('#valid_rate_range').formToArray());
			},
			scope: '#valid_rate_range'
		});
	}
	current_field++;
}

function add_desc(rate_id) {
	if( $('#add_rate_desc_'+rate_id).css('display') == 'none' ) {
		$('#add_rate_desc_'+rate_id).find('textarea').val('');
		$('#add_rate_desc_'+rate_id).fadeIn('slow').find('textarea').addClass('req-string');
	}
	else {
		$('#add_rate_desc_'+rate_id).fadeOut('fast').find('textarea').removeClass('req-string');
	}
}

function edit_desc(rate_id,mode) {
	if ( mode ) {
		rate_id = 'regular';
	}

	if ( $('#rate_desc_'+rate_id).css('display') == 'none' ) {
		$('#rate_desc_'+rate_id).fadeIn('slow');
	}
	else {
		$('#rate_desc_'+rate_id).fadeOut('fast');
	}
}

function save_desc(rate_id, mode) {
	if ( mode ) {
		rate_id = 'regular';
	}

	var value = $('#save_desc_'+rate_id).val();
	xajax_saveDesc(rate_id,value,mode);
}

function removeRate(rate_id) {
	$('#add_rate_'+rate_id).remove();
	$('#add_rate_desc_'+rate_id).remove();

	if ( $('#rate_ranges_table tr.tmp').length == 0 ) {
		$('#booking_rate_range').hide();
		$('#label_save_range').hide();
	}
}

function errorShow(error) {
	printMessage('error', error);
}

{/literal}
//]]>
</script>
<!-- additional javascripts end -->

<!-- booking details end -->
