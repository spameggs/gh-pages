<!-- dom tpl -->
{math assign='column_limit' equation='ceil(total/2)' total=$total}

<div class="left"><div>
{foreach from=$testimonials item='testimonial' name='testimonialsF'}
	{include file=$smarty.const.RL_PLUGINS|cat:'testimonials'|cat:$smarty.const.RL_DS|cat:'item.tpl'}
	{if $column_limit == $smarty.foreach.testimonialsF.iteration}
		</div></div>
		<div class="right"><div>
	{/if}
{/foreach}
</div></div>
<div class="clear"></div>
<!-- dom tpl -->