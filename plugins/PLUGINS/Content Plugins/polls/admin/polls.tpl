<!-- polls tpl -->

<!-- navigation bar -->
<div id="nav_bar">
	<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.add}</span><span class="right"></span></a>
	<a href="{$rlBase}index.php?controller={$smarty.get.controller}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.items_list}</span><span class="right"></span></a>	
</div>

<!-- navigation bar end -->

{if $smarty.get.action == 'edit' || $smarty.get.action == 'add'}
	{assign var='sPost' value=$smarty.post}

	<!-- add new poll -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;poll={$smarty.get.poll}{/if}" method="post">
	<input type="hidden" name="submit" value="1" />
	{if $smarty.get.action == 'edit'}
		<input type="hidden" name="fromPost" value="1" />
	{/if}
	<script type="text/javascript">
		var step = 1;
	</script>
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
		<td class="name"><span class="red">*</span>{$lang.vote_items}</td>
		<td class="value">
			<table id="items" style="margin: 2px 0; width: auto;" class="form">
			{if $sPost.items}
				{foreach from=$sPost.items item='item' key='itemKey' name='iForeach'}
				{assign var='iteration' value=$smarty.foreach.iForeach.iteration-1}
				<tr id="item_{$itemKey}">
					<td class="field">
						{if $allLangs|@count > 1}
							<ul class="tabs">
								{foreach from=$allLangs item='language' name='langF'}
									<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
								{/foreach}
							</ul>
						{/if}
						{foreach from=$allLangs item='language' name='langF'}
							{assign var='lCode' value=$language.Code}
							{if $allLangs|@count > 1}
								<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">
							{/if}
							<input type="text" name="items[{$itemKey}][{$language.Code}]" value="{$item.$lCode}" maxlength="350" />
							{if $allLangs|@count > 1}
								<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
							</div>
							{/if}
						{/foreach}
					</td>
					<td style="padding: 0 10px;">
						<input type="hidden" id="color_{$itemKey}" name="color[]" value="{if $sPost.color.$iteration}{$sPost.color.$iteration}{else}#89b0cb{/if}" />
						<div class="colorSelector" id="colorSelector_{$itemKey}"><div style="background-color: {if $sPost.color.$iteration}{$sPost.color.$iteration}{else}#89b0cb{/if}"></div></div>
						<script type="text/javascript">
						var bsh_color_{$itemKey} = '{if $sPost.color.$iteration}{$sPost.color.$iteration}{else}#89b0cb{/if}';
						{literal}
						$(document).ready(function(){
							$('#colorSelector_{/literal}{$itemKey}{literal}').ColorPicker({
								color: bsh_color_{/literal}{$itemKey}{literal},
								onShow: function (colpkr) {
									$(colpkr).fadeIn(500);
									return false;
								},
								onHide: function (colpkr) {
									$(colpkr).fadeOut(500);
									return false;
								},
								onChange: function (hsb, hex, rgb) {
									$('#colorSelector_{/literal}{$itemKey}{literal} div').css('backgroundColor', '#' + hex);
									$('#colorSelector_{/literal}{$itemKey}{literal}').prev().val('#'+hex);
								}
							});
						});
						{/literal}
						</script>
					</td>
					<td>
						&nbsp;&nbsp;&nbsp;<a class="delete_item" onclick="$('#item_{$itemKey}').remove('');" href="javascript:void(0)">{$lang.remove}</a>
						<script type="text/javascript">
						step = {$itemKey} + 1;
						</script>
					</td>
				</tr>
				{/foreach}
			{/if}
			</table>
			<div style="margin: 4px 8px; height: 20px;" class="add_item"><a onclick="item_build();" href="javascript:void(0)">{$lang.add_item}</a></div>
		</td>
	</tr>
	<tr>
		<td class="name">{$lang.polls_use_create_in_own}</td>
		<td class="field">
			{if $sPost.random == '1'}
				{assign var='random_no' value='checked="checked"'}
			{elseif $sPost.random == '0'}
				{assign var='random_yes' value='checked="checked"'}
			{else}
				{assign var='random_no' value='checked="checked"'}
			{/if}
			<label><input {$random_yes} class="lang_add" type="radio" name="random" value="0" /> {$lang.yes}</label>
			<label><input {$random_no} class="lang_add" type="radio" name="random" value="1" /> {$lang.no}</label>
		</td>
	</tr>
	</table>
	<div id="block_styles">
		<table class="form">
		<tr>
			<td class="name"><span class="red">*</span>{$lang.block_side}</td>
			<td class="field">
				<select name="side">
					<option value="">{$lang.select}</option>
					{foreach from=$l_block_sides item='block_side' name='sides_f' key='sKey'}
					<option value="{$sKey}" {if $sKey == $sPost.side}selected="selected"{/if}>{$block_side}</option>
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
		</table>
	</div>
	<table class="form">	
	<tr>
		<td class="name"><span class="red">*</span>{$lang.status}</td>
		<td class="value">
			<select name="status" class="login_input_select lang_add">
				<option value="active" {if $sPost.status == 'active'}selected="selected"{/if}>{$lang.active}</option>
				<option value="approval" {if $sPost.status == 'approval'}selected="selected"{/if}>{$lang.approval}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="value">
			<input class="button lang_add" type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{else}{$lang.add}{/if}" />
		</td>
	</tr>
	</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	<!-- add new poll end -->
	
	<!-- javascripts -->
	<script type="text/javascript">
	//<![CDATA[
	var lang_name = '{$lang.name}';
	var lg = Array(
	{foreach from=$allLangs item='languages' name='lF'}
		'{$languages.Code}|{$languages.name}'{if !$smarty.foreach.lF.last},{/if}
	{/foreach}
	);

	{literal}
	$(document).ready(function(){
		if( $('input[name="random"]:checked').val() == 1)
		{
			$('#block_styles').slideUp('normal');
		}
		else
		{
			$('#block_styles').slideDown('normal');
		}
		$('input[name="random"]').change(function(){
			if( $(this).val() == 1)
			{
				$('#block_styles').slideUp('normal');
			}
			else
			{
				$('#block_styles').slideDown('normal');
			}
		})
	})
	function item_build()
	{
		var data = '';
		var item = '';
		data += '<tr id="item_'+step+'"><td class="field">';
		data += '<ul class="tabs">';
		for (var i = 0; i <= lg.length-1; i++)
		{
			item = lg[i].split('|');
			data += '<li lang="'+item[0]+'"';

			if(i == 0)
			{
				data += ' class="active" ';
			}
			data += '>'+item[1]+'</li>';
		}
		data += '</ul>';
		for (var i = 0; i <= lg.length-1; i++)
		{
			item = lg[i].split('|');
			if(lg.length > 1)
			{
				data += '<div class="tab_area '+item[0];
				if(i > 0)
				{
					data += ' hide';
				}
				data += '">';
			}
			data += '<input type="text" name="items['+step+']['+item[0]+']" value="" maxlength="350" />';
			if(lg.length > 1)
			{
				data += '<span class="field_description_noicon">'+lang_name+' (<b>'+item[1]+'</b>)</span>';
				data += '</div>';
			}
		}

		// remove button build
		data += '<\/td><td style="padding: 0 10px;"><input type="hidden" id="color_'+step+'" name="color[]" value="#89b0cb" /><div class="colorSelector" id="colorSelector_'+step+'"><div style="background-color: #89b0cb"></div></div><\/td><td>&nbsp;&nbsp;&nbsp;<a class="delete_item" onclick="$(\'#item_'+step+'\').remove();"  href="javascript:void(0)">'+lang['ext_remove']+'<\/a><\/td><\/tr>';
		$("#items").append(data);
		addColorPicker(step)
		step++;
		flynax.tabs(true);
	}
	function addColorPicker(step)
	{
		$('#colorSelector_'+step).ColorPicker({
			color: '#89b0cb',
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#colorSelector_'+step+' div').css('backgroundColor', '#' + hex);
				$('#colorSelector_'+step).prev().val('#'+hex);
			}
		});
	}
	//]]>
	{/literal}
	</script>
	<!-- javascripts end -->

