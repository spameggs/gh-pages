<!-- banner plans tpl -->

{if $smarty.get.action}
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/colorpicker/js/colorpicker.js"></script>
{assign var='sPost' value=$smarty.post}

<!-- add/edit banner plan -->
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
<form action="{$rlBaseC}module=banner_plans&amp;action={if $smarty.get.action == 'add'}add{else}edit&amp;plan={$smarty.get.plan}{/if}" method="post">
	<input type="hidden" name="submit" value="1" />
	{if $smarty.get.action == 'edit'}
		<input type="hidden" name="fromPost" value="1" />
	{/if}
	<table class="form">
	<tr>
		<td class="name"><span class="red">*</span>{$lang.name}</td>
		<td>
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
		<td class="name">{$lang.admin_only}</td>
		<td class="field">
			{assign var='checkbox_field' value='banners_admin'}

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
					<input {$banners_admin_yes} type="radio" id="{$checkbox_field}_yes" name="{$checkbox_field}" value="1" /> <label for="{$checkbox_field}_yes">{$lang.yes}</label>
					<input {$banners_admin_no} type="radio" id="{$checkbox_field}_no" name="{$checkbox_field}" value="0" /> <label for="{$checkbox_field}_no">{$lang.no}</label>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr class="admin-only {if $sPost.banners_admin}hide{/if}">
		<td class="name">{$lang.enable_for}</td>
		<td class="field">
			<fieldset class="light">
				<legend id="legend_accounts_tab_area" class="up" onclick="fieldset_action('accounts_tab_area');">{$lang.account_type}</legend>
				<div id="accounts_tab_area" style="padding: 0 10px 10px 10px;">
					<table>
					<tr>
						<td>
							<table>
							<tr>
							{foreach from=$account_types item='a_type' name='ac_type'}
								<td>
									<div style="margin: 0 20px 0 0;">
										<input {if $a_type.Key|in_array:$sPost.account_type}checked="checked"{/if} style="margin-bottom: 0px;" type="checkbox" id="account_type_{$a_type.ID}" value="{$a_type.Key}" name="account_type[]" /> <label for="account_type_{$a_type.ID}">{$a_type.name}</label>
									</div>
								</td>
							{if $smarty.foreach.ac_type.iteration%3 == 0 && !$smarty.foreach.ac_type.last}
							</tr>
							<tr>
							{/if}
							{/foreach}
							</tr>
							</table>
						</td>
						<td>
							<span class="field_description">{$lang.info_account_type_plans}</span>
						</td>
					</tr>
					</table>

					<div class="grey_area" style="margin: 8px 0 0;">
						<span onclick="$('#accounts_tab_area input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
						<span class="divider"> | </span>
						<span onclick="$('#accounts_tab_area input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
					</div>
				</div>
			</fieldset>
		</td>
	</tr>

	<tr>
		<td class="name"><span class="red">*</span>{$lang.banners_boxes}</td>
		<td class="field">
			{if $boxes}
			<fieldset class="light">
				<legend id="legend_boxes_tab_area" class="up" onclick="fieldset_action('boxes_tab_area');">{$lang.banners_boxes}</legend>
				<div id="boxes_tab_area" style="padding: 0 10px 10px 10px;">
					<table>
					<tr>
						<td>
							<table>
							<tr>
							{foreach from=$boxes item='b_box' name='b_type'}
								<td>
									<div style="margin: 0 20px 0 0;">
										<input {if $b_box.Key|in_array:$sPost.boxes}checked="checked"{/if} style="margin-bottom: 0px;" type="checkbox" id="boxes_{$b_box.Key}" value="{$b_box.Key}" name="boxes[]" /> <label for="boxes_{$b_box.Key}">{$b_box.name}</label>
									</div>
								</td>
							{if $smarty.foreach.b_type.iteration%3 == 0 && !$smarty.foreach.b_type.last}
							</tr>
							<tr>
							{/if}
							{/foreach}
							</tr>
							</table>
						</td>
						<td>
							<span class="field_description">{$lang.banners_boxesDesc}</span>
						</td>
					</tr>
					</table>

					<div class="grey_area" style="margin: 8px 0 0;">
						<span onclick="$('#boxes_tab_area input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
						<span class="divider"> | </span>
						<span onclick="$('#boxes_tab_area input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
					</div>
				</div>
			</fieldset>
			{else}
				{assign var='replace' value='<a target="_blank" class="static" href="'|cat:$rlBase|cat:'index.php?controller=banners&amp;module=banner_boxes&amp;action=add">$1</a>'}
				<span class="field_description">{$lang.banners_addBoxHint|regex_replace:'/\[(.*)\]/':$replace}</span>

				{if empty($errors)}
				<script type="text/javascript">
				{literal}
					$(document).ready(function() {
						printMessage('alert', '{/literal}{$lang.banners_bannerBoxesEmptyCP|regex_replace:"/\[(.*)\]/":$replace}{literal}');
					});
				{/literal}
				</script>
				{/if}
			{/if}
		</td>
	</tr>

	<tr>
		<td class="name"><span class="red">*</span>{$lang.banners_bannerType}</td>
		<td class="field">
			<fieldset class="light">
				<legend id="legend_banner_type_tab_area" class="up" onclick="fieldset_action('banner_type_tab_area');">{$lang.banners_bannerType}</legend>
				<div id="banner_type_tab_area" style="padding: 0 10px 10px 10px;">
					<table>
					<tr>
						<td>
							<table>
							<tr>
								<td>
									<div style="margin: 0 20px 0 0;">
										<input {if 'image'|in_array:$sPost.banner_type}checked="checked"{/if} style="margin-bottom: 0px;" type="checkbox" id="banner_type_image" value="image" name="banner_type[]" /> <label for="banner_type_image">{$lang.banners_bannerType_image}</label>
									</div>
								</td>
								<td>
									<div style="margin: 0 20px 0 0;">
										<input {if 'flash'|in_array:$sPost.banner_type}checked="checked"{/if} style="margin-bottom: 0px;" type="checkbox" id="banner_type_flash" value="flash" name="banner_type[]" /> <label for="banner_type_flash">{$lang.banners_bannerType_flash}</label>
									</div>
								</td>
								<td>
									<div style="margin: 0 20px 0 0;">
										<input {if 'html'|in_array:$sPost.banner_type}checked="checked"{/if} style="margin-bottom: 0px;" type="checkbox" id="banner_type_html" value="html" name="banner_type[]" /> <label for="banner_type_html">{$lang.banners_bannerType_html}</label>
									</div>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					</table>

					<div class="grey_area" style="margin: 8px 0 0;">
						<span onclick="$('#banner_type_tab_area input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
						<span class="divider"> | </span>
						<span onclick="$('#banner_type_tab_area input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
					</div>
				</div>
			</fieldset>
		</td>
	</tr>

	<style type="text/css">
	{literal}
		.box-checkboxes {
		    background-color: #F5F7F0;
		    background-position: left center;
		    background-repeat: no-repeat;
		    border: 1px solid #FFFFFF;
		    float: left;
		    height: 18px;
		    padding-left: 27px;
		    vertical-align: top;
		    width: 245px;
		}
	{/literal}
	</style>

	<tr>
		<td class="name"><span class="red">*</span>{$lang.banners_showCountries}</td>
		<td class="field">
			<fieldset class="light">
				<legend id="legend_countries_tab_area" class="up" onclick="fieldset_action('countries_tab_area');">{$lang.banners_countries}</legend>
				<div id="countries_tab_area" style="padding: 0 10px 10px 10px;">

					<table class="form">
					<tr>
						<td class="field">
							{assign var='checkbox_field' value='banners_geo'}

							{if $sPost.$checkbox_field == '0'}
								{assign var=$checkbox_field|cat:'_no' value='checked="checked"'}
							{elseif $sPost.$checkbox_field == '1'}
								{assign var=$checkbox_field|cat:'_yes' value='checked="checked"'}
							{elseif $sPost.$checkbox_field == '2'}
								{assign var=$checkbox_field|cat:'_exclude' value='checked="checked"'}
							{else}
								{assign var=$checkbox_field|cat:'_yes' value='checked="checked"'}
							{/if}

							<table>
							<tr>
								<td>
									<input {$banners_geo_exclude} type="radio" id="{$checkbox_field}_exclude" name="{$checkbox_field}" value="2" /> <label for="{$checkbox_field}_exclude">{$lang.banners_excludeSelected}</label>
									<input {$banners_geo_yes} type="radio" id="{$checkbox_field}_yes" name="{$checkbox_field}" value="1" /> <label for="{$checkbox_field}_yes">{$lang.banners_allowToAll}</label>
									<input {$banners_geo_no} type="radio" id="{$checkbox_field}_no" name="{$checkbox_field}" value="0" /> <label for="{$checkbox_field}_no">{$lang.banners_allowToSelected}</label>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr id="select-countries" class="{if !$sPost || $sPost.banners_geo == 1}hide{/if}">
						<td class="field">
							<div class="country-checkboxes">
								{foreach from=$countries item='entry' name='c_name'}
								{assign var='cflag' value=$smarty.const.RL_URL_HOME|cat:'templates/'|cat:$config.template|cat:'/img/flags/'|cat:$entry->Country_code|lower|cat:'.png'}
								<div style="background-image: url('{$cflag}');" title="{$entry->Country_name}" class="box-checkboxes">
									<input {if $entry->Country_code|in_array:$sPost.countries}checked="checked"{/if} type="checkbox" id="country_{$entry->Country_code}" value="{$entry->Country_code}" name="countries[]" />
									<label for="country_{$entry->Country_code}">{$entry->Country_name}</label>
								</div>
								{/foreach}
							</div>
							<div class="clear"></div>

							<div class="grey_area" style="margin: 8px 0 0;">
								<span onclick="$('#countries_tab_area input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
								<span class="divider"> | </span>
								<span onclick="$('#countries_tab_area input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
							</div>
						</td>
					</tr>
					</table>
				</div>
			</fieldset>
		</td>
	</tr>
	<tr class="admin-only {if $sPost.banners_admin}hide{/if}">
		<td class="name">{$lang.label_bg_color}</td>
		<td class="field">
			<div style="padding: 0 0 5px 0;">
				<input type="hidden" name="color" value="{if $sPost.color}{$sPost.color}{else}d8cfc4{/if}" />
				<div id="colorSelector" class="colorSelector"><div style="background-color: #{if $sPost.color}{$sPost.color}{else}d8cfc4{/if}"></div></div>
			</div>
		</td>
	</tr>
	<tr class="admin-only {if $sPost.banners_admin}hide{/if}">
		<td class="name">{$lang.price}</td>
		<td class="field">
			<input type="text" name="price" value="{if $sPost.price}{$sPost.price}{else}0{/if}" class="numeric" style="width: 50px; text-align: center;" /> <span class="field_description_noicon">&nbsp;{$config.system_currency}</span>
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span>{$lang.banners_bannerLiveFor}</td>
		<td class="field">
			<fieldset class="light">
				<legend id="legend_countries_tab_area" class="up" onclick="fieldset_action('banners_live_for');">{$lang.banners_bannerLiveFor}</legend>
				<div id="banners_live_for" style="padding: 0 10px 10px 10px;">
					<table class="form">
					<tr>
						<td class="field">
							{assign var='checkbox_field' value='banners_live_for_type'}

							{if $sPost.$checkbox_field == 'views'}
								{assign var=$checkbox_field|cat:'_views' value='checked="checked"'}
							{elseif $sPost.$checkbox_field == 'period'}
								{assign var=$checkbox_field|cat:'_period' value='checked="checked"'}
							{else}
								{assign var=$checkbox_field|cat:'_period' value='checked="checked"'}
							{/if}

							<table>
							<tr>
								<td>
									<input {$banners_live_for_type_period} type="radio" id="{$checkbox_field}_period" name="{$checkbox_field}" value="period" /> <label for="{$checkbox_field}_period">{$lang.banners_liveTypePeriod}</label>
									<input {$banners_live_for_type_views} type="radio" id="{$checkbox_field}_views" name="{$checkbox_field}" value="views" /> <label for="{$checkbox_field}_views">{$lang.banners_liveTypeViews}</label>
								</td>
							</tr>
							</table>
						</td>
					</tr>

					<tr id="banners_live_type_period" {if !$banners_live_for_type_period}class="hide"{/if}>
						<td class="field">
							<input type="text" name="period" value="{if $sPost.period}{$sPost.period}{else}0{/if}" class="numeric" style="width: 50px; text-align: center;" />
							<span class="field_description_noicon">{$lang.days}</span>
							<span class="field_description">{$lang.banners_bannerLiveForHint}</span>
						</td>
					</tr>
					<tr id="banners_live_type_views" {if !$banners_live_for_type_views}class="hide"{/if}>
						<td class="field">
							<input type="text" name="views" value="{if $sPost.views}{$sPost.views}{else}0{/if}" class="numeric" style="width: 50px; text-align: center;" />
							<span class="field_description_noicon">{$lang.banners_liveTypeViews}</span>
							<span class="field_description">{$lang.banners_bannerLiveForHintViews}</span>
						</td>
					</tr>
					</table>
				</div>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td class="name">{$lang.status}</td>
		<td class="field">
			<select name="status">
				<option value="active" {if $sPost.status == 'active'}selected="selected"{/if}>{$lang.active}</option>
				<option value="approval" {if $sPost.status == 'approval'}selected="selected"{/if}>{$lang.approval}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="no_divider"></td>
		<td class="field">
			<input type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{else}{$lang.add}{/if}" />
		</td>
	</tr>
	</table>
