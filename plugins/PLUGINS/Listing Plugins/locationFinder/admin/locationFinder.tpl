<!-- location finder -->

{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl' block_caption=$lang.locationFinder_settings}

	<table class="form">
	<tr>
		<td class="name">{$lang.locationFinder_position}</td>
		<td class="field">
			<select id="locationFinder_position">
				<option value="top">{$lang.locationFinder_form_top}</option>
				<option value="bottom" {if $config.locationFinder_position == 'bottom'}selected="selected"{/if}>{$lang.locationFinder_form_bottom}</option>
				<optgroup style="font-size: 11px;font-style: normal;padding: 0 0 4px 10px;" label="{$lang.locationFinder_place_in_form}">
					{foreach from=$groups item='group'}
						<option {if $config.locationFinder_position == $group.Key}selected="selected"{/if} style="font-size: 13px;" value="{$group.Key}">{$group.name}</option>
					{/foreach}
				</optgroup>
			</select>
		</td>
	</tr>
	</table>
	
	<div id="type_dom" class="hide">
		<table class="form">
		<tr>
			<td class="name">{$lang.locationFinder_position_type}</td>
			<td class="field">
				<label><input {if $config.locationFinder_type == 'prepend' || !$config.locationFinder_type}checked="checked"{/if} type="radio" name="post_type" value="prepend" /> {$lang.locationFinder_prepend}</label>
				<label><input {if $config.locationFinder_type == 'append'}checked="checked"{/if} type="radio" name="post_type" value="append" /> {$lang.locationFinder_append}</label>
			</td>
		</tr>
		</table>
	</div>
	
	<table class="form">
	<tr>
		<td class="name no_divider"></td>
		<td class="field">
			<input id="lf_button" type="button" class="button lang_add" value="{$lang.save}" />
		</td>
	</tr>
	</table>
	
{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

<script type="text/javascript">
{if $config.locationFinder_type == 'append' || $config.locationFinder_type == 'prepend' && ($config.locationFinder_position != 'bottom' && $config.locationFinder_position != 'top')}
{literal}
$(document).ready(function(){
	$('#type_dom').slideDown();
});
{/literal}
{/if}
{literal}

$('#locationFinder_position').change(function(){
	if ( $(this).val() == 'top' || $(this).val() == 'bottom' )
	{
		$('#type_dom').slideUp();
	}
	else
	{
		$('#type_dom').slideDown();
	}
});

$('#lf_button').click(function(){
	$(this).val(lang['loading']).attr('disabled', true);
	xajax_save($('#locationFinder_position').val(), $('#type_dom input[name=post_type]:checked').val());
});

{/literal}
</script>

<!-- location finder end -->