<!-- comments DOM -->

{if $comments}
	<ul class="comments">
	{foreach from=$comments item='comment'}
		<li>
			<div class="hlight hborder">
				<h3>
					{$comment.Title}
					{if $config.comments_rating_module}{section name='stars' start=1 loop=$comment.Rating+1}<span class="comment_star_small"></span>{/section}{/if}
				</h3>
				{$comment.Description|nl2br}
			</div>
			<span>
				<span class="dark"><b>{$comment.Author}</b></span> / {$comment.Date|date_format:$smarty.const.RL_DATE_FORMAT}
			</span>
		</li>
	{/foreach}
	</ul>
{else}
	<div class="info">{$lang.comment_absent}</div>
{/if}

<!-- comments DOM end -->