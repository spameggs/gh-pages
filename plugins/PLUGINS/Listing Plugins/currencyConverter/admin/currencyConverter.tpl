<!-- lcurrency converter tpl -->

<!-- navigation bar -->
<div id="nav_bar">
	<a href="javascript:void(0)" onclick="rlConfirm('{$lang.currencyConverter_update_confirm_notice}', 'xajax_updateRate');" class="button_bar"><span class="left"></span><span class="update">{$lang.currencyConverter_update_rate}</span><span class="right"></span></a>	
	<a href="javascript:void(0)" onclick="show('new_item');" class="button_bar"><span class="left"></span><span class="center_add">{$lang.currencyConverter_add_currency}</span><span class="right"></span></a>
</div>
<!-- navigation bar end -->

<!-- add new currency -->
<div id="new_item" class="hide">
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.currencyConverter_add_currency}
	<form onsubmit="addItem();return false;" action="" method="post">
	<table class="form">
	<tr>
		<td class="name"><span class="red">*</span>{$lang.currencyConverter_code}</td>
		<td class="value">
			<input class="w60" type="text" id="ni_code" maxlength="3" />
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span>{$lang.currencyConverter_rate}</td>
		<td class="value">
			<input class="w60 numeric" type="text" id="ni_rate" maxlength="20" style="text-align: center;" />
		</td>
	</tr>
	<tr>
		<td class="name">{$lang.currencyConverter_name}</td>
		<td class="value">
			<input type="text" id="ni_name" maxlength="50" />
		</td>
	</tr>
	
	<tr>
		<td class="name">{$lang.status}</td>
		<td class="value">
			<select id="ni_status">
				<option value="active">{$lang.active}</option>
				<option value="approval">{$lang.approval}</option>
			</select>
		</td>
	</tr>
	
	<tr>
		<td></td>
		<td>
			<input name="add_new_currency_submit" type="submit" value="{$lang.add}" />
			<a class="cancel" href="javascript:void(0)" onclick="$('#new_item').slideUp('normal')">{$lang.cancel}</a>
		</td>
	</tr>
	</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
</div>

{literal}
<script type="text/javascript">
var addItem = function(){
	$('input[name=add_new_currency_submit]').val(lang['loading']);
	xajax_addCurrency( $('#ni_code').val(), $('#ni_rate').val(), $('#ni_name').val(), $('#ni_status').val() );
}
</script>
{/literal}

<!-- add new currency end -->

<div id="grid"></div>
<script type="text/javascript">//<![CDATA[
var currencyGrid;

{literal}
$(document).ready(function(){
	
	currencyGrid = new gridObj({
		key: 'currency',
		id: 'grid',
		ajaxUrl: rlPlugins + 'currencyConverter/admin/currencyConverter.inc.php?q=ext',
		defaultSortField: 'Country',
		//remoteSortable: true,
		title: lang['currencyConverter_ext_caption'],
		fields: [
			{name: 'Code', mapping: 'Code', type: 'string'},
			{name: 'Rate', mapping: 'Rate'},
			{name: 'Status', mapping: 'Status', type: 'string'},
			{name: 'ID', mapping: 'ID'},
			{name: 'Symbol', mapping: 'Symbol', type: 'string'},
			{name: 'Country', mapping: 'Country', type: 'string'}
		],
		columns: [
			{
				header: lang['currencyConverter_code_ext'],
				dataIndex: 'Code',
				width: 80,
				fixed: true,
				id: 'rlExt_item_bold'
			},{
				header: lang['currencyConverter_rate_ext'],
				dataIndex: 'Rate',
				width: 120,
				fixed: true,
				id: 'rlExt_item',
				editor: new Ext.form.TextField({
					allowBlank: false
				}),
				renderer: function(val){
					return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
				}
			},{
				header: lang['currencyConverter_name_ext'],
				dataIndex: 'Country',
				width: 200,
				fixed: true,
				editor: new Ext.form.TextField({
					allowBlank: false
				}),
				renderer: function(val){
					return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
				}
			},{
				header: lang['currencyConverter_symbol_ext'],
				dataIndex: 'Symbol',
				width: 40,
				editor: new Ext.form.TextField({
					allowBlank: false
				}),
				renderer: function(val){
					return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
				}
			},{
				header: lang['ext_status'],
				dataIndex: 'Status',
				width: 90,
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
			}
		]
	});
	
	currencyGrid.init();
	grid.push(currencyGrid.grid);
	
});
{/literal}
//]]>
</script>

<!-- lcurrency converter tpl end -->