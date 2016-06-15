function loan_check(validate)
{
	var loanamt = $('#lm_loan_amount').val();
	var paymnt = $('#lm_loan_term').val();
	var rate = $('#lm_loan_rate').val();
	var errors = new Array();
	if( loanamt == '' || isNaN(parseFloat(loanamt)) || loanamt == 0 )
	{
		errors.push(lm_phrases['error_amount']);
		$('#lm_loan_amount').focus();
	}
	if( paymnt == '' || isNaN(parseFloat(paymnt)) || paymnt == 0 )
	{
		errors.push(lm_phrases['error_term']);		
		$('#lm_loan_term').focus();
	}
	if( rate == '' || isNaN(parseFloat(rate)) || rate == 0 )
	{
		errors.push(lm_phrases['error_rate']);		
		$('#lm_loan_rate').focus();
	}
	if ( errors.length > 0 )
	{
		printMessage('error', errors);
		return false;
	}
	else
	{
		if ( !validate ) {
			loan_show(
				$('#lm_loan_term').val(),
				$('#lm_loan_amount').val(),
				lm_configs['loan_term_mode'],
				$('#lm_loan_rate').val(),
				$('#lm_loan_date_month').val(),
				$('#lm_loan_date_year').val()
			);
		}
		else {
			return true;
		}
	}
}
var lm_encode_price = function( str, show_currency, delimiter ){
	str = str.toFixed(2);
	eval("var converted = '"+ str +"'");
	var index = converted.indexOf('.');
	var rest = lm_configs['show_cents'] ? '.00' : '';
	if (index >= 0)
	{
		rest = converted.substring(index);
		if ( rest == '.00' && !lm_configs['show_cents'] )
		{
			rest = '';
		}
		converted = converted.substring(0, index);
	}
	var res = '';
	converted = converted.reverse();
	for(var i = 0; i < converted.length; i++)
	{
		var char = converted.charAt(i);
		res += char;
		var j = i+1;
		if ( j % 3 == 0 && j != converted.length && delimiter !== false )
		{
			res += lm_configs['price_delimiter'];
		}
	}
	converted = res.reverse();
	converted = parseInt(converted) == 0 ? 0 : converted;
	converted = show_currency === false ? converted+rest : lm_configs['currency']+' '+converted+rest;
	return converted;
}
function loan_clear()
{
	$('#lm_loan_term').val('');
	$('#lm_loan_rate').val('');
	
	$('#lm_details_area').html('');
	$('#lm_amortization').slideUp();
	
	loan_build_payment_date();
}
function loan_build_payment_date()
{
	var date = new Date();
	var cur_month = date.getMonth();
	var selected = '';
	var months = '';
	for ( var i = 0; i < 12; i++ )
	{
		var month_number = i + 1;
		selected = i == cur_month ? ' selected="selected"' : '';
		months += '<option value="'+month_number+'"'+selected+'>' + $.datepicker.regional[lm_configs['lang_code']].monthNamesShort[i] + '</option>';
	}
	$('#lm_loan_date_month').html(months);
	var cur_year = date.getFullYear();
	var selected = '';
	var years = '';
	for ( var i = cur_year - 10; i < cur_year + 50; i++ )
	{
		selected = i == cur_year ? ' selected="selected"' : '';
		years += '<option value="'+i+'"'+selected+'>' + i + '</option>';
	}
	$('#lm_loan_date_year').html(years);
}
function lm_increase_month( start_year, start_month, months )
{
	var date = new Date(start_year, start_month-1, 1);
	date.setMonth(date.getMonth() + months);
	return date;
}
function loan_show(loan_term, loan_amount, term_unit, loan_rate, date_month, date_year) {	
	lm_configs['mode'] = true;
	var date_val = parseInt(loan_term);
	var amount = parseFloat(loan_amount);
	var numpay = term_unit == 'year' ? date_val * 12 : date_val;
	var rate = parseFloat(loan_rate);
	var date_month = parseInt(date_month);
	var date_year = parseInt(date_year);
	if ( lm_configs['loan_currency_mode'] == 'converted' )
	{
		lm_configs['currency'] = currencyConverter.config['currency'];
	}
	if ( term_unit == 'year' )
	{
		var new_year = date_year+date_val;
		if ( date_month == 1 )
		{
			var month_index = 11;
			new_year--;
		}
		else
		{
			var month_index = date_month - 2;
		}
		var date_off = $.datepicker.regional[lm_configs['lang_code']].monthNamesShort[month_index] +', '+new_year;
	}
	else
	{
		var new_date = lm_increase_month(date_year, date_month, date_val);
		//var year_index = new_date.getMonth() == 1 ? 11 : new_date.getMonth()-1;
		var year_index = new_date.getMonth();
		
		var date_off = $.datepicker.regional[lm_configs['lang_code']].monthNamesShort[year_index] +', '+new_date.getFullYear();
	}
	rate = rate / 100;
	var monthly  = rate / 12;
	var payment  = ( (amount * monthly) / (1 - Math.pow( (1 + monthly), -numpay) ) );
	var total    = payment * numpay;
	var interest = total - amount;
	var output = '<table class="table"> \
				<tr> \
					<td class="name">'+ lm_phrases['loan_amount'] +'</td> \
					<td class="value">'+ lm_encode_price(amount) +'</td> \
				</tr> \
				<tr> \
					<td class="name">'+ lm_phrases['num_payments'] +'</td> \
					<td class="value">'+numpay+'</td> \
				</tr> \
				<tr> \
					<td class="name">'+ lm_phrases['monthly_payment'] +'</td> \
					<td class="value">'+ lm_encode_price(payment) +'</td> \
				</tr> \
				<tr> \
					<td class="name">'+ lm_phrases['total_paid'] +'</td> \
					<td class="value">'+ lm_encode_price(total) +'</td> \
				</tr> \
				<tr> \
					<td class="name">'+ lm_phrases['total_interest'] +'</td> \
					<td class="value">'+ lm_encode_price(interest) +'</td> \
				</tr> \
				<tr> \
					<td class="name">'+ lm_phrases['payoff_date'] +'</td> \
					<td class="value">'+ date_off +'</td> \
				</tr> \
				</table>';
	$('#lm_details_area').html(output);
	$('#lm_show_amortization').fadeIn();
	var detail = '<table class="list"> \
					<tr class="header"> \
						<td align="center"><b>'+ lm_phrases['pmt_date'] +'</b></td> \
						<td class="divider"></td> \
						<td align="'+lm_right_align+'"><div style="margin-'+lm_right_align+': 5px;"><b>'+ lm_phrases['amount'] +'</b></div></td> \
						<td class="divider"></td> \
						<td align="'+lm_right_align+'"><div style="margin-'+lm_right_align+': 5px;"><b>'+ lm_phrases['interest'] +'</b></div></td> \
						<td class="divider"></td> \
						<td align="'+lm_right_align+'"><div style="margin-'+lm_right_align+': 5px;"><b>'+ lm_phrases['principal'] +'</b></div></td> \
						<td class="divider"></td> \
						<td align="'+lm_right_align+'"><div style="margin-'+lm_right_align+': 5px;"><b>'+ lm_phrases['balance'] +'</b></div></td> \
					</tr> \
					<tr class="body"> \
						<td class="first" align="center">-</td> \
						<td class="divider"></td> \
						<td align="'+lm_right_align+'">-</td> \
						<td class="divider"></td> \
						<td align="'+lm_right_align+'">-</td> \
						<td class="divider"></td> \
						<td align="'+lm_right_align+'">-</td> \
						<td class="divider"></td> \
						<td align="'+lm_right_align+'">'+lm_encode_price(amount)+'</td> \
					</tr>';

	newPrincipal = amount;
	var i = j = 1;
	var outInterest = 0;
	var outReduction = 0;
	var point = 12;
	if ( lm_configs['loan_term_mode'] == 'year' )
	{
		point = 13 - date_month;
	}
	while (i <= numpay) {
		newInterest  = monthly * newPrincipal;
		reduction    = payment - newInterest;
		newPrincipal = newPrincipal - reduction;
		outInterest  += newInterest;
		outReduction += reduction;
		if ( lm_configs['loan_term_mode'] == 'year' )
		{
			if ( i % point == 0 || i == numpay )
			{
				point += 12;
				var it_date = lm_increase_month(date_year, date_month, i-1);
				var pmt_date = $.datepicker.regional[lm_configs['lang_code']].monthNamesShort[it_date.getMonth()] +', '+it_date.getFullYear();
				
				detail += '<tr class="body"> \
							<td class="first" align="center"><span class="fLable">'+pmt_date+'</span></td> \
							<td class="divider"></td> \
							<td align="'+lm_right_align+'">'+lm_encode_price(payment, false)+'</td> \
							<td class="divider"></td> \
							<td align="'+lm_right_align+'">'+lm_encode_price(outInterest, false)+'</td> \
							<td class="divider"></td> \
							<td align="'+lm_right_align+'">'+lm_encode_price(outReduction, false)+'</td> \
							<td class="divider"></td> \
							<td align="'+lm_right_align+'"><b>'+lm_encode_price(newPrincipal, false)+'</b></td> \
						</tr>';
				
				outInterest = outReduction = 0;
				j++;
			}
		}
		else
		{
			var it_date = lm_increase_month(date_year, date_month, i-1);
			var pmt_date = $.datepicker.regional[lm_configs['lang_code']].monthNamesShort[it_date.getMonth()] +', '+it_date.getFullYear();
			detail += '<tr> \
							<td style="padding: 8px;" class="grey_line_1 grey_small" align="center"><span class="fLable">'+pmt_date+'</span></td> \
							<td></td> \
							<td class="grey_line_1 grey_small" align="'+lm_right_align+'">'+lm_encode_price(payment, false)+'</td> \
							<td></td> \
							<td class="grey_line_1 grey_small" align="'+lm_right_align+'">'+lm_encode_price(newInterest, false)+'</td> \
							<td></td> \
							<td class="grey_line_1 grey_small" align="'+lm_right_align+'">'+lm_encode_price(reduction, false)+'</td> \
							<td></td> \
							<td class="grey_line_1 grey_small" align="'+lm_right_align+'"><b>'+lm_encode_price(newPrincipal, false)+'</b></td> \
						</tr>';
		}
		i++;
	}
	detail += "</table>";
	$('#lm_amortization_area').html(detail);
	$('#lm_amortization').slideDown();
}
if(typeof window.reverse != 'function')
{
	String.prototype.reverse = function() {
	    var s = "";
	    var i = this.length;
	    while (i>0) {
	        s += this.substring(i-1,i);
	        i--;
	    }
	    return s;
	}
}