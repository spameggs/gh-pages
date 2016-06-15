<!-- invoice tpl -->
<!-- navigation bar -->
<div id="nav_bar">
	{if !$smarty.get.action} 
		<a href="{$rlBaseC}&amp;action=add" class="button_bar"><span class="left"></span>
			<span class="center_add">{$lang.invoices_add_item}</span><span class="right"></span>
		</a>
	{/if}
	<a href="{$rlBaseC|replace:'&amp;':''}" class="button_bar"><span class="left"></span>
		<span class="center_list">{$lang.invoices}</span><span class="right"></span>
	</a>
</div>
<!-- navigation bar end -->
{if isset($smarty.get.action)}
	{if $smarty.get.action == 'add' || $smarty.get.action == 'edit'}
		{assign var='sPost' value=$smarty.post}
		<!-- add/edit invoice -->
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
		<form action="{$rlBaseC}&amp;action={if $smarty.get.action == 'add'}add{else}edit&amp;item={$smarty.get.item}{/if}" method="post">
			<input type="hidden" name="submit" value="1" />
			{if $smarty.get.action == 'edit'}
				<input type="hidden" name="fromPost" value="1" />
			{/if}
			<table class="form"> 
			<tr>
				<td class="name"><span class="red">*</span>{$lang.username}</td>
				<td class="field">
					<input name="account" id="username" type="text" style="width: 150px;" value="{$sPost.account}" maxlength="30" />

					<script type="text/javascript">
					{literal}
						$(document).ready(function()
						{
							$('#username').rlAutoComplete();
						});
					{/literal}
					</script>
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.invoice_subject}</td>
				<td>
					<input type="text" name="subject" value="{$sPost.subject}" maxlength="255" />
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.invoice_total}</td>
				<td>
					<input type="text" name="total" value="{$sPost.total}" class="numeric" style="width: 50px; text-align: center;" /> <span class="field_description_noicon">&nbsp;{$config.system_currency}</span>
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.description}</td>
				<td class="field">                        	
					{fckEditor name='description' width='100%' height='140' value=$sPost.description}
				</td>
			</tr>
			<tr>
				<td class="no_divider"></td>
				<td class="field">
					<input type="submit" value="{if $smarty.get.action == 'edit'}{$lang.edit}{else}{$lang.add}{/if}" />
				</td>
			</tr>
			</table>
		</form>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
		<!-- add/edit invoice end -->

	{elseif $smarty.get.action == 'view'}
		{include file=$smarty.const.RL_PLUGINS|cat:'invoices'|cat:$smarty.const.RL_DS|cat:'admin'|cat:$smarty.const.RL_DS|cat:'invoice_details.tpl' invoice_info=$invoice_info}
	{/if}
{else}
	{assign var='plunginPath' value=$smarty.const.RL_PLUGINS|cat:'bankWireTransfer'|cat:$smarty.const.RL_DS}
	{if isset($smarty.get.module)}
		{include file=$plunginPath|cat:'admin'|cat:$smarty.const.RL_DS|cat:$smarty.get.module|cat:'.tpl'}
	{else}
		<!-- ext grid -->
		<div id="grid"></div>
		<script type="text/javascript">//<![CDATA[
		var invoicesGrid;
		{literal}
		$(document).ready(function(){
			invoicesGrid = new gridObj({
				key: 'invoices',
				id: 'grid',
				ajaxUrl: rlPlugins + 'invoices/admin/invoices.inc.php?q=ext',
				defaultSortField: 'Date',
				remoteSortable: true,
				checkbox: true,
				actions: [
					[lang['ext_delete'], 'delete']
				],
				title: lang['ext_invoices_manager'],
				fields: [
					{name: 'Txn_ID', mapping: 'Txn_ID'},
					{name: 'Username', mapping: 'Username', type: 'string'},
					{name: 'Account_ID', mapping: 'Account_ID', type: 'string'},
					{name: 'First_name', mapping: 'First_name', type: 'string'},
					{name: 'Last_name', mapping: 'Last_name', type: 'string'},
					{name: 'Subject', mapping: 'Subject', type: 'string'},
					{name: 'Total', mapping: 'Total'},
					{name: 'pStatus', mapping: 'pStatus'},
					{name: 'ID', mapping: 'ID', type: 'int'},
					{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
					{name: 'Pay_date', mapping: 'Pay_date', type: 'date', dateFormat: 'Y-m-d H:i:s'}
				],
				columns: [
					{
						header: lang['ext_id'],
						dataIndex: 'ID',
						width: 35,
						fixed: true,
						id: 'rlExt_black_bold'
					},{
						header: lang['ext_username'],
						dataIndex: 'Username',
						width: 120,
						fixed: true,
						renderer: function(username, obj, row){
							if ( username )
							{
								//var full_name = trim(row.data.Full_name) ? ' ('+trim(row.data.Full_name)+')' : '';
								var out = '<a class="green_11_bg" href="'+rlUrlHome+'index.php?controller=accounts&action=view&username='+row.data.Account_ID+'" ext:qtip="'+lang['ext_click_to_view_details']+'">'+username+'</a>';
							}
							else
							{
								var out = '<span class="delete">{/literal}{$lang.account_removed}{literal}</span>';
							}
							return out;
						}
					},{
						header: lang['ext_subject'],
						dataIndex: 'Subject',
						width: 20
					},{
						header: "{/literal}{$lang.invoice_txn_id}{literal}",
						dataIndex: 'Txn_ID',
						width: 100,
						fixed: true
					},{
						header: lang['ext_total']+' ('+rlCurrency+')',
						dataIndex: 'Total',
						width: 5
					},{
						header: lang['ext_date'],
						dataIndex: 'Date',
						width: 80,
						fixed: true,
						renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
					},{
						header: lang['ext_status'],
						dataIndex: 'pStatus',
						width: 80,
						fixed: true,
						renderer: function (val, obj, row) {
							if (val == lang['ext_paid'])
							{                
								obj.style += 'background: #D2E798;';                              
								return '<span>' + val + '</span>';  
							}
							else if (val == lang['ext_unpaid'])
							{
								obj.style += 'background: #FF878A;';
								return '<span>' + val + '</span>'; 
							}
						}
					},{
						header: lang['ext_actions'],
						width: 80,
						fixed: true,
						dataIndex: 'ID',
						sortable: false,
						renderer: function(data) {
							var out = "<center>";
							var splitter = false;
							
							out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=view&item="+data+"'><img class='view' ext:qtip='"+lang['ext_view']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
							out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_'+delete_mod]+"\", \"xajax_deleteItem\", \""+data+"\", \"load\" )' />";
							 
							out += "</center>";

							return out;
						}
					}
				]
			});
			invoicesGrid.init();
			grid.push(invoicesGrid.grid);
			// actions listener
			invoicesGrid.actionButton.addListener('click', function()
			{
				var sel_obj = invoicesGrid.checkboxColumn.getSelections();
				var action = invoicesGrid.actionsDropDown.getValue();
				if ( !action )
				{
					return false;
				}
				for( var i = 0; i < sel_obj.length; i++ )
				{
					invoicesGrid.ids += sel_obj[i].id;
					if ( sel_obj.length != i+1 )
					{
						invoicesGrid.ids += '|';
					}
				}
				if ( action == 'delete' )
				{
					Ext.MessageBox.confirm('Confirm', lang['ext_notice_'+delete_mod], function(btn){
						if ( btn == 'yes' )
						{
							xajax_deleteItem( invoicesGrid.ids );
						}
					});
				}
			});
			
		});
		{/literal}
		//]]>
		</script>
		<!-- ext grid end -->
	{/if}
{/if}
<!-- invoice tpl -->