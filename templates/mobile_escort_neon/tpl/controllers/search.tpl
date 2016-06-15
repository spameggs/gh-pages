<!-- search tpl -->

<!-- print search form -->
{if $search_forms}

	<!-- tabs -->
	<div class="tabs">
		<ul>
			{if $search_forms|@count > 0}
				{foreach from=$search_forms item='tab' key='tab_key' name='tabsF'}
					{assign var='tab_phrase' value='listing_types+name+'|cat:$listing_types[$tab_key].Key}
					<li class="{if $smarty.foreach.tabsF.first}first{/if}{if $smarty.foreach.tabsF.first && !$keyword_search} active{/if}" lang="{$tab_key}" id="tab_{$tab_key|replace:'_':''}">
						<span class="center"><span>{$lang[$tab_phrase]}</span></span>
					</li>
				{/foreach}
			{/if}
			
			{assign var='ks_phrase' value='blocks+name+keyword_search'}
			<li class="{if $keyword_search} active{/if}" lang="keyword" id="tab_keyword">
				<span class="center"><span>{$lang.$ks_phrase}</span></span>
			</li>
		</ul>
	</div>
	<div class="clear"></div>
	<!-- tabs end -->
	
	{foreach from=$search_forms item='search_form' key='sf_key' name='sformsF'}
		{assign var='spage_key' value=$listing_types[$sf_key].Page_key}
		
		<div id="area_{$sf_key|replace:'_':''}" class="tab_area{if !$smarty.foreach.sformsF.first || $keyword_search} hide{/if}">
			<form method="{$listing_types[$sf_key].Submit_method}" action="{$rlBase}{if $config.mod_rewrite}{$pages.$spage_key}/{$search_results_url}.html{else}?page={$pages.$spage_key}&amp;{$search_results_url}{/if}">
				{foreach from=$search_form item='group' name='qsearchF'}
					{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fields_search.tpl' fields=$group.Fields}
				{/foreach}
				
				<div class="padding" style="padding-top: 15px;">
					<div class="field">
						{$lang.sort_listings_by}
					</div>
			
					<select name="f[sort_by]">
						<option value="0">{$lang.select}</option>
						{foreach from=$search_form item='item'}
							{assign var='field' value=$item.Fields.0}
							{if $field.Type != 'checkbox'}
								<option value="{$field.Key}" {if $smarty.post.sort_field == $field.Key}selected{/if}>{$lang[$field.pName]}</option>
							{/if}
						{/foreach}
					</select>
					
					<select name="f[sort_type]">
						<option value="asc">{$lang.ascending}</option>
						<option value="desc" {if $smarty.post.sort_type == 'desc'}selected{/if}>{$lang.descending}</option>
					</select>
				</div>
				
				<div class="padding" style="padding-top: 10px;">
					<input class="tall" type="submit" name="search" value="{$lang.search}" />
					<label><input style="margin-{$text_dir}: 20px;" type="checkbox" name="f[with_photo]" value="true" /> {$lang.with_photos_only}</label>
					
					{if $listing_types[$sf_key].Advanced_search}
						<div style="padding-top: 8px;"><a class="static" title="{$lang.advanced_search}" href="{$rlBase}{if $config.mod_rewrite}{$pages.$spage_key}/{$advanced_search_url}.html{else}?page={$pages.$spage_key}&amp;{$advanced_search_url}{/if}">{$lang.advanced_search}</a></div>
					{/if}
				</div>
				
				<input type="hidden" name="action" value="search" />
				{assign var='post_form_key' value=$sf_key|cat:'_quick'}
				<input type="hidden" name="post_form_key" value="{$post_form_key}" />
			</form>
		</div>
	{/foreach}
	
{/if}
<!-- print search form -->

<!-- keyword search tab -->
<div id="area_keyword" class="tab_area{if !$keyword_search} hide{/if}">
	<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pages.search}.html{else}?page={$pages.search}{/if}">
		<input type="hidden" name="form" value="keyword_search" />
	
		<div id="qucik_search">
			<input type="text" name="f[keyword_search]"  maxlength="255" {if $smarty.post.f.keyword_search}value="{$smarty.post.f.keyword_search}"{/if} />
		</div>
	
		<div class="keyword_search_opt" style="display: block;">
			<div>
				{assign var='tmp' value=3}
				{section name='keyword_opts' loop=$tmp max=3}
					<label><input {if $fVal.keyword_search_type || $keyword_mode}{if $smarty.section.keyword_opts.iteration == $fVal.keyword_search_type || $keyword_mode == $smarty.section.keyword_opts.iteration}checked="checked"{/if}{else}{if $smarty.section.keyword_opts.iteration == 2}checked="checked"{/if}{/if} value="{$smarty.section.keyword_opts.iteration}" type="radio" name="f[keyword_search_type]" /> {assign var='ph' value='keyword_search_opt'|cat:$smarty.section.keyword_opts.iteration}{$lang.$ph}</label>
				{/section}
			</div>
		</div>
	</form>

	{if !empty($listings)}
		{if $sorting}
			<form method="get" action="">
				<div class="sorting">
					<select name="sort_by" class="default w90">
						<option value="">{$lang.select}</option>
						{foreach from=$sorting item='field_item' key='sort_key' name='fSorting'}
							{if $field_item.Key != 'state'} 
								<option value="{$sort_key}" {if $smarty.get.sort_by == $sort_key}selected="selected"{/if}>{$field_item.name}</option>
							{/if}
						{/foreach}
					</select>
					<select name="sort_type" class="default w110">
						<option value="asc">{$lang.ascending}</option>
						<option value="desc" {if $smarty.get.sort_type == 'desc'}selected="selected"{/if}>{$lang.descending}</option>
					</select>
					<input type="submit" name="submit" value="{$lang.sort}" />
				</div>
			</form>
		{/if}
	
		<!-- listings -->
		<div id="listings">
			{if !empty($listings)}
				<ul>
					{foreach from=$listings item='listing' key='key' name='listingsF'}
						{include file='blocks'|cat:$smarty.const.RL_DS|cat:'listing.tpl'}
					{/foreach}
				</ul>
			{/if}
		</div>
		<!-- listings end -->
		
		<!-- paging block -->
		{if $config.mod_rewrite}
			{paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page url=$category.Path var='listing'}
		{else}
			{paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page url=$category.ID var='category'}
		{/if}
		<!-- paging block end -->
	
	{else}
	
		{if $keyword_search}
			<div class="padding">{$lang.no_listings_found_deny_posting}</div>
		{/if}
		
	{/if}
</div>
<!-- keyword search tab end -->

<script type="text/javascript">
{literal}

$(document).ready(function(){
	if ( flynax.getHash() )
	{
		$('div.tabs li[lang='+flynax.getHash()+']').trigger('click');
	}
});

{/literal}
</script>

<!-- search tpl end -->