<!-- massmailer/newsletter -->
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}massmailer_newsletter/static/lib.js"></script>
<script type="text/javascript">//<![CDATA[[
massmailer.phrases['send_confirm'] = "{$lang.massmailer_newsletter_send_confirm}";
massmailer.phrases['completed'] = "{$lang.massmailer_newsletter_completed_notice}";
massmailer.config['id'] = {if $smarty.get.massmailer}{$smarty.get.massmailer}{else}false{/if};
//]]>
</script>

<!-- navigation bar -->
<div id="nav_bar">
	{if !$smarty.get.page && $smarty.get.action != 'add'}<a href="{$rlBaseC}page=newsletter" class="button_bar"><span class="left"></span><span class="center_recepeints">{$lang.massmailer_newsletter_recipients}</span><span class="right"></span></a>{/if}
	{if !$smarty.get.page && $smarty.get.action != 'add'}<a href="{$rlBaseC}action=add" class="button_bar"><span class="left"></span><span class="center_add">{$lang.massmailer_newsletter_add_massmailer}</span><span class="right"></span></a>{/if}
	<a href="{$rlBase}index.php?controller={$smarty.get.controller}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.items_list}</span><span class="right"></span></a>	
</div>
<!-- navigation bar end -->

{if $smarty.get.action == 'edit' || $smarty.get.action == 'add'}

	{assign var='sPost' value=$smarty.post}

	<!-- add new massmailer -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
	<form action="{$rlBaseC}action={if $smarty.get.action == 'add'}add{elseif $smarty.get.action == 'edit'}edit&amp;massmailer={$smarty.get.massmailer}{/if}" enctype="multipart/form-data" method="post">
		<input type="hidden" name="submit" value="1" />
		{if $smarty.get.action == 'edit'}
			<input type="hidden" name="fromPost" value="1" />
		{/if}
		<script type="text/javascript">
			var step = 1;
		</script>
		<table class="form">
		<tr>
			<td class="name"><span class="red">*</span>{$lang.key}</td>
			<td class="field">
				<input id="massmailer_key" {if $smarty.get.action == 'edit'}readonly="readonly" class="disabled"{/if} type="text" value="{$smarty.post.massmailer_key}" name="massmailer_key" size="12" maxlength="100" />
			</td>
		</tr>
	
		<tr>
			<td class="name">
				<span class="red">*</span>{$lang.massmailer_newsletter_subject}
			</td>
			<td class="field">
				<input class="w350" type="text" name="subject" value="{$sPost.subject}" />
			</td>
		</tr>
	
		<tr>
			<td class="name">
				<span class="red">*</span>{$lang.massmailer_newsletter_body}
			</td>
			<td class="field">
				<div style="padding: 0 0 5px 0;">
					<select id="var_sel">
						<option value="">{$lang.select}</option>
						{foreach from=$l_email_variables item='var'}
						<option value="{$var}">{$var}</option>
						{/foreach}
					</select>
					<input class="caret_button no_margin" id="input_{$language.Code}" type="button" value="{$lang.add}" style="margin-left: 5px" />
					<span class="field_description_noicon">{$lang.add_template_variable}</span>
				</div>
				
				{fckEditor name='body' width='100%' height='200' value=$sPost.body}
				
				<script type="text/javascript">
				{literal}
				
				$(document).ready(function(){
					$('.caret_button').click(function(){
						var variable = $('#var_sel').val();
						
						if ( variable == '' )
							return;
						
						var instance = CKEDITOR.instances['body'];
						instance.getSelection().getStartElement().appendHtml(variable);
					});
				});
				
				{/literal}
				</script>
			</td>
		</tr>
	
		<tr>
			<td class="name"><span class="red">*</span>{$lang.massmailer_newsletter_from}</td>
			<td class="field">
				<input type="text" name="from_mail" value="{if !empty($sPost.from_mail)}{$sPost.from_mail}{else}{$config.site_main_email}{/if}" maxlength="150" />
			</td>
		</tr>
		
		<tr>
			<td class="name">{$lang.massmailer_newsletter_recipients}</td>
			<td class="field">
				<ul class="clear_list" style="padding: 5px 0 0;">
					<li>
						<label>
							<input type="checkbox" name="newsletters_accounts" {if $sPost.newsletters_accounts}checked="checked"{/if} />
							{$lang.massmailer_newsletter_newsletter}
						</label>
					</li>
					<li>
						<label>
							<input type="checkbox" name="site_accounts" {if $sPost.site_accounts}checked="checked"{/if} />
							{$lang.account_type}
						</label>
					</li>
					{foreach from=$account_types item='type'}
						{assign var='sType' value=$sPost.type}
						{assign var='tKey' value=$type.Key}
						<li style="padding-left: 20px;">
							<label>
								<input class="accounts" type="checkbox" name="type[]" value="{$tKey}" {if $tKey|in_array:$sType}checked="checked"{/if} />
								{$type.name}
							</label>
						</li>
					{/foreach}
					<li>
						<label>
							<input type="checkbox" name="contact_us" {if $sPost.contact_us}checked="checked"{/if} />
							{$lang.massmailer_newsletter_contact_us}
						</label>
					</li>
				</ul>
			</td>
		</tr>
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
			<td>
				<input type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{elseif $smarty.get.action == 'add'}{$lang.add}{/if}" />
				{if $smarty.get.action == 'edit'}
					<input id="confirm" type="button" value="{$lang.massmailer_newsletter_send_and_save}" />
				{/if}
			</td>
		</tr>
		</table>
	</form>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	<!-- add new massmailer end -->
	
	{include file=$smarty.const.RL_PLUGINS|cat:'massmailer_newsletter'|cat:$smarty.const.RL_DS|cat:'admin'|cat:$smarty.const.RL_DS|cat:'send_interface.tpl'}
	
