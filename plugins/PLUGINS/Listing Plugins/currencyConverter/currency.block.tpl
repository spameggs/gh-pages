<!-- my currency end -->
{*if $curConv_rates[$curConv_country.Currency].Symbol}{assign var='cc_symbol' value=','|explode:$curConv_rates[$curConv_country.Currency].Symbol}{$cc_symbol.0}{else}{$curConv_rates[$curConv_country.Currency].Code}{/if*}

<table class="table">
<tr>
	<td class="name" style="width: 65px;">{$lang.currencyConverter_location}:</td>
	<td class="value">{if $curConv_country.Name}<img style="vertical-align: text-top;" title="{$curConv_country.Name}" alt="{$curConv_country.Name}" src="{$rlTplBase}img/flags/{$curConv_country.Code|lower}.png" /> {$curConv_country.Name}{else}{$lang.currencyConverter_na}{/if}</td>
</tr>
<tr>
	<td class="name" style="width: 65px;">{$lang.currencyConverter_currency}:</td>
	<td class="value">
		{if $curConv_country.Currency}
			<span id="curConv_1"><b>{if $curConv_search_key}{$curConv_rates[$curConv_search_key].Code}{else}{$curConv_rates[$curConv_country.Currency].Code}{/if}</b> <a id="curConv_change" href="javascript:void(0)" class="static">{$lang.currencyConverter_change}</a></span>
			<span id="curConv_2" class="hide">
				<select style="width: 55px;" id="curConv_code">
					<option value="0">{$lang.currencyConverter_choose}</option>
					{foreach from=$curConv_rates item='curConv_rate' key='currencyKey'}
						<option title="{$curConv_rate.Country}" {if $currencyKey == $curConv_country.Currency}selected="selected"{/if} value="{$currencyKey}">{$curConv_rate.Code}</option>
					{/foreach}
				</select>
				<span class="loading_highlight" id="curConv_loading" style="margin: 0 5px;vertical-align: text-top;"></span>
			</span>
		{else}
			<span id="curConv_1" class="hide"><b></b> <a id="curConv_change" href="javascript:void(0)" class="static">{$lang.currencyConverter_change}</a></span>
			<span id="curConv_2">
				<select style="width: 55px;" id="curConv_code">
					<option value="0">{$lang.currencyConverter_choose}</option>
					{foreach from=$curConv_rates item='curConv_rate' key='currencyKey'}
						<option title="{$curConv_rate.Country}" value="{$currencyKey}">{$curConv_rate.Code}</option>
					{/foreach}
				</select>
				<span class="loading_highlight" id="curConv_loading" style="margin: 0 5px;vertical-align: text-top;"></span>
			</span>
		{/if}
	</td>
</tr>
</table>

<script type="text/javascript">
currencyConverter.boxActions();
</script>

<!-- my currency block end -->