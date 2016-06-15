<!-- add banner -->

{if !$no_access}

<!-- steps -->
<table class="steps">
<tr>
	{assign var='allow_link' value=true}
	{foreach from=$bSteps item='step' name='stepsF' key='step_key'}
		{if $curStep == $step_key || !$curStep}{assign var='allow_link' value=false}{/if}
		<td id="step_{$step_key}" class="{if $smarty.foreach.stepsF.first}active{/if}{if !$show_step_caption && $smarty.foreach.stepsF.last} last{/if}">
			<div>
				<a href="{if $allow_link}{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$bSteps.$step_key.path}.html{else}?page={$pageInfo.Path}&amp;step={$bSteps.$step_key.path}{/if}{else}javascript:void(0){/if}" title="{$step.name}">
					{if $step.caption}<b>{$smarty.foreach.stepsF.iteration}</b>{if $show_step_caption}. {$step.name}{/if}{else}{$step.name}{/if}
				</a>
			</div>
		</td>
	{/foreach}
</tr>
</table>
<!-- steps -->

{assign var='sPost' value=$smarty.post}

<div class="highlight clear">

{if $curStep == 'plan'}
<!-- select a plan -->
<div class="area_plan step_area">
	<div class="caption">{$lang.select_plan}</div>

	<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$bSteps.$curStep.path}.html{else}?page={$pageInfo.Path}&amp;step={$bSteps.$curStep.path}{/if}">
		<input type="hidden" name="step" value="plan" />

		{include file=$smarty.const.RL_PLUGINS|cat:'banners'|cat:$smarty.const.RL_DS|cat:'banner_plans.tpl'}

		<table class="submit">
		<tr>
			<td class="field button"><span class="arrow"><input type="submit" value="{$lang.next_step}" id="plans_submit" /><label for="plans_submit" class="right">&nbsp;</label></span></td>
		</tr>
		</table>
	</form>
</div>
<!-- select a plan end -->

{elseif $curStep == 'form'}

<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}ckeditor/ckeditor.js"></script>
<div class="area_form step_area hide">
	<div class="caption">{$lang.fill_out_form}</div>

	<form id="banners-form" method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$bSteps.$curStep.path}.html{else}?page={$pageInfo.Path}&amp;step={$bSteps.$curStep.path}{/if}">
	<input type="hidden" name="step" value="form" />
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
		<td class="name"><span class="red">*</span> {$lang.banners_bannerBox}</td>
		<td class="field">
			<select name="banner_box">
			{foreach from=$planInfo.boxes item='box' name='fBox'}
				{if $sPost.banner_box == $box.Key}
					{assign var='sBox' value=$box}
				{else}
					{if $smarty.foreach.fBox.first}
						{assign var='sBox' value=$box}
					{/if}
				{/if}
				<option {if $sBox.Key == $box.Key}selected="selected"{/if} value="{$box.Key}" info="{$box.side}:{$box.width}:{$box.height}">{$box.name}</option>
			{/foreach}
			</select>
			<label id="banner_box_qt">
				{$lang.banners_bannerBoxNotice|replace:"[side]":$sBox.side|replace:"[width]":$sBox.width|replace:"[height]":$sBox.height}
			</label>
		</td>
	</tr>
	<tr>
		<td class="name"><span class="red">*</span> {$lang.banners_bannerType}</td>
		<td class="field">
			<select name="banner_type">
			{foreach from=$planInfo.types item='type'}
				<option {if $sPost.banner_type == $type.Key}selected="selected"{/if} value="{$type.Key}">{$type.name}</option>
			{/foreach}
			</select>
		</td>
	</tr>
	<tr id="b_link" {if $sPost.banner_type && $sPost.banner_type != 'image'}class="hide"{/if}>
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
	</table>

	<table class="submit">
	<tr>
		<td class="name">
			<a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$bSteps.plan.path}.html{else}?page={$pageInfo.Path}&amp;step={$bSteps.plan.path}{/if}" class="dark_12">
				{if $smarty.const.RL_LANG_DIR == 'ltr'}&larr;{else}&rarr;{/if} {$lang.perv_step}
			</a>
		</td>
		<td class="field button"><span class="arrow"><input type="submit" value="{$lang.next_step}" id="form_submit" /><label for="form_submit" class="right">&nbsp;</label></span></td>
	</tr>
	</table>

	</form>