{elseif $smarty.get.action == 'results'}
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
		<div style="margin: 10px;" class="grey_line"><b>{$poll_info.name}</b></div>
		{foreach from=$poll_items item='poll_item'}
		<div style="margin: 10px 15px 2px;" class="blue_11_normal">{$poll_item.name} - <b>{$poll_item.Votes}</b> {$lang.votes}</div>
		<div style="margin: 0 15px;width: {if $poll_item.width > 20}{$poll_item.width}{else}20{/if}px; background: {$poll_item.Color}; height: 12px; color: white;font-size: 9px;text-align: center;"><b>{$poll_item.percent}%</b></div>
		{/foreach}
		<div style="margin: 10px 15px;">{$lang.total_votes}: <b>{$total_votes}</b></div>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

{else}	
	<!-- polls grid create -->
	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	var pollsGrid;

	{literal}
	$(document).ready(function(){
		pollsGrid = new gridObj({
			key: 'polls',
			id: 'grid',
			ajaxUrl: rlPlugins + 'polls/admin/polls.inc.php?q=ext',
			defaultSortField: 'name',
			title: lang['ext_polls_manager'],
			fields: [
				{name: 'ID', mapping: 'ID'},
				{name: 'name', mapping: 'name'},
				{name: 'Random', mapping: 'Random'},
				{name: 'Status', mapping: 'Status'},
				{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d H:i:s'}
			],	
			columns: [
				{
					header: lang['ext_title'],
					dataIndex: 'name',
					width: 60,
					id: 'rlExt_item_bold'
				},{
					header: lang['ext_polls_use_create_in_own'],
					dataIndex: 'Random',
					width: 10
				},{
					header: lang['ext_add_date'],
					dataIndex: 'Date',
					width: 13,
					renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s')
				},{
					header: lang['ext_status'],
					dataIndex: 'Status',
					width: 10,
					editor: new Ext.form.ComboBox({
						store: [
							['active', lang['ext_active']],
							['approval', lang['ext_approval']]
						],
						mode: 'local',
						typeAhead: true,
						triggerAction: 'all',
						selectOnFocus: true
					}),
					renderer: function(val) {
						return '<div ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</div>';
					}
				},{
					header: lang['ext_actions'],
					width: 110,
					fixed: true,
					dataIndex: 'ID',
					sortable: false,
					renderer: function(data) {
						 out = "<center>";

							out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=results&poll="+data+"'><img class='view' src='"+rlUrlHome+"img/blank.gif' ext:qtip='"+lang['ext_results']+"' /></a>";
							out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&poll="+data+"'><img class='edit' src='"+rlUrlHome+"img/blank.gif' ext:qtip='"+lang['ext_edit']+"' /></a>";
							out += "<a onClick='rlConfirm( \""+lang['ext_notice_delete_poll']+"\", \"xajax_deletePoll\", \""+Array(data)+"\" )'><img class='remove' src='"+rlUrlHome+"img/blank.gif' ext:qtip='"+lang['ext_delete']+"' /></a></center>";

						return out;
					}
				}]
			});
			pollsGrid.init();
			grid.push(pollsGrid.grid);
		});
	{/literal}//]]>
	</script>
{/if}
<!-- polls tpl end -->
