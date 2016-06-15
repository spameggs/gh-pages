<!-- listings box -->
<!-- navigation bar -->
<div id="nav_bar">
	
	{if $aRights.$cKey.add && $smarty.get.action != 'add'}
		<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.add}</span><span class="right"></span></a>
	{/if}
	
	<a href="{$rlBase}index.php?controller={$smarty.get.controller}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.items_list}</span><span class="right"></span></a>
</div>
<!-- navigation bar end -->

{if $smarty.get.action == 'add' || $smarty.get.action == 'edit'}
	
{assign var='sPost' value=$smarty.post}

	<!-- add new/edit block -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
		<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;id={$smarty.get.id}{/if}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="submit" value="1" />
			
			{if $smarty.get.action == 'edit'}
				<input type="hidden" name="fromPost" value="1" />
				<input type="hidden" name="id" value="{$sPost.id}" />
			{/if}
			<table class="form">
			<tr>
				<td class="name">
					<span class="red">*</span>{$lang.listings_carousel_direction}
				</td>
				<td>
					<select id="direction" name="direction">
						<option value="vertical" {if 'vertical' == $sPost.direction}selected="selected"{/if}>{$lang.listings_carousel_vertical}</option>
						<option value="horizontal" {if 'vertical' != $sPost.direction}selected="selected"{/if}>{$lang.listings_carousel_horizontal}</option>
					</select>
				</td>
			</tr>
			<tr>
 				<td class="name"><span class="red">*</span>{$lang.listings_carousel_box}</td>
				<td class="field">
					<fieldset class="light">
						<legend id="legend_type" onclick="fieldset_action('type');" class="up">{$lang.blocks_list}</legend>
						<div id="type">
							<table id="list_box">
								<tr>
									{foreach from=$box item='item' name='typeF'}
									<td valign="top">
									<div style="padding: 2px 8px;">
										{assign var='name' value='blocks+name+'|cat:$item.Key}
										<input {if $item.disabled && !$item.ID|in_array:$sPost.box}accesskey="{$item.disabled}" disabled="disabled" checked="checked"{/if} class="checkbox" {if $item.ID|in_array:$sPost.box}checked="checked"{/if} id="type_{$item.ID}" type="checkbox" name="box[{$item.ID}]" value="{$item.ID}" /> <label class="cLabel" for="type_{$item.ID}">{$lang.$name}</label>
									</div>
									</td>
				
									{if $smarty.foreach.typeF.iteration % 3 == 0}
										</tr><tr>
									{/if}
									{/foreach}
								</tr>
							</table>
							<span class="field_description" style="margin: 10px 0 10px 4px;display: inline-block;">{$lang.listings_carousel_disabled_box}</span>
							<div class="grey_area" style="margin: 0 0 5px;">
								<span>
									<span onclick="$('#list_box input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
									<span class="divider"> | </span>
									<span onclick="$('#list_box input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
								</span>
							</div>
						</div>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.listings_carousel_number}</td>
				<td class="field">
					<input type="text" class="numeric" style="width: 60px; text-align: center;" name="number" value="{$sPost.number}" maxlength="2" />
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.listings_carousel_delay}</td>
				<td class="field">
					<input type="text" class="numeric" style="width: 60px; text-align: center;" name="delay" value="{$sPost.delay}" maxlength="2" />
					<span class="field_description">{$lang.listings_carousel_in_sec}</span>
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.listings_carousel_per_slide}</td>
				<td class="field">
					<input type="text" class="numeric" style="width: 60px; text-align: center;" name="per_slide" value="{$sPost.per_slide}" maxlength="2" />
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.listings_carousel_visible}</td>
				<td class="field">
					<input type="text" class="numeric" style="width: 60px; text-align: center;" name="visible" value="{$sPost.visible}" maxlength="2" />
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.listings_carousel_round}</td>
				<td class="field">
					{if $sPost.round == '1'}
						{assign var='round_yes' value='checked="checked"'}
					{elseif $sPost.round == '0'}
						{assign var='round_no' value='checked="checked"'}
					{else}
						{assign var='round_yes' value='checked="checked"'}
					{/if}
					<label><input {$round_yes} class="lang_add" type="radio" name="round" value="1" /> {$lang.yes}</label>
					<label><input {$round_no} class="lang_add" type="radio" name="round" value="0" /> {$lang.no}</label>
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
	<script type="text/javascript">
		var block_id = '{$smarty.get.id}';
		var boxs = new Array();
		{foreach from=$box item='item'}
			boxs["{$item.ID}"] = new Array( '{$item.Side}', '{$item.disabled}', '{$item.Carousel_ID}');
		{/foreach}
		var vertical = new Array('left','right','middle_left','middle_right');		
		var carusel = {if $smarty.get.action == 'edit'}{$smarty.get.id}{else}''{/if};
		var diraction = $('#direction').val();
		{literal}
		
		$(document).ready(function(){
			$('#direction').change(function(){
				diraction = $(this).val();
				
				$('#list_box input').each(function(){
					var id = $(this).val();					
					$(this).removeAttr("disabled");
					if ( diraction == "vertical" )
					{
						if($.inArray(boxs[id][0], vertical) > -1)
						{
							if(boxs[id][1]=='1' && block_id!=boxs[id][2])
							{
								$(this).attr("disabled", "disabled");
							}
						}
						else
						{
							$(this).attr("disabled", "disabled");
						}
					}
					else
					{
						if(boxs[id][1]==1 && boxs[id][2]!=carusel)
						{
							$(this).attr("disabled", "disabled");
						}
					}
				});
			});
			$('#list_box input').each(function(){
				var id = $(this).val();					
				$(this).removeAttr("disabled");
				if ( diraction == "vertical" )
				{
					if($.inArray(boxs[id][0], vertical) > -1)
					{
						if(boxs[id][1]=='1' && block_id!=boxs[id][2])
						{
							$(this).attr("disabled", "disabled");
						}
					}
					else
					{
						$(this).attr("disabled", "disabled");
					}
				}
			});
		});
		
		{/literal}
		</script>

