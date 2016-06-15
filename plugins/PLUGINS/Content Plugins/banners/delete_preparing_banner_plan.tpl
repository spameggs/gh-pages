<!-- delete preparing banner plan -->

{if $planInfo.planMode}
	{assign var='deleteNoticeLang' value='banners_preBannerPlanDeleteNotice'}
	{assign var='fullDeleteNoticeLang' value='banners_fullBannersPlanDelete'}
	{assign var='replaceTag' value='plan'}
{else}
	{assign var='deleteNoticeLang' value='banners_preBannerBoxDeleteNotice'}
	{assign var='fullDeleteNoticeLang' value='banners_fullBannersBoxDelete'}
	{assign var='replaceTag' value='box'}
{/if}

{assign var='replace' value=`$smarty.ldelim`$replaceTag`$smarty.rdelim`}
<div>{$lang.$deleteNoticeLang|replace:$replace:$planInfo.name}</div>

<table class="list" style="margin: 0 0 15px 10px;">
{foreach from=$deleteDetails item='del_item'}
{if $del_item.items}
<tr>
	<td class="name" style="width: 115px">{$del_item.name}:</td>
	<td class="value">{if $del_item.link}<a target="_blank" href="{$del_item.link}">{/if}<b>{$del_item.items}</b>{if $del_item.link}</a>{/if}</td>
</tr>
{/if}
{/foreach}
</table>

{$lang.choose_removal_method}
<div style="margin: 5px 10px">
	<div style="padding: 2px 0;">
		<label><input checked="checked" type="radio" value="delete" name="del_action" onclick="$('#top_buttons').slideDown();$('#bottom_buttons').slideUp();" /> 
			{$lang.$fullDeleteNoticeLang}
		</label>
	</div>

	<div style="margin: 5px 0;">
		<div id="top_buttons">
			<input class="simple" type="button" value="{$lang.go}" onclick="delete_chooser('{$planInfo.id}', '{$planInfo.name}')" />
			<a class="cancel" href="javascript:void(0)" onclick="$('#delete_block').fadeOut()">{$lang.cancel}</a>
		</div>
	</div>
</div>

<!-- delete preparing banner plan end -->