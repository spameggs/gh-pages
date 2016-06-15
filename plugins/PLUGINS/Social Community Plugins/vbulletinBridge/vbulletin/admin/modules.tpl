<!-- modules tpl -->
<table class="lTable form">
	<tr>
		<td class="divider" style="text-align: center;"><div class="inner">{$lang.vbulletin_tableHeaderTitle}</div></td>
		<td></td>
		<td class="divider" style="text-align: center;"><div class="inner">{$lang.vbulletin_tableHeaderAction}</div></td>
		<td></td>
		<td class="divider" style="text-align: center;"><div class="inner">{$lang.vbulletin_tableHeaderSuccessful}</div></td>
		<td></td>
		<td class="divider" style="text-align: center;"><div class="inner">{$lang.vbulletin_tableHeaderExists}</div></td>
		<td></td>
		<td class="divider" style="text-align: center;"><div class="inner">{$lang.vbulletin_tableHeaderLastImport}</div></td>
	</tr>
{foreach from=$actions item='action' name='actionsName'}
	<tr class="body">
		<td class="{if $smarty.foreach.actionsName.iteration % 2 == 0}list_td{else}list_td_light{/if}">{$action.title}</td>
		{if $smarty.foreach.actionsName.first}<td style="width: 5px;" rowspan="100"></td>{/if}
		<td class="{if $smarty.foreach.actionsName.iteration % 2 == 0}list_td{else}list_td_light{/if}" align="center" {if $smarty.foreach.actionsName.first}style="width: 135px;"{/if}>
			<input type="button" onclick="{$action.func}(0);$(this).val('{$lang.loading}');" value="{$action.button}" style="margin: 0;width: 100px;" />
		</td>
		{if $smarty.foreach.actionsName.first}<td style="width: 5px;" rowspan="100"></td>{/if}
		<td class="{if $smarty.foreach.actionsName.iteration % 2 == 0}list_td{else}list_td_light{/if}" align="center"{if $smarty.foreach.actionsName.first} style="width: 135px;"{/if}>
			<div id="{$action.Module}_successful">{$action.successful}</div>
		</td>
		{if $smarty.foreach.actionsName.first}<td style="width: 5px;" rowspan="100"></td>{/if}
		<td class="{if $smarty.foreach.actionsName.iteration % 2 == 0}list_td{else}list_td_light{/if}" align="center"{if $smarty.foreach.actionsName.first} style="width: 135px;"{/if}>
			<div id="{$action.Module}_failed"><span{if $action.failed} class="red" style="font-weight: bold;"{/if}>{$action.failed}</span></div>
		</td>
		{if $smarty.foreach.actionsName.first}<td style="width: 5px;" rowspan="100"></td>{/if}
		<td class="{if $smarty.foreach.actionsName.iteration % 2 == 0}list_td{else}list_td_light{/if}" align="center"{if $smarty.foreach.actionsName.first} style="width: 160px;"{/if}>
			<div id="{$action.Module}_modify"><i>{if $action.date==0}N/A{else}{$action.date|date_format:'%b %d,%Y %I:%M %p'}{/if}</i></div>
		</td>
	</tr>
	{if !$smarty.foreach.actionsName.last}
	<tr>
		<td style="height: 5px;" colspan="9"></td>
	</tr>
	{/if}
{/foreach}
</table>
<!-- modules tpl end -->