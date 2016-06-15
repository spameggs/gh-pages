<!-- tag cloud tpl -->
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.caret.js"></script>

<!-- navigation bar -->
<div id="nav_bar">
	{if !isset($smarty.get.action)}
		<a onclick="show('search', '#action_blocks div');" href="javascript:void(0)" class="button_bar"><span class="left"></span><span class="center_search">{$lang.search}</span><span class="right"></span></a>
		<a onclick="show('import', '#action_blocks div');" href="javascript:void(0)" class="button_bar"><span class="left"></span><span class="center_import">{$lang.tc_import}</span><span class="right"></span></a>
		<a href="{$rlBaseC}action=defaults" class="button_bar"><span class="left"></span><span class="center">{$lang.tc_defaults}</span><span class="right"></span></a>
	{/if}

	{if $smarty.get.action != 'add'}
		<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.tc_add_tag}</span><span class="right"></span></a>
	{/if}
	<a href="{$rlBase}index.php?controller={$smarty.get.controller}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.tc_tag_list}</span><span class="right"></span></a>
</div>
<!-- navigation bar end -->

<div id="action_blocks">
	{if !isset($smarty.get.action)}

		<!-- search -->
		<div id="search" class="hide">
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.search}
		
		<form method="post" onsubmit="return false;" id="search_form" action="">
			<table class="form">
			<tr>
				<td class="name">{$lang.tc_name}</td>
				<td class="field">
					<input type="text" id="search_name" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.status}</td>
				<td class="field">
					<select id="search_status" style="width: 200px;">
						<option value="">- {$lang.all} -</option>
						<option value="active">{$lang.active}</option>
						<option value="approval">{$lang.approval}</option>
					</select>
				</td>
			</tr>			
			<tr>
				<td></td>
				<td class="field">
					<input type="submit" class="button" value="{$lang.search}" id="search_button" />
					<input type="button" class="button" value="{$lang.reset}" id="reset_search_button" />
				
					<a class="cancel" href="javascript:void(0)" onclick="show('search')">{$lang.cancel}</a>
				</td>
			</tr>
			
			</table>
		</form>
		
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
		</div>
		
		<script type="text/javascript">
		var remote_filters = new Array();
		{if $smarty.get.type}
			remote_filters.push( 'action||search' );
			remote_filters.push( 'Type||{$smarty.get.type}' );
		{/if}
		
		{literal}
		
		var search = new Array();
		var cookie_filters = new Array();

		$(document).ready(function(){
			
			if ( readCookie('tags_sc') || remote_filters.length > 0 )
			{
				$('#search').show();
				cookie_filters = remote_filters.length > 0 ? remote_filters : readCookie('tags_sc').split(',');

				for (var i in cookie_filters)
				{
					if ( typeof(cookie_filters[i]) == 'string' )
					{
						var item = cookie_filters[i].split('||');
						if ( item[0] != 'undefined' && item[0] != '' )
						{
							if ( item[0] == 'Lock' )
							{
								$('#search input').each(function(){
									var val = item[1] == 1 ? 'yes' : 'no';
								});
							}
							else
							{
								if ( item[0] == 'Parent_ID' )
								{
									item[0] = 'parent';
								}
								
								$('#search_'+item[0].toLowerCase()).selectOptions(item[1]);
							}
						}
					}
				}
			}
			
			$('#search_form').submit(function(){
				createCookie('tags_pn', 0, 1);
				search = new Array();
				search.push( new Array('action', 'search') );
				search.push( new Array('Name', $('#search_name').val()) );
				search.push( new Array('Type', $('#search_type').val()) );
				search.push( new Array('Status', $('#search_status').val()) );

				var save_search = new Array();
				for(var i in search)
				{
					if ( search[i][1] != '' && typeof(search[i][1]) != 'undefined'  )
					{
						save_search.push(search[i][0]+'||'+search[i][1]);
					}
				}
				createCookie('tags_sc', save_search, 1);
				
				tagsGrid.filters = search;
				tagsGrid.reload();
			});
			
			$('#reset_search_button').click(function(){
				eraseCookie('tags_sc');
				tagsGrid.reset();
				
				$("#search select option[value='']").attr('selected', true);
				$("#search input[type=text]").val('');
				$("#search input").each(function(){
					if ( $(this).attr('type') == 'radio' )
					{
						$(this).attr('checked', false);
					}
				});
			});
			
		});
		
		{/literal}
		</script>
		<!-- search end -->

		<!-- import -->
		<div id="import" class="hide">
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.search}
		
		<form method="post" onsubmit="return false;" id="import_form" action="">
			<table class="form">
			<tr>
				<td class="name">{$lang.tc_import_field}</td>
				<td class="field">
					<textarea cols="50" rows="2" name="tags"></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="field">
					<input type="submit" class="button" value="{$lang.import}" id="import_button" />
					<input type="button" class="button" value="{$lang.reset}" id="reset_import_button" />
				
					<a class="cancel" href="javascript:void(0)" onclick="show('search')">{$lang.cancel}</a>
				</td>
			</tr>
			
			</table>
		</form>
		
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
		</div>

		<script type="text/javascript">
		{literal}
			$(document).ready(function(){
				$('#import_form').submit(function(){
					xajax_importTags( $("#import textarea[name=tags]").val() );
				});
			
				$('#reset_import_button').click(function(){
					$("#import textarea[name=tags]").val('');
				});			
			});		
		{/literal}
		</script>
		<!-- import end -->
	{/if}
	
