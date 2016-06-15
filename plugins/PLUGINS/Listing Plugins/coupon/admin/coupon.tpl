<!-- coupon tpl -->

<!-- navigation bar -->
<div id="nav_bar">
	<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.add_item}</span><span class="right"></span></a>
	<a href="{$rlBase}index.php?controller={$smarty.get.controller}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.items_list}</span><span class="right"></span></a>
</div>
<!-- navigation bar end -->		

{if $smarty.get.action == 'edit' || $smarty.get.action == 'add'}

	{assign var='sPost' value=$smarty.post}

	<!-- add new news -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;coupon={$smarty.get.coupon}{/if}" method="post">
	<input type="hidden" name="submit" value="1" />
	{if $smarty.get.action == 'edit'}
		<input type="hidden" name="fromPost" value="1" />
	{/if}
	
	<table class="form">	
	<tr>
		<td class="name"><span class="red">*</span>{$lang.coupon_code}</td>
		<td class="field">
			<input style="width: 150px;" id="generate" value="{$smarty.post.generate_coupon_code}" name="generate_coupon_code" type="text"/>
			<a id="generate_code" href="javascript:void(0);">{$lang.coupon_generate_code}</a>
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span>{$lang.coupon_type}</td>
		<td class="field">
			<select name="type" class="login_input_select lang_add" style="width:150px;">
				<option value="persent" {if $sPost.type == 'persent'}selected{/if}>{$lang.coupon_persent}</option>
				<option value="cost" {if $sPost.type == 'cost'}selected{/if}>{$lang.coupon_cost}</option>
			</select>
		</td>
	</tr>	
	<tr>
		<td class="name"><span class="red">*</span>{$lang.coupon_discount}</td>
		<td class="field">
			<input class="numeric" style="text-align: center; width: 50px;" name="coupon_discount" value="{$smarty.post.coupon_discount}" type="text" size="4" maxlength="4"/>
		</td>
	</tr>
	<tr>
		<td class="name">{$lang.used_date}</td>
		<td class="field">
			{if $sPost.used_date == 'yes'}
				{assign var='used_date_yes' value='checked="checked"'}
				{assign var='date' value='1'}
			{elseif $sPost.used_date == 'no'}
				{assign var='used_date_no' value='checked="checked"'}
				{assign var='date' value='0'}
			{else}
				{assign var='used_date_yes' value='checked="checked"'}
				{assign var='date' value='1'}
			{/if}
			<label><input {$used_date_yes} type="radio" name="used_date" value="yes" > {$lang.yes}</label>
			<label><input {$used_date_no} type="radio" name="used_date" value="no" > {$lang.no}</label>
		</td>
	</tr>
	<tr id="available_coupone" {if $date != 1}class="hide"{/if}>
		<td class="name"><span class="red">*</span>{$lang.available_coupone}</td>
		<td class="field">
			<input style="width: 65px;" value="{$smarty.post.date_from}" name="date_from" type="text" size="12" maxlength="10" id="date_pick_from" />
			<img class="divider" alt="" src="{$rlTplBase}img/blank.gif" />
			<input  style="width: 65px;" value="{$smarty.post.date_to}" name="date_to" type="text" size="12" maxlength="10" id="date_pick_to"/>
		</td>
	</tr>
	<tr>
		<td class="name">{$lang.using_limit}</td>
		<td class="field">
			<input style="width: 50px;" class="numeric" name="using_limit" value="{$smarty.post.using_limit}" type="text" size="4" maxlength="4"/> 
			<span class="field_description">{$lang.coupon_using_limit_tip}</span>
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span>{$lang.coupon_for}</td>
		<td class="field">
			{if $sPost.account_or_type == 'type'}
				{assign var='account_or_type' value='checked="checked"'}
				{assign var='account_show' value='1'}
			{elseif $sPost.account_or_type == 'account'}
				{assign var='account_or_type_no' value='checked="checked"'}
				{assign var='account_show' value='0'}
			{else}
				{assign var='account_or_type' value='checked="checked"'}
				{assign var='account_show' value='1'}
			{/if}
			<label><input {$account_or_type} type="radio" name="account_or_type" value="type" > {$lang.account_type}</label>
			<label><input {$account_or_type_no} type="radio" name="account_or_type" value="account" > {$lang.account}</label>
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span>{$lang.enable_for}</td>
		<td class="field">
			<fieldset id="account_or_type" class="light{if $account_show == '0'} hide{/if}">
				<legend id="legend_accounts_tab_area" class="up" onclick="fieldset_action('accounts_tab_area');">{$lang.account_type}</legend>
				<div id="accounts_tab_area" style="padding: 0 10px 10px 10px;">
					<table>
					<tr>
						<td>
							<table>
							<tr>
							{foreach from=$account_types item='a_type' name='ac_type'}
								<td>
									<div style="margin: 0 20px 0 0;">
										<input {if $a_type.Key|in_array:$sPost.account_type}checked="checked"{/if} style="margin-bottom: 0px;" type="checkbox" id="account_type_{$a_type.ID}" value="{$a_type.Key}" name="account_type[]" /> <label for="account_type_{$a_type.ID}">{$a_type.name}</label>
									</div>
								</td>
								
							{if $smarty.foreach.ac_type.iteration%3 == 0 && !$smarty.foreach.ac_type.last}
							</tr>
							<tr>
							{/if}
							
							{/foreach}
							</tr>
							</table>
						</td>
					</tr>
					</table>

					<div class="grey_area" style="margin: 8px 0 0;">
						<span onclick="$('#accounts_tab_area input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
						<span class="divider"> | </span>
						<span onclick="$('#accounts_tab_area input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
					</div>
				</div>
			</fieldset>
			
			<fieldset id="username" class="light{if $account_show == '1'} hide{/if}">
				<legend id="legend_username_tab_area" class="up" onclick="fieldset_action('username_tab_area');">{$lang.username}</legend>
				<div id="username_tab_area" style="padding: 0 10px 10px 10px;">
					<input type="text" style="width:150px" name="username" value="{$smarty.post.username}" id="Account" />
				</div>
			</fieldset>
			<script type="text/javascript">
			{literal}
	
			$(document).ready(function(){				
				$('input[name=used_date]').change(function(){
					aditionalBlockHandlers();
				});
				
				var aditionalBlockHandlers = function(){
					var value = $('input[name=used_date]:checked').val();
						
					if ( value == 'yes' )
					{					
						$('#available_coupone').show();
					}
					else
					{
						$('#available_coupone').hide();		
					}
					return;
				};
				
				$('input[name=account_or_type]').change(function(){
					aditionalBlockHandler();
				});
				
				var aditionalBlockHandler = function(){
					var value = $('input[name=account_or_type]:checked').val();
						
					if ( value == 'type' )
					{					
						$('#account_or_type').show();
						$('#username').hide();	
					}
					else
					{
						$('#account_or_type').hide();
						$('#username').show();					
					}
					return;
				};
				
				/* autocomplete js */
				$('#Account').rlAutoComplete();
			});
			{/literal}
			</script>
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span>{$lang.plan}</td>
		<td class="field">
			<fieldset class="light">
				<legend id="legend_plans" class="up" onclick="fieldset_action('plan');">{$lang.plans}</legend>
				<div id="plan">
					<div id="plans_box">
						<div id="plan_checkboxed" style="margin: 0px 11px 5px;" {if !empty($sPost.show_on_all)}class="hide"{/if}>
							{foreach from=$plans item='plan'}
								<div style="padding: 3px 0px;">
									<input id="plan_{$plan.ID}"  type="checkbox" name="plan[]" {if $plan.ID|in_array:$sPost.plan}checked="checked"{/if} value="{$plan.ID}"/>									
									<label for="plan_{$plan.ID}">{$plan.name}</label>
								</div>
							{/foreach}
						</div>
						
						<div class="grey_area">
							<label><input class="checkbox" {if $sPost.show_on_all}checked="checked"{/if} type="checkbox" name="show_on_all" value="true" /> {$lang.sticky}</label>
							<span id="plan_nav" {if $sPost.show_on_all}class="hide"{/if}>
								<span onclick="$('#plan_checkboxed div input').attr('checked', true);" class="green_10">{$lang.check_all}</span>
								<span class="divider"> | </span>
								<span onclick="$('#plan_checkboxed div input').attr('checked', false);" class="green_10">{$lang.uncheck_all}</span>
							</span>
						</div>
					</div>
				</div>
			</fieldset>
			<script type="text/javascript">			
			{literal}
			
			$(document).ready(function(){
				$('input[name=show_on_all]').click(function(){
					$('#plan_checkboxed').slideToggle();
					$('#plan_nav').fadeToggle();
				});
			});
			
			{/literal}
			</script>
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span>{$lang.status}</td>
		<td class="field">
			<select name="status" class="login_input_select lang_add" style="width:150px;">
				<option value="active" {if $sPost.status == 'active'}selected{/if}>{$lang.active}</option>
				<option value="approval" {if $sPost.status == 'approval'}selected{/if}>{$lang.approval}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class="field">
			<input class="button lang_add" type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{else}{$lang.add}{/if}" />
		</td>
	</tr>
	</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	

	<script type="text/javascript">
		{literal}
		$(document).ready(function(){
		$('#generate_code').click(function(){
			var rand='';
			for(var i = 0; i < 3; i++)
			{
				rand +=  String.fromCharCode(97 + Math.round(Math.random() * 25));
				rand +=  String.fromCharCode(65 + Math.round(Math.random() * 25));
				rand +=  Math.round(Math.random() * 25);
			}
			$('#generate').val(rand);
			});
		});
		$(function()
            {
				$('#date_pick_from').datepicker({showOn: 'both', buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif', buttonImageOnly: true, dateFormat: 'yy-mm-dd'}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
            });
        $(function()
            {
				$('#date_pick_to').datepicker({showOn: 'both', buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif', buttonImageOnly: true, dateFormat: 'yy-mm-dd'}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
            });
		{/literal}
		

	</script>

	<!-- add new news end -->
{else}

	<div id="gridCouponCodeGrid"></div>
	<script type="text/javascript">//<![CDATA[
	
	var type = [
			[lang['ext_coupon_persent'], 'persent'],
			[lang['ext_coupon_cost'], 'cost']
		];
	{literal}
	
	var CouponCodeGrid;
	$(document).ready(function(){
		
		CouponCodeGrid = new gridObj({
			key: 'coupon',
			id: 'gridCouponCodeGrid',
			ajaxUrl: rlPlugins + 'coupon/admin/coupon.inc.php?q=ext',
			defaultSortField: 'ID',
			title: lang['ext_manager'],
			fields: [
				{name: 'ID', mapping: 'ID'},
				{name: 'Type', mapping: 'Type'},
				{name: 'Discount', mapping: 'Discount'},
				{name: 'Code', mapping: 'Code'},
				{name: 'Status', mapping: 'Status'},
				{name: 'Date_release', mapping: 'Date_release', type: 'date', dateFormat: 'Y-m-d H:i:s'}
			],
			columns: [
				{
					header: lang['ext_coupon_code'],
					dataIndex: 'Code',
					width: 13,
					editor: new Ext.form.TextField({
						allowBlank: false,
						allowDecimals: false
					}),
					renderer: function(val){
						return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
					}
				},{
					header: lang['ext_type'],
					dataIndex: 'Type',
					width: 11,
					editor: new Ext.form.ComboBox({
						store: type,
						displayField: 'value',
						valueField: 'key',
						typeAhead: true,
						mode: 'local',
						triggerAction: 'all',
						selectOnFocus:true
					})
				},{
					header: lang['ext_coupon_discount'],
					dataIndex: 'Discount',
					width: 10,
					editor: new Ext.form.NumberField({
						allowBlank: false,
						allowDecimals: false
					}),
					renderer: function(val){
						return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
					}
				},{
					header: lang['ext_add_date'],
					dataIndex: 'Date_release',
					width: 15,
					renderer:  function(val){
						var date = Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))(val);
						date = '<span class="build">'+date+'</span>';
						return date;
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
						
						
							out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&coupon="+data+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						
							out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onclick='rlConfirm( \""+lang['ext_notice_'+delete_mod]+"\", \"xajax_deleteCoupon\", \""+Array(data)+"\", \"section_load\" )' />";
						
						out += "</center>";
						
						return out;
					}
				}
			]
		});
		
		CouponCodeGrid.init();
		grid.push(CouponCodeGrid.grid);
		
	});
	{/literal}
	//]]>
	</script>
{/if}

<!-- coupon tpl end -->
