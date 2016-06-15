<!-- edit_banner tpl -->

{assign var='sPost' value=$smarty.post}

<div class="highlight clear">
	{if $sPost.banner_type == 'html'}
	<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}ckeditor/ckeditor.js"></script>
	{/if}

	<div class="area_form step_area">
		<form id="banners-form" onsubmit="return submit_photo_step();" method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?id={$bannerData.ID}{else}?page={$pageInfo.Path}&amp;id={$bannerData.ID}{/if}" enctype="multipart/form-data">
		<input type="hidden" name="submit_form" value="1" />
		<input type="submit" class="hide" name="submit_hack" value="1" />
		<table class="submit">
		<tr>
			<td class="name"><span class="red">*</span> {$lang.name}</td>
			<td class="field">
				{if $languages|@count > 1}
					<div class="ml_tabs">
						<ul>
						{foreach from=$languages item='language' name='langF'}
							<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
						{/foreach}
						</ul>
						<div class="nav left"></div>
						<div class="nav right"></div>
					</div>
					<div class="ml_tabs_content">
						{foreach from=$languages item='language' name='langF'}
						{assign var='lCode' value=$language.Code}
						<div lang="{$lCode}" {if !$smarty.foreach.langF.first}class="hide"{/if}>
							<input class="w350" type="text" name="name[{$lCode}]" value="{$sPost.name.$lCode}" /> <span>{$language.name}</span>
						</div>
						{/foreach}
					</div>
				{else}
					<input class="w350" type="text" name="name" value="{$sPost.name}" />
				{/if}
			</td>
		<tr>
			<td class="name">{$lang.banners_bannerBox}</td>
			<td class="field">
				<input type="text" class="disabled" value="{$bannerData.Box.name}" disabled="disabled" />
				<label id="banner_box_qt">
					{$lang.banners_bannerBoxNotice|replace:"[side]":$bannerData.Box.side|replace:"[width]":$bannerData.Box.width|replace:"[height]":$bannerData.Box.height}
				</label>
			</td>
		</tr>
		<tr>
			<td class="name">{$lang.banners_bannerType}</td>
			<td class="field">
				<input type="hidden" name="banner_type" value="{$bannerData.Type.key}" />
				<input type="text" class="disabled" value="{$bannerData.Type.name}" disabled="disabled" />
			</td>
		</tr>
		<tr id="b_link" {if $sPost.banner_type != 'image'}class="hide"{/if}>
			<td class="name">{$lang.banners_bannerLink}</td>
			<td class="field">
				<input type="text" name="link" value="{$sPost.link}" />
			</td>
		</tr>
		<tr id="btype_html" {if $sPost.banner_type != 'html'}class="hide"{/if}>
			<td class="name"><span class="red">*</span> {$lang.banners_bannerType_html}</td>
			<td class="field">
				<div class="hide">{$sPost.html}</div>
				<textarea id="banner_html" name="html" rows="3" cols="">{$sPost.html}</textarea>
			</td>
		</tr>

		{if $sPost.banner_type == 'flash'}
		<tr>
			<td class="name"><span class="red">*</span> {$lang.file}:</td>
			<td class="field">
			
				<div id="banner_flash_upload" {if $bannerData.Image}class="hide"{/if}>
					<input type="file" name="flash_file" id="flash_file" />
					<table class="grey_small">
					<tr>
						<td>{$lang.max_file_size}:</td>
						<td style="padding-left: 5px;"><em><b>{$max_file_size} MB</b></em></td>
					</tr>
					<tr>
						<td>{$lang.available_file_type}:</td>
						<td style="padding-left: 5px;"><b><em>swf</em></b></td>
					</tr>
					</table>
				</div>

				{if $bannerData.Image}
				<div id="fileupload" style="padding:0;padding-bottom:10px;">
					<span class="item active">
						<object width="{$bannerData.Box.width}" height="{$bannerData.Box.height}" data="{$smarty.const.RL_FILES_URL}banners/{$bannerData.Image}" type="application/x-shockwave-flash">
							<param value="{$smarty.const.RL_FILES_URL}banners/{$bannerData.Image}" name="movie">
							<param value="opaque" name="transparent">
							<param name="allowscriptaccess" value="samedomain">
							<param value="direct_link=true" name="flashvars">
							<embed width="{$bannerData.Box.width}" height="{$bannerData.Box.height}" flashvars="direct_link=true" wmode="transparent" src="{$smarty.const.RL_FILES_URL}banners/{$bannerData.Image}">
						</object>
						<img src="{$rlTplBase}img/blank.gif" class="cancel" alt="{$lang.delete}" title="{$lang.delete}" />
					</span>
				</div>
				{/if}
			</td>
		</tr>
		{/if}
		</table>
		</form>

		{if $sPost.banner_type == 'image'}
			{include file=$smarty.const.RL_PLUGINS|cat:'banners'|cat:$smarty.const.RL_DS|cat:'upload'|cat:$smarty.const.RL_DS|cat:'account_manager.tpl' boxInfo=$bannerData.Box}
		{/if}

		<table class="submit">
		<tr>
			<td class="name"></td>
			<td class="field button">
				<input type="submit" id="banners_submit_button" value="{$lang.save}" id="checkout_submit" />
			</td>
		</tr>
		</table>
	</div>

	<script type="text/javascript">
		lang['banners_errorSelectFlashFile'] = '{$lang.banners_errorSelectFlashFile}';
		lang['banners_errorFormatFlashFile'] = '{$lang.banners_errorFormatFlashFile}';
		var flashFile = '{$bannerData.Image}';

		{if $sPost.banner_type == 'html'}
			flynax.htmlEditor(['banner_html']);
		{/if}
		flynax.mlTabs();

		{literal}
		$(document).ready(function() {
			$('input#banners_submit_button').click(function() {
				$('form#banners-form').submit();
			});

			$('#fileupload img.cancel').click(function() {
				xajax_bannersRemoveFlash(flashFile);
			});
		});

		//
		if ( submit_photo_step == undefined ) {
			var submit_photo_step = function() {
				{/literal}{if !$bannerData.Image}{literal}
					var flashFile = $.trim($('input#flash_file').val());
					if ( flashFile.length === 0 ) {
						printMessage('error', lang['banners_errorSelectFlashFile']);
						return false;
					}

					if ( flashFile.length !== 0 && flashFile.split('.').pop() != 'swf' ) {
						printMessage('error', lang['banners_errorFormatFlashFile']);
						return false;
					}
				{/literal}{/if}{literal}
				return true;
			}
		}
		{/literal}
	</script>
</div>

<!-- edit_banner tpl end -->