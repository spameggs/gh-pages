<!-- newsletter block -->
<div id="nl_subscribe">
	{$lang.massmailer_newsletter_your_name}
	<div style="padding: 0 0 5px;"><input type="text" id="newsletter_name" maxlength="150" style="width: 80%;" /></div>
	
	{$lang.massmailer_newsletter_your_e_mail}
	<div><input type="text" id="newsletter_email" maxlength="100" style="width: 80%" /></div>
	
	<div style="padding: 10px 0 0;">
		<input onclick="xajax_subscribe('subscribe', $('#newsletter_name').val(), $('#newsletter_email').val());$(this).val('{$lang.loading}');" type="button" value="{$lang.massmailer_newsletter_subscribe}"/>
	</div>
	<div style="padding: 5px 0">
		<a id="unsubscribe_link" href="javascript:void(0);" class="static">{$lang.massmailer_newsletter_unsubscribe}</a>
	</div>
</div>
<div id="nl_unsubscribe" class="hide">
	{$lang.massmailer_newsletter_your_e_mail}
	<div><input type="text" id="un_newsletter_email" maxlength="150" style="width: 80%" /></div>
	<div style="padding: 10px 0 0;">
		<input onclick="xajax_subscribe('unsubscribe', '', $('#un_newsletter_email').val());$(this).val('{$lang.loading}');" type="button" value="{$lang.massmailer_newsletter_unsubscribe}"/>
	</div>
	<div style="padding: 5px 0">
		<a id="subscribe_link" href="javascript:void(0);" class="static">{$lang.massmailer_newsletter_subscribe}</a>
	</div>
</div>
<script type="text/javascript">
{literal}
$(document).ready(function(){
	$('#unsubscribe_link').click(function(){
		$('#nl_subscribe').slideUp('normal');
		$('#nl_unsubscribe').slideDown('slow');
	});
	$('#subscribe_link').click(function(){
		$('#nl_unsubscribe').slideUp('normal');
		$('#nl_subscribe').slideDown('slow');
	});
});
{/literal}
</script>
<!-- newsletter block end -->