{else}
	<div id="gridListingsCarousel"></div>
	<script type="text/javascript">//<![CDATA[
	var listingsCarousel;

	{literal}
	$(document).ready(function(){
		
		listingsCarousel = new gridObj({
			key: 'listings_carousel',
			id: 'gridListingsCarousel',
			ajaxUrl: rlPlugins + 'listings_carousel/admin/listings_carousel.inc.php?q=ext',
			defaultSortField: 'ID',
			title: lang['ext_manager'],
			fields: [
				{name: 'ID', mapping: 'ID'},
				{name: 'Direction', mapping: 'Direction'},
				{name: 'Delay', mapping: 'Delay'},
				{name: 'Number', mapping: 'Number'},
				{name: 'Per_slide', mapping: 'Per_slide'},
				{name: 'Visible', mapping: 'Visible'},
				{name: 'Assigned_boxes', mapping: 'Assigned_boxes'},
				{name: 'Status', mapping: 'Status'}
			],
			columns: [
				{
					header: lang['ext_id'],
					dataIndex: 'ID',
					width: 40,
					fixed: true
				},{
					header: lang['listings_carousel_ext_assiged_boxes'],
					dataIndex: 'Assigned_boxes',
					width: 20
				},{
					header: lang['listings_carousel_ext_direction'],
					dataIndex: 'Direction',
					width: 120,
					fixed: true
				},{
					header: lang['listings_carousel_ext_number_of_listings'],
					dataIndex: 'Number',
					width: 120,
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
					header: lang['listings_carousel_ext_delay'],
					dataIndex: 'Delay',
					width: 80,
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
					header: lang['listings_carousel_ext_per_slide'],
					dataIndex: 'Per_slide',
					width: 80,
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
					header: lang['listings_carousel_ext_visible'],
					dataIndex: 'Visible',
					width: 80,
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
						
						
							out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&id="+data+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						
							out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onclick='rlConfirm( \""+lang['ext_notice_'+delete_mod]+"\", \"xajax_deleteCarouselBox\", \""+Array(data)+"\", \"section_load\" )' />";
						
						out += "</center>";
						
						return out;
					}
				}
			]
		});
		
		listingsCarousel.init();
		grid.push(listingsCarousel.grid);
		
	});
	{/literal}
	//]]>
	</script>
{/if}
<!-- listings box end -->