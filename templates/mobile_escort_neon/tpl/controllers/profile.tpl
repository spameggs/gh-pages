<!-- my profile -->

<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.qtip.js"></script>
<script type="text/javascript">flynax.qtip();</script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.textareaCounter.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}ckeditor/ckeditor.js"></script>

<!-- tabs -->
<div class="tabs">
	<ul>
		{foreach from=$tabs item='tab' name='tabF'}
			{if $tab.key != 'tell_friend'}
			<li class="{if $smarty.foreach.tabF.first}first{/if}{if $tab.active} active{/if}" id="tab_{$tab.key}">
				<span class="center">{$tab.name}<span></span></span>
			</li>
			{/if}
		{/foreach}
	</ul>
</div>
<div class="clear"></div>
<!-- tabs end -->

<!-- profile tab -->
<div id="area_profile" class="tab_area {if $smarty.request.info == 'account'}hide{/if}">
	<div class="highlight">
		
		<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html{else}?page={$pageInfo.Path}{/if}" enctype="multipart/form-data">
			<input type="hidden" name="info" value="profile" />
			<input type="hidden" name="fromPost_profile" value="1" />

			<div class="field">{$lang.username}:</div>
			<b>{$profile_info.Username}</b>{if $profile_info.Full_name} ({$profile_info.Full_name}){/if}
			
			<div class="field">{$lang.account_type}:</div>
			{$profile_info.Type_name}
			
			{if $profile_info.Type_description}
			<div class="type_tip">{$profile_info.Type_description}</div>
			{/if}
			
			<div class="field">{$lang.your_photo}:</div>
			<div class="canvas {if !$profile_info.Photo}canvas_empty{/if}">
				<img class="preview" src="{if $profile_info.Photo}{$smarty.const.RL_FILES_URL}{$profile_info.Photo}{else}{$rlTplBase}img/no-account.png{/if}" alt="" />
				<img src="{$rlTplBase}img/blank.gif" class="delete {if $profile_info.Photo}ajax{/if}" alt="{$lang.delete}" title="{$lang.delete}" />
			</div>
			<div><input type="file" name="thumbnail" /></div>
			
			<script type="text/javascript">//<![CDATA[
			var profile_thumbnail = '{$profile_info.Photo}';
			{literal}
			
			$(document).ready(function(){
				$('div.canvas img.ajax').flModal({
					caption: lang['warning'],
					content: '{/literal}{$lang.delete_confirm}{literal}',
					prompt: 'xajax_delProfileThumbnail',
					width: 'auto',
					height: 'auto'
				});
			});
			
			{/literal}
			//]]>
			</script>
			
			<div class="field">{$lang.mail}:</div>
			<div>
				<input type="text" name="profile[mail]" maxlength="150" {if $smarty.post.profile.mail}value="{$smarty.post.profile.mail}"{/if} />
				{if $config.account_edit_email_confirmation}
					<div id="email_change_notice" class="notice_message {if !$aInfo.Mail_tmp}hide{/if}">
						{if $aInfo.Mail_tmp}
							{$lang.account_edit_email_confirmation_info|replace:'[e-mail]':$aInfo.Mail_tmp}
						{else}
							<b>{$lang.notice}</b>: {$lang.account_edit_email_confirmation_notice}
							<script type="text/javascript">
							{literal}
							
							$(document).ready(function(){
								$('input[name="profile[mail]"]').focus(function(){
									$('#email_change_notice').fadeIn('slow');
								});
							});
							
							{/literal}
							</script>
						{/if}
					</div>
				{/if}
			</div>
			
			<div class="field"><label><input value="1" type="checkbox" {if $smarty.post.profile.display_email}checked="checked"{/if} name="profile[display_email]" /> {$lang.display_email}</label></div>
			
			{if $account_info.Own_location}
			<div class="name_top">{$lang.personal_address}:</div>
			<div>
				{if $profile_info.Own_address}
					<a target="_blank" href="{$profile_info.Personal_address}">
						{*http://{if $config.account_wildcard}{$profile_info.Own_address}.{$domain}{else}{$domain}/{$profile_info.Own_address}{/if}*}
						{$profile_info.Personal_address}
					</a>
				{else}
					{if $config.account_wildcard}
						http://<input type="text" style="width: 90px;" maxlength="30" name="profile[location]" {if $smarty.post.profile.location}value="{$smarty.post.profile.location}"{/if} />.{$domain}
					{else}
						http://{$domain}/<input type="text" style="width: 90px;" maxlength="30" name="profile[location]" {if $smarty.post.profile.location}value="{$smarty.post.profile.location}"{/if} />
					{/if}
					<div class="notice_message">{$lang.latin_characters_only}</div>
				{/if}
			</div>
			{/if}
			
			<div class="button">
				<input type="submit" value="{$lang.save}" id="profile_submit" />
			</div>
		</form>
		
	</div>
</div>
<!-- profile tab end -->

{if !empty($profile_info.Fields)}
<!-- account tab -->
<div id="area_account" class="tab_area {if $smarty.request.info != 'account'}hide{/if}">
	<div class="highlight">
	
		<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html{else}?page={$pageInfo.Path}{/if}" enctype="multipart/form-data">
			<input type="hidden" name="info" value="account" />
			<input type="hidden" name="fromPost_account" value="1" />
			
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'profile_account.tpl'}

			<div class="button"><input type="submit" name="finish" value="{$lang.edit}" /></div>
		</form>
	
	</div>
</div>
<!-- account tab end -->
{/if}

<!-- manage password tab -->
<div id="area_password" class="tab_area hide">
	<div class="highlight">
	
		<div class="field">{$lang.current_password}:</div>
		<input type="password" id="current_password" maxlength="30" />
		
		<div class="field">{$lang.new_password}:</div>
		<input name="profile[password]" type="password" id="new_password" maxlength="30" />
		
		<div class="field">{$lang.new_password_repeat}:</div>
		<input type="password" id="password_repeat" maxlength="30" />
		
		<div class="button"><input id="change_password" type="button" value="{$lang.change}" /></div>
		
		<script type="text/javascript">
		{literal}
		
		$(document).ready(function(){
			$('#change_password').click(function(){
				xajax_changePass( $('#current_password').val(), $('#new_password').val(), $('#password_repeat').val() );
				$(this).val('{/literal}{$lang.loading}{literal}');
			});
		});
		
		{/literal}
		</script>
	</div>
</div>
<!-- manage password tab -->

<script type="text/javascript">
{literal}

var accountClicked = false;
$(document).ready(function(){
	$('div.tabs li#tab_account').click(function(){
		if ( !accountClicked )
		{
			flynax.mlTabs();
			accountClicked = true;
		}
	});
});

{/literal}

{if $smarty.request.info == 'account'}
	accountClicked = true;
	
	{literal}
	
	$(document).ready(function(){
		flynax.mlTabs();
	});
	
	{/literal}
	
{/if};

</script>

{rlHook name='profileBlock'}

<!-- my profile end -->
