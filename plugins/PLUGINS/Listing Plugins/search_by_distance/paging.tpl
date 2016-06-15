<!-- search by distance paging tpl -->
{*assign var='sbd_calc' value=200*}

{if $sbd_calc > $config.sbd_listings_per_page}
	{math assign='pages' equation='ceil(calc/per_page)' calc=$sbd_calc per_page=$config.sbd_listings_per_page}

	{assign var='sbd_start' value=0}
	{if $sbd_page > 4}
		{assign var='sbd_start' value=$sbd_page-4}
	{/if}

	{if $pages - $sbd_page > 3}
		{if $sbd_page <= 3}
			{math assign='sbd_dif' equation='6-page' page=$sbd_page}
		{else}
			{assign var='sbd_dif' value=3}
		{/if}

		{assign var='sbd_end' value=$sbd_page+$sbd_dif}
	{else}
		{assign var='sbd_end' value=$pages}
	{/if}
	
	<ul class="paging" id="sbd_paging">
	{if $sbd_page > 1}
		<li class="navigator first" title="{$lang.page} #1"><a accesskey="1" href="javascript:;">&laquo;</a></li>
		<li class="navigator ls" title="{$lang.prev_page}"><a accesskey="{$sbd_page-1}" href="javascript:;">&lsaquo;</a></li>
		{if $sbd_page > 4}
			<li class="point">...</li>
		{/if}
	{/if}
	
	{section name='pages' start=$sbd_start loop=$sbd_end}
		{assign var='sbd_set_page' value=$sbd_start+$smarty.section.pages.iteration}

		{if ($sbd_page && $sbd_page == $sbd_set_page) || (!$sbd_page && $smarty.section.pages.first)}
			<li class="active">{$sbd_set_page}</li>
		{else}
			<li><a title="{$lang.page} #{$sbd_set_page}" href="javascript:void(0)">{$sbd_set_page}</a></li>
		{/if}
	{/section}
	
	{if $pages > $sbd_page}
		{if $pages > $sbd_end && $pages - $sbd_page > 3}
			<li class="point">...</li>
		{/if}
		<li class="navigator rs" title="{$lang.next_page}"><a accesskey="{$sbd_page+1}" href="javascript:;">&rsaquo;</a></li>
		<li class="navigator last" title="{$lang.page} #{$pages}"><a accesskey="{$pages}" href="javascript:;">&raquo;</a></li>
	{/if}
	</ul>
{/if}
<!-- search by distance paging tpl end -->