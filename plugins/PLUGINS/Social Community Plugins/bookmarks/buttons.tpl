<script type="text/javascript" src="http://s7.addthis.com/js/{if $type == 'floating_bar'}300{else}250{/if}/addthis_widget.js{if $config.bookmarks_addthis_id}#pubid={$config.bookmarks_addthis_id}{/if}"></script>

<script type="text/javascript">
var addthis_config = {literal}{{/literal}
	ui_language: "{$smarty.const.RL_LANG_CODE}"{if $type != 'floating_bar'},
	ui_delay: 200{/if}
{literal}}{/literal}
</script>

{assign var='services' value=','|explode:$services}

{if $align == 'right'}
	{assign var='bookmark_style' value='float: right;'}
{elseif $align == 'center'}
	{assign var='bookmark_style' value='display: inline-block;'}
{/if}

<div style="{if $align == 'center'}text-align: center;{/if}{if !$block.Tpl}padding: 0 0 5px 0;{/if}">
	{if $type == 'vertical_share_counter'}
		<a class="addthis_counter" style="{$bookmark_style}"></a>
	{elseif $type == 'floating_bar'}
		<div id="bookmark_floating_bar" class="hide addthis_toolbox addthis_floating_style addthis_{if $view_mode == 'medium'}32x32{elseif $view_mode == 'small'}16x16{else}counter{/if}_style" style="position: absolute; background: #{$color};left: auto;right: auto;top: 0;border-radius: 6px;direction: ltr;">
		    <div class="addthis_toolbox addthis_default_style{if $view_mode == 'medium'} addthis_32x32_style{/if}" style="direction: ltr;">
			    {foreach from=$services item='service'}
					<span><a class="addthis{if $service != 'counter'}_button{/if}_{$service}" {if $service == 'facebook_like'}fb:like:layout="box_count"{elseif $service == 'tweet'}tw:count="vertical"{elseif $service == 'google_plusone'}g:plusone:size="tall"{/if}></a></span>
				{/foreach}
		    </div>
		</div>
		<script type="text/javascript">
		var bookmarks_side = '{$block.Side}';
		var bookmarks_view_mode = '{$view_mode}';
		var bookmarks_template = '{$config.template}';
		{literal}
		
		$(document).ready(function(){
			$('#bookmark_floating_bar').closest('.no_design').hide();
			$($('#main_container').length > 0 ? '#main_container' : '#main_container_home').append($('#bookmark_floating_bar'));
			switch(bookmarks_view_mode){
				case 'large':
					var bar_width = 70+10;
					break;
				case 'medium':
					var bar_width = 46+10;
					break;
				case 'small':
					var bar_width = 30+10;
					break;
			}
			var paddingTop = parseInt($('#content').css('paddingTop'));
			
			var patt = /\_modern$/gi;
			if ( patt.test(bookmarks_template) )
			{
				paddingTop += 4;
			}
			else if ( bookmarks_template == 'realty_spring' )
			{
				paddingTop = 0;
			}

			if ( bookmarks_side == 'middle_left' )
			{
				if ( rlLangDir == 'rtl' )
				{
					$('#bookmark_floating_bar').css('margin-right', -bar_width).css('margin-top', paddingTop);
				}
				else
				{
					$('#bookmark_floating_bar').css('margin-left', -bar_width).css('margin-top', paddingTop);
				}
			}
			else
			{
				var width = $('#main_container').width() || $('#main_container_home').width();
				if ( rlLangDir == 'rtl' )
				{
					$('#bookmark_floating_bar').css('margin-right', parseInt(width)+10).css('margin-top', paddingTop);
				}
				else
				{
					$('#bookmark_floating_bar').css('margin-left', parseInt(width)+10).css('margin-top', paddingTop);
				}
			}
			$('#bookmark_floating_bar').show();
			
			$(document).scroll(function(){
				bookmarksScroll();
			});
			
			bookmarksScroll();
		});
		
		var bookmarksScroll = function(){
			var pos = $('#main_container').position() || $('#main_container_home').position();
			
			if ( !pos )
				return;
			
			var offset = bookmarksGetOffset();
			
			if ( offset >= pos.top )
			{
				$('#bookmark_floating_bar').css('position', 'fixed');
			}
			else
			{
				$('#bookmark_floating_bar').css('position', 'absolute');
			}
		}
		
		var bookmarksGetOffset = function(){
			var top_offset;
			
			if ( self.pageYOffset )
			{
				top_offset = self.pageYOffset;
			}
			else if ( document.documentElement && document.documentElement.scrollTop )
			{
				top_offset = document.documentElement.scrollTop;// Explorer 6 Strict
			}
			else if ( document.body )
			{
				top_offset = document.body.scrollTop;// all other Explorers
			}
			
			return top_offset;
		}
		
		{/literal}
		</script>
	{elseif $type == 'tweet_like_share'}
		<div class="addthis_toolbox addthis_pill_combo" style="{$bookmark_style}height: 25px;">       
		    <a class="addthis_button_tweet" tm:count="vertical"></a> 
		    <a class="addthis_button_facebook_like"></a>  
		    <a class="addthis_counter addthis_pill_style"></a>
		</div>
	{elseif $type == 'toolbox_facebook_like'}
		<div class="addthis_toolbox addthis_default_style" style="{$bookmark_style}height: 25px;">
			{foreach from=$services item='service'}
				<a class="addthis_button_{$service}" {if $service == 'google_plusone'}g:plusone:size="medium"{/if} href="javascript:void(0);"></a>
			{/foreach}
			<span class="addthis_separator"> </span>
			<a class="addthis_button_facebook_like"></a>
		</div>
	{elseif $type == 'googleplus_like_tweet'}
		{if $services}
			<div class="addthis_toolbox addthis_default_style" style="{$bookmark_style}height: 25px;">
				{foreach from=$services item='service'}
					<a class="addthis{if $service != 'counter'}_button{/if}_{$service} addthis_pill_style addthis_nonzero" {if $service == 'google_plusone'}g:plusone:size="medium"{/if}></a>
				{/foreach}
			</div>
		{/if}
	{elseif $type == '32x32_icons_addthis'}
		<div class="addthis_toolbox addthis_32x32_style addthis_default_style" style="{$bookmark_style}">
			{foreach from=$services item='service'}
				<a class="addthis_button_{$service}" {if $service == 'google_plusone'}g:plusone:size="medium"{/if} href="javascript:void(0);"></a>
			{/foreach}
		</div>
	{elseif $type == '64x64_icons_aquaticus'}
		<div class="addthis_toolbox" style="{$bookmark_style}">
			<div class="custom_images">
				{foreach from=$services item='service'}
					<a class="addthis_button_{if $service == 'compact'}more{else}{$service}{/if}" {if $service == 'google_plusone'}g:plusone:size="medium"{/if}><img src="http://www.addthis.com/cms-content/images/gallery/{if $service == 'compact'}addthis_64{else}aquaticus_{$service}{/if}.png" width="64" height="64" alt="Share to {$service}" /></a>
				{/foreach}
			</div>
		</div>
	{elseif $type == 'css3_share_buttons'}
		<div class="addthis_toolbox addthis_share_btn" style="{$bookmark_style}">
			<a href="http://addthis.com/bookmark.php" class="addthis_button_compact" {if $color}style="background: #{$color};"{/if}>
				<span>{$lang.bookmarks_share}</span>
			</a>
		</div>
	{elseif $type == '32x32_vertical_icons'}
		<div class="addthis_toolbox addthis_32x32_style" style="{$bookmark_style}">
			<div class="custom_images">
				{foreach from=$services item='service'}
					<a class="addthis_button_{$service}" {if $service == 'google_plusone'}g:plusone:size="medium"{/if}></a>
				{/foreach}
			</div>
		</div>
	{elseif $type == 'share_button'}
		<a class="addthis_button" style="{$bookmark_style}"></a>
	{elseif $type == 'vertical_layout_menu'}
		<div class="addthis_toolbox" style="{$bookmark_style}">
			<div class="vertical">
				{foreach from=$services item='service'}
					<a class="addthis_button_{$service}" {if $service == 'google_plusone'}g:plusone:size="medium"{/if} style="{if $color}color: #{$color};{/if}padding-bottom: 3px;">{$bsh_services[$service]}</a>
				{/foreach}
			</div>
		</div>
	{/if}
	<div class="clear"></div>
</div>