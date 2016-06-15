<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>{foreach from=$title item='title_item' name='titleF'}{if $smarty.foreach.titleF.first}{$title_item}{else} &#171; {$title_item}{/if}{/foreach}</title>
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta name="generator" content="Escort Agency" />
<meta http-equiv="Content-Type" content="text/html; charset={$config.encoding}" />
<meta name="description" content="{$pageInfo.meta_description|strip_tags}" />
<meta name="Keywords" content="{$pageInfo.meta_keywords|strip_tags}" />
<link rel="canonical" href="{$pageInfo.canonical}" />
{if $smarty.cookies.content_accepted}
	<link href="{$rlTplBase}css/style.css" type="text/css" rel="stylesheet" />
	<link href="{$rlTplBase}css/common.css" type="text/css" rel="stylesheet" />
	<link href="{$smarty.const.RL_LIBS_URL}jquery/fancybox/jquery.fancybox.css" type="text/css" rel="stylesheet" />
	{if $config.gallery_slideshow}
	<link href="{$smarty.const.RL_LIBS_URL}jquery/fancybox/helpers/jquery.fancybox-buttons.css" type="text/css" rel="stylesheet" />
	{/if}
	<link href="{$rlTplBase}css/jquery.ui.css" type="text/css" rel="stylesheet" />
	{if $smarty.const.RL_LANG_DIR == 'rtl'}
		<link href="{$rlTplBase}css/rtl.css" type="text/css" rel="stylesheet" />
		{assign var='text_dir' value='right'}
		{assign var='text_dir_rev' value='left'}
	{else}
		{assign var='text_dir' value='left'}
		{assign var='text_dir_rev' value='right'}
	{/if}
	{if $rss}
		<link rel="alternate" type="application/rss+xml" title="{$rss.title}" href="{$rlBase}{if $config.mod_rewrite}{$pages.rss_feed}/{if $rss.item}{$rss.item}/{/if}{if $rss.id}{$rss.id}/{/if}{else}?page={$pages.rss_feed}{if $rss.item}&amp;item={$rss.item}{/if}{if $rss.id}&amp;id={$rss.id}{/if}{/if}" />
	{/if}
{else}
	<link href="{$rlTplBase}css/welcome.css" type="text/css" rel="stylesheet" />
{/if}
<link type="image/x-icon" rel="shortcut icon" href="{$rlTplBase}img/favicon.ico" />
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.color.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/cookie.js"></script>
<script type="text/javascript" src="{$rlTplBase}js/lib.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}javascript/fl.lib.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.ui.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/datePicker/i18n/ui.datepicker-{$smarty.const.RL_LANG_CODE|lower}.js"></script>
{include file='js_config.tpl'}
{rlHook name='tplHeader'}
{$ajaxJavascripts}
</head>
<body {if !$smarty.cookies.content_accepted}class="welcome"{/if}>
	<div id="content_height">
		{if $smarty.cookies.content_accepted}
			<div id="menu-line"></div>
			<div class="header-sun"></div>
			<div class="footer-sun"></div>
			<!-- header -->
			<div id="header">
				{include file='blocks'|cat:$smarty.const.RL_DS|cat:'user_navbar.tpl'}
				<div id="logo">
					<a href="{$rlBase}" title="{$config.site_name}">
						<img alt="" src="{$rlTplBase}img/{if $smarty.const.RL_LANG_DIR == 'rtl'}rtl/{/if}logo.png" />
					</a>
				</div>
				<!-- main menu -->
				<div id="main_menu_container">
					{include file='menus'|cat:$smarty.const.RL_DS|cat:'main_menu.tpl'}
				</div>
				<!-- main menu end -->
			</div>
			<!-- header end -->
			<div id="middle_light">
				<div id="main_container">
		{/if}