</div>

<script type="text/javascript">
{literal}

	function bannerTypeChange(from, to, step) {
		$('#banners-form tr#'+ from).fadeOut('fast', function() {
			$('#banners-form tr#'+ to).fadeIn('normal');
		});

		if ( step ) {
			$('#step_media').fadeIn('fast')
		}
		else {
			$('#step_media').fadeOut('fast')
		}
	}

	if ( $('select[name=banner_type]').val() == 'html' ) {
		bannerTypeChange('b_link', 'btype_html', 0);
	}

	$(document).ready(function() {
		$('select[name=banner_box]').change(function() {
			var box = $(this).find('option:selected').attr('info').split(':');
			var noticeText = '{/literal}{$lang.banners_bannerBoxNotice|escape:"quotes"}{literal}'.replace('[side]', box[0]).replace('[width]', box[1]).replace('[height]', box[2]);
			$('label#banner_box_qt').html(noticeText);
		});

		$('select[name=banner_type]').change(function() {
			if ( $(this).val() == 'html' ) {
				bannerTypeChange('b_link', 'btype_html', 0);
			}
			else if ( $(this).val() == 'flash' ) {
				$('#banners-form tr#btype_html').fadeOut('fast');
				$('#banners-form tr#b_link').fadeOut('fast');
			}
			else {
				bannerTypeChange('btype_html', 'b_link', 1);
			}
		});
	});

{/literal}
</script>

{elseif $curStep == 'media'}

<!-- upload -->
<div class="area_media step_area hide">
	<div class="caption">{$lang.banners_addBannerContent}</div>

	{if $boxInfo.type == 'image'}
		{include file=$smarty.const.RL_PLUGINS|cat:'banners'|cat:$smarty.const.RL_DS|cat:'upload'|cat:$smarty.const.RL_DS|cat:'account_manager.tpl'}
	{/if}

	<form method="post" onsubmit="return submit_photo_step('{$boxInfo.type}');" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$bSteps.$curStep.path}.html{else}?page={$pageInfo.Path}&amp;step={$bSteps.$curStep.path}{/if}" enctype="multipart/form-data">
		<input type="hidden" name="step" value="media" />
		<input type="hidden" name="type" value="{$boxInfo.type}" />
		<table class="submit">
		{if $boxInfo.type == 'flash'}
		<tr>
			<td class="name"><span class="red">*</span> {$lang.file}:</td>
			<td class="field">

				<div id="banner_flash_upload" {if $bannerData.Image && $bannerData.Image != 'html'}class="hide"{/if}>
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

				{if $bannerData.Image && $bannerData.Image != 'html'}
				<div id="fileupload" style="padding:0;padding-bottom:10px;">
					<span class="item active">
						<object width="{$boxInfo.width}" height="{$boxInfo.height}" data="{$smarty.const.RL_FILES_URL}banners/{$bannerData.Image}" type="application/x-shockwave-flash">
							<param value="{$smarty.const.RL_FILES_URL}banners/{$bannerData.Image}" name="movie">
							<param value="opaque" name="transparent">
							<param name="allowscriptaccess" value="samedomain">
							<param value="direct_link=true" name="flashvars">
							<embed width="{$boxInfo.width}" height="{$boxInfo.height}" flashvars="direct_link=true" wmode="transparent" src="{$smarty.const.RL_FILES_URL}banners/{$bannerData.Image}">
						</object>
						<img src="{$rlTplBase}img/blank.gif" class="cancel" alt="{$lang.delete}" title="{$lang.delete}" />
					</span>
				</div>
				{/if}
			</td>
		</tr>
		{/if}

		<tr>
			<td class="name button">
				<a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$prev_step.path}.html{else}?page={$pageInfo.Path}&amp;step={$prev_step.path}{/if}" class="dark_12">{if $smarty.const.RL_LANG_DIR == 'ltr'}&larr;{else}&rarr;{/if} {$lang.perv_step}</a>
			</td>
			<td class="field button">
				<span class="arrow"><input type="submit" value="{$lang.next_step}" id="photo_submit" /><label for="photo_submit" class="right">&nbsp;</label></span>
			</td>
		</tr>
		</table>
	</form>

	{if $boxInfo.type == 'flash'}
	<script type="text/javascript">
	lang['banners_errorSelectFlashFile'] = '{$lang.banners_errorSelectFlashFile}';
	lang['banners_errorFormatFlashFile'] = '{$lang.banners_errorFormatFlashFile}';
	var flashFile = '{$bannerData.Image}';

	{literal}
		function submit_photo_step(type) {
			if ( type == 'flash' ) {
				var flashFile = $.trim($('input#flash_file').val());
				if ( flashFile.length === 0 ) {
					printMessage('error', lang['banners_errorSelectFlashFile']);
					return false;
				}

				if ( flashFile.length !== 0 && flashFile.split('.').pop() != 'swf' ) {
					printMessage('error', lang['banners_errorFormatFlashFile']);
					return false;
				}
			}
		}

		$(document).ready(function() {
			$('#fileupload img.cancel').click(function() {
				xajax_bannersRemoveFlash(flashFile);
			});
		});
	{/literal}
	</script>
	{/if}
