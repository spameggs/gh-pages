<!-- sbd countries tpl -->

<!-- navigation bar -->
<div id="nav_bar">
	<a href="javascript:void(0)" onclick="show('new_item');" class="button_bar"><span class="left"></span><span class="center_add">{$lang.sbd_add_country}</span><span class="right"></span></a>
</div>
<!-- navigation bar end -->

<!-- add new country -->
<div id="new_item" class="hide">
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.sbd_add_country}
	<form onsubmit="addItem();return false;" action="" method="post">
	<table class="form">
	<tr>
		<td class="name"><span class="red">*</span>{$lang.sbd_country_code}</td>
		<td class="field">
			<input class="w60" type="text" id="ni_code" maxlength="2" />
			<span class="field_description">{$lang.sbd_country_code_desc}</span>
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span>{$lang.name}</td>
		<td class="field names">
			{if $allLangs|@count > 1}
				<ul class="tabs">
					{foreach from=$allLangs item='language' name='langF'}
					<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
					{/foreach}
				</ul>
			{/if}
			
			{foreach from=$allLangs item='language' name='langF'}
				{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
				<input lang="{$language.Code}" class="nl_name w350" type="text" maxlength="350" />
				{if $allLangs|@count > 1}
						<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
					</div>
				{/if}
			{/foreach}
		</td>
	</tr>
	
	<tr>
		<td></td>
		<td>
			<input name="add_new_country_submit" type="submit" value="{$lang.add}" />
			<a class="cancel" href="javascript:void(0)" onclick="$('#new_item').slideUp('normal')">{$lang.cancel}</a>
		</td>
	</tr>
	</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
</div>
<!-- add new country end -->

<!-- edit country -->
<div id="edit_item" class="hide">
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.sbd_edit_country}
	<form onsubmit="editItem();return false;" action="" method="post">
	<table class="form">
	<tr>
		<td class="name"><span class="red">*</span>{$lang.sbd_country_code}</td>
		<td class="field">
			<input class="disabled w60" readonly="readonly" type="text" id="ei_code" maxlength="2" />
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span>{$lang.name}</td>
		<td class="field names">
			{if $allLangs|@count > 1}
				<ul class="tabs">
					{foreach from=$allLangs item='language' name='langF'}
					<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
					{/foreach}
				</ul>
			{/if}
			
			{foreach from=$allLangs item='language' name='langF'}
				{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
				<input lang="{$language.Code}" class="el_name w350" type="text" maxlength="350" />
				{if $allLangs|@count > 1}
						<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
					</div>
				{/if}
			{/foreach}
		</td>
	</tr>
	
	<tr>
		<td></td>
		<td>
			<input name="edit_country_submit" type="submit" value="{$lang.edit}" />
			<a class="cancel" href="javascript:void(0)" onclick="$('#edit_item').slideUp('normal')">{$lang.cancel}</a>
		</td>
	</tr>
	</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
</div>
<!-- edit country end -->

{literal}
<script type="text/javascript">
var addItem = function(){
	$('input[name=add_new_country_submit]').val(lang['loading']);
	var names = new Array();
	$('div#new_item td.names input').each(function(){
		names[$(this).attr('lang')] = $(this).val();
	});
	
	xajax_addCountry($('#ni_code').val(), names);
}
var editItem = function(){
	$('input[name=edit_country_submit]').val(lang['loading']);
	var names = new Array();
	$('div#edit_item td.names input').each(function(){
		names[$(this).attr('lang')] = $(this).val();
	});
	
	xajax_updateCountry($('#ei_code').val(), names);
}

var editCountry = function(code){
	xajax_editFillIn(code);
}
</script>
{/literal}

<div id="grid"></div>
<script type="text/javascript">//<![CDATA[
var currencyGrid;

{literal}
$(document).ready(function(){
	
	countriesGrid = new gridObj({
		key: 'sbd_countries',
		id: 'grid',
		fieldID: 'Code',
		ajaxUrl: rlPlugins + 'search_by_distance/admin/sbd_countries.inc.php?q=ext',
		defaultSortField: 'Name',
		//remoteSortable: true,
		title: lang['sbd_ext_caption'],
		fields: [
			{name: 'Code', mapping: 'Code', type: 'string'},
			{name: 'Status', mapping: 'Status', type: 'string'},
			{name: 'Name', mapping: 'Name', type: 'string'}
		],
		columns: [
			{
				header: lang['sbd_ext_country_name'],
				dataIndex: 'Name',
				width: 50
			},{
				header: lang['sbd_ext_country_code'],
				dataIndex: 'Code',
				width: 140,
				fixed: true,
				id: 'rlExt_item_bold'
			},{
				header: lang['ext_status'],
				dataIndex: 'Status',
				width: 110,
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
				renderer: function(val){
					return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
				}
			},{
				header: lang['ext_actions'],
				width: 70,
				fixed: true,
				dataIndex: 'Code',
				sortable: false,
				renderer: function(data) {
					var out = "<center>";
					
					out += "<img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' onclick='editCountry(\""+ data +"\")' />";
					out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_delete']+"\", \"xajax_removeCountry\", \""+Array(data)+"\" )' />";
					
					out += "</center>";
					
					return out;
				}
			}
		]
	});
	
	countriesGrid.init();
	grid.push(countriesGrid.grid);
	
});
{/literal}
//]]>
</script>

<!-- sbd countries tpl end -->