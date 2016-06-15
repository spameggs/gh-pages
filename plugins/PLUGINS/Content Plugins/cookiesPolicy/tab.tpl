<!-- cookiesPolicy tab -->
<div id="cookies_policy_{$config.cookiesPolicy_position|replace:' ':'_'|lower}" {if $smarty.const.RL_LANG_DIR == 'rtl'}class="cp-rtl"{/if}>
	<div class="cookies_policy_icon" id="cookies_policy_icon_{$config.cookiesPolicy_position|replace:' ':'_'|lower}">C</div>

	<div id="cookies_policy_big_form" class="cookies_policy_big_form_{$config.cookiesPolicy_position|replace:' ':'_'|lower} hide">
		<div class="header"><div>{$lang.cookies_policy_cookie_control}</div></div>
		<div class="content">{$lang.cookies_policy_content_text}</div>
		<div class="buttons_content">
			{if !$smarty.cookies.cookies_policy}<input type="button" class="cookie_accept" value="{$lang.cookies_policy_accept}" />{/if}
			<input type="button" class="cookie_decline" value="{$lang.cookies_policy_decline}" />
		</div>
	</div>
</div>
<script type="text/javascript">
var CP_show_cookie_notice = {if !$smarty.cookies.cookies_policy}true{else}false{/if};
var CP_redirect_url = '{$config.cookiesPolicy_redirect_url}';
</script>
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}cookiesPolicy/static/lib.js"></script>
<!-- cookiesPolicy tab end -->
