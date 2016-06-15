<!-- saved search tpl -->

<div class="highlight">
	{if !empty($contact)}	
		{if empty($messages)}
		
			<div class="info">{$lang.no_messages}</div>
			
		{else}
			<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.textareaCounter.js"></script>
			
			<!-- messages area -->
			<table class="list">
			<tr class="header">
				<td style="width: 50px;">{$lang.user}</td>
				<td class="divider"></td>
				<td class="last">
					<table class="sTable">
					<tr>
						<td class="lalign">{$lang.message}</td>
						<td class="ralign"><input style="margin-top: 0;" type="checkbox" id="check_all" name="check_all" /></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			<div id="messages_area">
				{include file='blocks'|cat:$smarty.const.RL_DS|cat:'messages_area.tpl'}
			</div>
			<div class="mass_actions_light">
				<a href="javascript:void(0)" class="remove_msg" title="{$lang.remove_selected_messages}">{$lang.remove_selected}</a>
			</div>
			<!-- messages area end -->
			
			<div style="padding: 20px 0 0">
				<textarea rows="4" cols="" id="message_text"></textarea>
				<div class="button">
					<input onclick="xajax_sendMessage('{$contact.ID}', $('#message_text').val(), {if $contact.Admin}1{else}0{/if});" type="button" value="{$lang.send}" />
				</div>
			</div>
			
			<script type="text/javascript">	
			var period = {$config.messages_refresh};
	
			{literal}
			
			function refresh()
			{
				setTimeout( "refresh()", period*1000 );
				
				var ids = '';
				$('.del_mess').each(function(){
					if ( $(this).is(':checked') )
					{
						ids += $(this).attr('id').split('_')[1]+',';
					}
				});
				ids = ids.substring(0, ids.length-1);
				
				xajax_refreshMessagesArea('{/literal}{$contact.ID}{literal}', ids);
			}
			
			var checkboxControl = function(){
				$('input.del_mess').unbind('click').click(function(){
					if ( $('input.del_mess:checked').length == 0 )
						$('#check_all').attr('checked', false);
				});
			};
			
			refresh();
			checkboxControl();
			
			$(document).ready(function(){
				$('#check_all').click(function(){
					var $checkbox = $('input.del_mess');
					$checkbox.attr('checked', $(this).is(':checked'));
				});
				
				$('#uncheck_all').click(function(){
					$('.del_mess').attr('checked', false);
				});
				
				$('.remove_msg').flModal({
					caption: '{/literal}{$lang.warning}{literal}',
					content: '{/literal}{$lang.remove_message_notice}{literal}',
					prompt: 'mRemoveMsg()',
					width: 'auto',
					height: 'auto'
				});
				
				$('#message_text').textareaCount({
					'maxCharacterSize': rlConfig['messages_length'],
					'warningNumber': 20
				});
				
				$('#message_text').keydown( function(e){
					if ( e.ctrlKey && e.keyCode == 13 )
					{
						{/literal}xajax_sendMessage('{$contact.ID}', $(this).val(), {if $contact.Admin}1{else}0{/if});{literal}
					}
				});

				$('#messages_area').scrollTop($('#messages_area')[0].scrollHeight);
			});
			
			var mRemoveMsg = function(){
				var ids = '';
				$('.del_mess').each(function(){
					if ( $(this).is(':checked') )
					{
						ids += $(this).attr('id').split('_')[1]+',';
					}
				});
				ids = ids.substring(0, ids.length-1);
				
				if ( ids != '' )
				{
					xajax_removeMsg(ids, {/literal}{$contact.ID}{literal});
				}
			}
			
			{/literal}
			</script>
		{/if}
	
	{else}
	
		{if !empty($contacts)}
		
			<table class="list">
			<tr class="header">
				<td style="width: 80px;">{$lang.user}</td>
				<td class="divider"></td>
				<td class="last">
					<table class="sTable">
					<tr>
						<td class="lalign">{$lang.message}</td>
						<td class="ralign"><input style="margin-top: 1px;" type="checkbox" id="check_all" /></td>
					</tr>
					</table>
				</td>
			</tr>
			{foreach from=$contacts item='item' name='searchF' key='contact_id'}
				{assign var='status_key' value=$item.Status}
				<tr class="body{if $item.Status == 'new'} new{/if}" id="item_{$contact_id}">
					<td valign="top">
						<a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?id={$item.From}{else}?page={$pageInfo.Path}&amp;id={$item.From}{/if}{if $item.Admin}&amp;administrator{/if}" title="{$lang.chat_with} {$item.Full_name}">
							{if empty($item.Photo)}
								<img class="avatar70" alt="" src="{$rlTplBase}img/blank.gif" />
							{else}
								<img class="photo" alt="" src="{$smarty.const.RL_URL_HOME}files/{$item.Photo}" style="width: 66px" />
							{/if}
						</a>
						<div style="padding-top: 3px;" class="text-overflow">{$item.Full_name}</div>
						{if $item.Admin}<b>{$lang.website_admin}</b>{/if}
					</td>
					<td class="divider"></td>
					<td class="last">
						<table class="sTable">
						<tr>
							<td class="lalign">
								<div class="message_content_lim">{$item.Message|nl2br|replace:'\n':'<br />'}</div>
								<div class="message_date">{$item.Date|date_format:$smarty.const.RL_DATE_FORMAT}</div>
							
								<a class="reply" title="{$lang.new_message}" href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?id={$item.From}{else}?page={$pageInfo.Path}&amp;id={$item.From}{/if}{if $item.Admin}&amp;administrator{/if}">
									{if $item.Count > 0}{$lang.reply}{else}{$lang.show_chat}{/if}
								</a>
								{if $item.Count > 0}
									<a title="{$lang.new_message}" href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?id={$item.From}{else}?page={$pageInfo.Path}&amp;id={$item.From}{/if}{if $item.Admin}&amp;administrator{/if}">({$item.Count})</a>
								{/if}
								
								{rlHook name='messagesNav'}
							</td>
							<td class="ralign"><input type="checkbox" name="del_mess" class="del_mess" id="contact_{$item.From}" /></td>
						</tr>
						</table>
					</td>
				</tr>
			{/foreach}
			</table>
			
			<div class="mass_actions_light">
				<a class="remove_contacts" href="javascript:void(0)" title="{$lang.remove_selected_messages}">{$lang.remove_selected}</a>
			</div>
			
			<script type="text/javascript">
			{literal}
			
			$(document).ready(function(){
				$('.del_mess').click(function(){
					if ($('.del_mess:checked').length == 0)
						$('#check_all').attr('checked', false);
				});
				
				$('#check_all').click(function(){
					if ( $(this).is(':checked') )
					{
						$('.del_mess').attr('checked', true);
					}
					else
					{
						$('.del_mess').attr('checked', false);
					}
				});
				
				$('.remove_contacts').click(function(){
					var ids = '';
					$('.del_mess').each(function(){
						if ( $(this).is(':checked') )
						{
							ids += $(this).attr('id').split('_')[1]+',';
						}
					});
					ids = ids.substring(0, ids.length-1);
					
					if ( ids != '' )
					{
						$(this).flModal({
							caption: '{/literal}{$lang.warning}{literal}',
							content: '{/literal}{$lang.remove_contact_notice}{literal}',
							prompt: 'xajax_removeContacts("'+ ids +'")',
							width: 'auto',
							height: 'auto',
							click: false
						});
					}
				});
			});
			
			{/literal}
			</script>
			
		{else}
			<div class="info">{$lang.no_messages}</div>
		{/if}
	{/if}
</div>

<!-- saved search tpl end -->