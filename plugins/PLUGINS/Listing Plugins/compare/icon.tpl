<!-- compare icon on listing details -->

{assign var='compare_cookie_ids' value=','|explode:$smarty.cookies.compare_listings}

<li class="compare-icon">
	<span class="{if $listing_data.ID|in_array:$compare_cookie_ids}remove{else}add{/if}" accesskey="{$listing_data.ID}">
		<a href="javascript:void(0)" rel="nofollow">{if $listing_data.ID|in_array:$compare_cookie_ids}{$lang.compare_remove_from_compare}{else}{$lang.compare_add_to_compare}{/if}</a>
		<a href="javascript:void(0)">
			<img src="{$rlTplBase}img/blank.gif" alt="" />
		</a>
	</span>
</li>

<script type="text/javascript">
{literal}

$(document).ready(function(){
	$('li.compare-icon a').click(function(){
		flCompare.action($(this).parent(), true);
	});
});

{/literal}
</script>

<!-- compare icon on listing details end -->