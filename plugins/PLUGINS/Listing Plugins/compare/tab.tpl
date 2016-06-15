<!-- compare listings tab -->
<div id="compare_listings_fixed">
	<div class="highlight_dark hborder search" id="compare_listings_tab">
		<label>{$lang.compare_comparison_table}</label> <span class="counter"></span>
	</div>
	<div class="highlight_dark hborder" id="compare_listings_area">
		<div>
			<div class="title">{$lang.compare_listings_to_be_compare}</div>
			<div class="body">
				<span class="info">{$lang.compare_no_listings_to_compare}</span>
			</div>
			<div class="button hide">
				<a class="button" href="{$rlBase}{if $config.mod_rewrite}{$pages.compare_listings}.html{else}?page={$pages.compare_listings}{/if}">{$lang.compare_compare}</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">//<![CDATA[
var flCompare = new Object();
flCompare.phrases = new Array();
flCompare.requestUrl = '{$seoBase}',
flCompare.visible = false;
flCompare.counter = false;
flCompare.phrases['add'] = "{$lang.compare_add_to_compare}";
flCompare.phrases['remove'] = "{$lang.compare_remove_from_compare}";
flCompare.phrases['notice'] = "{$lang.compare_remove_notice}";
flCompare.phrases['warning'] = "{$lang.warning}";
flCompare.phrases['compare_comparison_table'] = "{$lang.compare_comparison_table}";
flCompare.phrases['compare_default_view'] = "{$lang.compare_default_view}";
flCompare.phrases['powered_by'] = "{$lang.powered_by|replace:'"':'&quot;'}";
flCompare.phrases['copy_rights'] = "{$lang.copy_rights|replace:'"':'&quot;'}";
//]]>
</script>
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}compare/static/lib.js"></script>
<!-- compare listings tab end -->