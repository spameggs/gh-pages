<!-- filed bound boxes tpl -->
<!-- navigation bar -->
<div id="nav_bar">
	{if $aRights.$cKey.add}
		{if !$smarty.get.action && !$smarty.get.box}
			<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.fb_add}</span><span class="right"></span></a>
		{elseif $smarty.get.action == 'edit'}
			<a href="{$rlBaseC}box={$smarty.get.box}" class="button_bar"><span class="left"></span><span class="center_build">{$lang.manage}</span><span class="right"></span></a>
		{/if}
	{/if}
	{if $smarty.get.box && $smarty.get.key}
		<a href="{$rlBase}index.php?controller={$smarty.get.controller}&amp;box={$smarty.get.box}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.fb_items_list}</span><span class="right"></span></a>
	{else}
		<a href="{$rlBase}index.php?controller={$smarty.get.controller}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.fb_boxes_list}</span><span class="right"></span></a>
	{/if}
	{if $smarty.get.box && !$smarty.get.key}
		<a href="javascript:void(0)" class="button_bar" onclick="xajax_rebuildItems('{$smarty.get.box}')"><span class="left"></span><span class="center_build">{$lang.fb_rebuild_box_items}</span><span class="right"></span></a>
	{/if}
</div>
<!-- navigation bar end -->
{if $smarty.get.action == 'edit_item'}
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	{assign var='sPost' value=$smarty.post}
	<form action="{$rlBaseC}box={$smarty.get.box}&amp;action=edit_item&amp;item={$smarty.get.item}" method="post" enctype="multipart/form-data">
		<input type="hidden" name="fromPost" value="1" />
		<table class="form">
		{if $box_info.Icons}
		<tr>
			<td class="name">{$lang.fb_item_icon}</td>
			<td class="field">
				<div id="category_icon_upload">
					<table class="sTable">
						<tr>
							<td>
								<input class="file" type="file" name="icon"/>
							</td>
						</tr>
					</table>
				</div>
				{if !empty($sPost.icon)}                                                    
					<div id="gallery">                                                         
						<div style="margin: 1px 0 4px 0;">
							<fieldset style="margin-top:10px">
								<legend id="legend_details" class="up" onclick="fieldset_action('details');">{$lang.fb_current_icon}</legend>
								<div id="fileupload" class="ui-widget">
									<span class="item active template-download" style="width: {math equation="x + y" x=$box_info.Icons_width y=4}px; height: {math equation="x + y" x=$box_info.Icons_height y=4}px;">   
										<img src="{$smarty.const.RL_FILES_URL}{$sPost.icon}" class="thumbnail" />   
										<img title="Delete" alt="Delete" class="delete" src="{$rlTplBase}/img/blank.gif" onclick="xajax_deleteIcon('{$item_id}');" />   
									</span>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="loading" id="photos_loading" style="width: 100%;"></div>
				{/if}
			</td>
		</tr>
		{/if}
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
{elseif $smarty.get.action}
	{assign var='sPost' value=$smarty.post}
	<!-- add -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;box={$smarty.get.box}{/if}" method="post">
		<input type="hidden" name="submit" value="1" />
		{if $smarty.get.action == 'edit'}
			<input type="hidden" name="fromPost" value="1" />
		{/if}
		<table class="form">
		<tr>
			<td class="name"><span class="red">*</span>{$lang.key}</td>
			<td class="field">
				<input {if $smarty.get.action == 'edit'}readonly="readonly"{/if} class="{if $smarty.get.action == 'edit'}disabled{/if}" name="key" type="text" style="width: 150px;" value="{$sPost.key}" maxlength="30" />
			</td>
		</tr>

		<tr>
			<td class="name"><span class="red">*</span>{$lang.fb_block_name}</td>
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
						<input type="text" name="name[{$language.Code}]" value="{$sPost.name[$language.Code]}" style="width: 250px;" maxlength="50" />
					{if $allLangs|@count > 1}
						<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
					</div>
					{/if}
				{/foreach}
			</td>
		</tr>
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
		<table class="form">
		<tr>
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
				$(document).ready(function(){
					$('#legend_pages').click(function(){
						fieldset_action('pages');
					});
					$('input#show_on_all').click(function(){
						$('#pages_cont').slideToggle();
						$('#pages_nav').fadeToggle();
					});
					$('#pages input').click(function(){
						if ( $('#pages input:checked').length > 0 )
						{
							//$('#show_on_all').prop('checked', false);
						}
					});
					$('input[name=icons]').click(function(){
						iconsClickHandler();
					});
					iconsClickHandler();
					$('input[name=postfix]').click(function(){
						postfixClickHandler();
					});
					postfixClickHandler();
					$('select[name=field]').click(function(){
						fieldsSelectorHandler();
					});
					fieldsSelectorHandler();
				});
				var postfixClickHandler = function(){
					var enabled = parseInt( $('input[name=postfix]:checked').val() ) ? 1 : 0;
					
					if ( enabled != 0 )
					{
						$('#path_postfix').html('.html');
					}
					else
					{
						$('#path_postfix').html('/');
					}
					return;
				};
				var iconsClickHandler = function(){
					var enabled = parseInt( $('input[name=icons]:checked').val() ) ? 1 : 0;
					
					if ( enabled != 0 )
					{
						$('#icons_options').slideDown();
					}
					else
					{
						$('#icons_options').slideUp();						
					}
					return;
				};
				var fieldsSelectorHandler = function(){
					var field_key = $('select[name=field] option:selected').html();

					if( $('select[name=field]').val() != '0' && $('select[name=field]').val() != '' && field_key != '' )
					{ 
						str = str2path(field_key);
						$('input[name=path]').val(str);
					}
					return;
				};
				var str2path = function( str ){
					str = str.replace(/[^a-z0-9]+/ig, '-');
					str = str.toLowerCase();
	
					return str ? str : '';
				};
			{/literal}
			</script>
			</td>
		</tr>
		<tr>
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
		<!-- directly field-bound options -->
		<tr>
			<td class="name">{$lang.fb_settings}</td>
			<td class="field">
			<fieldset class="light">
				<legend id="legend_fb_settings" class="up" onclick="fieldset_action('fb_settings');">{$lang.settings}</legend>
				<div id="fb_settings">				
					<table class="form wide">
						<tr>
							<td class="name"><span class="red">*</span>{$lang.fb_field}</td>
							<td class="field">
								<select name="field" {if $smarty.get.action == 'edit'}disabled="disabled" class="disabled"{/if}>
									<option value="0">{$lang.select}</option>
									{foreach from=$fields item="field"}
										<option {if $sPost.field == $field.Key}selected="selected"{/if} value="{$field.Key}">{$field.name}</option>
									{/foreach}
								</select>
							</td>
						</tr>					
						<tr>
							<td class="name">{$lang.listing_type}</td>
							<td class="field">
								<select name="listing_type">
									<option value="">- {$lang.all} -</option>
										{foreach from=$listing_types item='l_type'}
											<option {if $sPost.listing_type == $l_type.Key}selected="selected"{/if} value="{$l_type.Key}">{$l_type.name}</option>
										{/foreach}
								</select>
							</td>
						</tr>	
						<tr>
							<td class="name">{$lang.fb_box_path}</td>
							<td class="field">
								<table>
								<tr>
									<td><span style="padding: 0 5px 0 0;" class="field_description_noicon">{$smarty.const.RL_URL_HOME}</span></td>
									<td><input type="text" name="path" value="{$sPost.path}" /></td>
									<td><span class="field_description_noicon">/{$lang.fb_option_key}</span><span class="field_description_noicon" style="padding:0" id="path_postfix">{if $sPost.postfix}.html{else}/{/if}</span></td>
									<td><span class="field_description_noicon" id="cat_postfix_el">{if $sPost.type}{if $listing_types[$sPost.type].Cat_postfix}.html{else}/{/if}{/if}</span>{if $smarty.get.action == 'add'}<span class="field_description"> - {$lang.fb_regenerate_path_desc}</span>{/if}</td>
								</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="name">{$lang.fb_show_empty}</td>
							<td class="field">
								{if $sPost.show_empty == '1'}
									{assign var='empty_yes' value='checked="checked"'}
								{elseif $sPost.show_empty == '0'}
									{assign var='empty_no' value='checked="checked"'}
								{else}
									{assign var='empty_yes' value='checked="checked"'}
								{/if}
								<label><input {$empty_yes} class="lang_add" type="radio" name="show_empty" value="1" /> {$lang.yes}</label>
								<label><input {$empty_no} class="lang_add" type="radio" name="show_empty" value="0" /> {$lang.no}</label>
							</td>
						</tr>
						<tr>
							<td class="name">{$lang.fb_listings_count}</td>
							<td class="field">
								{if $sPost.show_count == '1'}
									{assign var='count_yes' value='checked="checked"'}
								{elseif $sPost.show_count == '0'}
									{assign var='count_no' value='checked="checked"'}
								{else}
									{assign var='count_yes' value='checked="checked"'}
								{/if}
								<label><input {$count_yes} class="lang_add" type="radio" name="show_count" value="1" /> {$lang.yes}</label>
								<label><input {$count_no} class="lang_add" type="radio" name="show_count" value="0" /> {$lang.no}</label>
							</td>
						</tr>
						<tr>
							<td class="name">{$lang.fb_html_postfix}</td>
							<td class="field">
								{if $sPost.postfix == '1'}
									{assign var='postfix_yes' value='checked="checked"'}
								{elseif $sPost.postfix == '0'}
									{assign var='postfix_no' value='checked="checked"'}
								{else}
									{assign var='postfix_yes' value='checked="checked"'}
								{/if}
								<label><input {$postfix_yes} class="lang_add" type="radio" name="postfix" value="1" /> {$lang.yes}</label>
								<label><input {$postfix_no} class="lang_add" type="radio" name="postfix" value="0" /> {$lang.no}</label>
							</td>
						</tr>
						<tr>
							<td class="name"><span class="red">*</span>{$lang.fb_cols}</td>
							<td class="field">
								<input type="text" class="numeric" name="columns" style="width:30px" value="{if $sPost.columns}{$sPost.columns}{else}3{/if}"/>
							</td>
						</tr>
						<tr>
							<td class="name"><span class="red">*</span>{$lang.fb_page_cols}</td>
							<td class="field">
								<input type="text" class="numeric" name="page_columns" style="width:30px" value="{if $sPost.page_columns}{$sPost.page_columns}{else}3{/if}"/>
							</td>
						</tr>
						<tr>
							<td class="name">{$lang.fb_enable_icons}</td>
							<td class="field">
								{if $sPost.icons == '1'}
									{assign var='icons_yes' value='checked="checked"'}
								{elseif $sPost.icons == '0'}
									{assign var='icons_no' value='checked="checked"'}
								{else}
									{assign var='icons_yes' value='checked="checked"'}
								{/if}
								<label><input {$icons_yes} class="lang_add" type="radio" name="icons" value="1" /> {$lang.yes}</label>
								<label><input {$icons_no} class="lang_add" type="radio" name="icons" value="0" /> {$lang.no}</label>
							</td>
						</tr>
					</table>
					<div id="icons_options">
					<table class="form wide">
						<tr>
							<td class="name"><span class="red">*</span>{$lang.fb_icons_position}</td>
							<td class="field">
								<select name="icons_position">
									{foreach from=","|explode:"left,right,top,bottom" item="side"}
										<option {if $sPost.icons_position}{if $sPost.icons_position == $side}selected="selected"{/if}{elseif $side == 'top'}selected="selected"{/if} value="{$side}">{$side}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr>
							<td class="name">{$lang.width}</td>
							<td class="field">
								<input type="text" class="numeric" name="icons_width" style="width:30px" value="{if $sPost.icons_width}{$sPost.icons_width}{else}70{/if}"/>
								<span class="field_description">{$lang.fb_icons_sizes_hint}</span>
							</td>
						</tr>
						<tr>
							<td class="name">{$lang.height}</td>
							<td class="field">
							<input type="text" class="numeric" name="icons_height" style="width:30px" value="{if $sPost.icons_height}{$sPost.icons_height}{else}70{/if}"/>
							</td>
						</tr>
					</table>
					</div>
				</div>
				</fieldset>
			</td>
		</tr>
		</table>
