	{if $smarty.cookies.content_accepted}
				</div>
			</div>
			
			<div id="crosspiece"></div>
		</div>
		
		<div id="bottom_bg">
		
			<!-- footer -->
			<div id="footer">
				<div class="menu">{include file='menus'|cat:$smarty.const.RL_DS|cat:'footer_menu.tpl'}</div>
				
				<div class="left">
					<span>&copy; {$smarty.now|date_format:'%Y'}, {$lang.powered_by} </span><a title="{$lang.powered_by} {$lang.copy_rights}" href="{$lang.flynax_url}">{$lang.copy_rights}</a>
				</div>
				<div class="right">
					<script type="text/javascript">//<![CDATA[
					document.write('<div class="fb-like" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false"></div>');
					//]]>
					</script>

					{if $aHooks.facebookConnect}
						{if $config.facebookConnect_module && $config.facebookConnect_appid && $config.facebookConnect_secret && $config.facebookConnect_account_type}
							{assign var='facebookConnect_configured' value=true}
						{/if}
					{/if}

					{if !$facebookConnect_configured}
					<div id="fb-root"></div>
					{literal}
					<script type="text/javascript">//<![CDATA[
						(function(d, s, id) {
							var js, fjs = d.getElementsByTagName(s)[0];
							if (d.getElementById(id)) return;
							js = d.createElement(s); js.id = id;
							js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=159469340782582";
							fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));
					//]]>
					</script>
					{/literal}
					{/if}

					<div class="tweet_padding"><a href="https://twitter.com/share" class="twitter-share-button">Tweet</a></div>
					{literal}
					<script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					{/literal}
				</div>
			</div>
			<!-- footer end -->
			
		</div>
			
		{rlHook name='tplFooter'}
	{else}
			<div id="crosspiece"></div>
		</div>
		
		<div class="welcome-footer">
			<span>&copy; {$smarty.now|date_format:'%Y'}, {$lang.powered_by} </span><a title="{$lang.powered_by} {$lang.copy_rights}" href="{$lang.flynax_url}">{$lang.copy_rights}</a>
		</div>
	{/if}
	
</body>
</html>