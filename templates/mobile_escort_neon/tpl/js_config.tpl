<script type="text/javascript">//<![CDATA[
	var rlUrlHome = '{$rlTplBase}';
	var rlUrlRoot = '{$rlBase}';
	var rlLangDir = '{$smarty.const.RL_LANG_DIR}';
	var rlLang = '{$smarty.const.RL_LANG_CODE|lower}';
	var rlPageInfo = new Array();
	rlPageInfo['key'] = '{$pageInfo.Key}';
	rlPageInfo['path'] = '{if $pageInfo.Path_real}{$pageInfo.Path_real}{else}{$pageInfo.Path}{/if}';
	var rlConfig = new Array();
	rlConfig['mod_rewrite'] = {$config.mod_rewrite};
	var lang = new Array();
	lang['photo'] = '{$lang.photo}';
	lang['of'] = '{$lang.of}';
	lang['close'] = '{$lang.close}';
	lang['cancel'] = '{$lang.cancel}';
	lang['remove_from_favorites'] = '{$lang.remove_from_favorites}';
	lang['add_to_favorites'] = '{$lang.add_to_favorites}';
	lang['notice_removed_from_favorites'] = '{$lang.notice_listing_removed_from_favorites}';
	lang['password_strength_pattern'] = '{$lang.password_strength_pattern}';
	lang['no_favorite'] = '{$lang.no_favorite}';
	lang['notice_reg_length'] = '{$lang.notice_reg_length}';
	lang['characters_left'] = '{$lang.characters_left}';
	lang['loading'] = '{$lang.loading}';
	lang['unsaved_photos_notice'] = '{$lang.unsaved_photos_notice}';
	lang['gateway_fail'] = '{$lang.notice_payment_gateway_does_not_chose}';
	lang['notice_bad_file_ext'] = '{$lang.notice_bad_file_ext}';
	
	var rlConfig = new Array();
	rlConfig['seo_url'] = '{$rlBase}';
	rlConfig['tpl_base'] = '{$rlTplBase}';
	rlConfig['files_url'] = '{$smarty.const.RL_FILES_URL}';
	rlConfig['libs_url'] = '{$smarty.const.RL_LIBS_URL}';
	rlConfig['mod_rewrite'] = {$config.mod_rewrite};
	rlConfig['sf_display_fields'] = {$config.sf_display_fields};
	rlConfig['account_password_strength'] = {$config.account_password_strength};
	rlConfig['messages_length'] = {$config.messages_length};
	
	{literal}
	var qtip_style = new Object({
		width: 'auto',
		background: '#80b63a',
		color: 'white',
		border: {
			width: 7,
			radius: 2,
			color: '#80b63a'
		},
		tip: 'bottomLeft'
	});
	{/literal}
//]]>
</script>