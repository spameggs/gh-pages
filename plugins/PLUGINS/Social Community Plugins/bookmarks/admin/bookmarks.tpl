<!-- bookmarks tpl -->

<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/colorpicker/js/colorpicker.js"></script>

<style type="text/css">
  @import url("http://s7.addthis.com/static/r07/widget48.css");
  {literal}
  .at15t_google_plusone
  {
		background: url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}bookmarks/static/gallery.png') 0 0 no-repeat;
  }
  .at15t_facebook_like
  {
		background: url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}bookmarks/static/gallery.png') 0 -16px no-repeat;
  }
  .at15t_tweet
  {
		background: url('{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}bookmarks/static/gallery.png') 0 -32px no-repeat;
  }
  {/literal}
</style> 

<!-- navigation bar -->
<div id="nav_bar">
	<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.bsh_add_block}</span><span class="right"></span></a>
	<a href="{$rlBaseC}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.items_list}</span><span class="right"></span></a>
</div>
<!-- navigation bar end -->

{if $smarty.get.action}

	{assign var='sPost' value=$smarty.post}

	<!-- add new/edit block -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;item={$smarty.get.item}{/if}" method="post" enctype="multipart/form-data">
		<input type="hidden" name="submit" value="1" />
		{if $smarty.get.action == 'edit'}
			<input type="hidden" name="fromPost" value="1" />
		{/if}
		<table class="form">
		<tr>
			<td class="name"><span class="red">*</span>{$lang.key}</td>
			<td class="field">
				<input {if $smarty.get.action == 'edit'}readonly="readonly"{/if} {if $smarty.get.action == 'edit'}class="disabled"{/if} name="key" type="text" value="{$sPost.key}" maxlength="30" />
			</td>
		</tr>
		
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
			<td class="name"><span class="red">*</span>{$lang.bsh_bookmark_type}</td>
			<td class="field">
				<div class="bookmarks">
					{foreach from=$bookmarks item='bookmark'}
						<div style="width: 218px;float: left;margin: 0 5px 5px 0;border: 2px #f2f2f2 solid;border-radius: 4px;" class="item {if $sPost.type && $bookmark.Key != $sPost.type} hide{/if}" id="item_{$bookmark.Key}">
							<label for="input_{$bookmark.Key}" {if is_array($bookmark.Services)}accesskey="{','|implode:$bookmark.Services}"{/if}><img alt="{$lang[$bookmark.Name]}" src="{$smarty.const.RL_PLUGINS_URL}bookmarks/examples/{$bookmark.Key}.jpg" /></label>
							<div class="controller_title" style="font-size: 13px;font-weight: bold;padding: 5px 0 10px;text-align: center;">
								{$lang[$bookmark.Name]}
								<div>
									<input {if $sPost.type == $bookmark.Key}checked="checked" class="hide"{/if} id="input_{$bookmark.Key}" type="radio" name="type" value="{$bookmark.Key}" />
									<div style="padding: 5px 0 0 0;" {if $bookmark.Key != $sPost.type}class="hide"{/if}><a href="javascript:void(0)" class="static bsh_restore">{$lang.bsh_choose_another}</a></div>
								</div>
							</div>
						</div>
					{/foreach}
					<div class="clear"></div>
				</div>
				
				<script type="text/javascript">
				var bsh_bookmarks = new Array();
				{foreach from=$bookmarks item='bookmark'}
				bsh_bookmarks['{$bookmark.Key}'] = new Object({literal}{{/literal}
					align: {if $bookmark.Align}true{else}false{/if},
					services: {if $bookmark.Services}true{else}false{/if},
					color: {if $bookmark.Color}true{else}false{/if}
				{literal}}{/literal});
				{/foreach}
				
				var current_bookmark = '{$sPost.type}';
				
				{literal}
				
				$(document).ready(function(){
				
					$('.bookmarks div.item input').click(function(){
						var self = this;
						$('.bookmarks div.item').fadeOut('fadeOut', function(){
							$(self).hide('');
							$(self).next().show('');
						});
						$(this).parent().parent().parent().fadeIn('fadeOut');
						flynax.slideTo('.bookmarks');
						
						var services = $(this).parent().parent().prev().attr('accesskey');
						var id = $(this).attr('id').split('input_')[1];
						
						bookmarksFieldsHandler(id, services);
					});
					
					$('a.bsh_restore').click(function(){
						$('.bookmarks div.item').fadeIn();
						$(this).parent().hide();
						$(this).parent().prev().show().attr('checked', false);
					});
					
					$('select[name=view_mode]').change(function(){
						viewModeHandler($('input#input_'+current_bookmark).attr('id').split('input_')[1]);
					})
					
					if ( current_bookmark )
					{
						var id = $('input#input_'+current_bookmark).attr('id').split('input_')[1];
						var services = $('input#input_'+current_bookmark).parent().parent().prev().attr('accesskey');
						
						bookmarksFieldsHandler(id, services);
					}
				
				});
				
				var bookmarksFieldsHandler = function(id, services){
					if ( services )
					{
						services = services.split(',');
						$('label.at15t').parent().hide();
						
						for (var i = 0; i < services.length; i++)
						{
							$('label.at15t_'+services[i]).parent().show();
						}
					}
					else
					{
						$('label.at15t').parent().show();
					}
					
					if ( bsh_bookmarks[id].align )
					{
						$('#bsh_align').slideDown();
					}
					else
					{
						$('#bsh_align').slideUp();
					}
					
					if ( bsh_bookmarks[id].services )
					{
						$('#bsh_services').slideDown();
					}
					else
					{
						$('#bsh_services').slideUp();
					}
					
					if ( bsh_bookmarks[id].color )
					{
						$('#bsh_color').slideDown();
					}
					else
					{
						$('#bsh_color').slideUp();
					}
					
					if ( id == 'floating_bar' )
					{
						$('.dynamic').hide();
						$('#bsh_view_mode').show();
					}
					else
					{
						$('.dynamic').show();
						$('#bsh_view_mode').hide();
					}
					
					viewModeHandler(id);
					
					$('label.at15t:hidden input').attr('checked', false);
				}
				
				var viewModeHandler = function(id){
					if ( id == 'floating_bar' )
					{
						if ( $('select[name=view_mode]').val() == 'large' )
						{
							$('div.services > div.counter').show();
							$('div.services > div.button').hide();
							$('div.services > div.button input').attr('checked', false);
						}
						else
						{
							$('div.services > div.counter').hide();
							$('div.services > div.button').show();
							$('div.services > div.counter input').attr('checked', false);
						}
					}
					else
					{
						$('div.services > div.counter').show();
						$('div.services > div.button').show();
					}
				}
				
				{/literal}
				</script>
			</td>
		</tr>
		</table>
		
		<div id="bsh_services" class="hide">
			<table class="form">
			<tr>
				<td class="name">{$lang.bookmarks_services}</td>
				<td class="field">
					<div class="services" style="padding: 5px 0;">
					 	{foreach from=$services item='service' name='services' key='service_key'}
				 			<div class="{if $smarty.foreach.services.iteration <= 4}counter{else}button{/if}"><label class="at15t at15t_{$service_key}" style="float: left;width: 140px;margin: 0 0 5px 0;">
				 				<input {if $service_key|in_array:$sPost.services}checked="checked"{/if} style="margin: 2px 2px 8px 3px;" type="checkbox" value="{$service_key}" name="services[]" /> {$service}
				 			</label></div>
				 			{if $smarty.foreach.services.iteration == 4}
				 			<div class="clear" style="height: 10px;"></div>
				 			{/if}
					 	{/foreach}
					 	<div class="clear"></div>
				 	</div>
				</td>
			</tr>
			</table>
		</div>
		
		<div id="bsh_view_mode" class="hide">
			<table class="form">
			<tr>
				<td class="name"><span class="red">*</span>{$lang.bookmarks_view_mode}</td>
				<td class="field">
					<select name="view_mode">
					 	<option value="0">{$lang.select}</option>
					 	{foreach from=$view_modes item='mode_name' name='modeF' key='mode_key'}
				 			<option {if $sPost.view_mode == $mode_key}selected="selected"{/if} value="{$mode_key}">{$mode_name}</option>
					 	{/foreach}
				 	</select>
				</td>
			</tr>
			</table>
		</div>
		
		<div id="bsh_align" class="hide dynamic">
			<table class="form">
			<tr>
				<td class="name">{$lang.bookmarks_align}</td>
				<td class="field">
					<select name="align">
					 	<option value="0">{$lang.select}</option>
					 	{foreach from=$aligns item='align_name' key='align'}
					 		<option {if $sPost.align == $align}selected="selected"{/if} value="{$align}">{$align_name}</option>
					 	{/foreach}
					</select>
				</td>
			</tr>
			</table>
		</div>
		
		<div id="bsh_color" class="hide">
			<table class="form">
			<tr>
				<td class="name">{$lang.bookmarks_color}</td>
				<td class="field">
					<input type="hidden" name="color" value="{if $sPost.color}{$sPost.color}{else}#89b0cb{/if}" />
					<div id="colorSelector" class="colorSelector"><div style="background-color: #{if $sPost.color}{$sPost.color}{else}89b0cb{/if}"></div></div>
	
					<script type="text/javascript">
					var bsh_color = '{if $sPost.color}{$sPost.color}{else}89b0cb{/if}';
					{literal}
					
					$(document).ready(function(){
						
						$('#colorSelector').ColorPicker({
							color: '#'+bsh_color,
							onShow: function (colpkr) {
								$(colpkr).fadeIn(500);
								return false;
							},
							onHide: function (colpkr) {
								$(colpkr).fadeOut(500);
								return false;
							},
							onChange: function (hsb, hex, rgb) {
								$('#colorSelector div').css('backgroundColor', '#' + hex);
								$('input[name=color]').val(hex);
							}
						});
	
					});
					
					{/literal}
					</script>
				</td>
			</tr>
			</table>
		</div>
		
		<table class="form dynamic">
		<tr>
			<td class="name">{$lang.use_block_design}</td>
			<td class="field">
				{if $sPost.tpl == '1'}
					{assign var='tpl_yes' value='checked="checked"'}
				{elseif $sPost.tpl == '0'}
					{assign var='tpl_no' value='checked="checked"'}
				{else}
					{assign var='tpl_no' value='checked="checked"'}
				{/if}
				<label><input {$tpl_yes} type="radio" name="tpl" value="1" /> {$lang.yes}</label>
				<label><input {$tpl_no} type="radio" name="tpl" value="0" /> {$lang.no}</label>
			</td>
		</tr>
		</table>
		
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
									<input class="checkbox" {if $page.ID|in_array:$sPost.pages}checked="checked"{/if} id="page_{$page.ID}" type="checkbox" name="pages[{$page.ID}]" value="{$page.ID}" /> <label class="cLabel" for="page_{$page.ID}">{$page.name}</label>
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
	function loadLevel(id)
	{
		if ($('#cat_'+id).attr('class').split(' ').indexOf('working') < 0)
		{
			if ($('#cat_'+id).attr('class').split(' ').indexOf('expanded') < 0)
			{
				$('#cat_loading_'+id).fadeIn('notaml');
				$('#cat_'+id).removeClass('cat_item_plus').addClass('cat_item_minus').addClass('working');
				xajax_getCatLevel(id, false, 'category_level_checkbox.tpl');
			}
			else
			{
				if ($('#parent_'+id).css('display') == 'none')
				{
					$('#parent_'+id).slideDown('slow');
					$('#cat_'+id).removeClass('cat_item_plus').addClass('cat_item_minus');
				}
				else
				{
					$('#parent_'+id).slideUp('normal');
					$('#cat_'+id).removeClass('cat_item_minus').addClass('cat_item_plus');
				}
			}
		}
	}
	{/literal}
	
	{if $exp_cats}
		xajax_openTree('{$exp_cats}', 'category_level_checkbox.tpl');
	{/if}
	
	</script>
	
	<!-- additional JS -->
	{if $sPost.type}
	<script type="text/javascript">
	{literal}
	$(document).ready(function(){
			block_banner('btype_{/literal}{$sPost.type}{literal}', '#btypes div');
		}
	);
	
	{/literal}
	</script>
	{/if}
	<!-- additional JS end -->

