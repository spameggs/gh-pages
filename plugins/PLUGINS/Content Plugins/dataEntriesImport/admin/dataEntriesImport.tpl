<!-- dataEntriesImport tpl -->

{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_start.tpl'}

{assign var='sPost' value=$smarty.post}

<form action="{$rlBaseC|replace:'&amp;':''}" method="post" enctype="multipart/form-data" onsubmit="return submit_form();">
	<input type="hidden" name="upload" value="1" />

	<table class="form">
	<tr>
		<td class="name">{$lang.dataEntriesImport_import_to}</td>
		<td class="field">
			{assign var='radio_field' value='import_to'}

			{if $sPost.$radio_field == 'new'}
				{assign var=$radio_field|cat:'_new' value='checked="checked"'}
				{assign var='data_entry_exists' value='class="hide"'}
			{elseif $sPost.$radio_field == 'exists'}
				{assign var=$radio_field|cat:'_exists' value='checked="checked"'}
				{assign var='data_entry_new' value='class="hide"'}
			{else}
				{assign var=$radio_field|cat:'_new' value='checked="checked"'}
				{assign var='data_entry_exists' value='class="hide"'}
			{/if}

			<table>
			<tr>
				<td>
					<input {$import_to_new} type="radio" id="{$radio_field}_new" name="{$radio_field}" value="new" /> <label for="{$radio_field}_new">{$lang.dataEntriesImport_import_to_new}</label>
					<input {$import_to_exists} type="radio" id="{$radio_field}_exists" name="{$radio_field}" value="exists" /> <label for="{$radio_field}_exists">{$lang.dataEntriesImport_import_to_exists}</label>
				</td>
			</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td class="name">
			<span class="red">*</span>{$lang.dataEntriesImport_data_entry}
		</td>
		<td class="field">
			<div {$data_entry_new} id="data_entry_new">
				<fieldset class="light">
					<legend id="legend_df_entry" class="up" onclick="fieldset_action('df_entry');">{$lang.dataEntriesImport_data_entry}</legend>
					<div id="df_entry">

						<table class="form">
						<tr>
							<td class="name">{$lang.dataEntriesImport_df_parent}</td>
							<td class="field">
								<input type="hidden" name="import_to_parent_new" value="0" />
								<div>
									<select name="df_zero_level_new" class="df_new_level_1">
										<option value="0">{$lang.select}</option>
										{foreach from=$data_formats item='entry'}
										<option {if $sPost.data_entry_exists == $entry.ID}selected="selected"{/if} value="{$entry.ID}">{$entry.name}</option>
										{/foreach}
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td class="name"><span class="red">*</span>{$lang.name}</td>
							<td class="field">
								{if $allLangs|@count > 1}
									<ul class="tabs">
										{foreach from=$allLangs item='language' name='langF'}
										<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
										{/foreach}
									</ul>
								{/if}

								{foreach from=$allLangs item='language' name='langF'}
									{if $allLangs|@count > 1}<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">{/if}
									<input type="text" name="name[{$language.Code}]" value="{$sPost.name[$language.Code]}" maxlength="350" />
									{if $allLangs|@count > 1}
											<span class="field_description_noicon">{$lang.name} (<b>{$language.name}</b>)</span>
										</div>
									{/if}
								{/foreach}
							</td>
						</tr>
						<tr>
							<td class="name">{$lang.order_type}</td>
							<td class="field">
								<select name="order_type">
									<option value="alphabetic" {if $sPost.order_type == 'alphabetic'}selected="selected"{/if}>{$lang.alphabetic_order}</option>
									<option value="position" {if $sPost.order_type == 'position'}selected="selected"{/if}>{$lang.position_order}</option>
								</select>
							</td>
						</tr>
						</table>

					</div>
				</fieldset>
			</div>
			<div {$data_entry_exists} id="data_entry_exists">
				<input type="hidden" name="import_to_parent" value="0" />
				<div>
					<select name="df_zero_level" class="df_level_1">
						<option value="0">{$lang.select}</option>
						{foreach from=$data_formats item='entry'}
						<option {if $sPost.data_entry_exists == $entry.ID}selected="selected"{/if} value="{$entry.ID}">{$entry.name}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</td>
	</tr>

	<tr>
		<td class="name">
			<span class="red">*</span>{$lang.dataEntriesImport_source}
		</td>
		<td class="field">
			<input type="file" class="file" name="source" />
			<span class="field_description">{$lang.dataEntriesImport_extensions_desc}</span>
		</td>
	</tr>

	<tr id="source_delimiter" class="hide">
		<td class="name">
			<span class="red">*</span>{$lang.dataEntriesImport_delimiter}
		</td>
		<td class="field">
			<table>
			<tr>
				<td>
					<select name="delimiter">
						<option {if $sPost.delimiter == 'new_line'}selected="selected"{/if} value="new_line">{$lang.dataEntriesImport_delimiter_new_line}</option>
						<option {if $sPost.delimiter == 'comma'}selected="selected"{/if} value="comma">{$lang.dataEntriesImport_delimiter_comma}</option>
						<option {if $sPost.delimiter == 'tab'}selected="selected"{/if} value="tab">{$lang.dataEntriesImport_delimiter_tab}</option>
						<option {if $sPost.delimiter == 'another'}selected="selected"{/if} value="another">{$lang.dataEntriesImport_delimiter_another}</option>
					</select>
				</td>
				<td style="width: 5px;"></td>
				<td {if $sPost.delimiter != 'another'}class="hide"{/if}>
					<input type="text" class="text" name="delimiter_another" value="{$sPost.delimiter_another}" />
				</td>
			</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td></td>
		<td class="field">
			<input class="submit" type="submit" value="{$lang.dataEntriesImport_upload}" />
		</td>
	</tr>
	</table>

</form>

{include file='blocks'|cat:$smarty.const.RL_DS|cat:'m_block_end.tpl'}

<script type="text/javascript">
var import_to_id = 0;
var df_level = 0;
var tmp_df_list = [];
var languages = [];
var dfLevelChanged;
var current_df_mode = 0; // 0 = new, 1 = exists

{foreach from=$allLangs item='language'}
	languages.push(['{$language.Code}', '{$language.name}']);
{/foreach}

{literal}
	$(document).ready(function() {
		$('input[name="{/literal}{$radio_field}{literal}"]').click(function() {
			if ( $(this).val() == 'exists' ) {
				$('div#data_entry_new').fadeOut('fast', function() {
					$('div#data_entry_exists').fadeIn('fast');
				});
				current_df_mode = 1;
			}
			else {
				$('div#data_entry_exists').fadeOut('fast', function() {
					$('div#data_entry_new').fadeIn('fast');
				});
				current_df_mode = 0;
			}
		});

		$('select[name=delimiter]').change(function() {
			if ( $(this).val() == 'another' ) {
				$('input[name="delimiter_another"]').parent().fadeIn('fast');
			}
			else {
				$('input[name="delimiter_another"]').parent().fadeOut('fast');
			}
		});

		$('input[name="source"]').change(function() {
			var sourceExtension = $(this).val().split('.').pop();
			if ( sourceExtension != 'xls' ) {
				$('tr#source_delimiter').fadeIn('fast');
			}
			else {
				$('tr#source_delimiter').fadeOut('fast');
			}
		});

		//
		$('select[name=df_zero_level], select[name=df_zero_level_new]').change(dfLevelChanged);
	});

	// pos: 0 = left, 1 = right
	function getModePrefix(pos) {
		return current_df_mode ? '' : (pos ? 'new_' : '_new');
	}

	function getDfLevelFromClass(s_class) {
		return parseInt(s_class.replace(current_df_mode ? 'df_level_' : 'df_new_level_', ''));
	}

	dfLevelChanged = function(sel) {
		var df_level = getDfLevelFromClass($(sel.target).attr('class'));

		if ( $(sel.target).val() != 0 ) {
			import_to_id = $(sel.target).val();
			$('input[name="import_to_parent'+ getModePrefix(0) +'"]').val(import_to_id);

			if ( tmp_df_list[import_to_id] !== undefined ) {
				dfLevelHandler(df_level);
			}
			else {
				xajax_getDFLevel(import_to_id, df_level);
			}
		}
		else {
			clearDfLevels(df_level);
			$('input[name="import_to_parent'+ getModePrefix(0) +'"]').val($(sel.target).attr('parent'));
		}
	}

	function clearDfLevels(skip_level) {
		// current_df_mode = 0; // 0 = new, 1 = exists
		$('select[class^=df_'+ getModePrefix(1) +'level_]').each(function() {
			if ( getDfLevelFromClass($(this).attr('class')) > skip_level ) {
				$(this).parent().remove();
			}
		});
	}

	//
	function dfLevelHandler(level) {
		clearDfLevels(level);
		df_level = $('select[class^=df_'+ getModePrefix(1) +'level_]').length + 1;

		var new_level_select = '<div style="padding-top:5px;"><select class="df_'+ getModePrefix(1) +'level_'+ df_level +'" parent="'+ import_to_id +'">';
		new_level_select += '<option value="0">{/literal}{$lang.select}{literal}</option>';

		for(var i=0; i < tmp_df_list[import_to_id].length; i++) {
			new_level_select += '<option value="'+ tmp_df_list[import_to_id][i].ID +'">'+ tmp_df_list[import_to_id][i].name +'</option>';
		}
		new_level_select += '</select></div>';

		$('select.df_'+ getModePrefix(1) +'level_'+ level).parent().after(new_level_select);
		$('select.df_'+ getModePrefix(1) +'level_'+ df_level).bind('change', dfLevelChanged);
	}

	// actions before submit
	function submit_form() {
		var fields = [];
		var errorMessage = '';

		var deImport = $('input[name="{/literal}{$radio_field}{literal}"]:checked').val();
		if ( deImport == 'new' ) {
			for (var i=0; i < languages.length; i++) {
				var dfName = $.trim($('input[name="name['+ languages[i][0] +']"]').val());
				if ( dfName == '' ) {
					errorMessage += {/literal}"{$lang.notice_field_empty}".replace('{literal}{field}{/literal}', '<b>{$lang.name} ('+ languages[i][1] +')</b>') +'<br />';{literal}
					fields.push('name['+ languages[i][0] +']');
				}
			};
		}
		else {
			if ( $('input[name="import_to_parent"]').val() == 0 ) {
				errorMessage += {/literal}"{$lang.notice_field_empty}".replace('{literal}{field}{/literal}', '<b>{$lang.dataEntriesImport_data_entry}</b>') +'<br />';{literal}
				fields.push('df_zero_level');
			}
		}

		var source = $('input[name="source"]').val();
		if ( source == '' ) {
			errorMessage += {/literal}"{$lang.notice_field_empty}".replace('{literal}{field}{/literal}', '<b>{$lang.dataEntriesImport_source}</b>') +'<br />';{literal}
			fields.push('source');
		}
		else {
			var allowExtensions = ['txt', 'csv', 'xls'];
			var sourceExtension = source.split('.').pop();
			if ( allowExtensions.indexOf(sourceExtension) == -1 ) {
				errorMessage += {/literal}"{$lang.notice_bad_file_ext}".replace('{literal}{ext}{/literal}', '<b>'+ sourceExtension +'</b>') +'<br />';{literal}
				fields.push('source');
			}
		}

		if ( $('select[name=delimiter]').val() == 'another' && source.split('.').pop() != 'xls' ) {
			var delimiter = $.trim($('input[name="delimiter_another"]').val());
			if ( delimiter == '' ) {
				errorMessage += {/literal}"{$lang.notice_field_empty}".replace('{literal}{field}{/literal}', '<b>{$lang.dataEntriesImport_delimiter}</b>') +'<br />';{literal}
				fields.push('delimiter_another');
			}
		}

		if ( fields.length > 0 ) {
			printMessage('error', errorMessage);
			highlightFields(fields);
			return false;
		}
		return true;
	}

	// show error fields
	function highlightFields(fields) {
		var pattern = /[\w]+\[(\w{2})\]/i;
		for (var i=0; i < fields.length; i++) {
			if ( fields[i] != 'source' ) {

				if ( pattern.test(fields[i]) )
				{
					$('input[name="'+fields[i]+'"]').parent().parent().find('ul.tabs li[lang='+fields[i].match(pattern)[1]+']').addClass('error');
					$('input[name="'+fields[i]+'"]').click(function(){
						$(this).parent().parent().find('ul.tabs li[lang='+$(this).attr('name').match(pattern)[1]+']').removeClass('error');
					});
					$('textarea[name="'+fields[i]+'"]').parent().parent().parent().find('ul.tabs li[lang='+fields[i].match(pattern)[1]+']').addClass('error');
					$('textarea[name="'+fields[i]+'"]').click(function(){
						$(this).parent().parent().parent().find('ul.tabs li[lang='+$(this).attr('name').match(pattern)[1]+']').removeClass('error');
					});
				}

				$('input[name="'+ fields[i] +'"],select[name="'+ fields[i] +'"]').addClass('error');
				$('input[name="'+ fields[i] +'"],select[name="'+ fields[i] +'"]').focus(function() {
					$(this).removeClass('error');
				});
			}
		}
	}

{/literal}
</script>

<!-- dataEntriesImport tpl end -->