<!-- bulletin -->
{if !$errors}
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
<table class="lTable form">
<tr class="body">
	<td class="list_td_light">{$lang.vbulletin_installProductTitle}</td>
	<td style="width: 5px;" rowspan="100"></td>
	<td class="list_td_light" id="install_product_dom" align="center" style="width: 200px;">
	{if $productStatus == 'install'}
		<input id="install_product" type="button" onclick="xajax_installProduct();$(this).val('{$lang.loading}');" value="{$lang.install}" style="margin: 0;width: 100px;" />
	{else}
		<b>{$lang.$productStatus}</b>
	{/if}
	</td>
</tr>
</table>
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}
<div id="import_modules_dom">
	{include file=$smarty.const.RL_PLUGINS|cat:'vbulletin'|cat:$smarty.const.RL_DS|cat:'admin'|cat:$smarty.const.RL_DS|cat:'modules.tpl'}
</div>
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}
{/if}
<!-- bulletin end -->