</div>
<!-- upload end -->

{elseif $curStep == 'checkout'}

<!-- checkout -->
<div class="area_checkout step_area ">
	<div class="caption">{$lang.checkout}</div>

	{if isset($smarty.get.canceled)}
		<script type="text/javascript">
			printMessage('error', '{$lang.bannersNoticePaymentCanceled}', 0, 1);
		</script>
	{/if}

	<div class="dark" style="padding-bottom: 5px;">{$lang.checkout_step_info}</div>

	<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$bSteps.$curStep.path}.html{else}?page={$pageInfo.Path}&amp;step={$bSteps.$curStep.path}{/if}">
		<input type="hidden" name="step" value="checkout" />

		<ul id="payment_gateways">
			{if $config.use_paypal}
			<li>
				<img alt="" src="{$smarty.const.RL_LIBS_URL}payment/paypal/paypal.png" />
				<p><input {if $smarty.post.gateway == 'paypal' || !$smarty.post.gateway}checked="checked"{/if} type="radio" name="gateway" value="paypal" /></p>
			</li>
			{/if}
			{if $config.use_2co}
			<li>
				<img alt="" src="{$smarty.const.RL_LIBS_URL}payment/2co/2co.png" />
				<p><input {if $smarty.post.gateway == '2co'}checked="checked"{/if} type="radio" name="gateway" value="2co" /></p>
			</li>
			{/if}

			{rlHook name='paymentGateway'}
		</ul>

		<table class="submit">
		<tr>
			<td class="name button"><a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$prev_step.path}.html{else}?page={$pageInfo.Path}&amp;step={$prev_step.path}{/if}" class="dark_12">{if $smarty.const.RL_LANG_DIR == 'ltr'}&larr;{else}&rarr;{/if} {$lang.perv_step}</a></td>
			<td class="field button"><span class="arrow"><input type="submit" value="{$lang.next_step}" id="checkout_submit" /><label for="checkout_submit" class="right">&nbsp;</label></span></td>
		</tr>
		</table>
	</form>

	<script type="text/javascript">
		flynax.paymentGateway();
	</script>
</div>
<!-- checkout end -->

{elseif $curStep == 'done'}

<!-- done -->
<div class="area_done step_area hide">
	<div class="caption">{$lang.reg_done}</div>

	<div class="info">
		{if $config.banners_auto_approval}
			{$lang.banners_noticeAfterBannerAdding}
		{else}
			{$lang.banners_noticeAfterBannerAddingPending}
		{/if}
	</div>
	<span class="dark">
		{assign var='replace' value='<a href="'|cat:$returnLink|cat:'">$1</a>'}
		{$lang.banners_addOneMoreBanner|regex_replace:'/\[(.*)\]/':$replace}
	</span>
</div>
<!-- done end -->

{/if}

<script type="text/javascript">
{if $curStep}
	flynax.switchStep('{$curStep}');
	flynax.mlTabs();
{/if}
</script>

{/if}

<!-- add banner end -->