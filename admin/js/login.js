/* make username filed in focus */
$(document).ready(
	function(){
		$('#username').focus();
	}
);

/**
* check admin login form
*
* @param srting user_empty - error message in case when the user filed is empty
* @param string pass_empty - error message in case when the password filed is empty
*
* @return bool
* 
**/
function jsLogin( user_empty, pass_empty )
{
	if ( $("#username").val() != '' )
	{
		if ( $("#password").val() != '' )
		{
			$('#login_button').val(lang['ext_loading']);
			
			var pass_hash = $('#password').val();
			var password = hex_md5(sec_key)+hex_md5(pass_hash);

			xajax_logIn( $('#username').val(), password, $('#interface').val() );
		}
		else
		{
			fail_alert( '#password', pass_empty );
		}
	}
	else
	{
		fail_alert( '#username', user_empty );
	}
	
	return false;
}

/**
*
* alert the message and focus current field
*
* @param srting field - jQuery format field 
* @param string message - alert message text
* 
**/
function fail_alert( field, message )
{
	Ext.MessageBox.alert(lang['alert'], message, function(){
		if ( field != '' )
		{
			$(field).addClass('field_error');
			$(field).focus();
		}
	});
}
