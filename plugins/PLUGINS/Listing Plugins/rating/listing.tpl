<!-- listing rating -->
{if $listing.lr_rating}
	{math assign='average_rating' equation='round(rating/votes, 1)' rating=$listing.lr_rating votes=$listing.lr_rating_votes}
	{math assign='rating_rest' equation='(av_rating - floor(av_rating))*100+5' av_rating=$average_rating}
	{assign var='star' value=`$smarty.ldelim`number`$smarty.rdelim`}
	<ul class="lising_rating {if $smarty.const.RL_LANG_DIR == rtl}lising_rating_rtl{/if}">
	{section name='ratingS' start=0 loop=$config.rating_stars_number}<li title="{$lang.rating_rating}: {$average_rating}" {if $smarty.section.ratingS.iteration <= $average_rating}class="active"{/if}>{if $average_rating|ceil == $smarty.section.ratingS.iteration}<div style="width: {$rating_rest}%;"></div>{/if}</li>{/section}
	</ul>
{/if}
<!-- listing rating end -->