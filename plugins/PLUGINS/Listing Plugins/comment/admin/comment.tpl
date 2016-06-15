<!-- comments tok -->
<!-- navigation bar -->
<div id="nav_bar">
	<a href="javascript:void(0)" onclick="show('search')" class="button_bar"><span class="left"></span><span class="center_search">{$lang.search}</span><span class="right"></span></a>
	<a href="{$rlBase}index.php?controller={$smarty.get.controller}" class="button_bar"><span class="left"></span><span class="center_list">{$lang.items_list}</span><span class="right"></span></a>
</div>
<!-- navigation bar end -->
{if $smarty.get.action == 'edit'}
	{assign var='sPost' value=$smarty.post}
	<!-- edit comment -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
		<form action="{$rlBaseC}action=edit&amp;id={$smarty.get.id}" method="post">
			<input type="hidden" name="submit" value="1" />
			{if $smarty.get.action == 'edit'}
				<input type="hidden" name="fromPost" value="1" />
			{/if}
			<table class="form">
			<tr>
				<td class="name"><span class="red">*</span>{$lang.comment_author}</td>
				<td class="field"><b>{$sPost.author}</b></td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.title}</td>
				<td class="field">
					<input value="{$smarty.post.title}" class="w350" name="title" type="text" size="100" />
				</td>
			</tr>
			<tr>
				<td class="name"><span class="red">*</span>{$lang.description}</td>
				<td class="field">
					{*fckEditor name='description' width='100%' height='200' value=$smarty.post.description*}
					<textarea name="description" rows="10" cols="" style="height: 150px;">{$smarty.post.description}</textarea>
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
	<!-- edit comment -->
{else}
	<!-- search -->
	<div id="search" class="hide">
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.search}
			<table class="form">
			<tr>
				<td class="name w130">{$lang.listing_type}</td>
				<td class="field">
					<select id="listing_type" style="width: 200px;">
						<option value="">- {$lang.all} -</option>
						{foreach from=$listing_types item='listing_type'}
							<option value="{$listing_type.Key}" {if $listing_type.Key == $smarty.get.listing_type}selected="selected"{/if}>{$listing_type.name}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td class="name w130">{$lang.date}</td>
				<td class="field">
					<input style="width: 65px;" type="text" value="{$smarty.post.date_from}" size="12" maxlength="10" id="date_from" />
					<img class="divider" alt="" src="{$rlTplBase}img/blank.gif" />
					<input style="width: 65px;" type="text" value="{$smarty.post.date_to}" size="12" maxlength="10" id="date_to"/>
				</td>
			</tr>
			<tr>
				<td class="name w130">{$lang.status}</td>
				<td class="field">
					<select id="search_status" style="width: 200px;">
						<option value="">- {$lang.all} -</option>
						{foreach from=$statuses item='user_status'}
							<option value="{$user_status}" {if $user_status == $smarty.get.status}selected="selected"{/if}>{$lang.$user_status}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="field">
					<input id="search_button" type="submit" value="{$lang.search}" />
					<input type="button" value="{$lang.reset}" id="reset_filter_button" />
					
					<a class="cancel" href="javascript:void(0)" onclick="show('search')">{$lang.cancel}</a>
				</td>
			</tr>
			</table>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
	</div>
	<script type="text/javascript">
	{literal}
	var sFields = new Array('listing_type', 'search_status', 'date_from', 'date_to');
	var cookie_filters = new Array();
	$(document).ready(function(){
		$(function(){
			$('#date_from').datepicker({showOn: 'both', buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif', buttonText: '{/literal}{$lang.dp_choose_date}{literal}', buttonImageOnly: true, dateFormat: 'yy-mm-dd'}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
			$('#date_to').datepicker({showOn: 'both', buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif', buttonText: '{/literal}{$lang.dp_choose_date}{literal}', buttonImageOnly: true, dateFormat: 'yy-mm-dd'}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
	    });
	    if ( readCookie('comments_sc') && !cookies_filters )
		{
			$('#search').show();
			cookie_filters = readCookie('comments_sc').split(',');
			
			for (var i in cookie_filters)
			{
				if ( typeof(cookie_filters[i]) == 'string' )
				{
					var item = cookie_filters[i].split('||');
					$('#'+item[0]).selectOptions(item[1]);
				}
			}
			cookie_filters.push(new Array('search', 1));
		}
	    $('#search_button').click(function(){    	
	    	var sValues = new Array();
	    	var filters = new Array();
	    	var save_cookies = new Array();	
	    	for(var si = 0; si < sFields.length; si++)
	    	{
	    		sValues[si] = $('#'+sFields[si]).val();
	    		filters[si] = new Array(sFields[si], $('#'+sFields[si]).val());
	    		save_cookies[si] = sFields[si]+'||'+$('#'+sFields[si]).val();
	    	}
	    	// save search criteria
			createCookie('comments_sc', save_cookies, 1);
			filters.push(new Array('search', 1));
	    	commentsGrid.filters = filters;
	    	commentsGrid.reload();
	    });
	    $('#reset_filter_button').click(function(){
			eraseCookie('comments_sc');
			commentsGrid.reset();
			
			$("#search select option[value='']").attr('selected', true);
			$("#search input[type=text]").val('');
		});
	});
	{/literal}
	var cookies_filters = new Array();
	{if $smarty.get.status}
		cookies_filters[0] = new Array('search_status', '{$smarty.get.status}');
		cookies_filters.push(new Array('search', 1));
		$('#search_status option[value={$smarty.get.status}]').attr('selected', true);
		$('#search').show();
	{/if}
	</script>
	<!-- search end -->
	<!-- grid -->
	<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	var commentsGrid;
	{literal}
	$(document).ready(function(){
		commentsGrid = new gridObj({
			key: 'listingGroups',
			id: 'grid',
			ajaxUrl: rlPlugins +'comment/admin/comment.inc.php?q=ext',
			defaultSortField: 'Date',
			defaultSortType: 'DESC',
			title: lang['ext_manager'],
			remoteSortable: false,
			filters: cookies_filters,
			fields: [
				{name: 'ID', mapping: 'ID', type: 'int'},
				{name: 'Title', mapping: 'Title', type: 'string'},
				{name: 'Author', mapping: 'Author', type: 'string'},
				{name: 'User_IP', mapping: 'User_IP', type: 'string'},             /*New data definition for User-IP field*/ 
				{name: 'Listing_type', mapping: 'Listing_type', type: 'string'},
				{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
				{name: 'Status', mapping: 'Status'}
			],
			columns: [
				{
					header: lang['ext_title'],
					dataIndex: 'Title',
					width: 60,
					id: 'rlExt_item'
				},{
					header: '{/literal}{$lang.comment_author}{literal}',
					dataIndex: 'Author',
					width: 12
				},{
                    header: lang['ext_userip'],        /*Table header for User-IP field*/
                    dataIndex: 'User_IP',              /*User_IP Data Field*/
                    width: 13,
                },{
					header: lang['ext_type'],
					dataIndex: 'Listing_type',
					width: 8
				},{
					header: lang['ext_date'],
					dataIndex: 'Date',
					fixed: true,
					width: 90,
					renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
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
					renderer: function(id) {
						var out = "<center>";
						out += "<a href='"+rlUrlHome+"index.php?controller="+controller+"&action=edit&id="+id+"'><img class='edit' ext:qtip='"+lang['ext_edit']+"' src='"+rlUrlHome+"img/blank.gif' /></a>";
						out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['ext_notice_delete']+"\", \"xajax_deleteComment\", \""+id+"\" )' />";
						out += "</center>";
						
						return out;
					}
				}
			]
		});
		commentsGrid.init();
		grid.push(commentsGrid.grid);
	});
	{/literal}
	//]]>
	</script>
	<!-- grid end -->
{/if}
<!-- listing fields groups tpl end -->