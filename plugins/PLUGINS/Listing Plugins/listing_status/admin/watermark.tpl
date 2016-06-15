
{if $watermark}
	<table>
		<tr>
			<td>
			<div class="status_box">
				<input type="hidden" name="watermark{if $large}Large{/if}[{$code}]" value="{$watermark}"/>
				<img class="status" src="{$smarty.const.RL_URL_HOME}files/watermark/{$watermark}">
				<img src="{$rlTplBase}img/blank.gif" class="delete_item" alt="{$lang.delete}" title="{$lang.delete}" onclick='rlConfirm( "{$lang.delete_confirm|replace}", "xajax_deleteWatermark", Array("\"{$sPost.key}\"", "\"{$code}\"", "\"{$watermark}\"", "\"{$large}\""), "photo_loading", "smarty" );' />
			</div>
			</td>
			<td>
				<span class="field_description_noicon">{if $large}{$lang.ls_label_large}{else}{$lang.ls_label}{/if}</span>
			</td>
		</tr>
	</table>
{else}
	<input type="file" name="watermark{if $large}Large{/if}[{$code}]"/>
	<span class="field_description">{$lang.ls_watermarks_hint}</span>
{/if}

