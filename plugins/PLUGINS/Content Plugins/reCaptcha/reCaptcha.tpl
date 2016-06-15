<!-- reCaptcha tpl -->

{if $config.reCaptcha_module && $config.reCaptcha_public_key && $config.reCaptcha_private_key && $curl_loaded}

	<span id="reCaptcha{if $captcha_id}_{$captcha_id}{/if}" style="direction: ltr;" class="lalign"></span>
	
	<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
	<script type="text/javascript">//<![CDATA[
	var reCaptchaRequest = '{if $smarty.const.RL_MOBILE === true}'+rlUrlRoot+'plugins/{else}{$smarty.const.RL_PLUGINS_URL}{/if}reCaptcha/request.php';
	var reCaptchaTheme = '{$config.reCaptcha_theme}';
	var rcHandlerInit = false;
	{literal}
	
	Recaptcha.destroy();
	Recaptcha.create('{/literal}{$config.reCaptcha_public_key|trim}{literal}', 'reCaptcha{/literal}{if $captcha_id}_{$captcha_id}{/if}{literal}', {
		theme: reCaptchaTheme,
		lang: '{/literal}{$smarty.const.RL_LANG_CODE|lower}{literal}',
		callback: function(){
			var html = '<input id="{/literal}{if $captcha_id}{$captcha_id}_{/if}{literal}security_code" type="hidden" name="security_code{/literal}{if $captcha_id}_{$captcha_id}{/if}{literal}" />';
			if ( $('input[name=security_code{/literal}{if $captcha_id}_{$captcha_id}{/if}{literal}]').length <= 0 ) {
				$('#reCaptcha{/literal}{if $captcha_id}_{$captcha_id}{/if}{literal}').before(html);
			}
			
			$('#reCaptcha{/literal}{if $captcha_id}_{$captcha_id}{/if}{literal} input[name=recaptcha_response_field]').focus(function(){
				if ( !rcHandlerInit ) {
					rc_handler();
				}
			});
		}
	});

	var rc_request = function( callback ) {
		var challenge = Recaptcha.get_challenge();
		var response = Recaptcha.get_response();
		$('input[name=security_code{/literal}{if $captcha_id}_{$captcha_id}{/if}{literal}]').val(response);
		$.post(reCaptchaRequest, {challenge: challenge, response: response{/literal}{if $captcha_id}, id: '_{$captcha_id}'{/if}{literal}}, callback );
	}

	var rc_handler = function() {
		var form = $('#reCaptcha{/literal}{if $captcha_id}_{$captcha_id}{/if}{literal}').closest('form');
		var event = false;
		
		if ( form.attr('onsubmit') ) {
			eval("event = new Object(); event.handler = function() { "+form.attr('onsubmit')+" }");
			form.attr('onsubmit', 'return false;');
		}
		else {
			var events = jQuery._data(form.get(0), 'events');
			if ( events != undefined ) {
				event = events.submit[0];
			}
			else {
				event = new Object();
				event.handler = function() {
					form.unbind('submit').submit(function() {
						return true;
					});
					form.submit();
				}
			}
		}
		
		form.unbind('submit').submit(function() {
			var visible = true;
			for( var i = 0, parent; parent = $('#reCaptcha{/literal}{if $captcha_id}_{$captcha_id}{/if}{literal}').parents()[i]; i++ ) {
				if ( !$(parent).is(':visible') ) {
					visible = false;
					break;
				}
			}
			if ( visible ) {
				rc_request(event.handler);
			}
			else {
				event.handler();
				return true;
			}

			return false;
		});
		
		rcHandlerInit = true;
	}
	{/literal}
	//]]>
	</script>
{else}

	{include file='captcha.tpl' captcha_id=$captcha_id no_wordwrap=$no_wordwrap}

{/if}

<!-- reCaptcha tpl end -->
