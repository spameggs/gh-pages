<!-- search form, re-assigning fields for template -->
{foreach from=$fields item='group' name='qsearchF'}
	{if $group.Fields.0.Key != 'Category_ID'}
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fields_search.tpl' fields=$group.Fields}
	{/if}
{/foreach}
<!-- search form, re-assigning fields for template end -->