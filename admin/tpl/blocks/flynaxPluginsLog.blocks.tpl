<!-- Plugins log DOM -->
{if $change_log_content}
	<table class="sTable">
	{foreach from=$change_log_content item='log_item' key='pLog_key'}
	<tr>
		<td class="list-date">
			{$log_item.date|date_format:'%d'}
			<div>{$log_item.date|date_format:'%b'}</div>
		</td>
			
		<td class="list-body changelog_{$log_item.status}">
			<div class="changelog_item" id="pChangelog_{$pLog_key}">
                <span class="x-grid3-col-rlExt_item_bold">{$log_item.name}</span>
				<span class="dark_13">&rarr; {$log_item.version}</span>
				{if $log_item.status == 'current'}
					<span class="gray-border">{$lang.current_version}</span>
				{/if}
				
				<div class="grey_13">
					{$log_item.comment|strip_tags|nl2br}
				</div>
			</div>
		</td>
	</tr>
	{/foreach}
	</table>
{/if}
<!-- Plugins log DOM end -->