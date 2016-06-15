<!-- rss feeds tpl -->

<!-- navigation bar -->
<div id="nav_bar">
	<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.add_block}</span><span class="right"></span></a>
	<a href="{$rlBaseC}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.items_list}</span><span class="right"></span></a>
</div>
<!-- navigation bar end -->

{if $smarty.get.action == 'edit' || $smarty.get.action == 'add'}

	{assign var='sPost' value=$smarty.post}

	<!-- add new feed -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	
	<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;id={$smarty.get.id}{/if}" method="post">
		<input type="hidden" name="submit" value="1" />
		{if $smarty.get.action == 'edit'}
			<input type="hidden" name="fromPost" value="1" />
		{/if}
		
		<table class="form">
		
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
			<td class="name"><span class="red">*</span>{$lang.rssfeed_link}</td>
			<td class="field">
				<input type="text" name="url" value="{$sPost.url}" class="w350" />
			</td>
		</tr>
		<tr>
			<td class="name"><span class="red">*</span>{$lang.article_num}</td>
			<td class="field">
				<input type="text" name="article_num" value="{$sPost.article_num}" maxlength="5" class="w60" style="text-align: center;" />
			</td>
		</tr>
		<tr>
			<td class="name"><span class="red">*</span>{$lang.block_side}</td>
			<td class="field">
				<select name="side">
					<option value="">{$lang.select}</option>
					{foreach from=$l_block_sides item='block_side' name='sides_f' key='sKey'}
					<option value="{$sKey}" {if $sKey == $sPost.side}selected{/if}>{$block_side}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.use_block_design}</td>
			<td class="field">
				{if $sPost.tpl == '1'}
					{assign var='tpl_yes' value='checked="checked"'}
				{elseif $sPost.tpl == '0'}
					{assign var='tpl_no' value='checked="checked"'}
				{else}
					{assign var='tpl_yes' value='checked="checked"'}
				{/if}
				<label><input {$tpl_yes} class="lang_add" type="radio" name="tpl" value="1" /> {$lang.yes}</label>
				<label><input {$tpl_no} class="lang_add" type="radio" name="tpl" value="0" /> {$lang.no}</label>
			</td>
		</tr>
		<tr>
			<td class="name"><span class="red">*</span>{$lang.status}</td>
			<td class="field">
				<select name="status">
					<option value="active" {if $sPost.status == 'active'}selected="selected"{/if}>{$lang.active}</option>
					<option value="approval" {if $sPost.status == 'approval'}selected="selected"{/if}>{$lang.approval}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{else}{$lang.add}{/if}" />
			</td>
		</tr>
		</table>
	</form>
	
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	<!-- add new feed end -->

{else}

	<script type="text/javascript">
	// blocks sides list
	var block_sides = [
	{foreach from=$l_block_sides item='block_side' name='sides_f' key='sKey'}
		['{$sKey}', '{$block_side}']{if !$smarty.foreach.sides_f.last},{/if}
	{/foreach}
	];
	</script>

	<!-- grid -->
	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	var rssFeedGrid;
	
	{literal}
	$(document).ready(function(){
		
		rssFeedGrid = new gridObj({
			key: 'rssFeed',
			id: 'grid',
			ajaxUrl: rlPlugins + 'rssfeed/admin/rssfeed.inc.php?q=ext',
			defaultSortField: 'name',
			title: lang['rssfeed_ext_caption'],
			remoteSortable: false,
			fields: [
				{name: 'ID', mapping: 'ID'},
				{name: 'name', mapping: 'name'},
				{name: 'Side', mapping: 'Side'},
				{name: 'Tpl', mapping: 'Tpl'},
				{name: 'Article_num', mapping: 'Article_num'},
				{name: 'Status', mapping: 'Status'},
				{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d H:i:s'}
			],
			columns: [
				{
					header: lang['ext_name'],
					dataIndex: 'name',
					width: 20,
					id: 'rlExt_item_bold'
				},{
					header: lang['ext_article_num'],
					dataIndex: 'Article_num',
					width: 5,
					editor: new Ext.form.NumberField({
						allowBlank: false,
						allowDecimals: false
					}),
					renderer: function(val){
						return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
					}
				},{
					header: lang['ext_block_side'],
					dataIndex: 'Side',
					width: 8,
					editor: new Ext.form.ComboBox({
						store: block_sides,
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
					header: lang['ext_block_style'],
					dataIndex: 'Tpl',
					width: 8,
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
					header: lang['ext_status'],
					dataIndex: 'Status',
					fixed: true,
					width: 100,
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
					dataIndex: 'ID',
					sortable: false,
					renderer: function(data) {
						var out = "<center>";
						out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&id="+data+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_delete']+"\", \"xajax_deleteRssFeed\", \""+Array(data)+"\" )' />";
						out += "</center>";
						
						return out;
					}
				}
			]
		});

		rssFeedGrid.init();
		grid.push(rssFeedGrid.grid);
		
	});
	{/literal}
	//]]>
	</script>
	<!-- grid end -->

{/if}

<!-- rss feeds tpl -->