<!-- pdf export link -->

<li>
	<a target="_blank" href="{$rlBase}{if $config.mod_rewrite}{$pages.PdfExport}.html?listingID={$listing_data.ID}{else}?page={$pages.PdfExport}&amp;listingID={$listing_data.ID}{/if}">{$lang.title_pdf_export}</a>
	<a target="_blank" href="{$rlBase}{if $config.mod_rewrite}{$pages.PdfExport}.html?listingID={$listing_data.ID}{else}?page={$pages.PdfExport}&amp;listingID={$listing_data.ID}{/if}"><img style="vertical-align: top;margin-top: 1px;" src="{$smarty.const.RL_PLUGINS_URL}PdfExport/pdf.png" alt="{$lang.title_pdf_export}" title="{$lang.title_pdf_export}"/></a>
</li>

<!-- pdf export link end -->
