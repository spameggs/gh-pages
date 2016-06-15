<!-- paypalPro plugin -->

<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/numeric.js"></script>

<div class="highlight">
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='dpp_item_info' name=$lang.dpp_item_info}
		<table class="table">
		{if $listing.fields}
			<tr>
				<td class="name" width="180">{$lang.dpp_plan_name}</td>
				<td class="value">{assign var='plan_phrase_key' value=$plan_info.Type|cat:'_plan'}{$plan_info.name} ({$lang.$plan_phrase_key})</td>
			</tr>
			<tr>
				<td class="name" width="180">{$lang.dpp_item_price}</td>
				<td class="value">
					{if $config.system_currency_position == 'before'}{$config.system_currency}{/if}{$smarty.session.complete_payment.plan_info.Price}{if $config.system_currency_position == 'after'}{$config.system_currency}{/if}
				</td>
			</tr>
			<tr>
				<td class="name" width="180">{$lang.dpp_item_name}</td>
				<td class="value">{$listing.listing_title}</td>
			</tr> 
		{else}	
			<tr>
				<td class="name" width="180">{$lang.dpp_item_name}</td>
				<td class="value">{$smarty.session.complete_payment.item_name}</td>
			</tr>	
			<tr>
				<td class="name" width="180">{$lang.dpp_item_price}</td>
				<td class="value">
					{if $config.system_currency_position == 'before'}{$config.system_currency}{/if}{$smarty.session.complete_payment.plan_info.Price}{if $config.system_currency_position == 'after'}{$config.system_currency}{/if}
				</td>
			</tr>
		{/if}
		</table>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}

	<form action="{$rlBase}{if $config.mod_rewrite}{$pages.paypal_pro}.html{else}?page={$pages.paypal_pro}{/if}" method="post">
		<input type="hidden" name="form" value="submit" />
	
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='dpp_payment_info' name=$lang.dpp_credit_card_details style='fg'}
		<table class="submit">
			<tr>
				<td class="name">{$lang.dpp_card_number}:</td>
				<td class="field">
					<input type="text" id="card_number_1" name="dpp[card_number][1]" autocomplete="off" maxlength="4" value="{$smarty.post.dpp.card_number[1]}" class="numeric card_number" />
					<input type="text" id="card_number_2" name="dpp[card_number][2]" autocomplete="off" maxlength="4" value="{$smarty.post.dpp.card_number[2]}" class="numeric card_number" />
					<input type="text" id="card_number_3" name="dpp[card_number][3]" autocomplete="off" maxlength="4" value="{$smarty.post.dpp.card_number[3]}" class="numeric card_number" />
					<input type="text" id="card_number_4" name="dpp[card_number][4]" autocomplete="off" maxlength="4" value="{$smarty.post.dpp.card_number[4]}" class="numeric card_number" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_payment_types}:</td>
				<td class="field">
					<div style="float: left; padding-right: 5px;">
						<select id="dpp_payment_type" name="dpp[payment_type]">
							<option value="">{$lang.select}</option>
							{foreach from=$card_types item='card_name' key='card_type'}
								<option value="{$card_type}" {if $card_type == $smarty.post.card_type}selected="selected"{/if}>{$card_name}</option>
							{/foreach}
						</select>
					</div>
					
					<div class="hide" style="float: left; padding-bottom: 3px;">
						<img id="payment_type_img" src="{$smarty.const.RL_PLUGINS_URL}paypalPro/static/Visa.png" />	
					</div>
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_expiration_date}:</td>
				<td class="field" valign="bottom">
					<p style="float: {$text_dir};">
						<select name="dpp[month]" class="pp_select_month">
							<option value=""> - </option>
							{foreach from=$months item='month'}
								<option value="{$month}" {if $month == $smarty.post.dpp.month}selected="selected"{/if}>{$month}</option>
							{/foreach}
						</select>&nbsp;&nbsp;
					</p>
					<p style="float: {$text_dir};">
						<select name="dpp[year]" class="pp_select_year">
							<option value=""> - </option>
							{foreach from=$years item='year'}
								<option value="{$year}" {if $year == $smarty.post.dpp.year}selected="selected"{/if}>{$year}</option>
							{/foreach}
						</select>&nbsp;&nbsp;
					</p>
					<p style="float: {$text_dir};">
						{$lang.dpp_csc}:&nbsp;
						<input type="text" name="dpp[csc]" autocomplete="off" value="{$smarty.post.dpp.csc}" maxlength="4" class="numeric csc" />
					</p>
				</td>
			</tr>
		</table>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}

		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='dpp_payment_info' name=$lang.dpp_personal_details style='fg'}
		<table class="submit">
			<tr>
				<td class="name">{$lang.dpp_country}:</td>
				<td class="field">
					<select id="country" name="dpp[country]">
						<option value="">{$lang.select}</option>
						{foreach from=$countries item='country'}
							<option value="{$country.iso}" {if $smarty.post.dpp.country == $country.iso || $country.printable_name|replace:' ':'_'|lower == $account_info.country}selected="selected"{/if}>{$country.printable_name}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_first_name}:</td>
				<td class="field">
					<input type="text" name="dpp[first_name]" value="{if $smarty.post.dpp.first_name}{$smarty.post.dpp.first_name}{else}{$account_info.First_name}{/if}" class="w240" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_last_name}:</td>
				<td class="field">
					<input type="text" name="dpp[last_name]" value="{if $smarty.post.dpp.last_name}{$smarty.post.dpp.last_name}{else}{$account_info.Last_name}{/if}" class="w240" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_address_1}:</td>
				<td class="field">
					<input type="text" name="dpp[address_1]" value="{if $smarty.post.dpp.address_1}{$smarty.post.dpp.address_1}{else}{$account_info.address}{/if}" class="w240" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_address_2}:</td>
				<td class="field">
					<input type="text" name="dpp[address_2]" value="{$smarty.post.dpp.address_2}" class="w240" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_state}:</td>
				<td class="field">
					<select id="us_state" name="dpp[us_state]" class="hide">
						<option value="">{$lang.select}</option>
						{foreach from=$us_states item='state'}
							<option value="{$state.iso}" {if $smarty.post.dpp.us_state == $state.iso}selected="selected"{/if}>{$state.name}</option>
						{/foreach}
					</select>
					<input id="state" type="text" name="dpp[state]" value="{$smarty.post.dpp.state}" class="w240" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_city}:</td>
				<td class="field">
					<input type="text" name="dpp[city]" value="{if $smarty.post.dpp.city}{$smarty.post.dpp.city}{else}{$account_info.city}{/if}" class="w240" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_zip_code}:</td>
				<td class="field">
					<input type="text" name="dpp[zip_code]" value="{if $smarty.post.dpp.zip_code}{$smarty.post.dpp.zip_code}{else}{$account_info.zip_code}{/if}" class="numeric w70" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_phone}:</td>
				<td class="field">
					<span class="dark_11" style="color: #88857E;">{$lang.dpp_phone_example}</span><br />
					<input type="text" name="dpp[phone]" value="{if $smarty.post.dpp.phone}{$smarty.post.dpp.phone}{else}{$account_info.phone}{/if}" class="w240" />
				</td>
			</tr>
			<tr>
				<td class="name">{$lang.dpp_email}:</td>
				<td class="field">
					<input type="text" name="dpp[email]" value="{if $smarty.post.dpp.email}{$smarty.post.dpp.email}{else}{$account_info.Mail}{/if}" class="w240" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td class="field button">
					<input type="submit" value="{$lang.dpp_submit}" />
				</td>
			</tr>
		</table>
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
	</form>
