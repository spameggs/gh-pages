<!-- banner item -->

<div class="item" id="banner_{$mBanner.ID}">
	<table class="sTable">
	<tr>
		<td class="photo" valign="top">
			<div class="my-banner">
			{if $mBanner.Type == 'image'}
				{if $mBanner.Image}
				<a title="{$mBanner.name}" href="{$smarty.const.RL_FILES_URL}banners/{$mBanner.Image}">
					<img alt="" class="shadow" src="{$smarty.const.RL_FILES_URL}banners/{$mBanner.Image}" />
				</a>
				{/if}
			{elseif $mBanner.Type == 'flash'}
				<object id="flash_banner_{$mBanner.Key}" width="120" height="80" data="{$smarty.const.RL_FILES_URL}banners/{$mBanner.Image}" type="application/x-shockwave-flash">
					<param value="{$smarty.const.RL_FILES_URL}banners/{$mBanner.Image}" name="movie">
					<param value="opaque" name="transparent">
					<param name="allowscriptaccess" value="samedomain">
					<param value="direct_link=true" name="flashvars">
					<embed width="120" height="80" flashvars="direct_link=true" wmode="transparent" src="{$smarty.const.RL_FILES_URL}banners/{$mBanner.Image}">
				</object>
			{elseif $mBanner.Type == 'html'}
				{$mBanner.Html}
			{/if}
			</div>
		</td>
		<td class="fields" valign="top">
			<table>
				<tr>
					<td class="value">{$mBanner.name}</td>
				</tr>
			</table>
		</td>
		<td class="details" valign="top" rowspan="2">
			<table class="info">
			<tr>
				<td class="name">{$lang.added}:</td>
				<td class="value">{$mBanner.Date_release|date_format:$smarty.const.RL_DATE_FORMAT}</td>
			</tr>
			<tr>
				<td class="name">{$lang.status}:</td>
				<td class="value">
					{if $mBanner.Status == 'incomplete'}
						<a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?incomplete={$mBanner.ID}&amp;step={$mBanner.Last_step}{else}?page={$pageInfo.Path}&amp;incomplete={$mBanner.ID}&amp;step={$mBanner.Last_step}{/if}" class="{$mBanner.Status}">{$lang[$mBanner.Status]}</a>
					{elseif $mBanner.Status == 'expired'}
						<a href="{$rlBase}{if $config.mod_rewrite}{$pages.banners_renew}.html?id={$mBanner.ID}{else}?page={$pages.banners_renew}&amp;id={$mBanner.ID}{/if}" title="{$lang.banners_renewPlan}" class="{$mBanner.Status}">{$lang[$mBanner.Status]}</a>
					{else}
						<span {if $mBanner.Status == 'pending'}title="{$lang.banners_waitingApproval}"{/if} class="{$mBanner.Status}">{$lang[$mBanner.Status]}</span>
					{/if}
				</td>
			</tr>

			{if $mBanner.Date_to && $mBanner.Plan_type == 'period'}
			<tr>
				<td class="name">{$lang.active_till}:</td>
				<td class="value">{$mBanner.Date_to|date_format:$smarty.const.RL_DATE_FORMAT}</td>
			</tr>
			{elseif $mBanner.Date_to && $mBanner.Plan_type == 'views'}
			<tr>
				<td class="name">{$lang.banners_showsLeft}:</td>
				<td class="value">{math equation="x - y" x=$mBanner.Date_to y=$mBanner.Shows}</td>
			</tr>
			{/if}

			{if $mBanner.Key}
			<tr>
				<td class="name">{$lang.plan}:</td>
				<td class="value">{assign var='planName' value='banner_plans+name+'|cat:$mBanner.Key}{$lang.$planName}</td>
			</tr>
			{/if}

			<tr>
				<td class="name">{$lang.banners_bannerShows}:</td>
				<td class="value">{$mBanner.Shows}</td>
			</tr>

			{if $mBanner.Type == 'image'}
			<tr>
				<td class="name">{$lang.banners_bannerClicks}:</td>
				<td class="value">{if $mBanner.clicks}{$mBanner.clicks}{else}0{/if}</td>
			</tr>
			{/if}
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="nav_icons">
			<a title="{$lang.banners_editBanner}" class="nav_icon" href="{$rlBase}{if $config.mod_rewrite}{$pages.banners_edit_banner}.html?id={$mBanner.ID}{else}?page={$pages.banners_edit_banner}&amp;id={$mBanner.ID}{/if}">
				<span class="left">&nbsp;</span><span class="center">
				<img class="edit_listing" src="{$rlTplBase}img/blank.gif" alt="" />
				</span><span class="right">&nbsp;</span>
			</a>
			<a title="{$lang.banners_renewPlan}" class="nav_icon" href="{$rlBase}{if $config.mod_rewrite}{$pages.banners_renew}.html?id={$mBanner.ID}{else}?page={$pages.banners_renew}&amp;id={$mBanner.ID}{/if}">
				<span class="left">&nbsp;</span><span class="center">
				<img class="upgrade_listing" src="{$rlTplBase}img/blank.gif" alt="" />
				</span><span class="right">&nbsp;</span>
			</a>
		</td>
	</tr>
	</table>

	<img class="delete_highlight" id="delete_banner_{$mBanner.ID}" src="{$rlTplBase}img/blank.gif" alt="" title="{$lang.delete}" />
</div>

<!-- banner item end -->