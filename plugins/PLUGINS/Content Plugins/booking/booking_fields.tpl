<!-- booking fields -->

<table class="submit">
	{foreach from=$fields item='field'}
	{assign var='fKey' value=$field.Key}
	{assign var='fVal' value=$smarty.post.f}

	<tr>
		<td class="name">
			{if $field.Required}<span class="red">*</span>{/if} {$field.name}
			{if !empty($field.description)}
				<img class="qtip" alt="" title="{$field.description}" id="fd_{$field.Key}" src="{$rlTplBase}img/blank.gif" />
			{/if}
		</td>

		<td class="field">
			{if $field.Type == 'text'}
			<input class="text{if $field.Required} req-string{/if}{if $field.Condition=='isEmail'} req-email{elseif $field.Condition=='isUrl'} req-url{/if}" type="text" name="{$field.Key}" maxlength="{if $field.Values != ''}{$field.Values}{else}255{/if}" {if $fVal.$fKey}value="{$fVal.$fKey}"{elseif $field.Default}value="{$field.default}"{/if} />
			{elseif $field.Type == 'textarea'}
			<textarea rows="5" class="text{if $field.Required} req-string{/if}" name="{$field.Key}" id="textarea_{$field.Key}">{if $fVal.$fKey}{$fVal.$fKey}{elseif $field.Default}{$field.default}{/if}</textarea>
			<script type="text/javascript">//<![CDATA[
			{literal}
				$(document).ready(function() {
					$('#textarea_{/literal}{$field.Key}{literal}').textareaCount({
						'maxCharacterSize': {/literal}{$field.Values}{literal},
						'warningNumber': 20
					})
				});
			{/literal}//]]>
			</script>
			{elseif $field.Type == 'number'}
			<input class="numeric wauto {if $field.Required}req-string{/if} req-numeric" type="text" name="{$field.Key}" size="{if $field.Default}{$field.Default}{else}18{/if}" maxlength="{if $field.Default}{$field.Default}{else}18{/if}" {if $fVal.$fKey}value="{$fVal.$fKey}"{/if} />
			{elseif $field.Type == 'bool'}
			<input id="{$field.Key}_1" type="radio" value="1" name="{$field.Key}" {if $fVal.$fKey == '1'}checked="checked"{elseif $field.Default}checked="checked"{/if} /> <label for="{$field.Key}_1" class="fLable">{$lang.yes}</label>
			<input id="{$field.Key}_0" type="radio" value="0" name="{$field.Key}" {if $fVal.$fKey == '0'}checked="checked"{elseif !$field.Default}checked="checked"{/if} /> <label for="{$field.Key}_0" class="fLable">{$lang.no}</label>
			{/if}
		</td>
	</tr>
	{/foreach}
	</table>

	<table class="submit">
	<tr>
		<td class="name"></td>
		<td class="field">
			<input type="submit" class="button" id="checkValid" value="{$lang.booking_complete_booking}" />
			<span class="cancel">{$lang.cancel}</span>
		</td>
	</tr>
	</table>

<!-- booking fields end -->