<!-- import/export listings tpl -->
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}export_import/static/lib_admin.js"></script>
<!-- navigation bar -->
{if $smarty.get.action != 'export' && $smarty.get.action != 'export_table'}
<div id="nav_bar">
	<a href="{$rlBaseC}action=export" class="button_bar"><span class="left"></span><span class="center_export">{$lang.eil_export}</span><span class="right"></span></a>
</div>
{/if}
<!-- navigation bar end -->
{assign var='sPost' value=$smarty.post}
{if $ei_mode == 'import_upload'}
	<!-- import upload form -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action=import" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="import_file" />
		<table class="form">
		<tr>
			<td class="name"><span class="red">*</span>{$lang.eil_file_for_import}</td>
			<td class="field">
				<input type="file" name="file" />
				<span class="field_description">{$lang.eil_file_for_import_desc}</span>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.eil_pictures_archive}</td>
			<td class="field">
				<input type="file" name="archive" />
				<span class="field_description">{$lang.eil_pictures_archive_desc}</span>
				
				<div class="field_description" style="margin: 10px 0 0 0;">{$lang.eil_max_file_size} <b>{$max_file_size} MB</b></div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input name="" type="submit" value="{$lang.upload}" />
			</td>
		</tr>
		</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	<!-- import upload form end -->
{elseif $ei_mode == 'import_form'}
	<!-- import form -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action=import" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="import_form" />
		<input type="hidden" name="from_post" value="1" />
		<table class="form" style="margin-top: 10px;table-layout: fixed;width: auto;margin-top: 77px;">
		<tr>
			<td class="name"><div style="min-width: 160px;"><span class="red">*</span>{$lang.listings}</div></td>
			<td class="field">
				<table class="import" style="margin-top: -77px;">
				<tr class="col-checkbox no_hover">
					<td></td>
					{foreach from=$sPost.data.0 item='checkbox' name='checkboxF'}
					<td>
						{assign var='iter_checkbox' value=$smarty.foreach.checkboxF.iteration-1}
						<div><input {if isset($sPost.data) && $sPost.cols[$iter_checkbox]}checked="checked"{elseif !isset($sPost.data)}checked="checked"{/if} value="1" type="checkbox"  name="cols[{$iter_checkbox}]" /></div>
					</td>
					{/foreach}
				</tr>
				<tr class="header no_hover">
					<td class="row-checkbox"></td>
					{foreach from=$sPost.data.0 item='col' name='fieldF'}
						<td>
							{assign var='iter_field' value=$smarty.foreach.fieldF.iteration-1}
							<div>
								<span class="caption" title="{$col}">{$col}</span> - 
								<select style="width: 130px;" name="field[{$iter_field}]">
									<option value="">{$lang.eil_select_field}</option>
									<optgroup label="{$lang.eil_system_fields}">
										{foreach from=$system_fields item='field'}
											<option {if $sPost.field.$iter_field == $field.Key}selected="selected"{/if} value="{$field.Key}">{$field.name}</option>
										{/foreach}
									</optgroup>
									<optgroup label="{$lang.eil_listing_fields}">
										{foreach from=$listing_fields item='field'}
											<option {if $sPost.field.$iter_field == $field.Key}selected="selected"{/if} value="{$field.Key}">{$field.name}</option>
										{/foreach}
									</optgroup>
								</select>
							</div>
						</td>
					{/foreach}
				</tr>
				{foreach from=$sPost.data item='row' name='rowF'}
					{assign var='iter_row' value=$smarty.foreach.rowF.iteration-1}
					<tr class="body{if $smarty.foreach.rowF.iteration%2 == 0 && !$smarty.foreach.rowF.first} highlight{/if}">
						<td class="row-checkbox">
							<div><input {if isset($sPost.from_post) && $sPost.rows[$iter_row]}checked="checked"{elseif !isset($sPost.from_post) && !$smarty.foreach.rowF.first}checked="checked"{/if} type="checkbox" name="rows[{$iter_row}]" value="1" /></div>
						</td>
						{foreach from=$row item='col' name='colF'}
							<td>
								<div>
									{if $col|@strlen > 200 || $col|@strlen != $col|regex_replace:'/[\r\t\n]/':''|@strlen}
										<textarea rows="" cols="" style="width: 200px;" name="data[{$smarty.foreach.rowF.iteration-1}][{$smarty.foreach.colF.iteration-1}]">{$col|replace:'"':'&quot;'}</textarea>
									{else}
										<input type="text" value="{$col|replace:'"':'&quot;'}" name="data[{$smarty.foreach.rowF.iteration-1}][{$smarty.foreach.colF.iteration-1}]" />
									{/if}
								</div>
							</td>
						{/foreach}
					</tr>
				{/foreach}
				</table>
				
			</td>
		</tr>
		<tr>
			<td class="name"><span class="red">*</span>{$lang.listing_type}</td>
			<td class="field">
				<select class="w200" name="import_listing_type">
					<option value="">{$lang.select}</option>
					{foreach from=$listing_types item='l_type'}
						<option {if $sPost.import_listing_type == $l_type.Key}selected="selected"{/if} value="{$l_type.Key}">{$l_type.name}</option>
					{/foreach}
				</select>
				<span class="field_description">{$lang.eil_type_for_import_desc}</span>
			</td>
		</tr>
		<tr>
			<td class="name"><span class="red">*</span>{$lang.eil_default_category}</td>
			<td class="field">
				<select class="w200" name="import_category_id">
					<option value="">{if $categories}{$lang.select}{else}{$lang.eil_select_listing_type}{/if}</option>
					{if $categories}
						{foreach from=$categories item='category'}
							<option {if $category.Level == 0}class="highlight_opt"{/if} {if $category.margin}style="margin-left: {$category.margin}px;"{/if} value="{$category.ID}" {if $sPost.import_category_id == $category.ID}selected="selected"{/if}>{$lang[$category.pName]}</option>
						{/foreach}
					{/if}
				</select>
			</td>
		</tr>
		<tr>
			<td class="name"><span class="red">*</span>{$lang.eil_default_owner}</td>
			<td class="field">
				<input style="width: 188px;" type="text" value="{$sPost.import_account_id_tmp}" name="import_account_id" />
				<script type="text/javascript">
				var post_account_id = {if $sPost.import_account_id}{$sPost.import_account_id}{else}false{/if};
				{literal}
					$('input[name=import_account_id]').rlAutoComplete({add_id: true, id: post_account_id});
				{/literal}
				</script>
			</td>
		</tr>
		<tr>
			<td class="name"><span class="red">*</span>{$lang.eil_default_status}</td>
			<td class="field">
				<select class="w200" name="import_status">
					<option value="">{$lang.select}</option>
					<option value="active" {if $sPost.import_status == 'active'}selected="selected"{/if}>{$lang.active}</option>
					<option value="approval" {if $sPost.import_status == 'approval'}selected="selected"{/if}>{$lang.approval}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="name"><span class="red">*</span>{$lang.eil_default_plan}</td>
			<td class="field">
				<select class="w200" name="import_plan_id">
					<option value="">{$lang.select}</option>
					{if $plans}
						{foreach from=$plans item='plan'}
							{assign var='plan_type' value=$plan.Type|cat:'_plan'}
							<option value="{$plan.ID}" {if $sPost.import_plan_id == $plan.ID}selected="selected"{/if}>{$plan.name} ({$lang.$plan_type})</option>
						{/foreach}
					{/if}
				</select>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.eil_paid}</td>
			<td class="field">
				{assign var='checkbox_field' value='import_paid'}
				{if $sPost.$checkbox_field == '1'}
					{assign var=$checkbox_field|cat:'_yes' value='checked="checked"'}
				{elseif $sPost.$checkbox_field == '0'}
					{assign var=$checkbox_field|cat:'_no' value='checked="checked"'}
				{else}
					{assign var=$checkbox_field|cat:'_yes' value='checked="checked"'}
				{/if}
				<input {$import_paid_yes} type="radio" id="{$checkbox_field}_yes" name="{$checkbox_field}" value="1" /> <label for="{$checkbox_field}_yes">{$lang.yes}</label>
				<input {$import_paid_no} type="radio" id="{$checkbox_field}_no" name="{$checkbox_field}" value="0" /> <label for="{$checkbox_field}_no">{$lang.no}</label>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.eil_per_run}</td>
			<td class="field">
				<select style="width: 60px;" name="import_per_run">
					{if $plans}
						{foreach from=$per_run item='run'}
							<option value="{$run}" {if $sPost.import_per_run == $run}selected="selected"{/if}>{$run}</option>
						{/foreach}
					{/if}
				</select>
				<span class="field_description">{$lang.eil_per_run_desc}</span>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="field"><input type="submit" name="" value="{$lang.import}" /></td>
		</tr>
		</table>
	</form>
	<div class="import_note">
		<div>{$lang.eil_pictures_by_url_note}</div>
		<div>{$lang.eil_pictures_from_zip_note}</div>
		<div>{$lang.eil_sub_category_note}</div>
		<div>{$lang.eil_youtube_video_field_note}</div>
	</div>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	<!-- import form end -->
	<script type="text/javascript">//<![CDATA[
	var eil_listing_wont_imported = "{$lang.eil_listing_wont_imported}";
	var eil_column_wont_imported = "{$lang.eil_column_wont_imported}";
	var eil_select_listing_type = "{$lang.eil_select_listing_type}";
	var allow_match = {if $smarty.post.from_post || isset($smarty.get.edit)}false{else}true{/if};
	var matched_fields = 0;
	var listings_count = {$sPost.data|@count};
	var hide_first = {if $sPost.hide_first}true{else}false{/if};
	{literal}
	$(document).ready(function(){
		eil_rowHandler();
		eil_colHandler();
		$('input[name^=rows]').click(function(){
			var index = $('table.import tr.body td.row-checkbox input').index(this);
			eil_rowHandler(index);
		});
		$('input[name^=cols]').click(function(){
			eil_colHandler();
		});
		$('select[name=import_listing_type]').change(function(){
			eil_typeHandler($(this).val(), 'import_category_id');
		});
		/* match fields handler */
		$('table.import tr.header td div').each(function(){
			var field = $(this).find('span.caption').text();
			if ( field != '$' && !$(this).find('select').val() && field )
			{
				$(this).find('select optgroup:eq(1) option').each(function(){
					var pattern = new RegExp('^'+field+'?', 'i');
					if ( pattern.test($(this).text()) )
					{
						if ( allow_match ) {
							$(this).attr('selected', true);
						}
						matched_fields++;
						return false;
					}
				});
				if ( !$(this).find('select').val() )
				{
					$(this).find('select').addClass('error');
				}
			}
		});
		if ( (matched_fields > 2 || hide_first) && listings_count > 1 )
		{
			$('table.import tr.body:first').hide();
			$('table.import').after('<input type="hidden" name="hide_first" value="1" />');
		}
		$('table.import select.error').click(function(){
			$(this).removeClass('error');
		});
	});
	{/literal}
	//]]>
	</script>
{elseif $ei_mode == 'import_importing'}
	<!-- importing -->
	<script type="text/javascript">//<![CDATA[[
	importExport.phrases['completed'] = "{$lang.eil_completed}";
	importExport.config['per_run'] = {$import_details.1.value};
	//]]>
	</script>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<table class="list">
	{foreach from=$import_details item='item' name='itemF'}
	<tr>
		<td class="name">{$item.name}:</td>
		<td class="value{if $smarty.foreach.itemF.first} first{/if}">{$item.value}</td>
	</tr>
	{/foreach}
	<tr id="import_start_nav">
		<td style="height: 50px;">
			<span class="purple_13">&larr; </span><a class="cancel" href="{$rlBaseC}&amp;action=import&amp;edit" style="padding: 0;">{$lang.eil_back_to_import_form}</a>
		</td>
		<td class="value">
			<input id="start_import" type="button" value="{$lang.eil_start}" />
		</td>
	</tr>
	</table>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	{include file=$smarty.const.RL_PLUGINS|cat:'export_import'|cat:$smarty.const.RL_DS|cat:'admin'|cat:$smarty.const.RL_DS|cat:'import_interface.tpl'}
	<!-- importing -->
	<!-- listings grid create -->
	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	var imported_file = '{$smarty.session.iel_data.file_name|urlencode}';
	// collect plans
	var listing_plans = [
		{foreach from=$plans item='plan' name='plans_f'}
			['{$plan.ID}', '{$plan.name}']{if !$smarty.foreach.plans_f.last},{/if}
		{/foreach}
	];
	var mass_actions = [
		[lang['ext_activate'], 'activate'],
		[lang['ext_suspend'], 'approve'],
		[lang['ext_delete'], 'delete']
	];
	{literal}
	var listingsGrid;
	$(document).ready(function(){
		listingsGrid = new gridObj({
			key: 'importedListings',
			id: 'grid',
			ajaxUrl: rlUrlHome + 'controllers/listings.inc.php?q=ext&f_Import_file='+imported_file,
			defaultSortField: 'Date',
			defaultSortType: 'DESC',
			remoteSortable: false,
			checkbox: true,
			/*actions: mass_actions,*/
			filtersPrefix: true,
			title: lang['ext_imported_listings_manager'],
			expander: true,
			expanderTpl: '<div style="margin: 0 5px 5px 83px"> \
				<table> \
				<tr> \
				<td>{thumbnail}</td> \
				<td>{fields}</td> \
				</tr> \
				</table> \
				<div> \
			',
			affectedObjects: '#make_featured,#move_area',
			fields: [
				{name: 'ID', mapping: 'ID', type: 'int'},
				{name: 'title', mapping: 'title', type: 'string'},
				{name: 'Username', mapping: 'Username', type: 'string'},
				{name: 'Account_ID', mapping: 'Account_ID', type: 'int'},
				{name: 'Type', mapping: 'Type'},
				{name: 'Type_key', mapping: 'Type_key'},
				{name: 'Plan_name', mapping: 'Plan_name'},
				{name: 'Plan_ID', mapping: 'Plan_name'},
				{name: 'Plan_info', mapping: 'Plan_info'},
				{name: 'Cat_title', mapping: 'Cat_title', type: 'string'},
				{name: 'Cat_ID', mapping: 'Cat_ID', type: 'int'},
				{name: 'Cat_custom', mapping: 'Cat_custom', type: 'int'},
				{name: 'Status', mapping: 'Status'},
				{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
				{name: 'Pay_date', mapping: 'Pay_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
				{name: 'thumbnail', mapping: 'thumbnail', type: 'string'},
				{name: 'fields', mapping: 'fields', type: 'string'},
				{name: 'data', mapping: 'data', type: 'string'},
				{name: 'Allow_photo', mapping: 'Allow_photo', type: 'int'},
				{name: 'Allow_video', mapping: 'Allow_video', type: 'int'}
			],
			columns: [
				{
					header: lang['ext_id'],
					dataIndex: 'ID',
					width: 40,
					fixed: true,
					id: 'rlExt_black_bold'
				},{
					header: lang['ext_title'],
					dataIndex: 'title',
					width: 23,
					renderer: function(val, ext, row){
						var out = '<a href="'+rlUrlHome+'index.php?controller=listings&action=view&id='+row.data.ID+'">'+val+'</a>';
						return out;
					}
				},{
					header: lang['ext_owner'],
					dataIndex: 'Username',
					width: 8,
					id: 'rlExt_item_bold',
					renderer: function(username, ext, row){
						return "<a target='_blank' ext:qtip='"+lang['ext_click_to_view_details']+"' href='"+rlUrlHome+"index.php?controller=accounts&action=view&userid="+row.data.Account_ID+"'>"+username+"</a>"
					}
				},{
					header: lang['ext_type'],
					dataIndex: 'Type',
					width: 8/*,
					renderer: function(val, obj, row){
						var out = '<a target="_blank" ext:qtip="'+lang['ext_click_to_view_details']+'" href="'+rlUrlHome+'index.php?controller=listing_types&action=edit&key='+row.data.Type_key+'">'+val+'</a>';
						return out;
					}*/
				},{
					header: lang['ext_category'],
					dataIndex: 'Cat_title',
					width: 9/*,
					renderer: function(val, obj, row){
						var link = row.data.Cat_custom ? rlUrlHome+'index.php?controller=custom_categories' : rlUrlHome+'index.php?controller=browse&id='+row.data.Cat_ID;
						var out = '<a target="_blank" ext:qtip="'+lang['ext_click_to_view_details']+'" href="'+link+'">'+val+'</a>';
						return out;
					}*/
				},{
					header: lang['ext_add_date'],
					dataIndex: 'Date',
					width: 10,
					hidden: true,
					renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
				},{
					header: lang['ext_payed'],
					dataIndex: 'Pay_date',
					width: 8,
					renderer: function(val){
						if (!val)
						{
							var date = '<span class="delete" ext:qtip="'+lang['ext_click_to_set_pay']+'">'+lang['ext_not_payed']+'</span>';
						}
						else
						{
							var date = Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))(val);
							date = '<span class="build" ext:qtip="'+lang['ext_click_to_edit']+'">'+date+'</span>';
						}
						return date;
					},
					editor: new Ext.form.DateField({
						format: 'Y-m-d H:i:s'
					})
				},{
					header: lang['ext_plan'],
					dataIndex: 'Plan_ID',
					width: 11,
					/*editor: new Ext.form.ComboBox({
						store: listing_plans,
						mode: 'local',
						triggerAction: 'all'
					}),*/
					renderer: function (val, obj, row){
						if (val != '')
						{
							return '<img class="info" ext:qtip="'+row.data.Plan_info+'" alt="" src="'+rlUrlHome+'img/blank.gif" />&nbsp;&nbsp;<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
						}
						else
						{
							return '<span class="delete" ext:qtip="'+lang['ext_click_to_edit']+'" style="margin-left: 21px;">'+lang['ext_no_plan_set']+'</span>';
						}
					}
				},{
					header: lang['ext_status'],
					dataIndex: 'Status',
					width: 5,
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
					width: 120,
					fixed: true,
					dataIndex: 'data',
					sortable: false,
					resizeable: false,
					renderer: function(id, obj, row){
						var out = "<div style='text-align: right'>";
						var splitter = false;
						if ( cKey == 'browse' )
						{
							cKey = 'listings';
						}
						
						if ( row.data.Allow_photo )
						{
							out += "<a href='"+rlUrlHome+"index.php?controller=listings&action=photos&id="+id+"'><img class='photo' ext:qtip='"+lang['ext_manage_photo']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						}
						if ( row.data.Allow_video )
						{
							out += "<a href='"+rlUrlHome+"index.php?controller=listings&action=video&id="+id+"'><img class='video' ext:qtip='"+lang['ext_manage_video']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						}
						out += "<a href='"+rlUrlHome+"index.php?controller=listings&action=view&id="+id+"'><img class='view' ext:qtip='"+lang['ext_view_details']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						out += "<a href=\""+rlUrlHome+"index.php?controller=listings&action=edit&id="+id+"\"><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlPrompt( \""+lang['ext_notice_'+delete_mod]+"\",  \"xajax_deleteListing\", \""+id+"\" )' />";
						out += "</div>";
						return out;
					}
				}
			]
		});
		
	});
	{/literal}
	//]]>
	</script>
{elseif $ei_mode == 'export'}
	<!-- export conditions -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action=export" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="export_condition" />
		<input type="hidden" name="from_post" value="1" />
		<table class="form">
		<tr>
			<td class="name"><span class="red">*</span>{$lang.eil_file_format}</td>
			<td class="field">
				<select class="w130" name="export_format">
					<option value="">{$lang.select}</option>
					<option {if $sPost.export_format == 'xls'}selected="selected"{/if} value="xls">{$lang.eil_xls}</option>
					<option {if $sPost.export_format == 'csv'}selected="selected"{/if} value="csv">{$lang.eil_csv}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="name"><span class="red">*</span>{$lang.listing_type}</td>
			<td class="field">
				<select class="w200" name="export_listing_type">
					<option value="">{$lang.select}</option>
					{foreach from=$listing_types item='l_type'}
						<option {if $sPost.export_listing_type == $l_type.Key}selected="selected"{/if} value="{$l_type.Key}">{$l_type.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.category}</td>
			<td class="field">
				<select class="w200" name="export_category_id">
					<option value="">{if $categories}{$lang.select}{else}{$lang.eil_select_listing_type}{/if}</option>
					{if $categories}
						{foreach from=$categories item='category'}
							<option {if $category.Level == 0}class="highlight_opt"{/if} {if $category.margin}style="margin-left: {$category.margin}px;"{/if} value="{$category.ID}" {if $sPost.export_category_id == $category.ID}selected="selected"{/if}>{$lang[$category.pName]} ({$category.Count})</option>
						{/foreach}
					{/if}
				</select>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.posted_date}</td>
			<td class="field">
				<input style="width: 65px;" name="export_date_from" type="text" value="{$sPost.export_date_from}" size="12" maxlength="10" id="date_from" />
				<img class="divider" alt="" src="{$rlTplBase}img/blank.gif" />
				<input style="width: 65px;" name="export_date_to" type="text" value="{$sPost.export_date_to}" size="12" maxlength="10" id="date_to" />
			</td>
		</tr>
		</table>
		<div id="export_table" style="margin-top: 10px;">
			{if $fields}
				{include file=$smarty.const.RL_PLUGINS|cat:'export_import'|cat:$smarty.const.RL_DS|cat:'admin'|cat:$smarty.const.RL_DS|cat:'search.tpl'}
			{/if}
		</div>
		<table class="form">
		<tr>
			<td style="width: 185px;"></td>
			<td class="field"><input type="submit" name="" value="{$lang.eil_export}" /></td>
		</tr>
		</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	<!-- export conditions end -->
	<script type="text/javascript">//<![CDATA[
	var eil_select_listing_type = "{$lang.eil_select_listing_type}";
	{literal}
	$(document).ready(function(){
		$('select[name=export_listing_type]').change(function(){
			eil_typeHandler($(this).val(), 'export_category_id');
		});
		
		$(function(){
			$('#date_from').datepicker({showOn: 'both', buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif', buttonText: '{/literal}{$lang.dp_choose_date}{literal}', buttonImageOnly: true, dateFormat: 'yy-mm-dd'}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
			$('#date_to').datepicker({showOn: 'both', buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif', buttonText: '{/literal}{$lang.dp_choose_date}{literal}', buttonImageOnly: true, dateFormat: 'yy-mm-dd'}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
	    });
	});
	{/literal}
	//]]>
	</script>
{elseif $ei_mode == 'export_table'}
	<!-- export listings table -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action=export_table" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="export_table" />
		<input type="hidden" name="from_post" value="1" />
		
		<table style="margin: -3px 0 10px 0;">
		<tr>
			<td style="padding-right: 20px;">
				<span class="purple_13">&larr; </span><a class="cancel" href="{$rlBaseC}&amp;action=export" style="padding: 0;">{$lang.eil_back_to_export_criteria}</a>
			</td>
			<td class="value">
				<input style="margin-top: 0;" type="submit" name="submit" value="{$lang.save}" />
			</td>
		</tr>
		</table>
		<table class="import export">
		<tr class="col-checkbox no_hover">
			<td></td>
			{foreach from=$fields item='checkbox' name='checkboxF'}
			<td>
				{assign var='iter_checkbox' value=$smarty.foreach.checkboxF.iteration-1}
				<div><input {if isset($sPost.from_post) && $sPost.cols[$iter_checkbox]}checked="checked"{elseif !isset($sPost.from_post)}checked="checked"{/if} value="1" type="checkbox" name="cols[{$iter_checkbox}]" /></div>
			</td>
			{/foreach}
		</tr>
		<tr class="header">
			<td></td>
			{foreach from=$fields item='field'}
				<td><div>{$lang[$field.pName]}</div></td>
			{/foreach}
		</tr>
		{foreach from=$listings item='listing' name='rowF'}
		{assign var='iter_row' value=$smarty.foreach.rowF.iteration-1}
		<tr class="body{if $smarty.foreach.rowF.iteration%2 == 0 && !$smarty.foreach.rowF.first} highlight{/if}">
			<td class="row-checkbox">
				<div><input {if isset($sPost.from_post) && $sPost.rows[$iter_row]}checked="checked"{elseif !isset($sPost.from_post)}checked="checked"{/if} type="checkbox" name="rows[{$iter_row}]" value="1" /></div>
			</td>
			{foreach from=$fields item='field'}
				<td><div>{$listing[$field.Key]}</div></td>
			{/foreach}
		</tr>
		{/foreach}
		</table>
		<table style="margin-top: 10px;">
		<tr>
			<td style="padding-right: 20px;">
				<span class="purple_13">&larr; </span><a class="cancel" href="{$rlBaseC}&amp;action=export" style="padding: 0;">{$lang.eil_back_to_export_criteria}</a>
			</td>
			<td class="value">
				<input style="margin-top: 0;" type="submit" name="submit" value="{$lang.save}" />
			</td>
		</tr>
		</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	<!-- export listings table -->
	<script type="text/javascript">
	var eil_listing_wont_imported = "{$lang.eil_listing_wont_exported}";
	var eil_column_wont_imported = "{$lang.eil_column_wont_exported}";
	{literal}
	$(document).ready(function(){
		eil_rowHandler();
		eil_colHandler();
		$('input[name^=rows]').click(function(){
			var index = $('table.import tr.body td.row-checkbox input').index(this);
			eil_rowHandler(index);
		});
		$('input[name^=cols]').click(function(){
			eil_colHandler();
		});
		$('input[name^=cols]').each(function(){
			var index = $(this).closest('tr.col-checkbox').find('input').index(this) + 2;
			var count = 0;
			var empty = 0;
			$('table.import tr.body td:nth-child('+index+') div').each(function(){
				empty += trim($(this).html()) == '' ? 1 : 0;
				count ++;
			});
			if ( count == empty )
			{
				$(this).trigger('click');
			}
		});
	});
	{/literal}
	</script>
{/if}
<!-- import/export listings tpl end -->