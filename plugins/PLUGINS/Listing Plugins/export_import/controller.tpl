<!-- excel export/ import -->
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}export_import/static/lib.js"></script>
{assign var='sPost' value=$smarty.post}
<!-- tabs -->
{if !$iel_action}
	<div class="tabs">
		<ul>
			{foreach from=$tabs item='tab' name='tabF'}
			<li class="{if ($sPost.action == 'export_condition' || isset($smarty.get.refine)) && $tab.key == 'export'}active {elseif ($sPost.action != 'export_condition' && !isset($smarty.get.refine) ) && $tab.key == 'import'}active {/if}first" id="tab_{$tab.key}">
				<span class="center">{$tab.name}</span>
			</li>
			{/foreach}
		</ul>
	</div>
	<div class="clear"></div>
{/if}
<!-- tabs end -->
<div class="highlight eil">
	{if !$iel_action}
		<!-- import/export forms -->
		<div id="area_import" class="tab_area{if $sPost.action == 'export_condition' || isset($smarty.get.refine)} hide{/if}">
			<form action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html{else}?page={$pageInfo.Path}{/if}" method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="import_file" />
				<table class="submit">
				<tr>
					<td class="name"><span class="red">*</span>{$lang.eil_file_for_import}</td>
					<td class="field">
						<input type="file" name="file" />
						{$lang.eil_file_for_import_desc}
					</td>
				</tr>
				<tr>
					<td class="name">{$lang.eil_pictures_archive}</td>
					<td class="field">
						<input type="file" name="archive" />
						{$lang.eil_pictures_archive_desc}
						<div style="padding: 10px 0;">{$lang.eil_max_file_size} <b>{$max_file_size} MB</b></div>
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
		</div>
		<!-- export conditions -->
		<div id="area_export" class="tab_area{if $sPost.action != 'export_condition' && !isset($smarty.get.refine)} hide{/if}">
			<form action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html{else}?page={$pageInfo.Path}{/if}" method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="export_condition" />
				<input type="hidden" name="from_post" value="1" />
				<table class="search">
				<tr>
					<td class="field"><span class="red">*</span>{$lang.eil_file_format}</td>
					<td class="value">
						<select class="w130" name="export_format">
							<option value="">{$lang.select}</option>
							<option {if $sPost.export_format == 'xls'}selected="selected"{/if} value="xls">{$lang.eil_xls}</option>
							<option {if $sPost.export_format == 'csv'}selected="selected"{/if} value="csv">{$lang.eil_csv}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="field"><span class="red">*</span>{$lang.listing_type}</td>
					<td class="value">
						<select class="w200" name="export_listing_type">
							<option value="">{$lang.select}</option>
							{foreach from=$listing_types item='l_type'}
								{if $l_type.Key|in_array:$account_info.Abilities}
									<option {if $sPost.export_listing_type == $l_type.Key}selected="selected"{/if} value="{$l_type.Key}">{$l_type.name}</option>
								{/if}
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td class="field">{$lang.category}</td>
					<td class="value">
						<select class="w200" name="export_category_id">
							<option value="">{if $categories}{$lang.select}{else}{$lang.eil_select_listing_type}{/if}</option>
							{if $categories}
								{foreach from=$categories item='category'}
									<option {if $category.Level == 0}class="highlight_opt"{/if} {if $category.margin}style="margin-left: {$category.margin}px;"{/if} value="{$category.ID}" {if $sPost.export_category_id == $category.ID}selected="selected"{/if}>{$lang[$category.pName]}</option>
								{/foreach}
							{/if}
						</select>
					</td>
				</tr>
				<tr>
					<td class="field">{$lang.posted_date}</td>
					<td class="value">
						<input style="width: 90px;" name="export_date_from" type="text" value="{$sPost.export_date_from}" size="12" maxlength="10" id="date_from" />
						<img class="divider" alt="" src="{$rlTplBase}img/blank.gif" />
						<input style="width: 90px;" name="export_date_to" type="text" value="{$sPost.export_date_to}" size="12" maxlength="10" id="date_to" />
					</td>
				</tr>
				</table>
				
				<div id="export_table" style="margin-top: 10px;">
					{if $fields}
						{include file=$smarty.const.RL_PLUGINS|cat:'export_import'|cat:$smarty.const.RL_DS|cat:'search.tpl'}
					{/if}
				</div>
				<table class="search">
				<tr>
					<td class="field button"></td>
					<td class="value button"><input type="submit" name="" value="{$lang.eil_export}" /></td>
				</tr>
				</table>
			</form>
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
		</div>
		<!-- export conditions end -->
	{elseif $iel_action == 'import-table'}
		<!-- import table -->
		<div class="iel_table">
			<form action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/import-table.html{else}?page={$pageInfo.Path}&amp;action=import-table{/if}" method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="import_form" />
				<input type="hidden" name="from_post" value="1" />
				<table class="submit">
				<tr>
					<td class="name"><div style="min-width: 140px;padding-top: 73px;"><span class="red">*</span>{$lang.listings}</div></td>
					<td class="field">
						<table class="import list">
						<tr class="col-checkbox no_hover">
							<td></td>
							<td></td>
							<td class="divider"></td>
							{foreach from=$sPost.data.0 item='checkbox' name='checkboxF'}
							<td>
								{assign var='iter_checkbox' value=$smarty.foreach.checkboxF.iteration-1}
								<div><input {if isset($sPost.data) && $sPost.cols[$iter_checkbox]}checked="checked"{elseif !isset($sPost.data)}checked="checked"{/if} value="1" type="checkbox"  name="cols[{$iter_checkbox}]" /></div>
							</td>
							{if !$smarty.foreach.checkboxF.last}<td class="divider"></td>{/if}
							{/foreach}
						</tr>
						<tr class="header no_hover">
							<td class="row-checkbox no-style"></td>
							<td class="row-plan"><div><span style="max-width: 150px;" class="caption" title="{$lang.eil_prepaid_plan}">{$lang.eil_prepaid_plan}</span></div></td>
							<td class="divider"></td>
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
								{if !$smarty.foreach.fieldF.last}<td class="divider"></td>{/if}
							{/foreach}
						</tr>
						{foreach from=$sPost.data item='row' name='rowF'}
							{assign var='iter_row' value=$smarty.foreach.rowF.iteration-1}
							<tr class="body{if $smarty.foreach.rowF.iteration%2 == 0 && !$smarty.foreach.rowF.first} hlight{/if}">
								<td class="row-checkbox">
									<div><input {if isset($sPost.from_post) && $sPost.rows[$iter_row]}checked="checked"{elseif !isset($sPost.from_post) && !$smarty.foreach.rowF.first}checked="checked"{/if} type="checkbox" name="rows[{$iter_row}]" value="1" /></div>
								</td>
								<td class="row-plan">
									<div>
										{if $user_plans}
											<select class="w200" name="plan[{$iter_row}]">
												<option value="">{$lang.select}</option>
												{foreach from=$user_plans item='plan'}
													{assign var='plan_type' value=$plan.Type|cat:'_plan'}
													<option value="{$plan.ID}" {if $sPost.plan[$iter_row] == $plan.ID}selected="selected"{/if}>{$plan.name}</option>
												{/foreach}
											</select>
										{else}
											<select disabled="disabled" class="w200 disabled" name="plan[{$iter_row}]"><option value="">{$lang.eil_no_user_plans}</option></select>
										{/if}
										<ul class="type hide">
											<li>
												<label>
													<input {if $sPost.type[$iter_row] == 'standard'}checked="checked"{/if} type="radio" name="type[{$iter_row}]" value="standard" />
													{$lang.standard} <span></span>
												</label>
											</li>
											<li>
												<label>
													<input {if $sPost.type[$iter_row] == 'featured'}checked="checked"{/if} type="radio" name="type[{$iter_row}]" value="featured" /> 
													{$lang.featured} <span></span>
												</label>
											</li>
										</ul>
									</div>
								</td>
								<td class="divider"></td>
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
									{if !$smarty.foreach.colF.last}<td class="divider"></td>{/if}
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
								{if $l_type.Key|in_array:$account_info.Abilities}
									<option {if $sPost.import_listing_type == $l_type.Key}selected="selected"{/if} value="{$l_type.Key}">{$l_type.name}</option>
								{/if}
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
									<option {if $category.Level == 0}class="highlight_option"{/if} {if $category.margin}style="margin-left: {$category.margin}px;"{/if} value="{$category.ID}" {if $sPost.import_category_id == $category.ID}selected="selected"{/if}>{$lang[$category.pName]}</option>
								{/foreach}
							{/if}
						</select>
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
				<tr id="plans_tr">
					<td class="name"><span class="red">*</span>{$lang.eil_default_plan}</td>
					<td class="field">
						<select class="w200" name="import_plan_id">
							<option value="">{$lang.select}</option>
							{if $plans}
								{foreach from=$plans item='plan'}
									{assign var='plan_type' value=$plan.Type|cat:'_plan'}
									<option value="{$plan.ID}" {if $sPost.import_plan_id == $plan.ID}selected="selected"{/if}>{$plan.name} ({$lang.$plan_type}) - {if $plan.Price}{$config.system_currency}{$plan.Price}{else}{$lang.free}{/if}</option>
								{/foreach}
							{/if}
						</select>
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
					<td class="field"><input type="submit" name="" value="{$lang.eil_import}" /></td>
				</tr>
				</table>
			</form>
			<div class="import_note">
				<div>{$lang.eil_pictures_by_url_note}</div>
				<div>{$lang.eil_pictures_from_zip_note}</div>
				<div>{$lang.eil_sub_category_note}</div>
				<div>{$lang.eil_youtube_video_field_note}</div>
			</div>
		</div>
		<!-- import table end -->
		<script type="text/javascript">//<![CDATA[
		importExport.phrases['eil_default_view'] = "{$lang.eil_default_view}";
		importExport.phrases['eil_import_table'] = "{$lang.eil_import_table}";
		importExport.phrases['eil_free_listing'] = "{$lang.eil_free_listing}";
		importExport.phrases['eil_prepaid_package'] = "{$lang.eil_prepaid_package}";
		importExport.phrases['used_up'] = "{$lang.used_up}";
		importExport.phrases['powered_by'] = "{$lang.powered_by|replace:'"':'&quot;'}";
		importExport.phrases['copy_rights'] = "{$lang.copy_rights|replace:'"':'&quot;'}";
		importExport.phrases['unlimited'] = "{$lang.unlimited}";
		importExport.phrases['number_left'] = "{$lang.number_left}";
		{foreach from=$user_plans item='user_plan'}
			importExport.plans[{$user_plan.ID}] = new Array();
			{foreach from=$user_plan item='plan_value' key='plan_field'}
				importExport.plans[{$user_plan.ID}]['{$plan_field}'] = {if is_numeric($plan_value)}{$plan_value}{else}'{$plan_value}'{/if};
			{/foreach}
		{/foreach}
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
			eil_status();
			importExport.modeSwitcher();
			importExport.plansHandler();
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
			$('select[name=import_status]').change(function(){
				eil_status($(this).val());
			});
			/* match fields handler */
			$('table.import tr.header td:not(.divider) div').each(function(){
				var field = $(this).find('span.caption').text();
				if ( !$(this).find('select').val() && field )
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
			$('table.import td.row-plan select, table.import td.row-plan input').change(function(){
				importExport.plansHandler();
			});
		});
		{/literal}
		//]]>
		</script>
	{elseif $iel_action == 'import-preview'}
		<!-- importing -->
		<script type="text/javascript">//<![CDATA[[
		importExport.phrases['completed'] = "{$lang.eil_completed}";
		importExport.config['per_run'] = {$import_details.1.value};
		//]]>
		</script>
		<table class="table">
		{foreach from=$import_details item='item' name='itemF'}
		<tr>
			<td class="name">{$item.name}:</td>
			<td class="value{if $smarty.foreach.itemF.first} first{/if}">{$item.value}</td>
		</tr>
		{/foreach}
		</table>
		<div id="import_start_nav">
			<table class="table" style="margin-top: 15px;">
			<tr>
				<td class="name" style="padding-top: 9px;">
					<a class="dark_12" href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/import-table.html?edit{else}?page={$pageInfo.Path}&amp;action=import-table&amp;edit{/if}">&larr;{$lang.eil_back_to_import_form}</a>
				</td>
				<td class="value">
					<input id="start_import" type="button" value="{$lang.eil_start}" />
				</td>
			</tr>
			</table>
		</div>
		<div id="eil_statistic" class="hide">
			<div class="caption" style="padding-top: 30px">{$lang.eil_importing_caption}</div>
			<table class="table">
			<tr>
				<td class="name">
					{$lang.eil_total_listings}:
				</td>
				<td class="value">{$import_details.0.value}</td>
			</tr>
			</table>
			<div id="dom_area">
				<table class="table">
				<tr>
					<td class="name">
						{$lang.eil_importing}:
					</td>
					<td class="value" id="importing">1-{if $import_details.1.value > $import_details.0.value}{$import_details.0.value}{else}{$import_details.1.value}{/if}</td>
				</tr>
				</table>
			</div>
			<div class="progress hborder">
				<div>
					<div id="processing" class="highlight_dark search">
						<div id="loading_percent">0%</div>
					</div>
				</div>
			</div>
		</div>
		<!-- importing -->
	{elseif $iel_action == 'export-table'}
		<form action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/export-table.html{else}?page={$pageInfo.Path}&amp;action=export-table{/if}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="export_table" />
			<input type="hidden" name="from_post" value="1" />
			<table style="margin: 0 0 10px 0;">
			<tr>
				<td style="padding-right: 20px;">
					<a class="dark_12" href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?refine{else}?page={$pageInfo.Path}&amp;refine{/if}">&larr;{$lang.eil_back_to_export_criteria}</a>
				</td>
				<td class="value">
					<input style="margin-top: 0;" type="submit" name="submit" value="{$lang.save}" />
				</td>
			</tr>
			</table>
			<div class="iel_table">
				<table class="import export list">
				<tr class="col-checkbox no_hover">
					<td></td>
					{foreach from=$fields item='checkbox' name='checkboxF'}
					<td>
						{assign var='iter_checkbox' value=$smarty.foreach.checkboxF.iteration-1}
						<div><input {if isset($sPost.from_post) && $sPost.cols[$iter_checkbox]}checked="checked"{elseif !isset($sPost.from_post)}checked="checked"{/if} value="1" type="checkbox" name="cols[{$iter_checkbox}]" /></div>
					</td>
					{if !$smarty.foreach.checkboxF.last}
						<td></td>
					{/if}
					{/foreach}
				</tr>
				<tr class="header no_hover">
					<td class="no-style"></td>
					{foreach from=$fields item='field' name='fieldF'}
						<td><div>{$lang[$field.pName]}</div></td>
						{if !$smarty.foreach.fieldF.last}
							<td class="divider"></td>
						{/if}
					{/foreach}
				</tr>
				{foreach from=$listings item='listing' name='rowF'}
				{assign var='iter_row' value=$smarty.foreach.rowF.iteration-1}
				<tr class="body{if $smarty.foreach.rowF.iteration%2 == 0 && !$smarty.foreach.rowF.first} hlight{/if}">
					<td class="row-checkbox">
						<div><input {if isset($sPost.from_post) && $sPost.rows[$iter_row]}checked="checked"{elseif !isset($sPost.from_post)}checked="checked"{/if} type="checkbox" name="rows[{$iter_row}]" value="1" /></div>
					</td>
					{foreach from=$fields item='field' name='fieldF'}
						<td><div>{$listing[$field.Key]}</div></td>
						{if !$smarty.foreach.fieldF.last}
							<td class="divider"></td>
						{/if}
					{/foreach}
				</tr>
				{/foreach}
				</table>
			</div>	
			<table style="margin-top: 10px;">
			<tr>
				<td style="padding-right: 20px;">
					<a class="dark_12" href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?refine{else}?page={$pageInfo.Path}&amp;refine{/if}">&larr;{$lang.eil_back_to_export_criteria}</a>
				</td>
				<td class="value">
					<input style="margin-top: 0;" type="submit" name="submit" value="{$lang.save}" />
				</td>
			</tr>
			</table>
		</form>
		<!-- export listings table -->
		<script type="text/javascript">
		var eil_listing_wont_imported = "{$lang.eil_listing_wont_exported}";
		var eil_column_wont_imported = "{$lang.eil_column_wont_exported}";
		importExport.phrases['eil_default_view'] = "{$lang.eil_default_view}";
		importExport.phrases['eil_import_table'] = "{$lang.eil_export_listings}";
		importExport.phrases['powered_by'] = "{$lang.powered_by|replace:'"':'&quot;'}";
		importExport.phrases['copy_rights'] = "{$lang.copy_rights|replace:'"':'&quot;'}";
		{literal}
		$(document).ready(function(){
			eil_rowHandler();
			eil_colHandler();
			importExport.modeSwitcher();
			$('input[name^=rows]').click(function(){
				var index = $('table.import tr.body td.row-checkbox input').index(this);
				eil_rowHandler(index);
			});
			$('input[name^=cols]').click(function(){
				eil_colHandler();
			});
			$('input[name^=cols]').each(function(){
				var index = $(this).closest('tr.col-checkbox').find('input').index(this);
				index = (index * 2) + 2;
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
</div>
<!-- excel export/ import end -->