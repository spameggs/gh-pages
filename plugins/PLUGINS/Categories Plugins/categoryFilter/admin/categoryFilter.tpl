<!-- category filter tpl -->

<!-- navigation bar -->
<div id="nav_bar">
	{if !$smarty.get.action}
		<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.categoryFilter_add_filter_box}</span><span class="right"></span></a>
	{/if}
	{if $smarty.get.action != 'config'}
		<a href="{$rlBase}index.php?controller={$smarty.get.controller}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.categoryFilter_filter_boxes_list}</span><span class="right"></span></a>
	{else}
		<a href="javascript:void(0)" onclick="window.close()" class="button_bar"><span class="left"></span><span class="center_remove">{$lang.cancel}</span><span class="right"></span></a>
	{/if}
</div>
<!-- navigation bar end -->

{if $smarty.get.action == 'add' || $smarty.get.action == 'edit'}

	{assign var='sPost' value=$smarty.post}

	<!-- add new/edit item -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;item={$smarty.get.item}{/if}" method="post" enctype="multipart/form-data">
		<input type="hidden" name="submit" value="1" />
		{if $smarty.get.action == 'edit'}
			<input type="hidden" name="fromPost" value="1" />
		{/if}
		<table class="form">
		<tr>
			<td class="name"><span class="red">*</span>{$lang.categoryFilter_box_name}</td>
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
			<td class="name"><span class="red">*</span>{$lang.categoryFilter_filter_for}</td>
			<td class="field">
				<select name="mode">
					<option value="">{$lang.select}</option>
					<option {if $sPost.mode == 'type'}selected="selected"{/if} value="type">{$lang.categoryFilter_filter_for_type}</option>
					<option {if $sPost.mode == 'category'}selected="selected"{/if} value="category">{$lang.categoryFilter_filter_for_category}</option>
				</select>
				
				<script type="text/javascript">
				{literal}
				
				$(document).ready(function(){
					$('select[name=mode]').change(function(){
						cfMode();	
					});
					cfMode();
				});
				
				var cfMode = function(){
					var val = $('select[name=mode]').val();
					
					if ( !val )
					{
						$('div.filter_mode').slideUp('fast');
					}
					else if ( val == 'type' )
					{
						$('div#for_category').slideUp('fast');
						$('div#for_type').slideDown();
					}
					else if ( val == 'category' )
					{
						$('div#for_type').slideUp('fast');
						$('div#for_category').slideDown();
					}
				}
				
				{/literal}
				</script>
			</td>
		</tr>
		</table>
		
		<div class="hide filter_mode" id="for_type">
			<table class="form">
			<tr>
				<td class="name"><span class="red">*</span>{$lang.listing_type}</td>
				<td class="field">
					<select name="type">
					<option value="">- {$lang.all} -</option>
					{foreach from=$listing_types item='l_type'}
						<option value="{$l_type.Key}" {if $sPost.type == $l_type.Key}selected="selected"{/if}>{$l_type.name}</option>
					{/foreach}
					</select>
				</td>
			</tr>
			</table>
		</div>
		
		<div class="hide filter_mode" id="for_category">
			<table class="form">
			<tr>
				<td class="name"><span class="red">*</span>{$lang.categoryFilter_filter_mode_category}</td>
				<td class="field">
					<fieldset class="light">
						<legend id="legend_cats" class="up" onclick="fieldset_action('cats');">{$lang.categories}</legend>
						<div id="cats">						
						
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
							
						</div>
					</fieldset>
				</td>
			</tr>
			</table>
		</div>
		
		<table class="form">
		<tr>
			<td class="name"><span class="red">*</span>{$lang.block_side}</td>
			<td class="field">
				<select name="side">
					<option value="">{$lang.select}</option>
					{foreach from=$l_block_sides item='block_side' name='sides_f' key='sKey'}
					<option {if $sKey != 'left' && $sKey != 'right'}class="dynamic"{/if} value="{$sKey}" {if $sKey == $sPost.side}selected="selected"{/if}>{$block_side}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="name"><span class="red">*</span>{$lang.use_block_design}</td>
			<td class="field">
				{if $sPost.tpl == '1'}
					{assign var='tpl_yes' value='checked="checked"'}
				{elseif $sPost.tpl == '0'}
					{assign var='tpl_no' value='checked="checked"'}
				{else}
					{assign var='tpl_yes' value='checked="checked"'}
				{/if}
				<label><input {$tpl_yes} type="radio" name="tpl" value="1" /> {$lang.yes}</label>
				<label><input {$tpl_no} type="radio" name="tpl" value="0" /> {$lang.no}</label>
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
			<td class="field">
				<input type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{else}{$lang.add}{/if}" />
			</td>
		</tr>
		</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	<!-- add new block end -->
	
	<!-- select category action -->
	<script type="text/javascript">
	
	{literal}
	function cat_chooser(cat_id){
		return true;
	}
	{/literal}
	
	{if $smarty.post.parent_id}
		cat_chooser('{$smarty.post.parent_id}');
	{elseif $smarty.get.parent_id}
		cat_chooser('{$smarty.get.parent_id}');
	{/if}

	</script>
	<!-- select category action end -->
	
	<script type="text/javascript">
	{if $exp_cats}
		xajax_openTree('{$exp_cats}', 'category_level_checkbox.tpl');
	{/if}
	</script>

