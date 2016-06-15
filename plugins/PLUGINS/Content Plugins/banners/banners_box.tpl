<!-- banners box -->

<div class="banners-box {if $boxBetweenCategories}item{/if}">
{if $banners}
	{foreach from=$banners item='banner' name='bannerF'}
		{if $banner.Type == 'image'}
		<div class="banner bmb10" id="banner_{$banner.ID}" onclick="xajax_bannerClick({$banner.ID});" style="width:{$info.width}px; height:{$info.height}px;">
			{if $banner.Link}<a {if $banner.externalLink}target="_blank"{/if} {if !$banner.Follow}rel="nofollow"{/if} title="{$banner.name}" href="{$banner.Link}">{/if}
				<img alt="{$banner.name}" title="" src="{$smarty.const.RL_FILES_URL}{$info.folder}{$banner.Image}" />
			{if $banner.Link}</a>{/if}
		</div>
		{elseif $banner.Type == 'flash'}
		<div class="banner bmb10" title="{$banner.name}" style="width:{$info.width}px; height:{$info.height}px;">
			<object width="{$info.width}" height="{$info.height}" data="{$smarty.const.RL_FILES_URL}{$info.folder}{$banner.Image}" type="application/x-shockwave-flash">
				<param value="{$smarty.const.RL_FILES_URL}{$info.folder}{$banner.Image}" name="movie">
				<param name="wmode" value="transparent">
				<param value="direct_link=true" name="flashvars">
				<embed width="{$info.width}" height="{$info.height}" flashvars="direct_link=true" wmode="transparent" src="{$smarty.const.RL_FILES_URL}{$info.folder}{$banner.Image}">
			</object>
		</div>
		{elseif $banner.Type == 'html'}
		<div class="banner bmb10" style="width:{$info.width}px; height:{$info.height}px;">
			{$banner.Html}
		</div>
		{/if}
	{/foreach}
{else}
	<div class="banner" {if $boxBetweenCategories}style="display: inline;"{/if}>
		<table width="{$info.width}" height="{$info.height}" {if !$boxBetweenCategories}align="center"{/if}>
		<tr class="banner-here">
			<td align="center" valign="center">{$info.width} x {$info.height}</td>
		</tr>
		</table>
	</div>
{/if}
</div>

<!-- banners box end -->