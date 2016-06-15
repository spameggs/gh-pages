<!-- add listing -->
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.ui.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/numeric.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.qtip.js"></script>
<script type="text/javascript">flynax.qtip(); flynax.phoneField();</script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.textareaCounter.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}ckeditor/ckeditor.js"></script>
{rlHook name='addListingTopTpl'}
{if !$no_access} 
<!-- steps -->
<table class="steps">
  <tr> {assign var='allow_link' value=true}
    {foreach from=$steps item='step' name='stepsF' key='step_key'}
    {if $cur_step == $step_key || !$cur_step}{assign var='allow_link' value=false}{/if}
    <td id="step_{$step_key}" class="{if $smarty.foreach.stepsF.first}active{/if}{if !$show_step_caption && $smarty.foreach.stepsF.last} last{/if}"><div><a href="{if $allow_link}{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}{if $step_key == 'category'}.html?edit{else}/{$category.Path}/{$steps.$step_key.path}.html{/if}{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$steps.$step_key.path}{if $step_key == 'category'}&amp;edit{/if}{/if}{else}javascript:void(0){/if}" title="{$step.name}">{if $step.caption}<b>{$smarty.foreach.stepsF.iteration}</b>{if $show_step_caption}. {$step.name}{/if}{else}{$step.name}{/if}</a></div></td>
    {/foreach} </tr>
