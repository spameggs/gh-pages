<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
{assign var='hook_path' value=$smarty.const.RL_ROOT|cat:'templates'|cat:$smarty.const.RL_DS|cat:$config.mobile_template|cat:$smarty.const.RL_DS}
{include_php file=$hook_path|cat:'hook.inc.php'}
<title>{foreach from=$title item='title_item' name='titleF'}{if $smarty.foreach.titleF.first}{$title_item}{else} &#171; {$title_item}{/if}{/foreach}</title>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="generator" content="Escort Agency - Mobile Version" />
<meta http-equiv="Content-Type" content="text/html; charset={$config.encoding}" />
<meta name="description" content="{$pageInfo.meta_description}" />
<meta name="Keywords" content="{$pageInfo.meta_keywords}" />
<link rel="canonical" href="{$pageInfo.canonical}" />
<link href="{$rlTplBase}css/mobile.css" type="text/css" rel="stylesheet" />
<link href="{$rlTplBase}css/jquery.ui.css" type="text/css" rel="stylesheet" />
<!--link href="{$rlTplBase}css/jquery.ui.css" type="text/css" rel="stylesheet" /-->
<link href="{$smarty.const.RL_LIBS_URL}jquery/fancybox/jquery.fancybox.css" type="text/css" rel="stylesheet" />
{if $pageInfo.Controller == 'listing_details'}
	<link href="{$rlTplBase}css/photoswipe.css" type="text/css" rel="stylesheet" />
{/if}
<link rel="shortcut icon" href="{$rlTplBase}img/favicon.ico" />
{if $smarty.const.RL_LANG_DIR == 'rtl'}
	<link href="{$rlTplBase}css/rtl.css" type="text/css" rel="stylesheet" />
	{assign var='text_dir' value='right'}
	{assign var='text_dir_rev' value='left'}
{else}
	{assign var='text_dir' value='left'}
	{assign var='text_dir_rev' value='right'}
{/if}
{if $rss}
	<link rel="alternate" type="application/rss+xml" title="{$rss.title}" href="{$rlBase}{if $config.mod_rewrite}{$pages.rss_feed}/{if $rss.item}?{$rss.item}{else}{if $rss.id}?id={$rss.id}{if ($rss.type || $rss.period) && $rss.id}&amp;{elseif ($rss.type || $rss.period) && $rss.id}?{/if}{/if}{if $rss.type}type={$rss.type}{/if}{if $rss.period}&amp;period={$rss.period}{/if}{/if}{else}?page={$pages.rss_feed}{if $rss.item}&amp;{$rss.item}{else}&amp;{if $rss.id}id={$rss.id}{if $rss.type || $rss.period}&amp;{/if}{/if}{if $rss.type}type={$rss.type}{/if}{if $rss.period}&amp;period={$rss.period}{/if}{/if}{/if}" />
{/if}
{include file='js_config.tpl'}
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}javascript/fl.lib.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.js"></script>
<script type="text/javascript" src="{$rlTplBase}js/jquery.mobile.min.js"></script>
<script type="text/javascript" src="{$rlTplBase}js/lib.js"></script>
{rlHook name='tplHeader'}
{$ajaxJavascripts}
</head>
<body>
<div class="main_container">
	<!-- header block -->
	<div class="hearde_block">
		<div id="spray_left"></div>
		<div id="spray_right"></div>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'user_navbar.tpl'}				
		<div id="logo">
			<a href="{$rlBase}">
				<img alt="{$config.site_name}" title="{$config.site_name}" src="{$rlTplBase}img/blank.gif" />
			</a>
		</div>
	</div>
	{include file='menus'|cat:$smarty.const.RL_DS|cat:'main_menu.tpl'}
	<!-- header block end -->