<!-- directly field-bound options end -->
		<table class="form">
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
	<!-- add end -->
{else}
	{literal}
	<script type="text/javascript">
	var addItem = function(){
	{/literal}
		var names = new Array();

		{foreach from=$allLangs item='languages'}
		names['{$languages.Code}'] = $('#ni_{$languages.Code}').val();
		{/foreach}

		xajax_addItem($('#ni_key').val(), names, $('#ni_status').val(), '{$smarty.get.box}', $('#ni_default:checked').val());
	{literal}
	}
	</script>
	{/literal}
	{literal}
	<script type="text/javascript">
	var editItem = function(key){
	{/literal}
		var names = new Array();
		{foreach from=$allLangs item='languages'}
		names['{$languages.Code}'] = $('#ei_{$languages.Code}').val();
		{/foreach}
		xajax_editItem(key, names, $('#ei_status').val(), '{$smarty.get.box}', $('#ei_default:checked').val());
	{literal}
	}
	</script>
	{/literal}
	<!-- add new item end -->
	{if !$smarty.get.box}
	<script type="text/javascript">
		// blocks sides list
		var block_sides = [
			{foreach from=$l_block_sides item='block_side' name='sides_f' key='sKey'}
				['{$block_side}', '{$sKey}']{if !$smarty.foreach.sides_f.last},{/if}
			{/foreach}
		];
	</script>
	{/if}
	<!-- field-bound boxes grid -->
	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	{if $smarty.get.box}
		var itemsGrid;
		var box = '{$smarty.get.box}';
		{literal}
		$(document).ready(function(){			
			itemsGrid = new gridObj({
				key: 'data_items',
				id: 'grid',
				ajaxUrl: rlPlugins + 'fieldBoundBoxes/admin/field_bound_boxes.inc.php?q=ext&box='+box,
				defaultSortField: 'name',
				remoteSortable: true,
				title: lang['ext_field_bound_items_manager'],
				fields: [
					{name: 'name', mapping: 'name', type: 'string'},
					{name: 'Position', mapping: 'Position', type: 'int'},
					{name: 'Status', mapping: 'Status'},
					{name: 'Key', mapping: 'Key'},
					{name: 'Icons', mapping: 'Icons'}
				],
				columns: [
					{
						header: lang['ext_name'],
						dataIndex: 'name',
						width: 40,
						id: 'rlExt_item_bold'
					},{
						header: lang['ext_position'],
						dataIndex: 'Position',
						width: 70,
						fixed: true,
						editor: new Ext.form.NumberField({
							allowBlank: false,
							allowDecimals: false
						}),
						renderer: function(val){
							return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
						}
					},
					{
						header: lang['ext_status'],
						dataIndex: 'Status',
						width: 80,
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
						dataIndex: 'Key',
						sortable: false,
						renderer: function(val, obj, row ) {
							
							var out = "<center>";					
							/*out += "<img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' onClick='xajax_prepareEdit(\""+data+"\", \""+box+"\");$(\"#edit_item\").slideDown(\"normal\");$(\"#new_item\").slideUp(\"fast\");$(\"#ei_loading\").fadeIn(\"fast\")' />";*/

							out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&amp;box={/literal}{$smarty.get.box}{literal}&action=edit_item&item="+val+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";

							out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_delete']+"\", \"xajax_deleteFieldBoundItem\", \""+Array(val,box)+"\" )' />";
							out += "</center>";
							
							return out;
						}
					}
				]
			});
			itemsGrid.init();
			grid.push(itemsGrid.grid);
		});
		{/literal}
	{else}
		{literal}
		var fieldBoundBoxesGrid;
		$(document).ready(function(){
			fieldBoundBoxesGrid = new gridObj({
				key: 'field_bound_boxes',
				id: 'grid',
				ajaxUrl: rlPlugins + 'fieldBoundBoxes/admin/field_bound_boxes.inc.php?q=ext',
				defaultSortField: 'name',
				title: lang['ext_field_bound_manager'],
				fields: [
					{name: 'name', mapping: 'name', type: 'string'},
					{name: 'Field_name', mapping: 'Field_name'},
					{name: 'Position', mapping: 'Position', type: 'int'},
					{name: 'Status', mapping: 'Status'},
					{name: 'Tpl', mapping: 'Tpl'},
					{name: 'Side', mapping: 'Side'},
					{name: 'Key', mapping: 'Key'}
				],
				columns: [{
						header: lang['ext_name'],
						dataIndex: 'name',
						id: 'rlExt_item_bold',
						width: 40
					},{
						header: '{/literal}{$lang.fb_field}{literal}',
						dataIndex: 'Field_name',
						id: 'rlExt_item_bold',
						width: 40
					},{
						header: lang['ext_block_side'],
						dataIndex: 'Side',
						width: 10,
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
						renderer: function(val){
							return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
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
						renderer: function(val){
							return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
						}
					},{
						header: lang['ext_actions'],
						width: 90,
						fixed: true,
						dataIndex: 'Key',
						sortable: false,
						renderer: function(data) {
							var manage_link = data == 'years' ? "eval(Ext.MessageBox.alert(\""+lang['ext_notice']+"\", \""+lang['ext_data_format_auto']+"\"))" : '';
							var manage_href = data == 'years' ? "javascript:void(0)" : rlUrlHome+"index.php?controller="+controller+"&amp;box="+data;
							var out = "<center>";
							var splitter = false;
							
							out += "<a href="+manage_href+" onclick='"+manage_link+"'><img class='manage' ext:qtip='"+lang['ext_manage']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
							out += "<a href="+rlUrlHome+"index.php?controller="+controller+"&action=edit&amp;box="+data+"><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
							out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_'+delete_mod]+"\", \"xajax_deleteBox\", \""+Array(data)+"\", \"section_load\" )' />";
							out += "</center>";
							
							return out;
						}
					}
				]
			});
			fieldBoundBoxesGrid.init();
			grid.push(fieldBoundBoxesGrid.grid);
		});
		{/literal}
	{/if}
	//]]>
	</script>
{/if}
<!-- filed bound boxes tpl end -->