</table>
<!-- steps -->
<div class="highlight clear"> {if !$cur_step} 
  <!-- print sections/categories tree -->
  <div class="area_category step_area">
    <div class="caption">{$lang.select_category}</div>
    {if !$sections}
    {$lang.add_listing_deny}
    {else}
    <div style="padding-bottom: 12px;">{$lang.mobile_add_listing_notice}</div>
    <div class="tree"> {foreach from=$sections item='section'}
      {if !$section.Admin_only}
      {include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id=$section.ID name=$section.name}
      
      {if !empty($section.Categories)}
      {if $section.Categories|@count == 1 && !$section.Categories.0.Sub_cat} <a title="{$lang.add_listing_to|replace:$replace:$section.Categories.0.name}" href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$section.Categories.0.Path}/{$steps.plan.path}.html{if $section.Categories.0.Tmp}?tmp_id={$section.Categories.0.ID}{/if}{else}?page={$pageInfo.Path}&amp;step={$steps.plan.path}&amp;{if $section.Categories.0.Tmp}tmp_id{else}id{/if}={$section.Categories.0.ID}{/if}">{$section.Categories.0.name}</a> {else}
      <div id="type_section_{$section.Key}"> {include file='blocks'|cat:$smarty.const.RL_DS|cat:'category_level.tpl' categories=$section.Categories first=true} </div>
      <a href="" class="button hide">{$lang.select_category}</a> {/if}
      {else}
      {$lang.no_items_in_sections}
      {/if}
      
      {include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
      {/if}
      {/foreach} </div>
    <script type="text/javascript">
					{literal}
					$(document).ready(function(){
						mobileTreeLoadLevel();
					});
					var mobileTreeLoadLevel = function(tpl, callback, section){
						$('div.tree select').unbind('change').change(function(){
							/* clear next already created dropdowns */
							$(this).find('option:selected').attr('selected', 'selected');
							$(this).nextAll().remove();
							/* run xajax function */
							if ( $(this).find('option:selected').attr('id') )
							{
								var id = parseInt($(this).find('option:selected').attr('id').split('_')[2]);
								if ( !section )
								{
									var set_section = $(this).attr('class').replace('section_', '');
								}
								else
								{
									var set_section = $(this).attr('class') ? $(this).attr('class').replace('section_', '') : section;
								}
								xajax_getCatLevel(id, false, tpl, callback, '', set_section);
							}
							/* next button handler */
							if ( $(this).find('option:selected').hasClass('disabled') )
							{
								$(this).parent().next().fadeOut();
							}
							else
							{
								$(this).parent().next().fadeIn();
								$(this).parent().next().attr('href', $(this).val());
							}
						});
					};
					{/literal}
					</script> 
    {/if} </div>
  <!-- print sections/categories tree end --> 
  {else} 
  <!-- select a plan --> 
  {if $cur_step == 'plan'}
  <div class="area_plan step_area hide">
    <div class="caption">{$lang.select_plan}</div>
    <form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$category.Path}/{$steps.$cur_step.path}.html{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$steps.$cur_step.path}{/if}">
      <input type="hidden" name="step" value="plan" />
      <table class="plans">
        {foreach from=$plans item='plan' name='plansF'}
        <tr {if $plan.ID == $smarty.post.plan}class="active"{/if}> {assign var='item_disabled' value=false}
          {if $plan.Limit > 0 && $plan.Using == 0 && $plan.Using != ''}
          {assign var='item_disabled' value=true}
          {/if}
          <td class="radio"><input {if $item_disabled}disabled="disabled"{/if} id="plan_{$plan.ID}" type="radio" name="plan" value="{$plan.ID}" {if $plan.ID == $smarty.post.plan}checked="checked"{/if} /></td>
          <td class="label"><table class="bg{if $plan.Limit > 0 && $plan.Using == 0 && $plan.Using != ''} o60{/if}">
              <tr>
                <td class="left" {if $plan.Color}style="background-color: #{$plan.Color};"{/if}></td>
                <td class="center" {if $plan.Color}style="background-color: #{$plan.Color};"{/if}><div class="price">{if isset($plan.Listings_remains)}&rarr;{else}
                    {if $plan.Price > 0}
                    {if $config.system_currency_position == 'before'}{$config.system_currency}{/if}
                    {if $config.price_decimal == '.'}{$plan.Price}{else}{$plan.Price|replace:".":$config.price_decimal}{/if}
                    {if $config.system_currency_position == 'after'}{$config.system_currency}{/if}
                    {else}{$lang.free}{/if}{/if}</div>
                  <div class="type">{assign var='l_type' value=$plan.Type|cat:'_plan_short'}{$lang.$l_type}</div></td>
                <td class="right"><div class="relative">
                    <div {if $plan.Color}style="background-color: #{$plan.Color};"{/if}> {if $plan.Color}
                      <div class="tile" style="background-color: #{$plan.Color};"></div>
                      {/if}
                      <div class="bg"></div>
                    </div>
                  </div></td>
              </tr>
            </table></td>
          <td class="info {if $plan.Limit > 0 && $plan.Using == 0 && $plan.Using != ''} o60{/if}"><div class="title">{$plan.name} {if isset($plan.Listings_remains)}{$lang.available}{/if}</div>
            <ul class="features">
              <li class="period" title="{$lang.listing_live}">{if $plan.Listing_period}{$plan.Listing_period} {$lang.days}{else}{$lang.unlimited}{/if}</li>
              {if $plan.Image || $plan.Image_unlim}
              <li class="pics" title="{$lang.images_number}">{if $plan.Image_unlim}{$lang.unlimited}{else}{$plan.Image}{/if}</li>
              {/if}
              {if $plan.Video || $plan.Video_unlim}
              <li class="video" title="{$lang.number_of_videos}">{if $plan.Video_unlim}{$lang.unlimited}{else}{$plan.Video}{/if}</li>
              {/if}
            </ul>
            <div class="desc"> {if $plan.Limit > 0 && $plan.Using == 0 && $plan.Using != ''} <b>{$lang.plan_limit_using_deny}</b> {else}
              {if $plan.Advanced_mode}
              <div id="featured_option_{$plan.ID}" class="featured_option hide">
                <label>
                  <input class="{if $smarty.post.listing_type == 'standard' || !$smarty.post.listing_type}checked{/if}{if $plan.Package_ID && empty($plan.Standard_remains) && $plan.Standard_listings != 0} disabled{/if}" type="radio" name="listing_type" value="standard" />
                  {$lang.standard_listing} (<b>{if $plan.Standard_listings == 0}{$lang.unlimited}{else}{if isset($plan.Listings_remains)}{if empty($plan.Standard_remains)}{$lang.used_up}{else}{$plan.Standard_remains}{/if}{else}{$plan.Standard_listings}{/if}{/if}</b>) </label>
                <label>
                  <input class="{if $smarty.post.listing_type == 'featured'}checked{/if}{if $plan.Package_ID && empty($plan.Featured_remains) && $plan.Featured_listings != 0} disabled{/if}" type="radio" name="listing_type" value="featured" />
                  {$lang.featured_listing} (<b>{if $plan.Featured_listings == 0}{$lang.unlimited}{else}{if isset($plan.Listings_remains)}{if empty($plan.Featured_remains)}{$lang.used_up}{else}{$plan.Featured_remains}{/if}{else}{$plan.Featured_listings}{/if}{/if}</b>) </label>
              </div>
              {else}
              <div id="featured_option_{$plan.ID}" class="featured_option hide"> {$lang.listing_number} (<b>{if $plan.Listing_number == 0}{$lang.unlimited}{else}{if empty($plan.Listings_remains)}{$lang.used_up}{else}{$plan.Listings_remains}{/if}{/if}</b>) </div>
              {/if}
              {/if} </div></td>
        </tr>
        {/foreach}
      </table>
      <table class="submit button">
        <tr>
          <td class="name"><a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}.html?edit{else}?page={$pageInfo.Path}&amp;edit{/if}" class="dark_12">{if $smarty.const.RL_LANG_DIR == 'ltr'}&larr;{else}&rarr;{/if} {$lang.perv_step}</a></td>
          <td class="field"><span class="arrow">
            <input type="submit" value="{$lang.next_step}" id="plans_submit" />
            <label for="plans_submit" class="right">&nbsp;</label>
            </span></td>
        </tr>
      </table>
    </form>
    <script type="text/javascript">
				var plans = Array();
				var selected_plan_id = 0;
				var last_plan_id = 0;
				{foreach from=$plans item='plan'}
				plans[{$plan.ID}] = new Array();
				plans[{$plan.ID}]['Key'] = '{$plan.Key}';
				plans[{$plan.ID}]['Cross'] = {$plan.Cross};
				plans[{$plan.ID}]['Featured'] = {$plan.Featured};
				plans[{$plan.ID}]['Advanced_mode'] = {$plan.Advanced_mode};
				plans[{$plan.ID}]['Package_ID'] = {if $plan.Package_ID}{$plan.Package_ID}{else}false{/if};
				plans[{$plan.ID}]['Standard_listings'] = {$plan.Standard_listings};
				plans[{$plan.ID}]['Featured_listings'] = {$plan.Featured_listings};
				{/foreach}
				{literal}
				$(document).ready(function(){
					$('table.plans > tbody > tr').click(function(){
						if ( $(this).find('input[name=plan]:not(:disabled)') )
						{
							$('table.plans > tbody > tr').removeClass('active');
							$(this).addClass('active');
							planClickHandler($(this).find('input[name=plan]'));
							$(this).find('input[name=plan]').attr('checked', true);
						}
					});
					$('table.plans > tbody > tr:first > td.info').width($('table.plans > tbody > tr:first > td.info').width()-10);
					
					if ( $('table.plans input[name=plan]:checked').length == 0 )
					{
						$('table.plans input[name=plan]:not(:disabled):first').attr('checked', true);
					}
					planClickHandler($('table.plans input[name=plan]:checked'));
					$('table.plans input[name=plan]:checked').closest('tr').addClass('active');
				});
				var planClickHandler = function(obj){
					if ( obj.length == 0 )
						return;
					selected_plan_id = $(obj).attr('id').split('_')[1];
					if ( last_plan_id == selected_plan_id )
						return;
					last_plan_id = selected_plan_id;
					$('div.featured_option').hide();
					$('div.featured_option').prev().show();
					$('div.featured_option input').attr('disabled', true);
					//if ( plans[selected_plan_id]['Featured'] && plans[selected_plan_id]['Advanced_mode'] )
					if ( plans[selected_plan_id]['Package_ID'] || (plans[selected_plan_id]['Featured'] && plans[selected_plan_id]['Advanced_mode']) )
					{
						$('#featured_option_'+selected_plan_id).prev().hide();
						$('#featured_option_'+selected_plan_id).show();
						$('#featured_option_'+selected_plan_id+' input').attr('disabled', false);
						$('#featured_option_'+selected_plan_id+' input.disabled').attr('disabled', true);
						$('#featured_option_'+selected_plan_id+' input:not(.disabled):first').attr('checked', true);
						$('#featured_option_'+selected_plan_id+' input.checked').attr('checked', true);
					}
				}
				{/literal}
				</script> 
  </div>
  {/if} 
  <!-- select a plan end --> 
  <!-- fill in form --> 
  {if $cur_step == 'form'}
  <div class="area_form step_area hide">
    <div class="caption">{$lang.fill_out_form}</div>
    <form enctype="multipart/form-data" method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$category.Path}/{$steps.$cur_step.path}.html{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$steps.$cur_step.path}{/if}">
      <input type="hidden" name="step" value="form" />
      <input type="hidden" name="fromPost" value="1" />
      {rlHook name='addListingPreFields'}
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
      <script type="text/javascript">
					{literal}	
					$(document).ready(function(){
						flynax.mlTabs();
						{/literal}{if $config.address_on_map}flynax.onMapHandler();{/if}{literal}
					});
					{/literal}
					</script> 
      <!-- login/sing up form --> 
      {if $config.add_listing_without_reg && !$isLogin}
      {include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='authorization' name='<b>'|cat:$lang.authorization|cat:'</b>'}
      <div class="ml_tabs" style="margin-top: 10px;">
        <ul>
          <li lang="si" {if !$smarty.post.register.name}class="active"{/if}>{$lang.sign_in}</li>
          <li lang="su" {if $smarty.post.register.name}class="active"{/if}>{$lang.sign_up}</li>
        </ul>
      </div>
      <div class="ml_tabs_content" style="padding-bottom: 15px;">
        <div lang="si" {if $smarty.post.register.name}class="hide"{/if}>
          <div class="name">{$lang.username}</div>
          <input class="w180" type="text" name="login[username]" maxlength="25" value="{$smarty.post.login.username}" />
          <div class="name">{$lang.password}</div>
          <input class="w180" type="password" name="login[password]" maxlength="25" />
          <div style="padding: 10px 0 0;"><span class="black_12">{$lang.forgot_pass}</span> <a target="_blank" title="{$lang.remind_pass}" class="brown_12" href="{$rlBase}{if $config.mod_rewrite}{$pages.remind}.html{else}?page={$pages.remind}{/if}">{$lang.remind}</a></div>
        </div>
        <div lang="su" {if !$smarty.post.register.name}class="hide"{/if}>
          <div class="name">{$lang.your_name}</div>
          <input class="w180" type="text" name="register[name]" maxlength="100" value="{$smarty.post.register.name}" />
          <div class="name">{$lang.your_email}</div>
          <input class="w180" type="text" name="register[email]" maxlength="150" value="{$smarty.post.register.email}"  />
        </div>
      </div>
      <script type="text/javascript">
						{literal}
						$(document).ready(function(){
							$('input[name="register[name]"],input[name="register[email]"]').keydown(function(){
								$('input[name="login[username]"],input[name="login[password]"]').val('');
							});
							$('input[name="login[username]"],input[name="login[password]"]').keydown(function(){
								$('input[name="register[name]"],input[name="register[email]"]').val('');
							});
						});
						{/literal}
						</script> 
      {include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
      {/if} 
      <!-- login/sing up form end --> 
      {if $config.security_img_add_listing}
      {include file='captcha.tpl'}
      {/if}
      <table class="submit button">
        <tr>
          <td class="name"><a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$category.Path}/{$steps.plan.path}.html{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$steps.plan.path}{/if}" class="dark_12">{if $smarty.const.RL_LANG_DIR == 'ltr'}&larr;{else}&rarr;{/if} {$lang.perv_step}</a></td>
          <td class="field"><span class="arrow">
            <input type="submit" value="{$lang.next_step}" id="form_submit" />
            <label for="form_submit" class="right">&nbsp;</label>
            </span></td>
        </tr>
      </table>
    </form>
  </div>
  {/if} 
  <!-- fill in form end --> 
  <!-- add photo --> 
  {if $cur_step == 'photo'}
  <div class="area_photo step_area hide">
    <div class="caption">{$lang.add_photo}</div>
    {include file='blocks'|cat:$smarty.const.RL_DS|cat:'photo_manager.tpl'}
    <form method="post" onsubmit="return submit_photo_step();" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$category.Path}/{$steps.$cur_step.path}.html{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$steps.$cur_step.path}{/if}">
      <input type="hidden" name="step" value="photo" />
      <table class="submit">
        <tr>
          <td class="name button"><a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$category.Path}/{$prev_step.path}.html{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$prev_step.path}{/if}" class="dark_12">{if $smarty.const.RL_LANG_DIR == 'ltr'}&larr;{else}&rarr;{/if} {$lang.perv_step}</a></td>
          <td class="field button"><span class="arrow">
            <input type="submit" value="{$lang.next_step}" id="photo_submit" />
            <label for="photo_submit" class="right">&nbsp;</label>
            </span></td>
        </tr>
      </table>
    </form>
  </div>
  {/if} 
  <!-- add photo end --> 
  <!-- add video --> 
  {if $cur_step == 'video'}
  <div id="area_video" class="area_video step_area hide">
    <div class="caption">{$lang.add_video}</div>
    <div id="video_upload_dom"> {include file='blocks'|cat:$smarty.const.RL_DS|cat:'video_upload.tpl'} </div>
    {if $videos} 
    <script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}player/flowplayer.js"></script> 
    <script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.fancybox.js"></script> 
    <script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/fancybox/helpers/jquery.fancybox-buttons.js"></script> 
    {include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='uploadList' name=$lang.listing_video tall=true}
    {assign var='replace' value=`$smarty.ldelim`key`$smarty.rdelim`}
    <ul class="thumbnails inline">
      {foreach from=$videos item='video'}
      <li id="video_{$video.ID}" class="active"> {if $video.Type == 'local'} <img class="item cursor-move" src="{$smarty.const.RL_FILES_URL}{$video.Preview}" alt="" /> 
        <script type="text/javascript">//<![CDATA[
									{literal}
									$('#video_{/literal}{$video.ID}{literal} img.item').fancybox({
										padding: 10,
										width: {/literal}{$config.video_width}{literal},
										height: {/literal}{$config.video_height}{literal},
										content: '<a href="{/literal}{$smarty.const.RL_FILES_URL}{$video.Video}{literal}" style="display:block;width:{/literal}{$config.video_width}{literal}px;height:{/literal}{$config.video_height}{literal}px;" id="player"></a>',
										afterShow:	function(){
											flowplayer('player', rlConfig['libs_url']+'player/flowplayer-3.2.7.swf', {
												wmode: 'transparent',
												plugins: {
											        pseudo: {
											            url: rlConfig['libs_url']+'player/flowplayer.pseudostreaming-3.2.9.swf'
											        }
		    									},
		    									 clip: {
											        provider: 'pseudo',
											        url: '{/literal}{$smarty.const.RL_FILES_URL}{$video.Video}{literal}'
											    }
											});
										},
										afterClose: function(){
											$f().stop();
										},
										helpers: {
											media : {},
											overlay: {
												opacity: 0.5
											}
										}
									});
									{/literal}
									</script> 
        {else} <a class="youtube fancybox.iframe" href="http://www.youtube.com/embed/{$video.Preview}?autoplay=1"><img class="item cursor-move" src="{$l_youtube_thumbnail|replace:$replace:$video.Preview}" alt="" /></a> {/if} <img src="{$rlTplBase}img/blank.gif" class="delete" alt="{$lang.delete}" title="{$lang.delete}" /> </li>
      {/foreach}
    </ul>
    <div class="clear"></div>
    {include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
    {/if} 
    <script type="text/javascript">//<![CDATA[
				var video_listing_id = {$smarty.session.add_listing.listing_id};
				var sort_save = false;
				{literal}
				/* preview video handler */
				$('ul.thumbnails > li > a.youtube').fancybox({
					padding: 10,
					width: {/literal}{$config.video_width}{literal},
					height: {/literal}{$config.video_height}{literal},
					helpers: {
						media : {},
						overlay: {
							opacity: 0.5
						}
					}
				});
				/* delete video handler */
				$('#area_video ul.thumbnails img.delete').each(function(){
					$(this).flModal({
						caption: '{/literal}{$lang.warning}{literal}',
						content: '{/literal}{$lang.delete_confirm}{literal}',
						prompt: 'xajax_deleteVideo('+ $(this).parent().attr('id').split('_')[1] +', "'+ $(this).parent().attr('id') +'")',
						width: 'auto',
						height: 'auto'
					});
				});
				
				$('div#area_video ul.thumbnails').sortable({
					placeholder: 'hover',
					stop: function(event, obj){
						/* save sorting */
						var sort = '';
						var count = 0;
						$('div#area_video ul.thumbnails li').each(function(){
							var id = $(this).attr('id').split('_')[1];
							count++;
							var pos = $('div#area_video ul.thumbnails li').index($(this))+1;
							sort += id+','+pos+';';
						});
						
						if ( sort.length > 0 && count > 1 && sort_save != sort )
						{
							sort_save = sort;
							sort = rtrim(sort, ';');
							xajax_reorderVideo(video_listing_id, sort);
						}
					}
				});
				{/literal}
				//]]>
				</script>
    <form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$category.Path}/{$steps.$cur_step.path}.html{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$steps.$cur_step.path}{/if}">
      <input type="hidden" name="step" value="video" />
      <input type="hidden" name="redirect" value="1" />
      <table class="submit button">
        <tr>
          <td class="name"><a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$category.Path}/{$prev_step.path}.html{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$prev_step.path}{/if}" class="dark_12">{if $smarty.const.RL_LANG_DIR == 'ltr'}&larr;{else}&rarr;{/if} {$lang.perv_step}</a></td>
          <td class="field"><span class="arrow">
            <input type="submit" value="{$lang.next_step}" id="video_submit" />
            <label for="video_submit" class="right">&nbsp;</label>
            </span></td>
        </tr>
      </table>
    </form>
  </div>
  {/if} 
  <!-- add video end --> 
  <!-- checkout --> 
  {if $cur_step == 'checkout'}
  <div class="area_checkout step_area hide">
    <div class="caption">{$lang.checkout}</div>
    <div class="dark" style="padding-bottom: 5px;">{$lang.checkout_step_info}</div>
    <form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$category.Path}/{$steps.$cur_step.path}.html{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$steps.$cur_step.path}{/if}">
      <input type="hidden" name="step" value="checkout" />
      <ul id="payment_gateways">
        {if $config.use_paypal}
        <li> <img alt="" src="{$smarty.const.RL_LIBS_URL}payment/paypal/paypal.png" />
          <p>
            <input {if $smarty.post.gateway == 'paypal' || !$smarty.post.gateway}checked="checked"{/if} type="radio" name="gateway" value="paypal" />
          </p>
        </li>
        {/if}
        {if $config.use_2co}
        <li> <img alt="" src="{$smarty.const.RL_LIBS_URL}payment/2co/2co.png" />
          <p>
            <input {if $smarty.post.gateway == '2co'}checked="checked"{/if} type="radio" name="gateway" value="2co" />
          </p>
        </li>
        {/if}
        {rlHook name='paymentGateway'}
      </ul>
      <table class="submit button">
        <tr>
          <td class="name"><a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$category.Path}/{$prev_step.path}.html{else}?page={$pageInfo.Path}&amp;id={$category.ID}&amp;step={$prev_step.path}{/if}" class="dark_12">{if $smarty.const.RL_LANG_DIR == 'ltr'}&larr;{else}&rarr;{/if} {$lang.perv_step}</a></td>
          <td class="field"><span class="arrow">
            <input type="submit" value="{$lang.next_step}" id="checkout_submit" />
            <label for="checkout_submit" class="right">&nbsp;</label>
            </span></td>
        </tr>
      </table>
    </form>
    <script type="text/javascript">
					flynax.paymentGateway();
				</script> 
  </div>
  {/if} 
  <!-- checkout end --> 
  {rlHook name='addListingStepActionsTpl'} 
  <!-- done --> 
  {if $cur_step == 'done'}
  <div class="area_done step_area hide">
    <div class="caption">{$lang.reg_done}</div>
    <div class="info">{if $config.listing_auto_approval}{$lang.notice_after_listing_adding_auto}{else}{$lang.notice_after_listing_adding}{/if}</div>
    <span class="dark"> {assign var='replace' value='<a href="'|cat:$return_link|cat:'">$1</a>'}
    {$lang.add_one_more_listing|regex_replace:'/\[(.*)\]/':$replace} </span> </div>
  {/if} 
  <!-- done end --> 
  <script type="text/javascript">
			{if $cur_step}
				flynax.switchStep('{$cur_step}');
			{/if}
			{literal}
			$(document).ready(function(){
				$("input.numeric").numeric();
				$('input[name="f[price][value]"]').numeric({ decimal : "{/literal}{$config.price_decimal}{literal}" });
			});
			{/literal}
			</script> 
  {/if} </div>
{/if}
{rlHook name='addListingBottomTpl'}
<!-- add listing end -->