{elseif $smarty.get.action == 'send'}

	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
		<table class="list mn_list">
		<tr>
			<td class="name">{$lang.massmailer_newsletter_from}:</td>
			<td class="value">
				<span class="dark_13">{$massmailer_form.From}</span>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.massmailer_newsletter_to}:</td>
			<td class="value topfix">
				{if $massmailer_form.Recipients_newsletter}
					<div class="dark_13 mn_padding">{$lang.massmailer_newsletter_newsletter} ({$massmailer_form.Recipients_newsletter_count.Count})</div>
				{/if}
				{if !empty($massmailer_form.Recipients_accounts)}
					<div class="dark_13 mn_padding">{$lang.massmailer_newsletter_site_accounts}:
						<ul class="clear_list">
						{foreach from=$massmailer_form.Recipients_accounts item="recipients_accounts"}
							{assign var='newKey' value=$recipients_accounts.Key}
							<li>{$recipients_accounts.name} ({$massmailer_form.Recipients_accounts_count.$newKey})</li>
						{/foreach}
						</ul>
					</div>
				{/if}
				{if $massmailer_form.Recipients_contact_us}
					<div class="dark_13 mn_padding">{$lang.massmailer_newsletter_contact_us} ({$massmailer_form.Recipients_contact_us_count.Count})</div>
				{/if}
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.subject}:</td>
			<td class="value">
				<b>{$massmailer_form.Subject}</b>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.massmailer_newsletter_body}:</td>
			<td class="value topfix">
				<div class="mn_area">{$massmailer_form.Body}</div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="value">
				<input id="start_send" type="button" value="{$lang.massmailer_newsletter_send}" />
				<a style="padding: 0 0 0 10px" href="{$rlBase}index.php?controller={$smarty.get.controller}&amp;action=edit&amp;massmailer={$smarty.get.massmailer}">{$lang.edit}</a>
			</td>
		</tr>
		</table>		
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

	{include file=$smarty.const.RL_PLUGINS|cat:'massmailer_newsletter'|cat:$smarty.const.RL_DS|cat:'admin'|cat:$smarty.const.RL_DS|cat:'send_interface.tpl'}
	
{elseif $smarty.get.page == 'newsletter'}
	<!-- newsletter grid -->
	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	var newsletterGrid;
	
	{literal}
	$(document).ready(function(){
		
		newsletterGrid = new gridObj({
			key: 'newsletter',
			id: 'grid',
			ajaxUrl: rlPlugins + 'massmailer_newsletter/admin/massmailer_newsletter.inc.php?q=ext2',
			defaultSortField: 'Name',
			title: lang['ext_newsletter_manager'],
			fields: [
					{name: 'ID', mapping: 'ID'},
					{name: 'Name', mapping: 'Name'},
					{name: 'Mail', mapping: 'Mail'},
					{name: 'Status', mapping: 'Status'},
					{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d'}
			],
			columns: [
				{
					header: lang['ext_name'],
					dataIndex: 'Name',
					id: 'rlExt_item_bold',
					width: 23
				},{
					header: lang['ext_email'],
					dataIndex: 'Mail',
					width: 250,
					fixed: true
				},{
					header: lang['ext_subscribe_date'],
					dataIndex: 'Date',
					width: 120,
					fixed: true,
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
				},{
					header: lang['ext_status'],
					dataIndex: 'Status',
					width: 90,
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
					width: 60,
					fixed: true,
					dataIndex: 'ID',
					sortable: false,
					renderer: function(data) {
						return "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_delete']+"\", \"xajax_deleteNewsletter\", \""+data+"\", \"news_load\" )' />";
					}
				}
			]
		});
		
		newsletterGrid.init();
		grid.push(newsletterGrid.grid);
	});
	{/literal}
	//]]>
	</script>
	<!-- newsletter grid end -->
{else}
	<!-- massmailer grid -->
	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	var massmailerGrid;
	
	{literal}
	$(document).ready(function(){
		
		massmailerGrid = new gridObj({
			key: 'massmailer',
			id: 'grid',
			ajaxUrl: rlPlugins + 'massmailer_newsletter/admin/massmailer_newsletter.inc.php?q=ext',
			defaultSortField: 'Name',
			title: lang['ext_massmailer_manager'],
			fields: [
				{name: 'ID', mapping: 'ID'},
				{name: 'Name', mapping: 'Subject'},
				{name: 'Description', mapping: 'Body'},
				{name: 'Status', mapping: 'Status'},
				{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d'}
			],
			columns: [
				{
					header: lang['ext_subject'],
					dataIndex: 'Name',
					id: 'rlExt_item_bold',
					width: 30
				},{
					header: lang['ext_add_date'],
					dataIndex: 'Date',
					width: 120,
					fixed: true,
					renderer: function(val){
						return Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))(val);
					},
				},{
					header: lang['ext_status'],
					dataIndex: 'Status',
					width: 90,
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
					width: 80,
					fixed: true,
					dataIndex: 'ID',
					sortable: false,
					renderer: function(data) {
						var out = "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=send&massmailer="+data+"'><img class='send' ext:qtip='"+lang['ext_massmailer_send']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&massmailer="+data+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onclick='rlConfirm( \""+lang['ext_notice_'+delete_mod]+"\", \"xajax_deleteMassmailerNewsletter\", \""+Array(data)+"\", \"section_load\" )' />";
						return out;
					}
				}
			]
		});
		
		massmailerGrid.init();
		grid.push(massmailerGrid.grid);
		
	});
	{/literal}
	//]]>
	</script>
	<!-- massmailer grid end -->
{/if}
<!-- massmailer/newsletter -->
