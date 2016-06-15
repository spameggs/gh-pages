<!-- Facebook connect block -->

<div id="fb-root"></div>
<script type="text/javascript">
//<![CDATA[
var fbStatus = '{$fb_status}';
var autoRegPreventDetected = {if $autoRegPreventDetected}true{else}false{/if};
var isLogin = {if $isLogin}true{else}false{/if};

{literal}

$(document).ready(function() {
	if ( !document.getElementById('fb-nav-bar') ) {
		var fcDOM = '<img style="cursor:pointer;" alt="" title="{/literal}{$lang.fConnect_login_title}{literal}" src="{/literal}{$smarty.const.RL_PLUGINS_URL}{literal}facebookConnect/static/fb_login.png" onclick="fcLogin();" />';
		$('input[value=login]:first').parent().find('input[type=submit]').after(fcDOM);
	}
	else {
		// move FB icon
		if ( $('form[name="userbar_login"]').length ) {
			$('input[name=username]').before($('img[onclick="fcLogin();"]'));
			$('img[onclick="fcLogin();"]').css('margin-right', '5px');
		}
	}
});

window.fbAsyncInit = function() {
	FB.init({
		appId: '{/literal}{$config.facebookConnect_appid}{literal}', // App ID from the app dashboard
		channelUrl : '{/literal}{$config.bookmarks_fb_box_url}{literal}', // Channel file for x-domain comms
		cookie: true, // Check Facebook Login status
		xfbml: true // Look for social plugins on the page
	});

	if ( fbStatus != '' & fbStatus != 'active' ) {
		printMessage('warning', '{/literal}{$lang.notice_account_approval|escape:"quotes"|regex_replace:"/[\r\t\n]/":" "}{literal}');

		FB.getLoginStatus(function(response) {
			if ( response.authResponse ) {
				FB.logout();
			}
		});
	}

	if ( autoRegPreventDetected ) {
		FB.getLoginStatus(function(response) {
			if ( response.authResponse ) {
				FB.logout();
			}
		});
	}

	// bookmarks
	if ( $('#fl-facebook-funs').length > 0 ) {
		FB.Event.subscribe('xfbml.render',
			function(response) {
				var width = $('#fl-facebook-funs').width();
				$('.fb_iframe_widget iframe, .fb_iframe_widget > span').width(width);
			}
		);
	}
};

function fcLogin(mode) {
	if ( !isLogin ) {
		createCookie('need_login_through_fb', 1, 1); // on 1 day
	}
	else {
		eraseCookie('need_login_through_fb');
	}

	FB.getLoginStatus(function(response) {
		if ( response ) {
			if ( response.authResponse ) {
				FB.logout(function(response) {
					if ( mode == undefined ) {
						fcLogin();
					}
					else {
						window.location.href = '{/literal}{if $smarty.const.RL_MOBILE === true}{$smarty.const.RL_MOBILE_HOME}{else}{$smarty.const.RL_URL_HOME}{/if}{if $config.mod_rewrite}{$pages.login}.html?action=logout{else}index.php?page={$pages.login}&action=logout{/if}{literal}';
					}
				});
			}
			else {
				FB.login(function(response) {
					if ( response.authResponse ) {
						window.location.href = '{/literal}{if $smarty.const.RL_MOBILE === true}{$smarty.const.RL_MOBILE_HOME}{else}{$smarty.const.RL_URL_HOME}{/if}{literal}?token='+ response.authResponse.accessToken;
					}
				}, {scope:'email'});
			}
		}
		else {
			FB.login();
		}
	});
}

(function(d) {
	var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	d.getElementsByTagName('head')[0].appendChild(js);
}(document));

{/literal}
//]]>
</script>

{if $fb_email}
<script type="text/javascript">
//<![CDATA[
var fc_phrase_prompt = "{$lang.fConnect_prompt}";
var fc_email = "{$fb_email}";
{literal}

$(document).ready(function() {
	fConnect_force_prompt();
});

var fConnect_force_prompt = function() {
	$('div.error div.close').click();

	var result = prompt( fc_phrase_prompt.replace( '<br />', '\r\n' ).replace( '{email}', fc_email ) );
	if ( result == null ) {
		FB.getLoginStatus(function(response) {
			if ( response.authResponse ) {
				FB.logout();
			}
		});
	}
	else {
		xajax_fConnect(result);
	}
}

{/literal}
//]]>
</script>
{/if}

<!-- Facebook connect block end -->