</form>
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

<script type="text/javascript">
var bg_color = '{if $sPost.color}{$sPost.color}{else}d8cfc4{/if}';
{literal}

$(document).ready(function(){

	$('#colorSelector').ColorPicker({
		color: '#'+ bg_color,
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorSelector div').css('backgroundColor', '#' + hex);
			$('input[name=color]').val(hex);
		}
	});

	// show in countries
	$('div#countries_tab_area > table input[type=radio]').click(function() {
		if ( $(this).val() == 1 ) {
			$('tr#select-countries').fadeOut('fast');
		}
		else {
			$('tr#select-countries').fadeIn('fast');
		}
	});

	// admin only
	$('input[name=banners_admin]').click(function() {
		if ( $(this).val() == 1 ) {
			$('tr.admin-only').fadeOut('fast');
		}
		else {
			$('tr.admin-only').fadeIn('fast');
		}
	});

	// baners live for
	$('input[name=banners_live_for_type]').click(function() {
		if ( $(this).val() == 'period' ) {
			$('#banners_live_type_views').fadeOut('fast', function() {
				$('#banners_live_type_period').fadeIn('fast');
			});
		}
		else {
			$('#banners_live_type_period').fadeOut('fast', function() {
				$('#banners_live_type_views').fadeIn('fast');
			});
		}
	});
});

