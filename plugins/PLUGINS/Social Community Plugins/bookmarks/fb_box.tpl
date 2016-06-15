<!-- facebook funs box tpl -->

{if $config.bookmarks_fb_box_appid && $config.bookmarks_fb_box_url}
	{assign var='allow_fb_init' value=true}
	{if $aHooks.facebookConnect}
		{if $config.facebookConnect_appid && $config.facebookConnect_secret && $config.facebookConnect_account_type}
			{assign var='allow_fb_init' value=false}
		{/if}
	{/if}

	<div id="fl-facebook-funs"></div>
	<div id="fb-root"></div>
	<script type="text/javascript">//<![CDATA[[
	var allow_fb_init = {if $allow_fb_init}true{else}false{/if};
	{literal}
	$(document).ready(function(){
		var width = $('#fl-facebook-funs').width();
		$('.fb-like-box').attr('data-width', width);
		
		window.fbAsyncInit = function() {
			// init the FB JS SDK
			FB.init({
				appId      : '{/literal}{$config.bookmarks_fb_box_appid}{literal}',                        // App ID from the app dashboard
				channelUrl : '{/literal}{$config.bookmarks_fb_box_url}{literal}', // Channel file for x-domain comms
				status     : true,                                 // Check Facebook Login status
				xfbml      : true                                  // Look for social plugins on the page
			});
		
			FB.Event.subscribe('xfbml.render',
			    function(response) {
					$('.fb_iframe_widget iframe, .fb_iframe_widget > span').width(width);
			    }
			);
		};
		
		// Load the SDK asynchronously
		(function(d, s, id){
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/all.js";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	});

	{/literal}
	//]]>
	</script>

	<div class="fb-like-box" data-href="{$config.bookmarks_fb_box_url}" {if $config.bookmarks_fb_box_color == 'dark'}data-colorscheme="dark"{/if} data-show-faces="{if $config.bookmarks_fb_box_faces}true{else}false{/if}" data-stream="{if $config.bookmarks_fb_box_stream}true{else}false{/if}" data-header="{if $config.bookmarks_fb_box_header}true{else}false{/if}" {if $config.bookmarks_fb_box_border}data-border-color="{$config.bookmarks_fb_box_border}"{/if}></div>
{else}
	{$lang.bookmarks_fb_box_deny}
{/if}

<!-- facebook funs box tpl end -->