<tr>
	<td class="name">{$lang.ls_substatus}:</td>
	<td class="value">
		<select id="status_selector_{$listing.ID}" class="selector" style="width:85px;">
			{foreach from=$status item='item}
				{if $item.Type=='all'}
					<option value="{$item.Key}" {if $listing.Sub_status == $item.Key}selected="selected"{/if}>{$item.name}</option>
				{elseif $listing_type.Type|in_array:$item.Type}
					<option value="{$item.Key}" {if $listing.Sub_status == $item.Key}selected="selected"{/if}>{$item.name}</option>
				{/if}
			{/foreach}
		</select>
	<td>
</tr>
