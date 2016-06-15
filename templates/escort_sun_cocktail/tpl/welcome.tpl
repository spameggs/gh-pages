<!-- welcome screen -->

<div class="top-spirts">
	<div class="logo">
		<a href="{$rlBase}" title="{$config.site_name}">
			<img alt="" src="{$rlTplBase}img/{if $smarty.const.RL_LANG_DIR == 'rtl'}rtl/{/if}welcome-logo.png" />
		</a>
	</div>
	
	<div class="content-notice">
		{assign var='label_phrases' value=' '|explode:$lang.welcome_label_text}
		{foreach from=$label_phrases item='label_phrase' name='labelF'}
			{if $smarty.foreach.labelF.iteration <= 3}
				<div class="line{$smarty.foreach.labelF.iteration}">{$label_phrase}</div>
			{/if}
		{/foreach}
	</div>
	
	<div class="buttons">
		<a href="{$rlBase}" class="enter-btn">{$lang.welcome_enter}</a>
		<div><a href="{$config.exit_url}" class="exit-btn">{$lang.welcome_exit}</a></div>
	</div>
	
	<div class="message">{$lang.welcome_page_message}</div>
</div>

<script type="text/javascript">
{literal}

$(document).ready(function(){
	$('a.enter-btn').mouseup(function(){
		createCookie('content_accepted', 'true', 31);
	});
});

{/literal}
</script>

<!-- welcome screen end -->