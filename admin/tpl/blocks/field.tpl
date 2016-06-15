<!-- fields block ( for insert ) -->

<table class="form">

{foreach from=$fields item='field'}
	{assign var='fKey' value=$field.Key}
	{assign var='fVal' value=$smarty.post.f}

	<tr>
		<td class="name">
			{if $field.Required}
				<span class="red">*</span>
			{/if}
			{$lang[$field.pName]}
			
			{if $lang[$field.pDescription]}
				<img class="qtip" alt="" src="{$rlTplBase}img/blank.gif" title="{$lang[$field.pDescription]}" />
			{/if}
		</td>
		<td class="field {if $field.Condition == 'html'}ckeditor{/if}">

		{if $field.Type == 'text'}
			{if $field.Multilingual && $allLangs|@count > 1}
				<ul class="tabs">
					{foreach from=$allLangs item='language' name='langF'}
					<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
					{/foreach}
				</ul>
				
				{foreach from=$allLangs item='language' name='langF'}
					<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">
						<input class="w350" type="text" name="f[{$field.Key}][{$language.Code}]" maxlength="{if $field.Values != ''}{$field.Values}{else}255{/if}" {if $fVal.$fKey[$language.Code]}value="{$fVal.$fKey[$language.Code]}"{elseif $field.Default}value="{$lang[$field.pDefault]}"{/if} /> <span class="field_description_noicon">{$language.name}</span>
					</div>
				{/foreach}
			{else}
				<input class="w350" type="text" name="f[{$field.Key}]" maxlength="{if $field.Values != ''}{$field.Values}{else}255{/if}" {if $fVal.$fKey}value="{$fVal.$fKey}"{elseif $field.Default}value="{$lang[$field.pDefault]}"{/if} />
			{/if}
		{elseif $field.Type == 'textarea'}
			<script type="text/javascript">var textarea_fields = new Array();</script>
			{if $field.Multilingual && $allLangs|@count > 1}
				<ul class="tabs">
					{foreach from=$allLangs item='language' name='langF'}
					<li lang="{$language.Code}" {if $smarty.foreach.langF.first}class="active"{/if}>{$language.name}</li>
					{/foreach}
				</ul>
				
				{foreach from=$allLangs item='language' name='langF'}
					<div class="tab_area{if !$smarty.foreach.langF.first} hide{/if} {$language.Code}">
						{if $field.Condition == 'html'}<div class="hide">{if $fVal.$fKey[$language.Code]}{$fVal.$fKey[$language.Code]}{elseif $field.Default}{$lang[$field.default]}{/if}</div>{/if}
						<textarea id="textarea_{$field.Key}_{$language.Code}" rows="5" cols="" class="resizable" name="f[{$field.Key}][{$language.Code}]">{if $field.Condition != 'html'}{if $fVal.$fKey[$language.Code]}{$fVal.$fKey[$language.Code]}{elseif $field.Default}{$lang[$field.default]}{/if}{/if}</textarea>
						<script type="text/javascript">
						textarea_fields.push('textarea_{$field.Key}_{$language.Code}');
						</script>
					</div>
				{/foreach}
			{else}
				{if $field.Condition == 'html'}<div class="hide">{if $fVal.$fKey}{$fVal.$fKey}{elseif $field.Default}{$lang[$field.default]}{/if}</div>{/if}
				<textarea id="textarea_{$field.Key}" rows="5" cols="" class="resizable" name="f[{$field.Key}]">{if $field.Condition != 'html'}{if $fVal.$fKey}{$fVal.$fKey}{elseif $field.Default}{$lang[$field.default]}{/if}{/if}</textarea>
				<script type="text/javascript">
				textarea_fields.push('textarea_{$field.Key}');
				</script>
			{/if}
			
			{if $field.Condition == 'html'}
			<script type="text/javascript">
			{literal}
			
			flynax.htmlEditor(textarea_fields);
			
			{/literal}
			</script>
		{/if}
		{elseif $field.Type == 'number'}
			<input class="numeric w60" type="text" name="f[{$field.Key}]" size="{if $field.Values}{$field.Values|count_characters}{else}10{/if}" maxlength="{if $field.Values}{$field.Values|count_characters}{else}10{/if}" {if $fVal.$fKey}value="{$fVal.$fKey}"{elseif $field.Default}value="{$field.default}"{/if} />
		{elseif $field.Type == 'phone'}
			<span class="phone-field">
				{if $field.Opt1}
					+ <input type="text" name="f[{$field.Key}][code]" {if $fVal.$fKey.code}value="{$fVal.$fKey.code}"{/if} maxlength="4" size="3" class="wauto ta-center numeric" /> -
				{/if}
				{if $field.Condition}
					{assign var='df_source' value=$field.Condition|df}
					<select name="f[{$field.Key}][area]" class="w60">
						{foreach from=$df_source item='df_item' key='df_key'}
							<option value="{$lang[$df_item.pName]}" {if $fVal.$fKey.area}{if $lang[$df_item.pName] == $fVal.$fKey.area}selected="selected"{/if}{else}{if $df_item.Default}selected="selected"{/if}{/if}>{$lang[$df_item.pName]}</option>
						{/foreach}
					</select>
				{else}
					<input type="text" name="f[{$field.Key}][area]" {if $fVal.$fKey.area}value="{$fVal.$fKey.area}"{/if} maxlength="{$field.Default}" size="{$field.Default}" class="wauto ta-center numeric" />
				{/if}
				-
				<input type="text" name="f[{$field.Key}][number]" {if $fVal.$fKey.number}value="{$fVal.$fKey.number}"{/if} maxlength="{$field.Values}" size="{$field.Values+2}" class="wauto ta-center numeric" />
				{if $field.Opt2}
					{$lang.phone_ext_out} <input type="text" name="f[{$field.Key}][ext]" {if $fVal.$fKey.ext}value="{$fVal.$fKey.ext}"{/if} maxlength="4" size="3" class="wauto ta-center" />
				{/if}
			</span>
		{elseif $field.Type == 'date'}
			{if $field.Default == 'single'}
				<input class="text" type="text" id="date_{$field.Key}" name="f[{$field.Key}]" maxlength="10" style="width: 70px;float: left;" value="{$fVal.$fKey}" />
				<script type="text/javascript">
				{literal}
				$(document).ready(function(){
					$('#date_{/literal}{$field.Key}{literal}').datepicker({showOn: 'both', buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif', buttonText: '{/literal}{$lang.dp_choose_date}{literal}', buttonImageOnly: true, dateFormat: 'yy-mm-dd'}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
				});
				{/literal}
				</script>
			{elseif $field.Default == 'multi'}
				<table>
				<tr>
					<td><label for="date_{$field.Key}_from" class="fLable">{$lang.from}</label></td>
					<td style="width: 120px;"><input type="text" id="date_{$field.Key}_from" name="f[{$field.Key}][from]" maxlength="10" style="width: 70px;float: left;" value="{$fVal.$fKey.from}" /></td>
					<td><label for="date_{$field.Key}_to" class="fLable">{$lang.to}</label></td>
					<td style="width: 120px;"><input type="text" id="date_{$field.Key}_to" name="f[{$field.Key}][to]" maxlength="10" style="width: 70px;float: left;" value="{$fVal.$fKey.to}" /></td>
				</tr>
				</table> 
				<script type="text/javascript">
				{literal}
				$(document).ready(function(){
					$('#date_{/literal}{$field.Key}{literal}_from').datepicker({showOn: 'both', buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif', buttonText: '{/literal}{$lang.dp_choose_date}{literal}', buttonImageOnly: true, dateFormat: 'yy-mm-dd'}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
					$('#date_{/literal}{$field.Key}{literal}_to').datepicker({showOn: 'both', buttonImage: '{/literal}{$rlTplBase}{literal}img/blank.gif', buttonText: '{/literal}{$lang.dp_choose_date}{literal}', buttonImageOnly: true, dateFormat: 'yy-mm-dd'}).datepicker($.datepicker.regional['{/literal}{$smarty.const.RL_LANG_CODE}{literal}']);
				});
				{/literal}
				</script>
			{/if}
		{elseif $field.Type == 'mixed'}
			<input class="numeric float" type="text" name="f[{$field.Key}][value]" size="8" maxlength="15" {if $fVal.$fKey.value}value="{$fVal.$fKey.value}"{/if} style="width: 70px;" />
			<select class="float lm" name="f[{$field.Key}][df]" style="width: 80px;margin-bottom: 0;">
				{if !empty($field.Condition)}
					{assign var='df_source' value=$field.Condition|df}
				{else}
					{assign var='df_source' value=$field.Values}
				{/if}
				{foreach from=$df_source item='df_item'}
					<option value="{$df_item.Key}" {if $df_item.Key == $fVal.$fKey.df}selected="selected"{/if}>{$lang[$df_item.pName]}</option>
				{/foreach}
			</select>
		{elseif $field.Type == 'price'}
			<input class="numeric float" type="text" name="f[{$field.Key}][value]" size="8" maxlength="15" {if $fVal.$fKey.value}value="{$fVal.$fKey.value}"{/if} style="width: 70px;" />
			<select class="float lm" name="f[{$field.Key}][currency]" style="width: 60px;">
				{foreach from='currency'|df item='currency_item'}
					<option value="{$currency_item.Key}" {if $currency_item.Key == $fVal.$fKey.currency}selected="selected"{/if}>{$lang[$currency_item.pName]}</option>
				{/foreach}
			</select>
		{elseif $field.Type == 'bool'}
			<label><input type="radio" value="1" name="f[{$field.Key}]" {if $fVal.$fKey == '1'}checked="checked"{elseif $field.Default}checked="checked"{/if} /> {$lang.yes}</label>
			<label><input type="radio" value="0" name="f[{$field.Key}]" {if $fVal.$fKey == '0'}checked="checked"{elseif !$field.Default}checked="checked"{/if} /> {$lang.no}</label>
		{elseif $field.Type == 'select'}
			{rlHook name='apTplListingFieldSelect'}
			<select name="f[{$field.Key}]" {if $field.Condition == 'years'}style="width: 110px;"{/if}>
				<option value="0">{$lang.select}</option>
				{foreach from=$field.Values item='option' key='key'}
					{if $field.Condition}
						{assign var='key' value=$option.Key}
					{/if}
					<option value="{if $field.Condition}{$option.Key}{else}{$key}{/if}" {if $fVal.$fKey}{if $fVal.$fKey == $key}selected="selected"{/if}{else}{if $field.Default == $key}selected="selected"{/if}{/if}>{if $field.Condition == 'years'}{$option.name}{else}{$lang[$option.pName]}{/if}</option>
				{/foreach}
			</select>
		{elseif $field.Type == 'checkbox'}
			{if $field.Opt2}
				{assign var='col_num' value=$field.Opt2}
			{else}
				{assign var='col_num' value=3}
			{/if}
			{assign var='fDefault' value=$field.Default}
			<input type="hidden" name="f[{$field.Key}][0]" value="0" />
			<table {if $col_num > 2} class="fixed"{/if}>
			<tr>
			{foreach from=$field.Values item='option' key='key' name='checkboxF'}
				{if !empty($field.Condition)}
					{assign var="key" value=$option.Key}
				{/if}
				<td style="padding: 2px 0;">
					<label><input type="checkbox" value="{$key}" {if is_array($fVal.$fKey)}{foreach from=$fVal.$fKey item='chVals'}{if $chVals == $key}checked="checked"{/if}{/foreach}{else}{foreach from=$field.Default item='chDef'}{if $chDef == $key && !empty($chDef)}checked="checked"{/if}{/foreach}{/if} name="f[{$field.Key}][{$key}]" /> {$lang[$option.pName]}</label>
				</td>
				{if $smarty.foreach.checkboxF.iteration%$col_num == 0 && !$smarty.foreach.checkboxF.last}
				</tr>
				<tr>
				{/if}
			{/foreach}
			</tr>
			</table>
			<div style="padding: 2px 0 5px;"><a href="javascript:void(0)" onclick="$(this).parent().prev().find('input[type=checkbox]').attr('checked', true)">{$lang.check_all}</a> / <a onclick="$(this).parent().prev().find('input[type=checkbox]').attr('checked', false)" href="javascript:void(0)">{$lang.uncheck_all}</a></div>
		{elseif $field.Type == 'radio'}
			<input type="hidden" value="0" name="f[{$field.Key}]" />
			<table id="{$field.Key}_table">
			<tr>
			{foreach from=$field.Values item='option' key='key' name='radioF'}
				{if $field.Condition}
					{assign var='key' value=$option.Key}
				{/if}
				<td {if $smarty.foreach.radioF.total > 3}style="width: 33%"{/if}>
					<label><input type="radio" value="{$key}" name="f[{$field.Key}]" {if $fVal.$fKey}{if $fVal.$fKey == $key}checked="checked"{/if}{else}{if $field.Default == $key}checked="checked"{/if}{/if} /> {$lang[$option.pName]}</label>
				</td>
				{if $smarty.foreach.radioF.iteration%3 == 0 && !$smarty.foreach.radioF.last}
				</tr>
				<tr>
				{/if}
			{/foreach}
			</tr>
			</table>
		{elseif $field.Type == 'file' || $field.Type == 'image'}
			{assign var='field_type' value=$field.Default}
			<input type="hidden" name="f[{$field.Key}]" value="" />
			<input class="file" type="file" name="{$field.Key}" />{if $field.Type == 'file' && !empty($field.Default)}<span class="green_11"> <em>{$l_file_types.$field_type.name} (.{$l_file_types.$field_type.ext|replace:',':', .'})</em></span>{/if}
		{elseif $field.Type == 'accept'}
			<textarea style="width: 80%;" rows="6" readonly="readonly" class="text" name="{$field.Key}">{$field.default}</textarea><br />
			<input type="hidden" name="f[{$field.Key}]" value="no" />
			<input type="checkbox" id="{$field.Key}" name="f[{$field.Key}]" value="yes" /> <label for="{$field.Key}" class="fLable">{$lang.accept}</label>
			{if $field.Required}
				<span class="red">*</span>
			{/if}
		{/if}
		</td>
	</tr>	

{/foreach}

</table>

<!-- fields block ( for insert ) end -->