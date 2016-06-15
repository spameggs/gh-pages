<!-- export import categories tpl -->
<!-- navigation bar -->
<div id="nav_bar">
	{if $smarty.get.action == 'import' || !isset($smarty.get.action)}
	<a href="{$rlBaseC}action=export" class="button_bar"><span class="left"></span><span class="center_export">{$lang.importExportCategories_export}</span><span class="right"></span></a>
	{/if}
	{if $smarty.get.action == 'export' || !isset($smarty.get.action)}
	<a href="{$rlBaseC}action=import" class="button_bar"><span class="left"></span><span class="center_import">{$lang.importExportCategories_import}</span><span class="right"></span></a>
	{/if}
</div>
<!-- navigation bar end -->
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
{if !isset($smarty.get.action)}
	<img src="{$smarty.const.RL_PLUGINS_URL}importExportCategories/admin/static/example.png" alt="" title="" />
	<div class="clear"></div>
{elseif $smarty.get.action == 'import'}
	{if !$smarty.session.file_info}
		<form action="{$rlBaseC}action=import" method="post" enctype="multipart/form-data" onsubmit="return submit_form();">
		<input type="hidden" name="submit" value="1" />
			<table class="form">
			<tr>
				<td class="name">
					<span class="red">*</span>{$lang.file}
				</td>
				<td class="field">
					<input type="file" class="file" name="file_import" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="field">
					<input class="submit" type="submit" value="{$lang.importExportCategories_import}" />
				</td>
			</tr>
			</table>
		</form>
		<script type="text/javascript">
		{literal}
			var submit_form = function() {
				var importFile = $('[name=file_import]').val();
				if ( importFile == '' ) {
					
					printMessage('error', '{/literal}{$lang.importExportCategories_import_filename_empty|replace:"[field]":$lang.file}{literal}');
					return false;
				}
				else {
					if ( importFile.split('.')[1] != 'xls' ) {
						printMessage('error', '{/literal}{$lang.importExportCategories_incorrect_file_ext}{literal}');
						return false;
					}
				}
				return true;
			}
		{/literal}	
		</script>
	{else}
		<form action="{$rlBaseC}action=import" method="post">
			<input type="hidden" name="categories_add" value="add" />
			<input type="hidden" name="test" value="1" />

			<table id="import_table" class="form">
			<thead>
			<tr>
				<th></th>
			{foreach from=$selector_tr item='hCaption'}
				<th id="col_{$hKey}" class="list_td" style="padding:4px;">
					{assign var='trName' value='importExportCategories_selector_tr_'|cat:$hCaption}
					<b>{$lang.$trName}</b>
				</th>
			{/foreach}
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th></th>
			{foreach from=$selector_tr item='fKey'}
				<th id="col_{$fKey}" class="list_td" style="padding:4px;">
					{if $fKey == 'type'}
						<select class="importBorder" id="changeAllType">
							{foreach from=$listing_types item="type" key='lType'}
							<option value="{$type.Key}">{$type.name}</option>
							{/foreach}
						</select>
					{elseif $fKey == 'lock'}
						<select class="importBorder" id="changeAllLock">
							<option value="0">{$lang.no}</option>
							<option value="1">{$lang.yes}</option>
						</select>
					{/if}
				</th>
			{/foreach}
			</tr>
			</tfoot>
			{foreach from=$cats_array item='csv' key='key'}
			{assign var='csv_val' value=$cats_array.$key}
				<tr id="row_{$key}" class="{if $smarty.post.categories.$key.checkbox || !$smarty.post.categories_add}checked{else}unckeked{/if}{if in_array($key,$exist_keys)} exist{/if}">
					<td class="list_td" style="width:20px; border-bottom:1px #ccc solid; border-right:1px #ccc solid; padding:4px;">
						<input type="checkbox" name="categories[{$key}][checkbox]" class="row_checkbox" id="row_cb_{$key}" {if !$smarty.post.categories_add && !in_array($key,$exist_keys)}checked="checked"{elseif $smarty.post.categories.$key.checkbox}checked="checked"{/if} onclick="approval_cat('{$key}');"  />
					</td>
					{foreach from = $csv_val item = 'csv_value' key = 'key2'}
						<td {if ($key%2) ==0 }class="list_td_light"{else}class="divider"{/if} style="border-bottom:1px #ccc solid; padding:4px;">
							{if $key2 == 'key'}
								{if in_array($key,$exist_keys)}
								<input type="hidden" name="categories[{$key}][exist]" value="yes">
								{/if}
								<input type="hidden" name="categories[{$key}][key]" value="{$csv_value}">
								<span>{$csv_value}</span>
							{elseif $key2 == 'parent'}
								<input type="hidden" name="categories[{$key}][parent]" value="{if !empty($csv_value)}{$csv_value}{else}0{/if}">
								<span>{if !empty($csv_value)}{$csv_value}{else}{$lang.importExportCategories_no_parent}{/if}</span>
							{elseif $key2 == 'lock'}
								<select name="categories[{$key}][lock]" class="importBorder allLock">
									<option value="0" {if $csv_value == '0'}selected="true"{/if}>{$lang.no}</option>
									<option value="1"{if $csv_value == '1'}selected="true"{/if}>{$lang.yes}</option>
								</select>
							{elseif $key2 == 'type'}
								<select name="categories[{$key}][type]" class="importBorder allType">
									{foreach from=$listing_types item="type" key='typeKey'}
									<option value="{$type.Key}">{$type.name}</option>
									{/foreach}
								</select>
							{else}
								<input name="categories[{$key}][{$key2}]" type="text" class="importBorder" {if isset($smarty.post.categories) && !empty($smarty.post.categories)} value="{$smarty.post.categories.$key.$key2}"{else}value="{$csv_value}"{/if} />
							{/if}
						</td>
					{/foreach}
				</tr>
			{/foreach}
			</table>
			<input type="submit" name="test" id="import_bt" class="hide" value="{$lang.importExportCategories_import}" />
		</form>
		<script type="text/javascript">
		var flag = 0;
		var empty_text = '{$lang.importExportCategories_empty}';
		{literal}
		$(document).ready(function(){
			$('select#changeAllType').change(function(){
				var val = $(this).val();
				$('select.allType').find('option[value='+val+']').attr({selected:true});
			});

			$('select#changeAllLock').change(function(){
				var val = $(this).val();
				$('select.allLock').find('option[value='+val+']').attr({selected:true});
			});
		});
		if ( $('table#import_table input[type=checkbox]:checked').length == 0 ) {
			$('input#import_bt').fadeOut('fast');
			flag = 1;
			printMessage('info',empty_text);
		}
		else {
			$('input#import_bt').fadeIn('slow');
		}
		function approval_cat(id) {
			if ( $('#row_'+id).hasClass('unckeked') ) {
				$('#row_'+id).removeClass('unckeked').addClass('checked');
			}
			else {
				$('#row_'+id).addClass('unckeked').removeClass('checked');
			}

			if ( flag == 1 ) {
				$('input#import_bt').fadeIn('slow');
				flag = 0;
			}
			
			if ( $('table#import_table input[type=checkbox]:checked').length == 0 ) {
				$('input#import_bt').fadeOut('fast');
				flag = 1;
				printMessage('info',empty_text);
			}
		}
		{/literal}
		</script>
	{/if}
{elseif $smarty.get.action == 'export'}
	<form action="{$rlBaseC}action=export" method="post" onsubmit="return submit_form();">
		<input type="hidden" name="submit" value="1" />
		<table class="form">
		<tr>
			<td>
				<div id="cat_checkboxed" style="margin: 0 0 8px;{if $sPost.cat_sticky}display: none{/if}">
					<div class="tree">
						{foreach from=$sections item='section'}
							<fieldset class="light">
								<legend id="legend_section_{$section.ID}" class="up" onclick="fieldset_action('section_{$section.ID}');">{$section.name}</legend>
								<div id="section_{$section.ID}">
									{if !empty($section.Categories)}
										{include file='blocks'|cat:$smarty.const.RL_DS|cat:'category_level_checkbox.tpl' categories=$section.Categories first=true}
									{else}
										<div style="padding: 0 0 8px 10px;">{$lang.no_items_in_sections}</div>
									{/if}
								</div>
							</fieldset>
						{/foreach}
					</div>
				</div>
				<script type="text/javascript">
				var submit_form;
				var tree_selected = {if $smarty.post.categories}[{foreach from=$smarty.post.categories item='post_cat' name='postcatF'}['{$post_cat}']{if !$smarty.foreach.postcatF.last},{/if}{/foreach}]{else}false{/if};
				var tree_parentPoints = {if $parentPoints}[{foreach from=$parentPoints item='parent_point' name='parentF'}['{$parent_point}']{if !$smarty.foreach.parentF.last},{/if}{/foreach}]{else}false{/if};
				{literal}
				$(document).ready(function(){
					flynax.treeLoadLevel('checkbox', 'flynax.openTree(tree_selected, tree_parentPoints)', 'div#cat_checkboxed');
					flynax.openTree(tree_selected, tree_parentPoints);
					$('input[name=cat_sticky]').click(function(){
						$('#cat_checkboxed').slideToggle();
						$('#cats_nav').fadeToggle();
					});
					submit_form = function() {
						if ( $('#cat_checkboxed input[type=checkbox]:checked').length > 0 || $('input[name=cat_sticky]:checked').length > 0 ) {
							return true;
						}
						else {
							printMessage('info', '{/literal}{$lang.importExportCategories_empty}{literal}');
							return false;
						}
					}
				});
				{/literal}
				</script>
				<div class="grey_area">
					<label><input class="checkbox" {if $sPost.cat_sticky}checked="checked"{/if} type="checkbox" name="cat_sticky" value="true" /> {$lang.sticky}</label>
					<span id="cats_nav" {if $sPost.cat_sticky}class="hide"{/if}>
						<span onclick="$('#cat_checkboxed div.tree input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
						<span class="divider"> | </span>
						<span onclick="$('#cat_checkboxed div.tree input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
					</span>
				</div>
			</td>
		</tr>
		<tr>
			<td class="field">
				<input type="submit" id="export_btn" value="{$lang.importExportCategories_export}" />
			</td>
		</tr>
		</table>
	</form>
{/if}
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
<!-- export import categories tpl end -->