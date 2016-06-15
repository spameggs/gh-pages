<!-- content block -->

{if $pageInfo.Controller != 'home'}
	<!-- bread crumbs -->
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'bread_crumbs.tpl'}
	<!-- bread crumbs end -->
	
	{if $navIcons}
		<div class="fleft">
			<h1>{$pageInfo.name}</h1>
		</div>
		<div class="fright" id="content_nav_icons">	
			{if !empty($navIcons)}
				{foreach from=$navIcons item='icon'}
					{$icon}
				{/foreach}
			{/if}
		</div>
		<div class="clear"></div>
	{else}
		<h1>{$pageInfo.name}</h1>
	{/if}
{/if}

<div id="system_message">
	{if isset($errors)}
		<script type="text/javascript">//<![CDATA[
		var fixed_message = {if $fixed_message}false{else}true{/if};
		var error_fields = {if $error_fields}'{$error_fields}'{else}false{/if};

		var message_text = '<ul>';
		{foreach from=$errors item='error'}message_text += '<li>{$error}</li>';{/foreach}
		message_text += '</ul>';
		{literal}
		
		$(document).ready(function(){
			printMessage('error', message_text, error_fields, fixed_message);
		});
		
		{/literal}
		//]]>
		</script>
	{/if}
	{if isset($pNotice)}
		<script type="text/javascript">
		var message_text = '{$pNotice}';
		{literal}
		
		$(document).ready(function(){
			printMessage('notice', message_text, false, true);
		});
		
		{/literal}
		</script>
	{/if}
	{if isset($pAlert)}
		<script type="text/javascript">
		var message_text = '{$pAlert}';
		{literal}
		
		$(document).ready(function(){
			printMessage('warning', message_text, false, true);
		});
		
		{/literal}
		</script>
	{/if}
	
	<!-- no javascript mode -->
	<noscript>
	<div class="warning" style="margin-top: 3px;">
		<div class="inner">
			<div class="icon"></div>
			<div class="message">{$lang.no_javascript_warning}</div>
		</div>
	</div>
	</noscript>
	<!-- no javascript mode end -->
</div>

{if $pageInfo.Controller != 'home'}<div class="content_container">{/if}
	{if $pageInfo.Page_type == 'system'}
		{include file=$content}
	{else}
		<div class="padding" style="line-height: 20px;">{$staticContent}</div>
	{/if}
{if $pageInfo.Controller != 'home'}</div>{/if}

<!-- content block end -->