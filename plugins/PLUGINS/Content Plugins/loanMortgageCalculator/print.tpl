<!DOCTYPE html>
<html>
<head>
<title>{$listing_title} - {$pageInfo.title}</title>
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta name="generator" content="Flynax Classifieds Software" />
<meta http-equiv="Content-Type" content="text/html; charset={$config.encoding}" />
<meta name="description" content="{$pageInfo.meta_description|strip_tags}" />
<meta name="Keywords" content="{$pageInfo.meta_keywords|strip_tags}" />
<link href="{$smarty.const.RL_PLUGINS_URL}loanMortgageCalculator/static/print.css" type="text/css" rel="stylesheet" />
<link type="image/x-icon" rel="shortcut icon" href="{$rlTplBase}img/favicon.ico" />
{if $smarty.const.RL_LANG_DIR == 'rtl'}
	{assign var='text_dir' value='right'}
	{assign var='text_dir_rev' value='left'}
{else}
	{assign var='text_dir' value='left'}
	{assign var='text_dir_rev' value='right'}
{/if}
{if !$errors}
	<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.js"></script>
	<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/cookie.js"></script>
	<script type="text/javascript" src="{$rlTplBase}js/lib.js"></script>
	<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}javascript/flynax.lib.js"></script>
	<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.ui.js"></script>
	<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/datePicker/i18n/ui.datepicker-{$smarty.const.RL_LANG_CODE|lower}.js"></script>
	<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}loanMortgageCalculator/static/loan_calc.js"></script>
	<script type="text/javascript">
	var lm_configs = new Array();
	lm_configs['mode'] = false;
	lm_configs['show_cents'] = {$config.show_cents};
	lm_configs['price_delimiter'] = "{$config.price_delimiter}";
	lm_configs['currency'] = '{if $smarty.get.mode == 'converted'}{$smarty.get.currency}{else}{$currency_exp.1}{/if}';
	lm_configs['lang_code'] = '{if $smarty.const.RL_LANG_CODE == 'en'}en-GB{else}{$smarty.const.RL_LANG_CODE|lower}{/if}';
	lm_configs['loan_term_mode'] = '{$smarty.get.term_mode}';
	lm_configs['loan_currency_mode'] = 'original';
	lm_configs['loan_orig_amount'] = {$smarty.get.amount};
	lm_configs['loan_orig_currency'] = '{$currency_exp.2}';
	var lm_phrases = new Array();
	lm_phrases['loan_amount'] = '{$lang.loanMortgage_loan_amount}';
	lm_phrases['num_payments'] = '{$lang.loanMortgage_num_payments}';
	lm_phrases['monthly_payment'] = '{$lang.loanMortgage_monthly_payment}';
	lm_phrases['total_paid'] = '{$lang.loanMortgage_total_paid}';
	lm_phrases['total_interest'] = '{$lang.loanMortgage_total_interest}';
	lm_phrases['payoff_date'] = '{$lang.loanMortgage_payoff_date}';
	lm_phrases['pmt_date'] = '{$lang.loanMortgage_pmt_date}';
	lm_phrases['amount'] = '{$lang.loanMortgage_amount}';
	lm_phrases['interest'] = '{$lang.loanMortgage_interest}';
	lm_phrases['principal'] = '{$lang.loanMortgage_principal}';
	lm_phrases['balance'] = '{$lang.loanMortgage_balance}';
	lm_phrases['error_amount'] = '{$lang.loanMortgage_error_amount}';
	lm_phrases['error_term'] = '{$lang.loanMortgage_error_term}';
	lm_phrases['error_rate'] = '{$lang.loanMortgage_error_rate}';
	var lm_left_align = '{$text_dir}';
	var lm_right_align = '{$text_dir_rev}';
	{literal}
	$(document).ready(function(){
		{/literal}
		loan_show(
			'{$smarty.get.term}',
			'{$smarty.get.amount}',
			'{$smarty.get.term_mode}',
			'{$smarty.get.rate}',
			'{$smarty.get.date_month_number}',
			'{$smarty.get.date_year}'
		);
		{literal}
	});
	{/literal}
	</script>
{/if}
</head>
<body>
	<div class="container">
		{if !$errors}
			<div class="photo"><img src="{$smarty.const.RL_FILES_URL}{$main_photo}" /></div>
	
			<div class="info">
				<h1>{$listing_title}</h1>
				<table class="table">
				{foreach from=$listing_short item='field'}
				<tr>
					<td>{$field.name}:</td>
					<td>{$field.value}</td>
				</tr>
				{/foreach}
				<tr>
					<td class="divider" colspan="2">{$lang.seller_info}</td>
				</tr>
				{foreach from=$seller_short item='field'}
				<tr>
					<td>{$field.name}:</td>
					<td>{$field.value}</td>
				</tr>
				{/foreach}
				</table>
			</div>
			<div class="caption">{$lang.loanMortgage_calculation}</div>
			<div class="section">
				<div class="left">
					<div class="caption">{$lang.loanMortgage_loan_terms}</div>
					
					<table class="table">
					{foreach from=$loan_terms item='field'}
					<tr>
						<td>{$field.name}:</td>
						<td>{$field.value}</td>
					</tr>
					{/foreach}
					</table>
				</div>
			</div>
			<div class="section">
				<div class="right">
					<div class="caption">{$lang.loanMortgage_payments}</div>
					<div id="lm_details_area"></div>
				</div>
			</div>
			
			<div class="section-wide">
				<div class="caption">{$lang.loanMortgage_amz_schedule}</div>
				<div class="body" id="lm_amortization_area"></div>
			</div>
		{else}
			<ul>
			{foreach from=$errors item='error'}
				<li>{$error}</li>
			{/foreach}
			</ul>
		{/if}
	</div>
</body>
</html>