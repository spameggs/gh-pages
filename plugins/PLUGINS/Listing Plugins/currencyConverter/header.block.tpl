<!-- currency converter header code -->

<link href="{$smarty.const.RL_PLUGINS_URL}currencyConverter/static/style.css" type="text/css" rel="stylesheet" />
{if $smarty.const.RL_LANG_DIR == 'rtl'}
<link href="{$smarty.const.RL_PLUGINS_URL}currencyConverter/static/rtl.css" type="text/css" rel="stylesheet" />
{/if}

<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}currencyConverter/static/lib.js"></script>
<script type="text/javascript">
currencyConverter.config['currency'] = {if $curConv_search_key}'{$curConv_search_key}'{elseif $curConv_country.Currency}'{if $curConv_mapping[$curConv_country.Currency]}{$curConv_mapping[$curConv_country.Currency]}{else}{$curConv_country.Currency}{/if}'{else}false{/if};
currencyConverter.config['field'] = '{$config.currencyConverter_price_field}';
currencyConverter.config['show_flags'] = {$config.currencyConverter_show_flag};
currencyConverter.config['show_cents'] = {$config.show_cents};
currencyConverter.config['price_delimiter'] = "{$config.price_delimiter}";
currencyConverter.config['position'] = '{$config.currencyConverter_position}';
currencyConverter.config['featured'] = {if $config.currencyConverter_featured}true{else}false{/if};
currencyConverter.config['flags_dir'] = '{$smarty.const.RL_PLUGINS_URL}currencyConverter/static/flags/';
currencyConverter.phrases['converted'] = '{$lang.currencyConverter_converted}';

{foreach from=$curConv_rates item='curConv_rate' key='curConv_key'}
currencyConverter.rates['{$curConv_key}'] = new Array('{$curConv_rate.Rate}', ['{$curConv_rate.Code}'{if $curConv_rate.Symbol},{foreach from=','|explode:$curConv_rate.Symbol item='cc_rItem' name='ccF'}'{$cc_rItem|flHtmlEntriesDecode}'{if !$smarty.foreach.ccF.last},{/if}{/foreach}{/if}]);
{/foreach}

{literal}

$(document).ready(function(){
	currencyConverter.convert();
});

{/literal}
</script>

<!-- currency converter header code -->