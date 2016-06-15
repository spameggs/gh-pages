<!-- listings status -->
<style type="text/css">
		{literal}
			.status_box{
				position: relative;
				float: left;
			}
			.status		
			{
				border: 2px solid #D0D0D0;
				-moz-border-radius: 3px;
				-webkit-border-radius: 3px;
				border-radius: 3px;
			}
		{/literal}
	</style>
<!-- navigation bar -->
<div id="nav_bar">
	
	{if $aRights.$cKey.add && $smarty.get.action != 'add'}
		<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.ls_add_new_status}</span><span class="right"></span></a>
	{/if}
	
	<a href="{$rlBase}index.php?controller={$smarty.get.controller}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.ls_block_list}</span><span class="right"></span></a>
</div>
<!-- navigation bar end -->

{if $smarty.get.action == 'add' || $smarty.get.action == 'edit'}
	
{assign var='sPost' value=$smarty.post}
	
	<!-- add new/edit block -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
		<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;block={$smarty.get.block}{/if}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="submit" value="1" />
			
			{if $smarty.get.action == 'edit'}
				<input type="hidden" name="fromPost" value="1" />
				<input type="hidden" name="id" value="{$sPost.id}" />
			{/if}
			<table class="form">
			<tr>
				<td class="name"><span class="red">*</span>{$lang.key}</td>
				<td class="field">
					<input {if $sPost.key && $smarty.get.action == 'edit'}readonly="readonly"{/if} type="text" class="text" name="key" value="{$sPost.key}"  style="width: 139px;" />
				</td>
			</tr>
			<tr>
				<td class="name">
					<span class="red">*</span>{$lang.name}
				</td>
				<td class="field" style="padding:15px 0">
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
						{/if}

						<div style="margin: 5px 0 0;" id="watermark_{$language.Code}">
							{include file=$smarty.const.RL_PLUGINS|cat:'listing_status'|cat:$smarty.const.RL_DS|cat:'admin'|cat:$smarty.const.RL_DS|cat:'watermark.tpl' watermark=$sPost.watermark[$language.Code] code=$language.Code large='0'}
						</div>
						<div class="clear"></div>
						<div style="margin: 5px 0 0;" id="watermarkLarge_{$language.Code}">
							{include file=$smarty.const.RL_PLUGINS|cat:'listing_status'|cat:$smarty.const.RL_DS|cat:'admin'|cat:$smarty.const.RL_DS|cat:'watermark.tpl' watermark=$sPost.watermarkLarge[$language.Code] code=$language.Code large='1'}
						</div>
						<div class="clear"></div>
						{if $allLangs|@count > 1}
							</div>
						{/if}
					{/foreach}
				</td>
			</tr>
			
			<tr>
				<td class="name"><span class="red">*</span>{$lang.listing_type}</td>
				<td class="field">
					<fieldset class="light">
						<legend id="fieldset_type" onclick="fieldset_action('type');" class="up">{$lang.listing_type}</legend>
						<div id="type">
							<table id="list_rt">
								<tr>
									<td valign="top">
									{foreach from=$listing_types item='listing_type' name='typeF'}
									<div style="padding: 2px 8px;">
										<input class="checkbox" {if $listing_type.Type|in_array:$sPost.type}checked="checked"{/if} id="type_{$listing_type.Type}" type="checkbox" name="type[{$listing_type.Type}]" value="{$listing_type.Type}" /> <label class="cLabel" for="type_{$listing_type.Type}">{$listing_type.name}</label>
									</div>
									{assign var='perCol' value=$smarty.foreach.typeF.total/3|ceil}
				
									{if $smarty.foreach.typeF.iteration % $perCol == 0}
										</td>
										<td valign="top">
									{/if}
									{/foreach}
									</td>
								</tr>
							</table>
							<div class="grey_area" style="margin: 0 0 5px;">
								<span>
									<span onclick="$('#list_rt input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
									<span class="divider"> | </span>
									<span onclick="$('#list_rt input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
								</span>
							</div>
						</div>
					</fieldset>
				</td>
			</tr>
			
			<tr>
				<td class="name">{$lang.ls_delete_listings}</td>
				<td class="field">
					<select name="delete">
						<option value="0" {if empty($sPost.delete)}selected="selected"{/if}>{$lang.ls_unchanged}</option>						
						<option value="delete" {if $sPost.delete == 'delete'}selected="selected"{/if}>{$lang.ls_removal}</option>
						<option value="disabled" {if $sPost.delete == 'disabled'}selected="selected"{/if}>{$lang.ls_suspension}</option>
					</select>				
				</td>
			</tr>
			</table>

			<div id="show_days">
				<table class="form">
				<tr>
					<td class="name"><span class="red">*</span>{$lang.days}</td>
					<td class="field">
						<input type="text" class="numeric" name="days" value="{$sPost.days}" maxlength="10" style="width: 139px;" />
						<span class="field_description">{$lang.ls_delete_days_hint}</span>
					</td>
				</tr>
				</table>
			</div>

			<table class="form">			
			<tr>
				<td class="name">{$lang.ls_use_block}</td>
				<td class="field">
					{if $sPost.used_block == '1'}
						{assign var='use_block_yes' value='checked="checked"'}
						{assign var='use_block' value='1'}
					{elseif $sPost.used_block == '0'}
						{assign var='use_block_no' value='checked="checked"'}
						{assign var='use_block' value='0'}
					{else}
						{assign var='use_block_yes' value='checked="checked"'}
						{assign var='use_block' value='1'}
					{/if}
					<label><input {$use_block_yes} class="lang_add" type="radio" name="used_block" value="1" /> {$lang.yes}</label>
					<label><input {$use_block_no} class="lang_add" type="radio" name="used_block" value="0" /> {$lang.no}</label>
					<span class="field_description">{$lang.ls_use_block_info}</span>					
				</td>
			</tr>			
			</table>

			<div id="aditional_block" {if $use_block == 0}class="hide"{/if}>
			
			<table class="form" >		
				<tr>
					<td class="name">
						<span class="red">*</span>{$lang.ls_block_settings}
					</td>
					<td>
						<fieldset class="light">
						<legend id="legend_settings" class="up" onclick="fieldset_action('settings');">Settings</legend>
							<div id="settings" >		
								<table class="form" >		
									<tr>
										<td class="name">
											<span class="red">*</span>{$lang.ls_block_name}
										</td>
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
												<input type="text" name="block_name[{$language.Code}]" value="{$sPost.block_name[$language.Code]}" maxlength="350" />
												
												{if $allLangs|@count > 1}
														<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
													</div>
												{/if}
											{/foreach}
										</td>
									</tr>
									<tr>
										<td class="name"><span class="red">*</span>{$lang.listing_number}</td>
										<td class="field">
											<input type="text" class="numeric" name="count" value="{if $sPost.count}{$sPost.count}{else}3{/if}" maxlength="2" style="width: 139px;" />
										</td>
									</tr>
									<tr>
										<td class="name"><span class="red">*</span>{$lang.order_type}</td>
										<td class="field">
											<select name="order">
												<option value="latest" {if 'latest' == $sPost.order}selected="selected"{/if}>{$lang.ls_latest}</option>
												<option value="random" {if 'random' == $sPost.order}selected="selected"{/if}>{$lang.ls_random}</option>
											</select>
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
											});
											
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
										<td class="name"><span class="red">*</span>{$lang.ls_status_block}</td>
										<td class="field">
											<select name="block_status">
												<option value="active" {if $sPost.block_status == 'active'}selected="selected"{/if}>{$lang.active}</option>
												<option value="approval" {if $sPost.block_status == 'approval'}selected="selected"{/if}>{$lang.approval}</option>
											</select>
										</td>
									</tr>
								</table>
							</div>
						</fieldset>
					</td>
				</tr>
			</table>
			</div>
			<table class="form" >
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
	{literal}
		$(document).ready(function(){
			$('select[name=delete]').change(function(){
				delMethodHandler();
			});
			delMethodHandler();

			$('input[name=used_block]').change(function(){
				aditionalBlockHandler();
			});
		});
			
		var delMethodHandler = function(){
			var visible = $('select[name=delete] option:selected').val() != '0' ? 1 : 0;

			if ( visible != 0 )
			{
				$('#show_days').slideDown();
			}
			else
			{
				$('#show_days').slideUp();						
			}
			return;
		};
		var aditionalBlockHandler = function(){
			var enabled = parseInt( $('input[name=used_block]:checked').val() ) ? 1 : 0;

			if ( enabled != 0 )
			{
				$('#aditional_block').slideDown();
			}
			else
			{
				$('#aditional_block').slideUp();						
			}
			return;
		};
	{/literal}
	</script>

	<!-- additional JS -->
	{if $sPost.type}
	<script type="text/javascript">
	{literal}
	$(document).ready(function(){
		block_banner('btype_{/literal}{$sPost.type}{literal}', '#btypes div');
	});
	
	{/literal}
	</script>
	{/if}
	<!-- additional JS end -->