</div>

{if $smarty.get.action == 'defaults'}
	{assign var='sPost' value=$smarty.post}
	
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}

	<span class="field_description" style="margin:10px">{$lang.tc_defaults_hint}</span>

	<form action="{$rlBaseC}action=defaults" method="post" enctype="multipart/form-data">
	<input type="hidden" name="submit" value="1" />
	<input type="hidden" name="fromPost" value="1" />

	<table class="form">
		<tr>
			<td class="name">{$lang.title}</td>
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
						<input type="text" name="title[{$language.Code}]" value="{$sPost.title[$language.Code]}" class="w350" maxlength="50" />
					{if $allLangs|@count > 1}
							<span class="field_description_noicon">{$lang.title} (<b>{$language.name}</b>)</span>
						</div>
					{/if}
				{/foreach}
			</td>
		</tr>
		<tr>
			<td class="name">
				{$lang.description}
			</td>
			<td class="field">
				{if $allLangs|@count > 1}
					<ul class="tabs">
						{foreach from=$allLangs item='language' name='langF'}
						<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
						{/foreach}
					</ul>
				{/if}
				
				{foreach from=$allLangs item='language' name='langF'}
					{if $allLangs|@count > 1}<div class="ckeditor tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
					{assign var='dCode' value='description_'|cat:$language.Code}
					{fckEditor name='description_'|cat:$language.Code width='100%' height='140' value=$sPost.$dCode}
					{if $allLangs|@count > 1}</div>{/if}
				{/foreach}
			</td>
		</tr>
		
		<tr>
			<td class="name">{$lang.meta_description}</td>
			<td class="field">
				{if $allLangs|@count > 1}
					<ul class="tabs">
						{foreach from=$allLangs item='language' name='langF'}
						<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
						{/foreach}
					</ul>
				{/if}
				
				{foreach from=$allLangs item='language' name='langF'}
					{assign var='lMetaDescription' value=$sPost.meta_description}
					{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
					<textarea cols="50" rows="2" name="meta_description[{$language.Code}]">{$lMetaDescription[$language.Code]}</textarea>
					{if $allLangs|@count > 1}</div>{/if}
				{/foreach}
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.meta_keywords}</td>
			<td class="field">
				{if $allLangs|@count > 1}
					<ul class="tabs">
						{foreach from=$allLangs item='language' name='langF'}
						<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
						{/foreach}
					</ul>
				{/if}
				
				{foreach from=$allLangs item='language' name='langF'}
					{assign var='lMetaKeywords' value=$sPost.meta_keywords}
					{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
					<textarea cols="50" rows="2" name="meta_keywords[{$language.Code}]">{$lMetaKeywords[$language.Code]}</textarea>
					{if $allLangs|@count > 1}</div>{/if}
				{/foreach}
			</td>
		</tr>	
		<tr>
			<td></td>
			<td class="field">
				<input type="submit" value="{$lang.edit}" />
			</td>
		</tr>
		</table>
		</form>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
{elseif $smarty.get.action == 'add' || $smarty.get.action == 'edit'}
		{assign var='sPost' value=$smarty.post}
	
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
		<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;key={$smarty.get.key}{/if}" method="post" enctype="multipart/form-data">
		<input type="hidden" name="submit" value="1" />

		{if $smarty.get.action == 'edit'}
			<input type="hidden" name="fromPost" value="1" />
		{/if}
			
		<table class="form">
		<tr>
			<td class="name">
				{$lang.listing_type}
			</td>
			<td class="field">
				<select name="type" id="listing_type">
					<option value="0">{$lang.all}</option>
					{foreach from=$listing_types item='l_type'}
						{if $l_type.Page}
							<option value="{$l_type.Key}" {if $sPost.type == $l_type.Key}selected="selected"{/if}>{$l_type.name}</option>
						{/if}
					{/foreach}
				</select>
				<span class="field_description"> - {$lang.tc_ltype_hint}</span>
				<span id="listing_type_loading" style="margin-top: -2px;" class="loader">&nbsp;&nbsp;&nbsp;&nbsp;</span>
		</tr>
{*		<tr>
			<td class="name">{$lang.key}</td>
			<td class="field">
				<input {if $smarty.get.action == 'edit'}readonly="readonly"{/if} class="{if $smarty.get.action == 'edit'}disabled{/if}" name="key" type="text" style="width: 150px;" value="{$sPost.key}" maxlength="30" />
			</td>
		</tr>*}
		<tr>
			<td class="name">
				<span class="red">*</span>{$lang.tc_name}
			</td>
			<td class="field">
				<input type="text" name="tag" value="{$sPost.tag}" class="w350" maxlength="50" />
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.tc_tag_path}</td>
			<td class="field">
				{assign var='type_page' value='lt_'|cat:$type}
				<table>
				<tr>
					<td><span style="padding: 0 5px 0 0;" class="field_description_noicon">{$smarty.const.RL_URL_HOME}{$tags_page_path}<span id="ab">/</span><span id="ap"></span></span></td>
					<td><input type="text" name="path" value="{$sPost.path}" /></td>
					<td><span class="field_description_noicon" id="cat_postfix_el">{if $config.tc_urls_postfix}.html{else}/{/if}</span>
					<span class="field_description"> - {$lang.tc_path_hint}</span></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.title}</td>
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
						<input type="text" name="title[{$language.Code}]" value="{$sPost.title[$language.Code]}" class="w350" maxlength="50" />
					{if $allLangs|@count > 1}
							<span class="field_description_noicon">{$lang.title} (<b>{$language.name}</b>)</span>
						</div>
					{/if}
				{/foreach}
			</td>
		</tr>
		<tr>
			<td class="name">
				{$lang.description}
			</td>
			<td class="field">
				{if $allLangs|@count > 1}
					<ul class="tabs">
						{foreach from=$allLangs item='language' name='langF'}
						<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
						{/foreach}
					</ul>
				{/if}
				
				{foreach from=$allLangs item='language' name='langF'}
					{if $allLangs|@count > 1}<div class="ckeditor tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
					{assign var='dCode' value='description_'|cat:$language.Code}
					{fckEditor name='description_'|cat:$language.Code width='100%' height='140' value=$sPost.$dCode}
					{if $allLangs|@count > 1}</div>{/if}
				{/foreach}
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.meta_description}</td>
			<td class="field">
				{if $allLangs|@count > 1}
					<ul class="tabs">
						{foreach from=$allLangs item='language' name='langF'}
						<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
						{/foreach}
					</ul>
				{/if}
				
				{foreach from=$allLangs item='language' name='langF'}
					{assign var='lMetaDescription' value=$sPost.meta_description}
					{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
					<textarea cols="50" rows="2" name="meta_description[{$language.Code}]">{$lMetaDescription[$language.Code]}</textarea>
					{if $allLangs|@count > 1}</div>{/if}
				{/foreach}
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.meta_keywords}</td>
			<td class="field">
				{if $allLangs|@count > 1}
					<ul class="tabs">
						{foreach from=$allLangs item='language' name='langF'}
						<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
						{/foreach}
					</ul>
				{/if}
				
				{foreach from=$allLangs item='language' name='langF'}
					{assign var='lMetaKeywords' value=$sPost.meta_keywords}
					{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
					<textarea cols="50" rows="2" name="meta_keywords[{$language.Code}]">{$lMetaKeywords[$language.Code]}</textarea>
					{if $allLangs|@count > 1}</div>{/if}
				{/foreach}
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
			<td></td>
			<td class="field">
				<input type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{else}{$lang.add}{/if}" />
			</td>
		</tr>
		</table>
		</form>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
{else}
	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
		var tagsGrid;
		
		{literal}
		$(document).ready(function(){
			tagsGrid = new gridObj({
				key: 'tags',
				id: 'grid',
				ajaxUrl: rlPlugins + 'tag_cloud/admin/tag_cloud.inc.php?q=ext',
				defaultSortField: 'Date',
				defaultSortType: 'DESC',
				remoteSortable: true,
				filters: cookie_filters,
				title: {/literal}'{$lang.tc_manager}'{literal},
				fields: [
					{name: 'ID', mapping: 'ID', type: 'int'},
					{name: 'name', mapping: 'name', type: 'string'},
					{name: 'Type', mapping: 'Type'},
					{name: 'Count', mapping: 'Count'},
					{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
					{name: 'Status', mapping: 'Status'},
					{name: 'Key', mapping: 'Key'}
				],
				columns: [
					{
						header: '{/literal}{$lang.tc_tag}{literal}',
						dataIndex: 'name',
						width: 60,
						id: 'rlExt_item_bold'
					},{
						header: '{/literal}{$lang.tc_tag_count}{literal}',
						dataIndex: 'Count',
						width: 20,
						id: 'rlExt_item',
						editor: new Ext.form.NumberField({
							allowBlank: false,
							allowDecimals: false
						}),
						renderer: function(val){
							return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
						}
					},{
						header: lang['ext_add_date'],
						dataIndex: 'Date',
						width: 10,
						renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
					},{
						header: lang['ext_status'],
						dataIndex: 'Status',
						width: 10,
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
						renderer: function(id, obj, row) {
							var tag_id = row.data.ID;
							var tag_key = row.data.Key;

							var out = "<center>";
						
/*							out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=view&key="+tag_key+"'><img class='view' ext:qtip='"+lang['ext_view']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";*/
							out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&key="+tag_key+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
							out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onclick='rlConfirm( \""+lang['ext_notice_'+delete_mod]+"\", \"xajax_deleteTag\", \""+tag_key+"\" )' />";
							
							out += "</center>";
							
							return out;
						}
					}
				]
			});

			tagsGrid.init();
			grid.push(tagsGrid.grid);
			
		});
		{/literal}
		//]]>
	</script>
{/if}