{elseif $smarty.get.action == 'build'}

	<div id="filter_fields">{include file='blocks'|cat:$smarty.const.RL_DS|cat:'builder'|cat:$smarty.const.RL_DS|cat:'builder.tpl' no_groups=true}</div>
	<script type="text/javascript">//<![CDATA[
	{literal}
	
	$(document).ready(function(){
		/* change caption */
		$('div#filter_fields legend:first').html("{/literal}{$lang.categoryFilter_filters_form}{literal}");
		
		/* add config icon */
		$('div#filter_fields div.field_obj').each(function(){
			$(this).find('span.b_field_type')
				.addClass('categoryFilterFieldType')
				.after('<a class="categoryFilterFieldConfig" title="{/literal}{$lang.categoryFilter_configure_filter}{literal}"></a>');

			var href = location.href;
			var replace = new RegExp('(\&form$)', 'gi');
			href = href.replace(replace, '');
			var replace = new RegExp('\&action\=([^\&]+)', 'gi');
			href = href.replace(replace, '&action=config');
			var id = $(this).attr('id').split('_')[1];
			href += '&field='+ id;
			$(this).find('a.categoryFilterFieldConfig').attr('href', href);
			
			if ( $(this).closest('div#fields_container').length )
			{
				$(this).find('a.categoryFilterFieldConfig').addClass('suspended');
			}
		});
	});
	
	{/literal}
	//]]>
	</script>
	