</div>

<script type="text/javascript">

	var img_url = '{$smarty.const.RL_PLUGINS_URL}paypalPro/static/';

	{literal}
		$(document).ready(function() {
			
			$("input.numeric").numeric();

			$('#country').change(function() {
				if($(this).val() == 'US')
				{
					$('#state').hide();
					$('#us_state').fadeIn('normal');
				}
				else
				{
					$('#us_state').hide();
					$('#state').fadeIn('normal');
				}
			});

			$('#card_number_1').keyup(function()
			{
				var value = $(this).val();
				var type = '';

				/* Visa	*/
				if((/^4/).test(value))
				{
					$('#payment_type_img').attr('src', img_url + 'Visa.png');
					type = 'Visa';
				}
				/* Mastercard */
				if ((/^5[1-5]/).test(value))
				{
					$('#payment_type_img').attr('src', img_url + 'Mastercard.png');
					type = 'Mastercard';
				}
				/* Discover */
				if((/^6[0,2,4-5]/).test(value))
				{
					$('#payment_type_img').attr('src', img_url + 'Discover.png');
					type = 'Discover';
				}				
                /* Amex */
				if((/^3[47]/).test(value))
				{
					$('#payment_type_img').attr('src', img_url + 'Amex.png');
					type = 'Amex';
				}

				if(type != '')
				{
					$('#dpp_payment_type option').each(function()
					{
						if($(this).val() == type)
						{
							$(this).attr('selected', 'selected');
						}
					});
					$('#payment_type_img').parent().removeClass('hide');
				}
				else
				{
					$('#dpp_payment_type option:first').attr('selected', 'selected');

					$('#payment_type_img').parent().addClass('hide');
				}
			});

			/* go to next field	*/
			$('input[type="text"]').keyup(function()
			{
				if($(this).hasClass('card_number'))
				{
					var id = $(this).attr('id');
					id_next = id.split('_')[2];
					id_next++;

					if($(this).val().length == 4 && id_next < 5)
					{
						$('#card_number_' + id_next).focus();

						if($('#card_number_' + id_next).val() != '')
						{
							$('#card_number_' + id_next).select();
						}
					}
				}
			});

			/* change card type	*/
			$('#dpp_payment_type').change(function()
			{
				if($(this).val() != '')
				{
					$('#payment_type_img').attr('src', img_url + $(this).val() + '.png');
					$('#payment_type_img').parent().removeClass('hide');
				}
				else
				{
					$('#payment_type_img').parent().addClass('hide');
				}
			});
		});
	{/literal}
</script>

<!-- end paypalPro plugin -->