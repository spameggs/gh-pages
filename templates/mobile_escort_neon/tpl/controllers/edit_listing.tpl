<!-- edit listing -->

{rlHook name='editListingTop'}

<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.ui.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/numeric.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.qtip.js"></script>
<script type="text/javascript">flynax.qtip(); flynax.phoneField();</script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.textareaCounter.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}ckeditor/ckeditor.js"></script>

<div class="highlight clear">

	<!-- listing fieldset -->
	{if !empty($form)}
	
	<div class="caption">{$listing_title}</div>
	
	<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?id={$smarty.get.id}{else}?page={$pageInfo.Path}&amp;id={$smarty.get.id}{/if}" enctype="multipart/form-data">
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="fromPost" value="1" />
		
		<!-- plan info -->
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='plan_information' name=$lang.plan_detals}
			<table class="submit">
			<tr>
				<td class="name_top">{$lang.plan}:</td>
				<td class="field">{$plan_info.name} / <a class="brown_12" href="{$rlBase}{if $config.mod_rewrite}{$pages.upgrade_listing}.html?id={$smarty.get.id}{else}?page={$pages.upgrade_listing}&amp;id={$smarty.get.id}{/if}">{$lang.change}</a></td>
			</tr>
			<tr>
				<td class="name_top">{$lang.category}:</td>
				<td class="field">
					<span id="change_category_origin">
						<span>{$category.name}</span> {if $categories_list}/ <a class="brown_12" href="javascript:void(0);">{$lang.change}</a>{/if}
					</span>
					{if $categories_list}
						<span id="change_category_options" class="hide">						
							<select name="edit_category">
								{foreach from=$categories_list item='option'}
									<option {if $option.ID == $category.ID}selected="selected"{/if} {if $option.Lock}disabled="disabled"{/if} style="padding-{$text_dir}: {$option.margin}px;" value="{$option.ID}">{$lang[$option.pName]}{if $option.Lock} ({$lang.locked}){/if}</option>
								{/foreach}
							</select>
							
							<input id="apply_category_changes" class="low" type="button" value="{$lang.change}" />
							<span class="cancel">{$lang.cancel}</span>
						</span>
						
						<script type="text/javascript">
						{literal}
						
						$(document).ready(function(){
							$('#change_category_origin a').click(function(){
								$('#change_category_origin').fadeOut('fast', function(){
									$('#change_category_options').fadeIn();
								});
							});
							$('#change_category_options span.cancel').click(function(){
								$('#change_category_options').fadeOut('fast', function(){
									$('#change_category_origin').fadeIn();
									$('select[name=edit_category] option[selected=selected]').attr('selected', true);
								});
							});
							
							$('#apply_category_changes').flModal({
								caption: '{/literal}{$lang.notice}{literal}',
								content: '{/literal}{$lang.notice_change_listing_category}{literal}',
								prompt: 'xajax_changeListingCategory({/literal}{$listing_data.ID}{literal}, $("select[name=edit_category] option:selected").val());$("#apply_category_changes").val("'+lang['loading']+'");',
								width: 'auto',
								height: 'auto'
							});
						});
						
						{/literal}
						</script>
					{/if}
				</td>
			</tr>
			{if $plan_info.Image || $plan_info.Image_unlim}
			<tr>
				<td class="name_top">{$lang.listing_photos}:</td>
				<td class="field">{if $plan_info.Image_unlim}{$lang.unlimited}{else}{$plan_info.Image}{/if} / <a class="brown_12" href="{$rlBase}{if $config.mod_rewrite}{$pages.add_photo}.html?id={$smarty.get.id}{else}?page={$pages.add_photo}&amp;id={$smarty.get.id}{/if}">{$lang.manage_photos}</a></td>
			</tr>
			{/if}
			{if $plan_info.Video || $plan_info.Video_unlim}
			<tr>
				<td class="name_top">{$lang.listing_video}:</td>
				<td class="field">{if $plan_info.Video_unlim}{$lang.unlimited}{else}{$plan_info.Video}{/if} / <a class="brown_12" href="{$rlBase}{if $config.mod_rewrite}{$pages.add_video}.html?id={$smarty.get.id}{else}?page={$pages.add_video}&amp;id={$smarty.get.id}{/if}">{$lang.manage_video}</a></td>
			</tr>
			{/if}
			</table>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
		<!-- plan info end -->
		
		{rlHook name='editListingPreFields'}
		
		{foreach from=$form item='group'}
		{if $group.Group_ID}
			{if $group.Fields && $group.Display}
				{assign var='hide' value=false}
			{else}
				{assign var='hide' value=true}
			{/if}

			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id=$group.Key name=$lang[$group.pName]}
			{if $group.Fields}
				{include file='blocks'|cat:$smarty.const.RL_DS|cat:'field.tpl' fields=$group.Fields}
			{else}
				{$lang.no_items_in_group}
			{/if}
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
		{else}
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'field.tpl' fields=$group.Fields}
		{/if}
		{/foreach}
		
		{if $config.security_img_edit_listing}
			{include file='captcha.tpl'}
		{/if}
			
		<div class="button"><input type="submit" value="{$lang.edit}" /></div>
		
	</form>
	
	{/if}
	<!-- listing fieldset end -->
	
</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
	$("input.numeric").numeric();
	
	flynax.mlTabs();
	{/literal}{if $config.address_on_map}flynax.onMapHandler();{/if}{literal}
});
{/literal}
</script>

{rlHook name='editListingBottomTpl'}

<!-- edit listing end -->