<!-- comments tab -->

<div id="area_comments" class="tab_area hide">

	<div class="highlight">
		<div id="comments_dom">
			{include file=$smarty.const.RL_PLUGINS|cat:$smarty.const.RL_DS|cat:'comment'|cat:$smarty.const.RL_DS|cat:'comment_dom.tpl'}
		</div>
		
		<div class="username">{$lang.comment_add_comment}</div>
		
		<form name="add_comment" action="" method="post">
			<table class="submit">
			<tr>
				<td class="name">{$lang.comment_author} <span class="red">*</span></td>
				<td class="field">
					<input type="text" id="comment_author" maxlength="30" value="{if $isLogin}{$isLogin}{else}{$smarty.post.author}{/if}" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.comment_title} <span class="red">*</span></td>
				<td class="field">
					<input type="text" id="comment_title" size="40" value="{$smarty.post.title}" />
				</td>
			</tr>
			{if $config.comments_rating_module}
			<tr>
				<td class="name">{$lang.comment_rating}</td>
				<td class="field">
					{assign var='replace' value=`$smarty.ldelim`stars`$smarty.rdelim`}
					{section name='stars' start=1 loop=$config.comments_stars_number+1}<span title="{$lang.comment_set|replace:$replace:$smarty.section.stars.iteration}" id="comment_star_{$smarty.section.stars.iteration}" class="comment_star"></span>{/section}
				</td>
			</tr>
			{/if}
			<tr>
				<td class="name">{$lang.message} <span class="red">*</span></td>
				<td class="field">
					<textarea class="text" id="comment_message" rows="5" cols="">{$smarty.post.message}</textarea>
				</td>
			</tr>
			{if $config.security_img_comment_captcha}
			<tr>
				<td class="name">{$lang.security_code} <span class="red">*</span></td>
				<td class="field">
					{include file='captcha.tpl' no_caption=true captcha_id='comment'}
				</td>
			</tr>
			{/if}
			<tr>
				<td></td>
				<td class="field">
					<input type="submit" value="{$lang.comment_add_comment}" />
				</td>
			</tr>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript">
var comment_star = false;
var comment_timer = '';

{literal}
$(document).ready(function(){
	$('form[name=add_comment]').submit(function(){
		xajax_CommentAdd($('#comment_author').val(), $('#comment_title').val(), $('#comment_message').val(), $('#comment_security_code').val(), comment_star);
		$('form[name=add_comment] input[type=submit]').val('{/literal}{$lang.loading}{literal}');
		
		return false;
	});
	
	$('#comment_message').textareaCount({
		'maxCharacterSize': {/literal}{$config.comment_message_symbols_number}{literal},
		'warningNumber': 20
	})
	
	if ( flynax.getHash() == 'comments' )
	{
		tabsSwitcher('div.tabs li#tab_comments');
	}
	
	$('.comment_star').mouseover(function(){
		var id = $(this).attr('id').split('_')[2];
		
		if ( comment_star )
		{
			comment_timer = setTimeout("comment_fill("+id+")", 700);
		}
		else
		{		
			comment_fill(id);
		}
	});
	
	$('.comment_star').click(function(){
		comment_star = $(this).attr('id').split('_')[2];
	});
	
	$('.comment_star').mouseout(function(){
		clearTimeout(comment_timer);
		
		if ( comment_star )
		{
			return false;
		}
		
		$('.comment_star').removeClass('comment_star_active');
	});
});

var comment_fill = function(id)
{
	comment_star = false;
	id = parseInt(id);

	$('.comment_star').removeClass('comment_star_active');
	
	for(var i = 1; i <= id; i++)
	{
		$('#comment_star_'+i).addClass('comment_star_active');
	}
}

{/literal}
</script>

<!-- comments tab end -->