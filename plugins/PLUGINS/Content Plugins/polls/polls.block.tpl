<!-- poll block -->

{assign var='lang_site' value=$smarty.const.RL_LANG_CODE}
{if $poll}
	<div id="poll_container_{$poll.ID}">
	{if $poll.voted}
		{if $poll.Random == 1 || $block.Tpl == 0}<div class="dark" style="padding: 0 0 5px;"><b>{$poll.name.$lang_site}</b></div>{/if}
		
		<ul class="poll_results_list">
		{foreach from=$poll.items item='poll_item' name='pollsF'}
			<li>
				<div class="poll_caption">{$poll_item.name.$lang_site} (<b>{$poll_item.Votes}</b> {$lang.votes})</div>
				<div style="font-size: 10px;height: 14px;line-height: 11px;width: {if $poll_item.percent < 10}10{else}{$poll_item.percent}{/if}%; background: {$poll_item.Color};color: {if $poll_item.Color == '#ffffff'}#000000{else}#ffffff{/if}; text-align: center;">
					<b style="line-height: 13px;">{$poll_item.percent}%</b>
				</div>
			</li>
		{/foreach}
		</ul>
		
		<div class="dark">{$lang.total_votes}: <b>{$poll.total}</b></div>
		
	{else}
		{if $poll.Random == 1 || $block.Tpl == 0}<div class="dark" style="padding: 0 0 5px;"><b>{$poll.name.$lang_site}</b></div>{/if}
		<div id="poll_items_{$block.Key}">
			<ul class="poll_list">
			{foreach from=$poll.items item='poll_item' name='pollsF'}
				<li>
					<label>
						<span class="poll_item" style="background: {$poll_item.Color};"></span>
						<input class="vote_item" type="radio" name="poll_{$poll.Key}" value="{$poll_item.Key}" />
						{$poll_item.name.$lang_site}
					</label>
				</li>
			{/foreach}
			</ul>
			
			<table class="sTable" style="margin-top: 5px;">
			<tr>
				<td class="lalign">
					<input type="button" value="{$lang.vote}" id="vote_button_{$poll.ID}" />
				</td>
				<td class="ralign">
					<a href="javascript: void(0);" rel="nofollow" onclick="$('#poll_items_{$block.Key}').slideUp('normal');$('#poll_results_{$block.Key}').slideDown('normal');">{$lang.polls_view_results}</a>
				</td>
			</tr>
			</table>
			<script type="text/javascript">
			var loading = '{$lang.loading}';
			{literal}
			$(document).ready(function(){
				$('#vote_button_{/literal}{$poll.ID}{literal}').click(function(){
					var vote = 0;
					$('.vote_item').each(function(){
						if ($(this).is(':checked'))
						{
							vote = $(this).val();
						}
					});
					if( vote > 0 )
					{
						$(this).val(loading);
						xajax_vote('{/literal}{$poll.ID}{literal}', vote, {/literal}{$poll.Random}, {$block.Tpl}{literal});
					}
				});
			});
			{/literal}
			</script>
		</div>
		<div style="margin: 5px 3px" id="poll_results_{$block.Key}" class="hide">
			{foreach from=$poll.items item='poll_item' name='pollsF'}
				<div class="poll_item_conent">
					<div>{$poll_item.name.$lang_site} (<b>{$poll_item.Votes}</b> {$lang.votes})</div>
					<div style="font-size: 10px;height: 14px;line-height: 11px;width: {if $poll_item.percent < 10}10{else}{$poll_item.percent}{/if}%; background: {$poll_item.Color};color: {if $poll_item.Color == '#ffffff'}#000000{else}#ffffff{/if}; text-align: center;">
						<b style="line-height: 13px;">{$poll_item.percent}%</b>
					</div>
				</div>
			{/foreach}
			<div>{$lang.total_votes}: <b>{$poll.total}</b></div>
			<div class="ralign"><a href="javascript: void(0);" rel="nofollow" onclick="$('#poll_results_{$block.Key}').slideUp('normal');$('#poll_items_{$block.Key}').slideDown('normal');">{$lang.polls_back_for_vote}</a></div>
		</div>
	{/if}
	</div>
{else}
	{$lang.polls_not_created}
{/if}

<!-- poll block end -->