{else}
	
	<div id="gridListingStatus"></div>
	<script type="text/javascript">//<![CDATA[
	var ListingStatus;

	{literal}
	$(document).ready(function(){
		
		ListingStatus = new gridObj({
			key: 'listing_status',
			id: 'gridListingStatus',
			ajaxUrl: rlPlugins + 'listing_status/admin/listing_status.inc.php?q=ext',
			defaultSortField: 'ID',
			title: lang['ext_manager'],
			fields: [
				{name: 'ID', mapping: 'ID'},
				{name: 'name', mapping: 'name'},
				{name: 'Days', mapping: 'Days'},
				{name: 'Count', mapping: 'Count'},
				{name: 'Status', mapping: 'Status'}
			],
			columns: [
				{
					header: lang['ext_id'],
					dataIndex: 'ID',
					width: 35,
					fixed: true
				},{
					header: lang['ext_name'],
					dataIndex: 'name',
					width: 100
				},{
					header: lang['ext_days'],
					dataIndex: 'Days',
					width: 100,
					fixed: true,
					editor: new Ext.form.NumberField({
						allowBlank: false,
						maxValue: 365,
						minValue: 0
					}),
					renderer: function(val){
						return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
					}
				},{
					header: lang['ls_ext_number'],
					dataIndex: 'Count',
					width: 130,
					fixed: true,
					editor: new Ext.form.NumberField({
						allowBlank: false,
						maxValue: 30,
						minValue: 1
					}),
					renderer: function(val){
						return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
					}
				},{
					header: lang['ext_status'],
					dataIndex: 'Status',
					width: 100,
					fixed: true,
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
					width: 70,
					fixed: true,
					dataIndex: 'ID',
					sortable: false,
					renderer: function(data) {
						var out = "<center>";
						var splitter = false;
						
						
							out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&block="+data+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						
							out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onclick='rlConfirm( \""+lang['ext_notice_'+delete_mod]+"\", \"xajax_deleteStatusBlock\", \""+Array(data)+"\", \"section_load\" )' />";
						
						out += "</center>";
						
						return out;
					}
				}
			]
		});
		
		ListingStatus.init();
		grid.push(ListingStatus.grid);
		
	});
	{/literal}
	//]]>
	</script>
{/if}
<!-- listings status end -->
