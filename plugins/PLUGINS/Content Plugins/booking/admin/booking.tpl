<!-- booking system -->

<!-- navigation bar -->
<div id="nav_bar">

	{if $smarty.get.mode == 'booking_fields'}
	<a href="{$rlBaseC}mode=booking_fields&amp;action=add" class="button_bar"><span class="left"></span>
		<span class="center_add">{$lang.add} {$lang.booking_fields}</span><span class="right"></span>
	</a>
	{/if}

	{if $smarty.get.mode == 'ranges' && $smarty.get.view == 'rate_range'}
	<a href="javascript:void(0)" onclick="show('ranges_action_add');" class="button_bar"><span class="left"></span>
		<span class="center_add">{$lang.booking_rate_add}</span><span class="right"></span>
	</a>
	{/if}

	{if $smarty.get.mode != 'ranges'}
	<a href="{$rlBaseC}mode=ranges" class="button_bar"><span class="left"></span>
		<span class="center_list">{$lang.booking_admin_listings_tab}</span><span class="right"></span>
	</a>
	{/if}

	{if $smarty.get.mode != 'additionals'}
	<a href="{$rlBaseC}mode=additionals" class="button_bar"><span class="left"></span>
		<span class="center_list">{$lang.booking_additionals}</span><span class="right"></span>
	</a>
	{/if}

	{if $smarty.get.mode != 'booking_colors'}
	<a href="{$rlBaseC}mode=booking_colors" class="button_bar"><span class="left"></span>
		<span class="center_list">{$lang.booking_colors}</span><span class="right"></span>
	</a>
	{/if}

	{if $smarty.get.mode != 'booking_fields'}
	<a href="{$rlBaseC}mode=booking_fields" class="button_bar"><span class="left"></span>
		<span class="center_list">{$lang.booking_fields_list}</span><span class="right"></span>
	</a>
	{/if}

	{if $smarty.get.mode}
	<a href="{$rlBaseC|replace:'&amp;':''}" class="button_bar"><span class="left"></span>
		<span class="center_list">{$lang.booking_requests}</span><span class="right"></span>
	</a>
	{/if}
</div>

<div class="clear" style="*margin: -3px 0; *height: 1px;"></div>
<!-- navigation bar end -->

{assign var='sPost' value=$smarty.post}

{if $smarty.get.mode == 'booking_colors'}

