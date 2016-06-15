{if $listing.booking_module}
	{if $config.booking_binding_plans}
		{if in_array($listing.Plan_ID, ","|explode:$config.booking_plans)}
			{assign var='show_calendar' value='1'}
		{else}
			{assign var='show_calendar' value='0'}
		{/if}
	{else}
		{assign var='show_calendar' value='1'}
	{/if}
{else}
	{assign var='show_calendar' value='0'}
{/if}

{if $show_calendar}
<a href="{$rlBase}{if $config.mod_rewrite}{$pages.booking_details}.html?id={$listing.ID}{else}?page={$pages.booking_details}&amp;id={$listing.ID}{/if}">
	<img title="{$lang.booking_page_details}" alt="{$lang.booking_page_details}" src="{$smarty.const.RL_PLUGINS_URL}booking/img/listing_ico.png" />
</a>
{/if}