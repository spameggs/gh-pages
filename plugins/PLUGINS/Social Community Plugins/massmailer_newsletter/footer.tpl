<!-- massmailer/newslatter footer tpl hook -->
<div class="hide">
	{if $pageInfo.Key == 'registration' || $pageInfo.Key == 'my_profile'}
		<label id="mn_container_reg" style="display: block;margin: 3px 0;"><input value="1" type="checkbox" {if (isset($smarty.post.profile.mn_subscribe) && $smarty.post.profile.mn_subscribe) || !isset($smarty.post.profile.mn_subscribe)}checked="checked"{/if} name="profile[mn_subscribe]" /> {$lang.massmailer_newsletter_subscribe_to}</label>
		
		<script type="text/javascript">
		var mn_sign = '{if $pageInfo.Key == 'registration'}.{else}#{/if}';
		{literal}
		
		$(document).ready(function(){
			$('div'+mn_sign+'area_profile table.submit input[name="profile[display_email]"]').parent().after($('#mn_container_reg'));
		});
		
		{/literal}
		</script>
	{/if}
</div>
<!-- massmailer/newslatter footer tpl hook end -->