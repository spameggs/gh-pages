<div>
	{if $block_comments}
		<ul class="comments_block">
		{foreach from=$block_comments item='block_comment' name='commentF'}
			<li>
				<a title="{$block_comment.Listing_title}" {if $config.view_details_new_window}target="_blank"{/if} href="{$block_comment.Listing_link}">
					{$block_comment.Title}
				</a>
				<div class="dark">{$block_comment.Description|truncate:$config.comments_number_symbols_comments}</div>
				<div class="ralign"><b>{$block_comment.Author}</b></div>
			</li>
		{/foreach}
		</ul>
	{else}
		<div class="info">{$lang.comment_absent_comments_in_listings}</div>
	{/if}
</div>