{else}

	<!-- grid -->
	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	var listingGroupsGrid;
	
	{literal}
	$(document).ready(function(){
		
		bookmarkGrid = new gridObj({
			key: 'bookmarks',
			id: 'grid',
			ajaxUrl: rlPlugins + 'bookmarks/admin/bookmarks.inc.php?q=ext',
			defaultSortField: 'Name',
			title: lang['bookmarks_ext_caption'],
			remoteSortable: false,
			fields: [
				{name: 'Name', mapping: 'Name', type: 'string'},
				{name: 'Key', mapping: 'Key', typr: 'string'},
				{name: 'Status', mapping: 'Status', type: 'string'},
				{name: 'ID', mapping: 'ID'},
				{name: 'Type', mapping: 'Type', type: 'string'},
				{name: 'Align', mapping: 'Align', type: 'string'},
				{name: 'Tpl', mapping: 'Tpl', type: 'string'}
			],
			columns: [
				{
					header: lang['ext_name'],
					dataIndex: 'Name',
					width: 20,
					id: 'rlExt_item_bold'
				},{
					header: lang['ext_type'],
					dataIndex: 'Type',
					width: 20,
					id: 'rlExt_item'
				},{
					header: lang['bsh_ext_align'],
					dataIndex: 'Align',
					width: 10,
					editor: new Ext.form.ComboBox({
						store: [
							['left', lang['bsh_ext_left']],
							['center', lang['bsh_ext_center']],
							['right', lang['bsh_ext_right']]
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
						out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&item="+data+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_'+delete_mod]+"\", \"xajax_deleteBookmark\", \""+Array(data)+"\" )' />";
						out += "</center>";
						
						return out;
					}
				}
			]
		});
				
		bookmarkGrid.init();
		grid.push(bookmarkGrid.grid);
		
	});
	{/literal}
	//]]>
	</script>
	<!-- grid end -->
	
{/if}

<!-- bookmarks tpl end -->