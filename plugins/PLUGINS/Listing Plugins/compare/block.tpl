<!-- comparison results block -->
{if !$saved_list && $compare_listings}
	<div class="compare_results_block">
		<a title="{$lang.compare_save_results}" class="save_search" href="{$rlBase}{if $config.mod_rewrite}{$pages.compare_listings}/save.html{else}?page={$pages.compare_listings}&amp;save{/if}"><span></span></a>
		<a title="{$lang.compare_save_results}" href="{$rlBase}{if $config.mod_rewrite}{$pages.compare_listings}/save.html{else}?page={$pages.compare_listings}&amp;save{/if}">{$lang.compare_save_results}</a>
	</div>
{/if}
{if $saved_tables}
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' name=$lang.compare_my_tables}
	<ul class="comapre_saved_list">
		{foreach from=$saved_tables item='saved_item'}
		<li id="compare_saved_list_{$saved_item.ID}">
			<a href="{$rlBase}{if $config.mod_rewrite}{$pages.compare_listings}/{$saved_item.Path}.html{else}?page={$pages.compare_listings}&amp;id={$saved_item.ID}{/if}" class="{if $config.mod_rewrite}{if $saved_list == $saved_item.Path}active{/if}{else}{if $saved_list == $saved_item.ID}active{/if}{/if}">{$saved_item.Name}</a>
			<img title="{$lang.remove}" class="remove" src="{$rlTplBase}img/blank.gif" />
		</li>
		{/foreach}
	</ul>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
{/if}
<span id="compare_no_data_block" {if $saved_tables || $compare_listings}class="hide"{/if}>{$lang.compare_no_listings_to_compare}</span>
<script type="text/javascript">
{literal}
$(document).ready(function(){
	$('.compare_results_block').attr('id', 'content_nav_icons');
	$('ul.comapre_saved_list img.remove').each(function(){
		$(this).flModal({
			caption: '{/literal}{$lang.notice}{literal}',
			content: '{/literal}{$lang.compare_delete_table_notice}{literal}',
			prompt: 'xajax_removeTable('+ $(this).parent().attr('id').split('_')[3] +')',
			width: 'auto',
			height: 'auto'
		});
	});
});
{/literal}
</script>
<!-- comparison results block end -->