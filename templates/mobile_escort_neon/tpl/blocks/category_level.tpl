<!-- category level -->

{if $category.Type}
	{assign var='cat_type' value=$category.Type}
{else}
	{assign var='cat_type' value=$section.Type}
{/if}

{assign var='replace' value=`$smarty.ldelim`category`$smarty.rdelim`}

<select {if $section.Key}class="section_{$section.Key}"{/if} {if $category.ID}id="tree_area_{$category.ID}"{/if}>
	<option value="">{$lang.select}</option>
{foreach from=$categories item='cat' name='catF'}
	{if !empty($cat.Sub_cat) || ($cat.Add == '1' && $listing_types[$cat_type].Cat_custom_adding)}
		{assign var='sub_leval' value=true}
	{else}
		{assign var='sub_leval' value=false}
	{/if}
		
	<option {if $cat.Lock && !$sub_leval}disabled="disabled"{/if} class="{if !$sub_leval}no_child{/if} {if $cat.Lock}disabled{/if}" id="tree_cat_{$cat.ID}" value="{if $cat.Lock}javascript:void(0);{else}{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{if $cat.Tmp}tmp-category{else}{$cat.Path}{/if}/{$steps.plan.path}.html{if $cat.Tmp}?tmp_id={$cat.ID}{/if}{else}?page={$pageInfo.Path}&amp;step={$steps.plan.path}&amp;{if $cat.Tmp}tmp_id{else}id{/if}={$cat.ID}{/if}{/if}">{$cat.name} {if $cat.Lock}- {$lang.locked}{/if}</option>
{/foreach}
</select>

<!-- category level end -->