<!-- banner boxes -->

{if $smarty.get.action}

{assign var='sPost' value=$smarty.post}

<!-- add/edit banner box -->
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}module=banner_boxes&amp;action={if $smarty.get.action == 'add'}add{else}edit&amp;box={$smarty.get.box}{/if}" method="post">
		<input type="hidden" name="submit" value="1" />
		{if $smarty.get.action == 'edit'}
			<input type="hidden" name="fromPost" value="1" />
		{/if}

		<table class="form">
		{if $smarty.get.action == 'add' && $allowSelectBoxType}
		<tr>
			<td class="name">{$lang.banners_boxType}</td>
			<td class="field">
				{if $sPost.box_type == 'btc'}
					{assign var='box_type_btc' value='checked="checked"'}
				{elseif $sPost.box_type == 'def'}
					{assign var='box_type_def' value='checked="checked"'}
				{else}
					{assign var='box_type_def' value='checked="checked"'}
				{/if}
				<label><input {$box_type_def} class="lang_add" type="radio" name="box_type" value="def" /> {$lang.banners_boxType_default}</label>
				<label><input {$box_type_btc} class="lang_add" type="radio" name="box_type" value="btc" /> {$lang.banners_boxType_betweenCategories}</label>
			</td>
		</tr>
		{else}
			<input type="hidden" name="box_type" value="{if $sPost.box_type}{$sPost.box_type}{else}def{/if}" />
		{/if}

		<tr>
			<td class="name">
				<span class="red">*</span>{$lang.name}
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
					{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
					<input type="text" name="name[{$language.Code}]" value="{$sPost.name[$language.Code]}" maxlength="350" />
					{if $allLangs|@count > 1}
							<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
						</div>
					{/if}
				{/foreach}
			</td>
		</tr>

		<tr class="box-type-def {if $sPost.box_type && $sPost.box_type == 'btc'}hide{/if}">
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

		<tr class="box-type-btc {if !$sPost.box_type || $sPost.box_type == 'def'}hide{/if}">
			<td class="name"><span class="red">*</span>{$lang.banners_betweenCategories_field}</td>
			<td class="field">
				<fieldset class="light">
					<legend id="legend_parent_cats" class="up" onclick="fieldset_action('parent_cats');">{$lang.categories}</legend>
					<div id="parent_cats">
						<div id="cat_checkboxed" style="margin: 0 0 8px;">
							<div class="tree">
								<table width="100%"><tr><td>
								{foreach from=$sections item='section'}
									<fieldset class="light">
										<legend id="legend_parent_section_{$section.ID}" class="up" onclick="fieldset_action('parent_section_{$section.ID}');">{$section.name}</legend>
										<div id="parent_section_{$section.ID}">
											{if !empty($section.Categories)}
												<ul>
												{foreach from=$section.Categories item='s_cat'}
													<li>
														<label><input {if $sPost.box_after_category == $s_cat.Key}checked="checked"{/if} class="lang_add" type="radio" name="box_after_category" value="{$s_cat.Key}" /> {$s_cat.name}</label>
													</li>
												{/foreach}
												</ul>
											{else}
												<div style="padding: 0 0 8px 10px;">{$lang.no_items_in_sections}</div>
											{/if}
										</div>
									</fieldset>
								{/foreach}
								</td></tr></table>
							</div>
						</div>
					</div>
				</fieldset>
			</td>
		</tr>

		<tr class="box-type-def {if $sPost.box_type && $sPost.box_type == 'btc'}hide{/if}">
			<td class="name">{$lang.show_on_pages}</td>
			<td class="field" id="pages_obj">
				<fieldset class="light">
					{assign var='pages_phrase' value='admin_controllers+name+pages'}
					<legend id="legend_pages" class="up">{$lang.$pages_phrase}</legend>
					<div id="pages">
						<div id="pages_cont" {if !empty($sPost.show_on_all)}style="display: none;"{/if}>
							{assign var='bPages' value=$sPost.pages}
							<table class="sTable" style="margin-bottom: 15px;">
							<tr>
								<td valign="top">
								{foreach from=$pages item='page' name='pagesF'}
								{assign var='pId' value=$page.ID}
								<div style="padding: 2px 8px;">
									<input class="checkbox" {if isset($bPages.$pId)}checked="checked"{/if} id="page_{$page.ID}" type="checkbox" name="pages[{$page.ID}]" value="{$page.ID}" /> <label class="cLabel" for="page_{$page.ID}">{$page.name}</label>
								</div>
								{assign var='perCol' value=$smarty.foreach.pagesF.total/3|ceil}

								{if $smarty.foreach.pagesF.iteration % $perCol == 0}
									</td>
									<td valign="top">
								{/if}
								{/foreach}
								</td>
							</tr>
							</table>
						</div>

						<div class="grey_area" style="margin: 0 0 5px;">
							<label><input id="show_on_all" {if $sPost.show_on_all}checked="checked"{/if} type="checkbox" name="show_on_all" value="true" /> {$lang.sticky}</label>
							<span id="pages_nav" {if $sPost.show_on_all}class="hide"{/if}>
								<span onclick="$('#pages_cont input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
								<span class="divider"> | </span>
								<span onclick="$('#pages_cont input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
							</span>
						</div>
					</div>
				</fieldset>

				<script type="text/javascript">
				{literal}
				$(document).ready(function() {
					$('#legend_pages').click(function() {
						fieldset_action('pages');
					});
					$('input#show_on_all').click(function() {
						$('#pages_cont').slideToggle();
						$('#pages_nav').fadeToggle();
					});
				});
				{/literal}
				</script>
			</td>
		</tr>

		<tr class="box-type-def {if $sPost.box_type && $sPost.box_type == 'btc'}hide{/if}">
			<td class="name">{$lang.show_in_categories}</td>
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

							<div style="padding: 0 0 6px 37px;">
								<label><input {if !empty($sPost.subcategories)}checked="checked"{/if} type="checkbox" name="subcategories" value="1" /> {$lang.include_subcats}</label>
							</div>
						</div>

						<script type="text/javascript">
						var tree_selected = {if $smarty.post.categories}[{foreach from=$smarty.post.categories item='post_cat' name='postcatF'}['{$post_cat}']{if !$smarty.foreach.postcatF.last},{/if}{/foreach}]{else}false{/if};
						var tree_parentPoints = {if $parentPoints}[{foreach from=$parentPoints item='parent_point' name='parentF'}['{$parent_point}']{if !$smarty.foreach.parentF.last},{/if}{/foreach}]{else}false{/if};
						{literal}
						$(document).ready(function() {
							flynax.treeLoadLevel('checkbox', 'flynax.openTree(tree_selected, tree_parentPoints)', 'div#cat_checkboxed');
							flynax.openTree(tree_selected, tree_parentPoints);

							$('input[name=cat_sticky]').click(function() {
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

		<tr>
			<td class="name">{$lang.banners_bannerSettings}</td>
			<td class="field">
				<fieldset class="light">
					<legend id="legend_banners_settings" class="up" onclick="fieldset_action('banners_settings');">{$lang.banners_bannerSettings}</legend>
					<div id="banners_settings">
						<table class="form wide">
						<tr class="box-type-def {if $sPost.box_type && $sPost.box_type == 'btc'}hide{/if}">
							<td class="name"><span class="red">*</span>{$lang.banners_boxSettingsLimit}</td>
							<td class="field">
								<input style="width: 30px;" type="text" class="numeric" name="banners_limit" value="{if $sPost.banners_limit}{$sPost.banners_limit}{else}1{/if}" />
								<span class="field_description">{$lang.banners_boxSettingsLimitDesc}</span>
							</td>
						</tr>
						<tr>
							<td class="name"><span class="red">*</span>{$lang.banners_boxSettingsWidth}</td>
							<td class="field">
								<input style="width: 30px;" type="text" class="numeric box-type-def {if $sPost.box_type && $sPost.box_type == 'btc'}hide{/if}" name="banners_width" value="{$sPost.banners_width}" />
								<input style="width: 30px;" type="text" class="numeric box-type-btc {if !$sPost.box_type || $sPost.box_type == 'def'}hide{/if}" name="btc_banners_width" value="{$sPost.btc_banners_width}" />
								<span class="field_description_noicon">px</span>
							</td>
						</tr>
						<tr>
							<td class="name"><span class="red">*</span>{$lang.banners_boxSettingsHeight}</td>
							<td class="field">
								<input style="width: 30px;" type="text" class="numeric box-type-def {if $sPost.box_type && $sPost.box_type == 'btc'}hide{/if}" name="banners_height" value="{$sPost.banners_height}" />
								<input style="width: 30px;" type="text" class="numeric box-type-btc {if !$sPost.box_type || $sPost.box_type == 'def'}hide{/if}" name="btc_banners_height" value="{$sPost.btc_banners_height}" />
								<span class="field_description_noicon">px</span>
							</td>
						</tr>
						</table>
					</div>
				</fieldset>
			</td>
		</tr>

		<tr class="box-type-def {if $sPost.box_type && $sPost.box_type == 'btc'}hide{/if}">
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
			<td class="field">
				<input type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{else}{$lang.add}{/if}" />
			</td>
		</tr>
		</table>
	</form>
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
<!-- add/edit banner box end -->

<!-- select category action -->
<script type="text/javascript">
{literal}
	function cat_chooser(cat_id) { return true; }

	$(document).ready(function() {
		$('input[name=box_type]').click(function() {
			if ( $(this).val() == 'def' ) {
				$('.box-type-btc').fadeOut('fast', function() {
					$('.box-type-def').fadeIn('fast');
				});
			}
			else {
				$('.box-type-def').fadeOut('fast', function() {
					$('.box-type-btc').fadeIn('fast');
				});
			}
		});
	});
{/literal}

{if $smarty.post.parent_id}
	cat_chooser('{$smarty.post.parent_id}');
{elseif $smarty.get.parent_id}
	cat_chooser('{$smarty.get.parent_id}');
{/if}
</script>
<!-- select category action end -->

<!-- additional JS -->
{if $sPost.type}
<script type="text/javascript">
{literal}
$(document).ready(function() {
	block_banner('btype_{/literal}{$sPost.type}{literal}', '#btypes div');
});

{/literal}
</script>
{/if}
<!-- additional JS end -->

{else}

<!-- delete banner box block -->
<div id="delete_block" class="hide">
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.banners_deleteBannersBox}
		<div id="delete_container">
			{$lang.detecting}
		</div>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

	<script type="text/javascript">//<![CDATA[
	var delete_confirm_phrase = "{$lang.banners_noticeDeleteEmptyBannerBox}";
	{literal}

	function delete_chooser(key, name) {
		rlPrompt(delete_confirm_phrase.replace('{box}', name), 'xajax_deleteBox', key);
	}
	{/literal}
	//]]>
	</script>
</div>
<!-- delete banner box block end -->

<div id="grid"></div>
<script type="text/javascript">//<![CDATA[
var bannerBoxes;

{literal}
$(document).ready(function(){

	bannerBoxes = new gridObj({
		key: 'bannerBoxes',
		id: 'grid',
		ajaxUrl: rlUrlHome + 'controllers/blocks.inc.php?controller=banners&q=ext',
		defaultSortField: false,
		title: lang['banners_bannerBoxesTitleOfManager'],
		fields: [
			{name: 'ID', mapping: 'ID'},
			{name: 'Key', mapping: 'Key'},
			{name: 'name', mapping: 'name'},
			{name: 'banners_size', mapping: 'banners_size'},
			{name: 'banners_limit', mapping: 'banners_limit'},
			{name: 'Side', mapping: 'Side'},
			{name: 'Tpl', mapping: 'Tpl'},
			{name: 'Position', mapping: 'Position'},
			{name: 'Status', mapping: 'Status'}
		],
		columns: [
			{
				header: lang['ext_name'],
				dataIndex: 'name',
				id: 'rlExt_item_bold',
				width: 40
			},{
				header: lang['banners_boxLimit'],
				dataIndex: 'banners_limit',
				width: 10
			},{
				header: lang['banners_boxSize'],
				dataIndex: 'banners_size',
				width: 10
			},{
				header: lang['ext_block_side'],
				dataIndex: 'Side',
				width: 10,
				renderer: function(val){
					return val !== null ? val : '{/literal}{$lang.banners_boxBetweenCategories_side}{literal}';
				}
			},{
				header: lang['ext_block_style'],
				dataIndex: 'Tpl',
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
				renderer: function(val) {
					return '<span ext:qtip="'+ lang['ext_click_to_edit']+'">'+ val +'</span>';
				}
			},{
				header: lang['ext_position'],
				dataIndex: 'Position',
				width: 10,
				editor: new Ext.form.NumberField({
					allowBlank: false,
					allowDecimals: false
				}),
				renderer: function(val, row, entry){
					if ( entry.data.Side === null ) {
						return '{/literal}{$lang.banners_boxBetweenCategories_side}{literal}';
					}
					return '<span ext:qtip="'+ lang['ext_click_to_edit'] +'">'+ val +'</span>';
				}
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
				renderer: function(val) {
					return '<div ext:qtip="'+ lang['ext_click_to_edit'] +'">'+ val +'</div>';
				}
			},{
				header: lang['ext_actions'],
				width: 70,
				fixed: true,
				dataIndex: 'Key',
				sortable: false,
				renderer: function(data) {
					var out = "<center>";
					var splitter = false;

					out += "<a href='"+ rlUrlHome +"index.php?controller="+ controller +"&module=banner_boxes&action=edit&box="+ data +"'><img class='edit' ext:qtip='"+ lang['ext_edit'] +"' src='"+ rlUrlHome +"img/blank.gif' /></a>";
					out += "<img class='remove' ext:qtip='"+ lang['ext_delete'] +"' src='"+ rlUrlHome +"img/blank.gif' onclick='rlConfirm( \""+ lang['ext_notice_delete'] +"\", \"xajax_prepareDeleting\", \""+ Array(data) +"\", \"section_load\" )' />";
					out += "</center>";

					return out;
				}
			}
		]
	});

	bannerBoxes.init();
	grid.push(bannerBoxes.grid);

	// subscribe to event
	bannerBoxes.grid.addListener('beforeedit', function(editEvent) {
		if ( editEvent.record.data.Side === null && editEvent.field != 'Status' ) {
			bannerBoxes.store.rejectChanges();
			Ext.MessageBox.alert(lang['ext_notice'], '{/literal}{$lang.banners_categoryBoxNoticeInGrid}{literal}');
		}
	});
});
{/literal}
//]]>
</script>

{/if}

<!-- banner boxes tpl end -->