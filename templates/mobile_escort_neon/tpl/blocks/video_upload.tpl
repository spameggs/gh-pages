<!-- file uploader -->

{if $video_allow && !$plan_info.Video_unlim}
	{assign var='replace' value=`$smarty.ldelim`number`$smarty.rdelim`}
	{assign var='video_left' value=$lang.upload_video_left|replace:$replace:$video_allow}
{else}
	{assign var='video_left' value=$lang.upload_video}
{/if}

{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='uploadVideo' name=$video_left tall=true}
	{if $video_allow || $plan_info.Video_unlim}
		<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}{if $pageInfo.Key == 'add_video'}.html?id={$smarty.get.id}{else}/{$category.Path}/{$steps.$cur_step.path}.html{/if}{else}?page={$pageInfo.Path}{if $pageInfo.Key == 'add_video'}&amp;id={$smarty.get.id}{else}&amp;id={$category.ID}&amp;step={$steps.$cur_step.path}{/if}{/if}" enctype="multipart/form-data">
			<input name="step" value="video" type="hidden" />
			<div class="name">{$lang.video_type}:</div>
			<select id="video_type" name="type" >
				<option value="">{$lang.select}</option>
				<option {if $smarty.post.type == 'youtube'}selected="selected"{/if} value="youtube">{$lang.youtube}</option>
				<option {if $smarty.post.type == 'local'}selected="selected"{/if} value="local">{$lang.local}</option>
			</select>
			
			<div id="local_video" class="hide upload">
				<div class="name">{$lang.file}:</div>
				<input class="file" type="file" name="video" accept="video/*"  />
				<table class="grey_small">
				<tr>
					<td>{$lang.max_file_size}:</td>
					<td style="padding-left: 5px;"><em><b>{$max_file_size}</b></em></td>
				</tr>
				<tr>
					<td>{$lang.available_file_type}:</td>
					<td style="padding-left: 5px;">
						{foreach from=$l_player_file_types item=item key='f_type' name='file_typesF'}
						<b><em>{$f_type}</em></b>{if !$smarty.foreach.file_typesF.last},{/if}
						{/foreach}
					</td>
				</tr>
				</table>
					
				<div class="name">{$lang.preview_image}:</div>
				<input class="file" type="file" name="preview" accept="image/*" />
			</div>
			
			<div id="youtube_video" class="hide upload">
				<div class="name">{$lang.link_or_embed}:</div>
				<textarea cols="" rows="4" name="youtube_embed">{$smarty.post.youtube_embed}</textarea>
			</div>
			
			<div class="button"><input class="button" type="submit" name="finish" value="{$lang.upload}" /></div>
		</form>
	{else}
		{assign var='replace_count' value=`$smarty.ldelim`count`$smarty.rdelim`}
		{assign var='replace_plan' value=`$smarty.ldelim`plan`$smarty.rdelim`}
		<div class="dark">{$lang.no_more_videos|replace:$replace_count:$plan_info.Plan_video|replace:$replace_plan:$plan_info.name}</div>
	{/if}
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}

<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.ui.js"></script>
<script type="text/javascript">
	flynax.uploadVideoUI();
</script>

<!-- file uploader end -->