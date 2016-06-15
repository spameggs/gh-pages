<tr>
	<td class="name">{$lang.ls_substatus}:</td>
	<td class="value" id="status_bar">		
		<select id="status_selector_{$smarty.get.id}" class="selector" style="width:85px;">
			{foreach from=$status item='item}
				{if $item.Type=='all'}
					<option value="{$item.Key}" {if $listing_info.Sub_status == $item.Key}selected="selected"{/if}>{$item.name}</option>
				{elseif $listing_type.Type|in_array:$item.Type}
					<option value="{$item.Key}" {if $listing_info.Sub_status == $item.Key}selected="selected"{/if}>{$item.name}</option>
				{/if}
			{/foreach}
		</select>
		<script type="text/javascript">
		{literal}
			$(document).ready(function(){
				$('#status_bar select.selector').change(function(){ 
					var id = $(this).attr('id').split('_')[2]; 
					xajax_changeStatus(id, $(this).val(), true); 
				}); 
			});
		{/literal}
		</script>
	<td>
</tr>