{elseif $smarty.get.action == 'config'}

	{assign var='sPost' value=$smarty.post}
	
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	
	<form action="{$rlBaseC}action=config&amp;item={$smarty.get.item}&amp;field={$smarty.get.field}" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="config" />
		
		<table class="form">
		<tr>
			<td class="name">{$lang.categoryFilter_filter_field}</td>
			<td class="field"><b>{$lang[$field_info.pName]} ({$lang[$field_info.Type_pName]})</b></td>
		</tr>
		{if $field_info.Type != 'checkbox'}
		<tr>
			<td class="name"><span class="red">*</span>{$lang.categoryFilter_visible_items_limit}</td>
			<td class="field">
				<input type="text" class="w60" style="text-align: center;" name="items_display_limit" value="{$sPost.items_display_limit}" />
			</td>
		</tr>
		{/if}
		<tr>
			<td class="name"><span class="red">*</span>{$lang.categoryFilter_view_mode}</td>
			<td class="field">
				<select name="mode" {if $field_info.Type != 'number' && $field_info.Type != 'mixed' && $field_info.Type != 'price' && $field_info.Condition != 'years'}disabled="disabled" class="disabled"{/if}>
					{foreach from=$modes item='mode_name' key='mode_key'}
						<option value="{$mode_key}" {if $sPost.mode == $mode_key}selected="selected"{/if}>{$mode_name}</option>
					{/foreach}
				</select>
				{if $field_info.Type != 'number' && $field_info.Type != 'price' && $field_info.Condition != 'years'}
					<span class="field_description">{$lang.categoryFilter_mode_limit_notice}</span>
				{/if}
			</td>
		</tr>
		</table>
		
		{if $field_info.Type != 'text'}
		<div id="items_area">
			<table class="form">
			<tr>
				<td class="name"><span class="red">*</span>{$lang.categoryFilter_filter_items}</td>
				<td class="field">
					{if $sPost.items}
						{if $field_info.Mode == 'group'}
						<div>
							{$min_max_stat}
							<div id="append_items" style="padding-top: 8px;">
						{/if}
						{foreach from=$sPost.items item='item_use' key='item_key'}
							<div {if $allLangs|@count == 1}style="padding: 0 0 5px 0;"{/if}>
								{if $field_info.Mode == 'group'}
								<div class="cf-nav">
									<input type="text" style="width: 35px" name="from[{$item_key}]" value="{$sPost.from[$item_key]}" /><span title="{$lang.categoryFilter_change_sign}" class="{$sPost.sign[$item_key]}"></span><input type="text" style="width: 35px" name="to[{$item_key}]" value="{$sPost.to[$item_key]}" />
									<input type="hidden" name="sign[{$item_key}]" value="{$sPost.sign[$item_key]}" />
									<input type="hidden" name="exist[{$item_key}]" value="1" />
								</div>
								<div class="cf-names">
								{/if}
								
								{if $allLangs|@count > 1}
									<ul class="tabs">
										{foreach from=$allLangs item='language' name='langF'}
										<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
										{/foreach}
									</ul>
								{/if}
								
								{foreach from=$allLangs item='language' name='langF'}
									{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
									<input type="text" name="items[{$item_key}][{$language.Code}]" value="{$item_use[$language.Code]}" maxlength="350" />
									{if $allLangs|@count > 1}
											<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
										</div>
									{/if}
								{/foreach}
								
								{if $field_info.Mode == 'group'}
								</div>
			
								<a href="javascript:void(0);" style="margin-left: 10px;" class="delete_item delete_item">{$lang.remove}</a>
								{/if}
							</div>
						{/foreach}
						{if $field_info.Mode == 'group'}
							</div>
							<div class="add_item"><a onclick="cf_add_item();" href="javascript:void(0)">{$lang.add_item}</a></div>
						</div>
						{/if}
					{else}
						{assign var='replace' value='<a href="javascript:void(0)" id="build_items">$1</a>'}
						<span style="margin-left: 0;" class="field_description">{$lang.categoryFilter_items_auto|regex_replace:'/\[(.*)\]/':$replace}</span>
						
						<div class="hide">
							{$min_max_stat}
							<div id="append_items" style="padding-top: 8px;"></div>
							<div class="add_item"><a onclick="cf_add_item();" href="javascript:void(0)">{$lang.add_item}</a></div>
						</div>
					{/if}
				</td>
			</tr>
			</table>
		</div>
		{/if}
		
		<table class="form">
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
			<td class="field">
				<input type="submit" value="{$lang.edit}" />
				<a onclick="window.close();" href="javascript:void(0)" class="cancel">{$lang.cancel}</a>
			</td>
		</tr>
		</table>
		{if $field_info.Type == 'text'}
		{elseif $field_info.Type == 'radio'}
		{/if}
	</form>
	
	<div class="hide item_source">
		<div {if $allLangs|@count == 1}style="padding: 0 0 5px 0;"{/if}>
			<div class="cf-nav">
				<input type="text" style="width: 35px" name="from[[key]]" value="" /><span title="{$lang.categoryFilter_change_sign}" class="between"></span><input type="text" style="width: 35px" name="to[[key]]" value="" />
				<input type="hidden" name="sign[[key]]" value="between" />
			</div>
			<div class="cf-names">
				{if $allLangs|@count > 1}
					<ul class="tabs">
						{foreach from=$allLangs item='language' name='langF'}
						<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
						{/foreach}
					</ul>
				{/if}
				
				{foreach from=$allLangs item='language' name='langF'}
					{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
					<input type="text" name="items[[key]][{$language.Code}]" maxlength="350" />
					{if $allLangs|@count > 1}
							<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
						</div>
					{/if}
				{/foreach}
			</div>
			
			<a href="javascript:void(0);" style="margin-left: 10px;" class="delete_item delete_row">{$lang.remove}</a>
		</div>
	</div>
	
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	
	<script type="text/javascript">
	var filter_cond = '{$field_info.Condition}';
	var filter_mode = '{$field_info.Mode}';
	var filter_type = '{$field_info.Type}';
	var cf_current_item = {if $sPost.items}{$sPost.items|@count}+1{else}1{/if};
	{literal}

	$(document).ready(function(){
		$('select[name=mode]').change(function(){
			cf_mode();
		});
		cf_mode();
		cf_sign();
		cf_remove();
		cf_delete();
		
		$('a#build_items').click(function(){
			$(this).parent().next().show();
			$(this).parent().remove();
		});
		
		if ( filter_mode == 'group' )
		{
			flynax.copyPhrase = function(){};
		}
		
		$('div.cf-nav span').each(function(){
			if ( $(this).hasClass('greater') )
			{
				$(this).next().addClass('disabled').attr('readonly', true).val('');
				$(this).prev().removeClass('disabled').attr('readonly', false);
			}
			else if ( $(this).hasClass('less') )
			{
				$(this).next().removeClass('disabled').attr('readonly', false);
				$(this).prev().addClass('disabled').attr('readonly', true).val('');
			}
		});
	});
	
	var cf_sign = function(){
		$('div.cf-nav span').unbind('click').click(function(){
			if ( $(this).hasClass('between') )
			{
				$(this).removeClass('between').addClass('greater');
				$(this).next().addClass('disabled').attr('readonly', true)/*.val('')*/;
				$(this).prev().removeClass('disabled').attr('readonly', false);
				$(this).parent().find('input[name^=sign]').val('greater');
			}
			else if ( $(this).hasClass('greater') )
			{
				$(this).removeClass('greater').addClass('less');
				$(this).next().removeClass('disabled').attr('readonly', false);
				$(this).prev().addClass('disabled').attr('readonly', true)/*.val('')*/;
				$(this).parent().find('input[name^=sign]').val('less');
			}
			else if ( $(this).hasClass('less') )
			{
				$(this).removeClass('less').addClass('between');
				$(this).next().removeClass('disabled').attr('readonly', false);
				$(this).prev().removeClass('disabled').attr('readonly', false);
				$(this).parent().find('input[name^=sign]').val('between');
			}
		});
	}
	
	var cf_mode = function(){
		var val = $('select[name=mode]').val();
		if ( val == 'slider' || (val == 'auto' && (filter_type == 'price' || filter_type == 'mixed' || filter_type == 'number' || filter_cond == 'years')) )
		{
			$('#items_area').slideUp('fast');
		}
		else
		{
			$('#items_area').slideDown();
		}
	}	
	
	var cf_add_item = function(){
		var source = $('div.item_source').html();
		$('div#append_items').append(source.replace(/\[key\]/gi, cf_current_item));
		//$('div#append_items').find('img.copy-phrase').remove();
		cf_sign();
		cf_remove();
		cf_current_item++;
		flynax.tabs();
	}
	
	var cf_remove = function(){
		$('a.delete_row').unbind('click').click(function(){
			$(this).parent().remove();
		});
	}
	
	var cf_delete = function(){
		$('a.delete_item').unbind('click').click(function(){
			var item_id = $(this).parent().find('div.cf-nav input[name^=sign]').attr('name');
			var pattern = new RegExp('\\[([^\\]]+)\\]');
			item_id = item_id.match(pattern);
			
			$(this).html(lang['loading']).css('text-decoration', 'none').css('cursor', 'default');
			xajax_removeRow(item_id[1]);
		});
	}
	
	{/literal}
	</script>
	
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
	var categoryFilterGrid;
	
	{literal}
	$(document).ready(function(){
		
		categoryFilterGrid = new gridObj({
			key: 'categoryFilter',
			id: 'grid',
			ajaxUrl: rlPlugins + 'categoryFilter/admin/categoryFilter.inc.php?q=ext',
			defaultSortField: 'ID',
			title: lang['categoryFilter_ext_caption'],
			remoteSortable: false,
			fields: [
				{name: 'Name', mapping: 'Name', type: 'string'},
				{name: 'Mode', mapping: 'Mode', type: 'string'},
				{name: 'Status', mapping: 'Status', type: 'string'},
				{name: 'ID', mapping: 'ID'},
				{name: 'Categories', mapping: 'Categories', type: 'string'},
				{name: 'Side', mapping: 'Side', type: 'string'},
				{name: 'Tpl', mapping: 'Tpl', type: 'string'}
			],
			columns: [
				{
					header: lang['ext_name'],
					dataIndex: 'Name',
					width: 15,
					id: 'rlExt_item_bold'
				},{
					header: lang['categoryFilter_related_categories'],
					dataIndex: 'Categories',
					width: 20,
					id: 'rlExt_item'
				},{
					header: "{/literal}{$lang.categoryFilter_filter_for}{literal}",
					dataIndex: 'Mode',
					width: 120,
					fixed: true
				},{
					header: lang['ext_block_side'],
					dataIndex: 'Side',
					width: 100,
					fixed: true,
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
					width: 100,
					fixed: true,
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
					width: 100,
					fixed: true,
					dataIndex: 'ID',
					sortable: false,
					renderer: function(data) {
						var out = "<center>";
						out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=build&item="+data+"&amp;form'><img class='build' ext:qtip='"+lang['ext_build']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&item="+data+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_'+delete_mod]+"\", \"xajax_deleteBox\", \""+Array(data)+"\" )' />";
						out += "</center>";
						
						return out;
					}
				}
			]
		});
				
		categoryFilterGrid.init();
		grid.push(categoryFilterGrid.grid);
		
	});
	{/literal}
	//]]>
	</script>
	<!-- grid end -->
	
{/if}

<!-- category filter tpl end -->