{/literal}
</script>

<!-- add/edit banner plan end -->

{else}

<!-- delete banner plan block -->
<div id="delete_block" class="hide">
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.banners_deleteBannersPlan}
		<div id="delete_container">
			{$lang.detecting}
		</div>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

	<script type="text/javascript">//<![CDATA[
	var delete_confirm_phrase = "{$lang.banners_noticeDeleteEmptyBannerPlan}";
	{literal}

	function delete_chooser(key, name) {
		rlConfirm(delete_confirm_phrase.replace('{plan}', name), 'xajax_deletePlan', key);
	}
	{/literal}
	//]]>
	</script>
</div>
<!-- delete banner plan block end -->

<div id="grid"></div>
<script type="text/javascript">//<![CDATA[
var bannerPlans;

{literal}
$(document).ready(function(){

	bannerPlans = new gridObj({
		key: 'bannerPlans',
		id: 'grid',
		ajaxUrl: rlPlugins + 'banners/admin/banner_plans.inc.php?q=ext',
		defaultSortField: false,
		title: lang['banners_bannerPlansTitleOfManager'],
		fields: [
			{name: 'ID', mapping: 'ID'},
			{name: 'Key', mapping: 'Key'},
			{name: 'name', mapping: 'name'},
			{name: 'Side', mapping: 'Side'},
			{name: 'Status', mapping: 'Status'},
			{name: 'Position', mapping: 'Position', type: 'int'},
			{name: 'Admin', mapping: 'Admin'},
			{name: 'Price', mapping: 'Price', type: 'float'},
			{name: 'Boxes', mapping: 'Boxes'}
		],
		columns: [
			{
				header: lang['ext_name'],
				dataIndex: 'name',
				id: 'rlExt_item_bold',
				width: 40
			},{
				header: '{/literal}{$lang.admin_only}{literal}',
				dataIndex: 'Admin',
				width: 10,
				editor: new Ext.form.ComboBox({
					store: [
						['1', lang['ext_yes']],
						['0', lang['ext_no']]
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
				header: lang['ext_price']+' ('+rlCurrency+')',
				dataIndex: 'Price',
				width: 7,
				css: 'font-weight: bold;',
				editor: new Ext.form.NumberField({
					allowBlank: false,
					allowDecimals: true
				}),
				renderer: function(val){
					return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
				}
			},{
				header: lang['ext_position'],
				dataIndex: 'Position',
				width: 6,
				editor: new Ext.form.NumberField({
					allowBlank: false,
					allowDecimals: false
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
				dataIndex: 'ID',
				sortable: false,
				renderer: function(data) {
					var out = "<center>";
					var splitter = false;

					out += "<a href='"+ rlUrlHome +"index.php?controller="+ controller +"&module=banner_plans&action=edit&plan="+ data +"'><img class='edit' ext:qtip='"+ lang['ext_edit'] +"' src='"+ rlUrlHome +"img/blank.gif' /></a>";
					out += "<img class='remove' ext:qtip='"+ lang['ext_delete'] +"' src='"+ rlUrlHome +"img/blank.gif' onclick='rlConfirm( \""+ lang['ext_notice_delete'] +"\", \"xajax_prepareDeleting\", \""+ Array(data) +"\", \"section_load\" )' />";
					out += "</center>";

					return out;
				}
			}
		]
	});

	bannerPlans.init();
	grid.push(bannerPlans.grid);

	// subscribe to event
	bannerPlans.grid.addListener('beforeedit', function(editEvent) {
		if ( $.trim(editEvent.record.data.Boxes) == '' && editEvent.field == 'Status' ) {
			bannerPlans.store.rejectChanges();
			Ext.MessageBox.alert(lang['ext_notice'], lang['banners_plan_without_boxes']);
		}
	});

});
{/literal}
//]]>
</script>

{/if}

<!-- banner plans tpl end -->