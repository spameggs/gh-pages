<!-- listing rating DOM -->
{math assign='average_rating' equation='round(rating/votes, 1)' rating=$listing_data.lr_rating votes=$listing_data.lr_rating_votes}
{math assign='rating_rest' equation='(av_rating - floor(av_rating))*100+5' av_rating=$average_rating}
{assign var='star' value=`$smarty.ldelim`number`$smarty.rdelim`}
<ul class="lising_rating_ul{if !$rating_denied && (($config.rating_prevent_visitor && $isLogin) || !$config.rating_prevent_visitor) && (!$config.rating_prevent_owner || ($config.rating_prevent_owner && $listing_data.Account_ID != $account_info.ID))} listing_rating_available{/if}">
{section name='ratingS' start=0 loop=$config.rating_stars_number}<li title="{if $config.rating_prevent_visitor && !$isLogin}{$lang.rating_prevent_visitor}{elseif $rating_denied}{$lang.rating_static}{elseif $config.rating_prevent_owner && $listing_data.Account_ID == $account_info.ID}{$lang.rating_prevent_owner}{else}{$lang.rating_set|replace:$star:$smarty.section.ratingS.iteration}{/if}" {if $smarty.section.ratingS.iteration <= $average_rating}class="active"{/if}>{if $average_rating|ceil == $smarty.section.ratingS.iteration}<div style="width: {$rating_rest}%;"></div>{/if}</li>{/section}
</ul>
<ul>
	<li><span class="name">{$lang.rating_current_rating}:</span> {$average_rating}</li>
	<li><span class="name">{$lang.rating_total_votes}:</span> {$listing_data.lr_rating_votes}</li>
</ul>
<!-- listing rating DOM end -->