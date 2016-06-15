	<!-- footer block --> 
	<div id="standard_link">
		<a href="{$smarty.const.RL_URL_HOME}?standard">{$lang.mobile_standart_version}</a>
	</div>

	<div class="footer">
		<div class="inner">
			{include file='menus'|cat:$smarty.const.RL_DS|cat:'footer_menu.tpl'}
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'lang_selector.tpl'}
			
			<span>&copy; {$smarty.now|date_format:'%Y'}, {$lang.powered_by}</span>
			<a title="{$lang.powered_by} {$lang.copy_rights}" href="{$lang.reefless_url}">{$lang.copy_rights}</a>
		</div>
	</div>
	<!-- footer block end -->
</div>

{rlHook name='tplFooter'}

</body>
</html>