{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
<form action="{$rlBaseC}mode=booking_colors" method="post">
	<input type="hidden" name="submit" value="1" />
	<table class="form">
	<tr>
		<td class="name">{$lang.booking_admin_colors_day_select}</td>
		<td class="field">
			<input type="hidden" name="b[xselect]" value="{$sPost.b.xselect}" />
			<div class="colorSelector" id="colorSelector_xselect"><div style="background-color: {$sPost.b.xselect}"></div></div>
		</td>
	</tr>
	<tr>
		<td class="name">{$lang.booking_admin_colors_available}</td>
		<td class="field">
			<input type="hidden" name="b[available]" value="{$sPost.b.available}" />
			<input type="hidden" name="b[available_rgb]" value="{$sPost.b.available_rgb}" />
			<div class="colorSelector" id="colorSelector_available"><div style="background-color: {$sPost.b.available}"></div></div>
		</td>
	</tr>
	<tr>
		<td class="name">{$lang.booking_admin_colors_booked}</td>
		<td class="field">
			<input type="hidden" name="b[booked]" value="{$sPost.b.booked}" />
			<input type="hidden" name="b[booked_rgb]" value="{$sPost.b.booked_rgb}" />
			<div class="colorSelector" id="colorSelector_booked"><div style="background-color: {$sPost.b.booked}"></div></div>
		</td>
	</tr>
	<tr>
		<td class="name">{$lang.booking_admin_colors_requested}</td>
		<td class="field">
			<input type="hidden" name="b[requested]" value="{$sPost.b.requested}" />
			<input type="hidden" name="b[requested_rgb]" value="{$sPost.b.requested_rgb}" />
			<div class="colorSelector" id="colorSelector_requested"><div style="background-color: {$sPost.b.requested}"></div></div>
		</td>
	</tr>
	<tr>
		<td class="name">{$lang.booking_admin_colors_closed}</td>
		<td class="field">
			<input type="hidden" name="b[closed]" value="{$sPost.b.closed}" />
			<input type="hidden" name="b[closed_rgb]" value="{$sPost.b.closed_rgb}" />
			<div class="colorSelector" id="colorSelector_closed"><div style="background-color: {$sPost.b.closed}"></div></div>
		</td>
	</tr>

	<tr>
		<td></td>
		<td class="field">
			<input type="submit" value="{$lang.save}" />
		</td>
	</tr>
	</table>
</form>

<script type="text/javascript">//<![CDATA[
{literal}
	$(document).ready(function() {
		function addColorPicker(step) {
			$('#colorSelector_'+ step).ColorPicker({
				color: $('input[name="b['+ step +']"]').val(),
				onShow: function(colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function(colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function(hsb, hex, rgb) {
					$('#colorSelector_'+ step +' div').css('backgroundColor', '#'+ hex);
					$('#colorSelector_'+ step).parent().find('input[name="b['+ step +']"]').val('#'+ hex);
					$('input[name="b['+ step +'_rgb]"]').val(rgb.r+ ','+ rgb.g +','+ rgb.b);
				}
			});
		}

		addColorPicker('xselect');
		addColorPicker('available');
		addColorPicker('booked');
		addColorPicker('requested');
		addColorPicker('closed');
	});
{/literal}
//]]>
</script>
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

{elseif $smarty.get.mode == 'booking_fields'}

    {if isset($smarty.get.action)}
    {include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
    {assign var='sPost' value=$smarty.post}

    <!-- add new field -->
    <form action="{$rlBaseC}mode=booking_fields&action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;field={$smarty.get.field}{/if}" method="post">
    <input type="hidden" name="submit" value="1" />
    {if $smarty.get.action == 'edit'}
	    <input type="hidden" name="fromPost" value="1" />
    {/if}
    <table class="form">
    <tr>
	<td class="name"><span class="red">*</span>{$lang.key}</td>
	<td class="field">
	    <input {if $smarty.get.action == 'edit'}readonly{/if} class="{if $smarty.get.action == 'edit'}disabled{else}text{/if} lang_add" name="key" type="text" style="width: 150px;" value="{$sPost.key}" maxlength="30" />
	</td>
    </tr>

	<tr>
		<td class="name"><span class="red">*</span>{$lang.name}</td>
		<td class="field">
			{if $allLangs|@count > 1}
				<ul class="tabs">
					{foreach from=$allLangs item='language' name='langF'}
					<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
					{/foreach}
				</ul>
			{/if}

			{foreach from=$allLangs item='language' name='langF'}
				{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
				<input type="text" name="name[{$language.Code}]" value="{$sPost.name[$language.Code]}" maxlength="350" />
				{if $allLangs|@count > 1}
						<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
					</div>
				{/if}
			{/foreach}
		</td>
	</tr>

    <tr>
		<td class="name">{$lang.description}</td>
		<td class="field">
			{if $allLangs|@count > 1}
				<ul class="tabs">
					{foreach from=$allLangs item='language' name='langF'}
					<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
					{/foreach}
				</ul>
			{/if}

			{foreach from=$allLangs item='language' name='langF'}
				{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
				<textarea rows="" cols="" name="description[{$language.Code}]">{$sPost.description[$language.Code]}</textarea>
				{if $allLangs|@count > 1}</div>{/if}
			{/foreach}
		</td>
	</tr>

	<tr>
		<td class="name"><span class="red">*</span>{$lang.required_field}</td>
		<td class="field">
			{if $sPost.required == '1'}
				{assign var='required_yes' value='checked'}
			{elseif $sPost.required == '0'}
				{assign var='required_no' value='checked'}
			{else}
				{assign var='required_no' value='checked'}
			{/if}
			<input {$required_yes} class="lang_add" type="radio" id="req_yes" name="required" value="1" /> <label for="req_yes">{$lang.yes}</label>
			<input {$required_no} class="lang_add" type="radio" id="req_no" name="required" value="0" /> <label for="req_no">{$lang.no}</label>
		</td>
	</tr>

	<tr>
		<td class="name"><span class="red">*</span>{$lang.status}</td>
		<td class="field">
			<select name="status" class="login_input_select lang_add">
				<option value="active" {if $sPost.status == 'active'}selected{/if}>{$lang.active}</option>
				<option value="approval" {if $sPost.status == 'approval'}selected{/if}>{$lang.approval}</option>
			</select>
		</td>
	</tr>

    <tr>
		<td class="name"><span class="red">*</span>{$lang.field_type}</td>
		<td class="field">
			<select {if $smarty.get.action == 'edit'}disabled{/if} onchange="field_types(this.value);" name="type" class="login_input_select lang_add {if $smarty.get.action == 'edit'}disabled{/if}">
				<option value="">{$lang.select_field_type}</option>
				{foreach from=$b_types item='bType' key='key'}
				<option {if $sPost.type == $key}selected{/if} value="{$key}">{$bType}</option>
				{/foreach}
			</select>
			{if $smarty.get.action == 'edit'}
			<input type="hidden" name="type" value="{$sPost.type}" />
			{/if}
		</td>
	</tr>
	</table>

	<!-- additional options -->
	<div id="additional_options">

    <script type="text/javascript">//<![CDATA[
		var langs_list = Array(
			{foreach from=$allLangs item='languages' name='lF'}
			'{$languages.Code}|{$languages.name}'{if !$smarty.foreach.lF.last},{/if}
			{/foreach}
		);
	//]]>
	</script>

	<!-- text field -->
	{assign var='textDefault' value=$sPost.text.default}
	<div id="field_text" class="hide">
	<table class="form">
	<tr>
		<td class="name">{$lang.default_value}</td>
		<td class="field">
			{if $allLangs|@count > 1}
				<ul class="tabs">
					{foreach from=$allLangs item='language' name='langF'}
					<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
					{/foreach}
				</ul>
			{/if}

			{foreach from=$allLangs item='language' name='langF'}
				{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
				<input type="text" name="text[default][{$language.Code}]" value="{$textDefault[$language.Code]}" maxlength="350" />
				{if $allLangs|@count > 1}
						<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
					</div>
				{/if}
			{/foreach}
		</td>
	</tr>

	{assign var='text_cond' value=$sPost.text}
    <tr>
		<td class="name">{$lang.check_condition}</td>
		<td class="field">
		    <select class="lang_add" name="text[condition]">
				<option value="">- {$lang.condition} -</option>
				{foreach from=$l_cond item='condition' key='cKey'}
				<option {if $text_cond.condition == $cKey}selected{/if} value="{$cKey}">{$condition}</option>
				{/foreach}
		    </select>
		</td>
	</tr>

	<tr>
		<td class="name">{$lang.maxlength}</td>
		<td class="field">
		    <input class="text lang_add numeric" name="text[maxlength]" type="text" style="width: 50px; text-align: center;" value="{$sPost.text.maxlength}" maxlength="3" />
			<span class="field_description">{$lang.default_text_value_des}</span>
		</td>
	</tr>
	</table>
	</div>
	<!-- text field end -->

	<!-- textarea field -->
	{assign var='textarea' value=$sPost.textarea}
	<div id="field_textarea" class="hide">
		<table class="form">
		<tr>
			<td class="name">{$lang.maxlength}</td>
			<td class="field">
				<input class="text lang_add numeric" name="textarea[maxlength]" type="text" style="width: 50px; text-align: center;" value="{$textarea.maxlength}" maxlength="4" />
				<span class="field_description">{$lang.default_textarea_value_des}</span>
			</td>
		</tr>
	</table>
	</div>
	<!-- textarea field end -->

	<!-- number field -->
	{assign var='number' value=$sPost.number}
	<div id="field_number" class="hide">
		<table class="form">
		<tr>
			<td class="name">{$lang.maxlength}</td>
			<td class="field">
				<input class="numeric" name="number[max_length]" type="text" style="width: 60px; text-align: center;" value="{$number.max_length}" maxlength="8" />
				<span class="field_description">{$lang.number_field_length_hint}</span>
			</td>
		</tr>
		</table>
	</div>
	<!-- number field end -->

	</div>
	<!-- additional options end -->

	<!-- additional JS -->
	{if $sPost.type != false}
	<script type="text/javascript">//<![CDATA[
		field_types('{$sPost.type}');
	//]]>
	</script>
	{/if}
	<!-- additional JS end -->

    <table class="sTable">
    <tr>
	<td style="width: 180px"></td>
	<td>
	    <input class="button lang_add" type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{else}{$lang.add}{/if}" />
	</td>
    </tr>
    </table>
    </form>
    <!-- add new field end -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
    {else}

	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	{literal}
	$(document).ready(function() {

		bookingFields = new gridObj({
			key: 'bookingFields',
			id: 'grid',
			ajaxUrl: rlPlugins + 'booking/admin/booking.inc.php?q=ext',
			defaultSortField: false,
			title: lang['booking_fields_manager'],
			fields: [
				{name: 'name', mapping: 'name', type: 'string'},
				{name: 'Type', mapping: 'Type'},
				{name: 'Required', mapping: 'Required'},
				{name: 'Position', mapping: 'Position', type: 'int'},
				{name: 'Status', mapping: 'Status'},
				{name: 'Key', mapping: 'Key'}
			],
			columns: [
				{
					header: lang['ext_name'],
					dataIndex: 'name',
					width: 60,
					id: 'rlExt_item_bold'
				},{
					id: 'rlExt_item',
					header: lang['ext_type'],
					dataIndex: 'Type',
					width: 30
				},{
					header: lang['ext_required_field'],
					dataIndex: 'Required',
					width: 17,
					editor: new Ext.form.ComboBox({
						store: [
							['1', lang['ext_yes']],
							['0', lang['ext_no']]
						],
						displayField: 'value',
						valueField: 'key',
						emptyText: lang['ext_not_available'],
						typeAhead: true,
						mode: 'local',
						triggerAction: 'all',
						selectOnFocus:true
					}),
					renderer: function(val){
						return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
					}
				},{
					header: lang['ext_position'],
					dataIndex: 'Position',
					width: 10,
					editor: new Ext.form.TextField({
						allowBlank: false
					}),
					renderer: function(val){
						return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
					}
				},{
					header: lang['ext_status'],
					dataIndex: 'Status',
					width: 100,
					fixed: true,
					editor: new Ext.form.ComboBox({
						store: [
							['active', lang['ext_active']],
							['approval', lang['ext_approval']]
						],
						displayField: 'value',
						valueField: 'key',
						typeAhead: true,
						mode: 'local',
						triggerAction: 'all',
						selectOnFocus:true
					}),
					renderer: function(val) {
						return '<div ext:qtip="'+ lang['ext_click_to_edit'] +'">'+ val +'</div>';
					}
				},{
					header: lang['ext_actions'],
					width: 70,
					fixed: true,
					dataIndex: 'Key',
					sortable: false,
					renderer: function(data) {
						var out = "<center>";
						out += "<img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' onClick='location.href=\""+rlUrlHome+"index.php?controller="+controller+"&mode=booking_fields&action=edit&field="+data+"\"' />";
						out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_delete_field']+"\", \"xajax_deleteLField\", \""+Array(data)+"\", \"field_load\" )' class='delete' />";
						out += "</center>";
						return out;
					}
				}
			]
		});

		bookingFields.init();
		grid.push(bookingFields.grid);
	});

	{/literal}//]]>
	</script>

    {/if}

{elseif $smarty.get.id}

	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<fieldset class="light">
		<legend id="legend_cats" class="up" onclick="fieldset_action('booking_page_details');">{$lang.booking_page_details}</legend>
		<div id="booking_page_details">
			<table class="form">
			{if !empty($requests.ref_number)}
			<tr>
				<td class="name">{$lang.booking_ref_number}:</td>
				<td class="field">{$requests.ref_number}</td>
			</tr>
			{/if}
			<tr>
				<td class="name">{$lang.booking_checkin}:</td>
				<td class="field">{$requests.From|date_format:$smarty.const.RL_DATE_FORMAT}</td>
			</tr>
			<tr>
				<td class="name">{$lang.booking_checkout}:</td>
				<td class="field">{$requests.To|date_format:$smarty.const.RL_DATE_FORMAT}</td>
			</tr>
			<tr>
				<td class="name">{$lang.booking_req_status}:</td>
				<td class="field">{$requests.Stat}</td>
			</tr>
			<tr>
				<td class="name">{$lang.booking_amount}:</td>
				<td class="field">{$defPrice.currency} {str2money string=$requests.Amount}</td>
			</tr>
			</table>
		</div>
	</fieldset>

	<fieldset class="light">
		<legend id="legend_cats" class="up" onclick="fieldset_action('booking_client_details');">{$lang.booking_client_details}</legend>
		<div id="booking_client_details">
			<table class="form">
			{foreach from=$requests.fields key='fKey' item='field'}
			{assign var='field_value' value=$field.value}

			{if $field.Type == 'bool'}
				{if $field.value == '1'}
					{assign var='field_value' value=$lang.yes}
				{else}
					{assign var='field_value' value=$lang.no}
				{/if}
			{/if}

			<tr>
				<td class="name">{$field.name}:</td>
				<td class="field">
					{if $field.Condition == 'isUrl'}
						<a href="{$field_value}" title="{$field_value}">{$field_value}</a>
					{else}
						{$field_value}
					{/if}
				</td>
			</tr>
			{/foreach}
			</table>
		</div>
	</fieldset>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

{elseif $smarty.get.mode == 'ranges'}

	{if !isset($smarty.get.listing_id)}

	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	var binding_days = {$config.booking_bind_checkin_checkout};

	{literal}
	$(document).ready(function() {

		bookingRateRanges = new gridObj({
			key: 'bookingRateRanges',
			id: 'grid',
			ajaxUrl: rlPlugins + 'booking/admin/booking.inc.php?q=ext_ranges',
			defaultSortField: false,
			title: '{/literal}{$lang.ext_listing_rate_range_manager}{literal}',
			fields: [
				{name: 'ID', mapping: 'ID'},
				{name: 'ref', mapping: 'ref'},
				{name: 'title', mapping: 'title', type: 'string'}
			],
			columns: [
				{
					header: lang['ext_id'],
					dataIndex: 'ID',
					width: 40,
					fixed: true
				},{
					header: lang['ext_ref_number'],
					dataIndex: 'ref',
					hidden: {/literal}{if $refExists}false{else}true{/if}{literal},
					width: 5
				},{
					header: lang['ext_booking_listing_title'],
					dataIndex: 'title',
					width: 60,
					id: 'rlExt_item_bold'
				},{
					header: lang['ext_actions'],
					width: 100,
					fixed: true,
					dataIndex: 'ID',
					sortable: false,
					renderer: function(data) {
						var out = "<center>";

						out += "<img class='view' ext:qtip='"+lang['ext_booking_rate_ranges']+"' src='"+rlPlugins+"booking/img/ranges.png' onClick='location.href=\""+rlUrlHome+"index.php?controller="+controller+"&mode=ranges&view=rate_range&listing_id="+data+"\"' />";
						if ( binding_days ) {
							out += "<img class='view' ext:qtip='"+lang['ext_booking_binding_days']+"' src='"+rlPlugins+"booking/img/bindings.png' onClick='location.href=\""+rlUrlHome+"index.php?controller="+controller+"&mode=ranges&view=binding_days&listing_id="+data+"\"' />";
						}
						out += "</center>";

						return out;
					}
				}
			]
		});

		bookingRateRanges.init();
		grid.push(bookingRateRanges.grid);
	});

	{/literal}//]]>
	</script>

	{else}

		<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}booking/js/jquery.form.js"></script>
		<script type="text/javascript">
			var listing_id = parseInt('{$smarty.get.listing_id}');
		</script>

		{if $smarty.get.view == 'rate_range'}

		<div id="ranges_action_add" class="hide">
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.add_item}
		<form id="ranges_form" onsubmit="addItem();$('input[name=item_submit]').val('{$lang.loading}');return false;" action="#" method="post">
			<table class="form">
			<tr>
				<td class="name"><span class="red">*</span>{$lang.from}</td>
				<td class="field">
					<input type="text" name="date_from" id="date_from" style="width: 100px;" maxlength="10" />
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.to}</td>
				<td class="field">
					<input type="text" name="date_to" id="date_to" style="width: 100px;" maxlength="10" />
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.price}</td>
				<td class="field">
					<input type="text" name="price" class="numeric" style="width: 125px;" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.description}</td>
				<td class="field">
					<textarea name="desc" rows="3" style="height: auto;" cols=""></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="field">
					<input type="submit" name="item_submit" value="{$lang.add}" />
					<a onclick="$('#ranges_action_add').slideUp('normal');$('#ranges_form').resetForm();" href="javascript:void(0)" class="cancel">{$lang.close}</a>
				</td>
			</tr>
			</table>
		</form>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
		</div>

		<div id="grid"></div>
		<script type="text/javascript">//<![CDATA[
		var binding_days = {$config.booking_bind_checkin_checkout};

		{literal}
		$(document).ready(function() {

			bookingRateRangesList = new gridObj({
				key: 'bookingRateRanges',
				id: 'grid',
				ajaxUrl: rlPlugins + 'booking/admin/booking.inc.php?q=ext_ranges_list&id='+ listing_id,
				defaultSortField: false,
				title: '{/literal}{$lang.booking_rate_range}{literal}',
				fields: [
					{name: 'ID', mapping: 'ID'},
					{name: 'From', mapping: 'From', type: 'date', dateFormat: 'timestamp'},
					{name: 'To', mapping: 'To', type: 'date', dateFormat: 'timestamp'},
					{name: 'Price', mapping: 'Price', type: 'string'},
					{name: 'desc', mapping: 'desc', type: 'string'}
				],
				columns: [
					{
						header: "From",
						dataIndex: 'From',
						width: 40,
						renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
					},{
						header: "To",
						dataIndex: 'To',
						width: 40,
						renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
					},{
						header: "Price",
						dataIndex: 'Price',
						width: 40
					},{
						header: "Description",
						dataIndex: 'desc',
						width: 130
					},{
						header: lang['ext_actions'],
						width: 70,
						fixed: true,
						dataIndex: 'ID',
						sortable: false,
						renderer: function(data) {
							var out = "<center>";
							out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm(\"{/literal}{$lang.booking_remove_confirm}{literal}\", \"xajax_deleteRateRange\", \""+ [data, 1] +"\", \"field_load\", \"smarty\" )' class='delete' />";
							out += "</center>";
							return out;
						}
					}
				]
			});

			bookingRateRangesList.init();
			grid.push(bookingRateRangesList.grid);

			//
			var dates = $('#date_from, #date_to').datepicker({
				showOn: 'both',
				buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif',
				buttonImageOnly: true,
				dateFormat: 'dd-mm-yy',
				minDate: new Date(),
				onSelect: function(selectedDate) {
					if ( this.id == "date_from" ) {
						var instance = $(this).data("datepicker"),
						date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
						date.setDate(date.getDate() + 1);
						dates.not(this).datepicker("option", "minDate", date);

						var mMonth = date.getMonth() + 1;
						var mDay = date.getDate();
						mMonth = mMonth < 10 ? '0'+ mMonth : mMonth;
						mDay = mDay < 10 ? '0'+ mDay : mDay;

						$('#date_to').val(mDay +'-'+ mMonth +'-'+ date.getFullYear());
					}
				}
			}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
		});

		function addItem() {
			xajax_saveRateRange(listing_id, $('#ranges_form').formToArray(), 1);
		}

		{/literal}//]]>
		</script>

		{else if $smarty.get.view == 'binding_days'}
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
			{include file=$smarty.const.RL_PLUGINS|cat:'booking'|cat:$smarty.const.RL_DS|cat:'binding_days.tpl'}
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

			<script type="text/javascript">
			{literal}
				var bind_click = 0;
				function bind_edit() {
					if( bind_click == 0 ) {
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
			{/literal}
			</script>
		{/if}
	{/if}
{elseif $smarty.get.mode == 'additionals'}
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}

	<form action="{$rlBaseC}mode=additionals" method="post">
		<input type="hidden" name="submit" value="1" />

		<table class="form">
		<tr>
			<td class="name">{$lang.booking_admin_price_field_devide_price}</td>
			<td class="field">
				<select name="price_field">
					{foreach from=$price_fields item='field'}
					<option value="{$field.Key}"{if $field.Key == $current_price_field} selected="selected"{/if}>{$field.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>

		{if $config.booking_binding_plans}
		<tr>
			<td class="name">{$lang.booking_admin_show_on_plans}</td>
			<td class="field" id="pages_obj">
				<fieldset class="light">
					{assign var='plans_phrase' value='admin_controllers+name+listing_plans'}
					<legend id="legend_pages" class="up" onclick="fieldset_action('pages');">{$lang.$plans_phrase}</legend>
					<div id="pages">
						<div id="pages_cont">
							{assign var='bPlan' value=$sPost.plan}
							<table class="sTable" style="margin-bottom: 15px;">
							<tr>
								<td valign="top">
								{foreach from=$plans item='plan' name='sPlan'}
								<div style="padding: 2px 8px;">
									<input class="checkbox" {if in_array($plan.ID, $sPost.plan)}checked="checked"{/if} id="plan_{$plan.ID}" type="checkbox" name="plan[{$plan.ID}]" value="{$plan.ID}" /> 
									<label class="cLabel" for="plan_{$plan.ID}">{$plan.name}</label>
								</div>
								{assign var='perCol' value=$smarty.foreach.sPlan.total/3|ceil}

								{if $smarty.foreach.sPlan.iteration % $perCol == 0}
									</td>
									<td valign="top">
								{/if}
								{/foreach}
								</td>
							</tr>
							</table>
						</div>

						<div class="grey_area" style="margin: 0 0 5px;">
							<span id="pages_nav" {if $sPost.show_on_all}class="hide"{/if}>
								<span onclick="$('#pages_cont input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
								<span class="divider"> | </span>
								<span onclick="$('#pages_cont input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
							</span>
						</div>
					</div>
				</fieldset>
			</td>
		</tr>
		{/if}

		<tr>
			<td class="name">{$lang.booking_admin_rent_time_frame}</td>
			<td class="field">
				{assign var='cur_time_frame' value=$cur_rent_time_frame|unserialize}

				<table class="form">
				<tr>
					<td class="name">{$lang.booking_admin_rent_time_frame_per_day}</td>
					<td class="field">
						<select name="time_frame[day]">
						{foreach from=$rent_time_frame item='time_frame'}
							<option value="{$time_frame.value}"{if $cur_time_frame.day == $time_frame.value} selected="selected"{/if}>{$time_frame.name}</option>
						{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td class="name">{$lang.booking_admin_rent_time_frame_per_week}</td>
					<td class="field">
						<select name="time_frame[week]">
						{foreach from=$rent_time_frame item='time_frame'}
							<option value="{$time_frame.value}"{if $cur_time_frame.week == $time_frame.value} selected="selected"{/if}>{$time_frame.name}</option>
						{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td class="name">{$lang.booking_admin_rent_time_frame_per_month}</td>
					<td class="field">
						<select name="time_frame[month]">
						{foreach from=$rent_time_frame item='time_frame'}
							<option value="{$time_frame.value}"{if $cur_time_frame.month == $time_frame.value} selected="selected"{/if}>{$time_frame.name}</option>
						{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td class="name">{$lang.booking_admin_rent_time_frame_per_year}</td>
					<td class="field">
						<select name="time_frame[year]">
						{foreach from=$rent_time_frame item='time_frame'}
							<option value="{$time_frame.value}"{if $cur_time_frame.year == $time_frame.value} selected="selected"{/if}>{$time_frame.name}</option>
						{/foreach}
						</select>
					</td>
				</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td></td>
			<td class="field">
				<input type="submit" value="{$lang.save}" />
			</td>
		</tr>
		</table>
	</form>

	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
{else}

    <div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	{literal}
	$(document).ready(function() {

		bookingRequestsGrid = new gridObj({
			key: 'bookingRequests',
			id: 'grid',
			fieldID: 'Request_ID',
			ajaxUrl: rlPlugins + 'booking/admin/booking.inc.php?q=ext_stat',
			defaultSortField: false,
			title: '{/literal}{$lang.booking_requests}{literal}',
			fields: [
				{name: 'Listing_ID', mapping: 'Listing_ID'},
				{name: 'ref_number', mapping: 'ref_number'},
				{name: 'Listing_title', mapping: 'Listing_title', type: 'string'},
				{name: 'Booking_status', mapping: 'Booking_status'},
				{name: 'Key', mapping: 'Key'},
				{name: 'Booking_client', mapping: 'Booking_client'},
				{name: 'Booking_from', mapping: 'Booking_from', type: 'date', dateFormat: 'U'},
				{name: 'Booking_to', mapping: 'Booking_to', type: 'date', dateFormat: 'U'},
				{name: 'Booking_ID', mapping: 'Booking_ID'},
				{name: 'Request_ID', mapping: 'Request_ID'}
			],
			columns: [
				{
					header: lang['ext_booking_listing_id'],
					dataIndex: 'Listing_ID',
					width: 7
				},{
					header: lang['ext_booking_listing_title'],
					dataIndex: 'Listing_title',
					width: 60,
					id: 'rlExt_item_bold'
				},{
					header: lang['ext_booking_client'],
					dataIndex: 'Booking_client',
					width: 15
				},{
					header: lang['ext_booking_checkin'],
					dataIndex: 'Booking_from',
					width: 15,
					renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
				},{
					header: lang['ext_booking_checkout'],
					dataIndex: 'Booking_to',
					width: 15,
					renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
				},{
					header: lang['ext_status'],
					dataIndex: 'Booking_status',
					width: 15,
					editor: new Ext.form.ComboBox({
						store: [
							['process', lang['ext_booking_process']],
							['booked', lang['ext_booking_booked']],
							['refused', lang['ext_booking_refused']]
						],
						displayField: 'value',
						valueField: 'key',
						typeAhead: true,
						mode: 'local',
						triggerAction: 'all',
						selectOnFocus:true
					}),
					renderer: function(val){
						return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
					}
				},{
					header: lang['ext_actions'],
					width: 70,
					fixed: true,
					dataIndex: 'Request_ID',
					sortable: false,
					renderer: function(data) {
						var out = "<center>";

						out += "<img class='view' ext:qtip='"+lang['ext_view']+"' src='"+rlUrlHome+"img/blank.gif' onClick='location.href=\""+rlUrlHome+"index.php?controller="+controller+"&action=view&id="+data+"\"' />";
						out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_booking_remove_notice_ap']+"\", \"xajax_deleteRequest\", \""+Array(data)+"\" )' class='delete' />";
						out += "</center>";

						return out;
					}
				}
			]
		});

		bookingRequestsGrid.init();
		grid.push(bookingRequestsGrid.grid);
	});

	{/literal}//]]>
	</script>

{/if}

<!-- booking system end -->
