var booking_calBack = new Array();

function errorShow(error)
{
    printMessage('error', error);
	book_color(true);
}

function xSelect(o_id)
{
    if(deny === 0)
    {
        return errorShow( deny_text );
    }

    if (first==1)
    {
    	if ( o_id == s_id )
			{
			book_color(true);
			first = 0;
			return;
		}

		var mass_bind_checkout = bind_checkout.split(',');

		if( bind_in_out && bind_checkout != '' && !in_array(date('D', o_id), mass_bind_checkout) )
		{
			errorShow( check_out_only_text.replace('[days]', bind_checkout) );
		}
		else
		{
			var tdays=Math.abs(eval(o_id-s_id))/(60*60*24);
			tdays = Math.floor(tdays);

			if (tdays<min_bl && min_bl > 0)
			{
				errorShow( min_bl_text.replace('[min]', '<span class="daySize">'+min_bl+'</span>') )
			}
			else if (tdays>max_bl && max_bl > 0)
			{
				errorShow( max_bl_text.replace('[max]', '<span class="daySize">'+max_bl+'</span>') )
			}
			else
			{
				if (s_id>o_id)
				{
					var result = bUpdate(o_id, s_id, '-');
					var start_book = date(bookingDateFormat.replace(/%/g, '').replace('b', 'M'), o_id);
					var end_book = date(bookingDateFormat.replace(/%/g, '').replace('b', 'M'), s_id);

					db_start = o_id;
					db_end = s_id;
				}
				else
				{
					var result = bUpdate(o_id, s_id, '+');
					var start_book = date(bookingDateFormat.replace(/%/g, '').replace('b', 'M'), s_id);
					var end_book = date(bookingDateFormat.replace(/%/g, '').replace('b', 'M'), o_id);

					db_start = s_id;
					db_end = o_id;
				}

				if(result === true)
				{
					var html = '<table class="submit">';
					html += '<tr><td class="name">'+ booking_checkin +':</td><td class="field">' + start_book+'</td></tr>';
					html += '<tr><td class="name">'+ booking_checkout +':</td><td class="field">'+ end_book + '</td></tr>';
					html += '<tr><td class="name">'+ booking_nights +':</td><td class="field">'+ tdays +'</td></tr>';
					html += '<tr><td class="name">'+ booking_amount +':</td><td class="field">'+ defCurrency +' '+ show_total_cost +'</td></tr>';
					html += '<tr><td class="name"></td><td class="field"><input type="button" onclick="nextStep(this);" value="'+ booking_next_step +'" /></td></tr>';
					html += '</table>';

					$(message_obj).html(html);

					$('#book_price').html('Nights: <b>'+tdays+'</b> total cost: '+defCurrency+' '+show_total_cost);
					$('#nextStep').fadeIn();
					$('#booking_message_obj').fadeIn();

					for ( var i = 0; i < booking_calBack.length; i++ )
					{
						if ( booking_calBack[i] != '' )
						{
							eval(booking_calBack[i]);
						}
					}
				}
				else
				{
					errorShow( already_booked_text );
				}
			}
		}
		first=0;
    }
    else
    {
		if( $(day_prefix + o_id).hasClass('booked') || $(day_prefix + o_id).hasClass('prbooked') )
		{
			errorShow( booked_day_text );
		}
		else if ( $(day_prefix + o_id).hasClass('closed') )
		{
			errorShow( closed_day_text );
		}
		else
		{
			var mass_bind_checkin = bind_checkin.split(',');

			if( bind_in_out && bind_checkin != '' && !in_array(date('D', o_id), mass_bind_checkin) )
			{

				errorShow( check_in_only_text.replace("[days]", bind_checkin) );
			}
			else
			{
				if (s_id > 0)
				{
					s_id = cur_id;
				}

				book_color(true);
				bUpdate(o_id);
				$('#nextStep').fadeOut('fast');
				$('#booking_message_obj').fadeOut('fast');

				first=1;
				s_id=o_id;
				cur_id=o_id;
			}
		}
    }
}

function bUpdate(o_id, s_id, mod)
{
	if (first==1)
	{
		var tdays=Math.abs(eval(o_id-s_id))/(60*60*24);
		tdays = Math.floor(tdays);

		var tdate = s_id;

		for (var i=0; i<tdays; i++)
		{
			tdate = eval( tdate + mod + 86400);

			if( $(day_prefix + tdate).hasClass('booked') || $(day_prefix + tdate).hasClass('prbooked') || $(day_prefix + tdate).hasClass('closed') )
			{
				return false;
			}

			selected[index] = tdate;
			index++;
		}
		book_color(false,false,o_id,s_id,mod); // Paint calendar
	}
	else
	{
		$(message_obj).html('');
		$(day_prefix + o_id).addClass('daySelect');
		$('#error_obj').fadeOut('fast');
		$('#ufvalid input, #ufvalid textarea').removeClass('error-input');
		$('div#step_2').slideUp('fast');
		selected[index] = o_id;
		index++;
	}

	return true;
}

function book_color(erase,st,o_id,s_id,mod)
{
	var iteration = 1;
	var calc = 0;
	var bDebug = '';

	for ( var id = 0; id < selected.length; id++ )
	{
		if ( erase === true )
		{
			$(day_prefix + selected[id]).removeClass('daySelect');
		}
		else
		{
			$(day_prefix + selected[id]).addClass('daySelect');
		}

		if( first == 1 )
		{
			/* listing rate range */
			for( var idR in usRange )
			{
				if(fixed_range == 1)
				{
					if(mod == '+')
					{
						if( iteration != selected.length )
						{
							if( selected[0] >= usRange[idR][0] && selected[0] <= usRange[idR][1])
							{
								bDebug += parseFloat( usRange[idR][2].split('|')[0] ) +' + ';
								total_cost += parseFloat( usRange[idR][2].split('|')[0] );
							}
						}
					}
					else
					{
						if( iteration != 1 )
						{
							if( selected[selected.length-1] >= usRange[idR][0] && selected[selected.length-1] <= usRange[idR][1])
							{
								bDebug += parseFloat( usRange[idR][2].split('|')[0] ) +' + ';
								total_cost += parseFloat( usRange[idR][2].split('|')[0] );
							}
						}
					}
				}
				else
				{
					if( selected[id] >= usRange[idR][0] && selected[id] <= usRange[idR][1] )
					{
						if(mod == '+')
						{
							if( iteration != selected.length )
							{
								bDebug += parseFloat( usRange[idR][2].split('|')[0] ) +' + ';
								total_cost += parseFloat( usRange[idR][2].split('|')[0] );
								calc++;
							}
						}
						else
						{
							if( iteration != 1 )
							{
								bDebug += parseFloat( usRange[idR][2].split('|')[0] ) +' + ';
								total_cost += parseFloat( usRange[idR][2].split('|')[0] );
								calc++;
							}
						}
					}
				}
		    }
		}
		iteration++;
	}

	if( first == 1 )
	{
		if(fixed_range == 0)
		{
			bDebug += booking_debug ? '( '+ (selected.length - 1) +' - '+ calc +' ) * '+ defPrice : '';
			total_cost += ( ( selected.length - 1 ) - calc) * defPrice;
		}
		else
		{
			if(total_cost == 0)
			{
				bDebug += booking_debug ? (selected.length - 1) +' * '+ defPrice : '';
				total_cost += ( selected.length - 1 ) * defPrice;
			}
		}

		if ( booking_debug ) {
			//console.log(bDebug);
		}
		show_total_cost = str2money(total_cost);
	}

	if ( erase === true )
	{
		selected = [];
		index = 0;
		total_cost = 0;
	}
	if ( st === true )
	{
		if ( selected[0] != '' )
		{
			$(day_prefix + selected[0]).addClass('daySelect');
		}
	}
}

function str2money(price)
{
	var price_split = price.toFixed(2).split('.');
	var price_int = strrev(price_split[0]);
	var price_cents = ( price_split[1] ? price_split[1] : '' ) == '00' ? '' : '.' + price_split[1];
	var len = price_int.length;
	var val = '';

	for ( var i = 0; i <= len; i++ )
	{
		val += price_int.charAt(i);
		if ( (( i + 1 ) % 3 == 0) && ( i + 1 < len ) )
		{
			val += price_delimiter;
		}
	}

	val = strrev(val) + price_cents;

    return val;
}

function strrev(string)
{
	string = string+'';

	var grapheme_extend = /(.)([\uDC00-\uDFFF\u0300-\u036F\u0483-\u0489\u0591-\u05BD\u05BF\u05C1\u05C2\u05C4\u05C5\u05C7\u0610-\u061A\u064B-\u065E\u0670\u06D6-\u06DC\u06DE-\u06E4\u06E7\u06E8\u06EA-\u06ED\u0711\u0730-\u074A\u07A6-\u07B0\u07EB-\u07F3\u0901-\u0903\u093C\u093E-\u094D\u0951-\u0954\u0962\u0963\u0981-\u0983\u09BC\u09BE-\u09C4\u09C7\u09C8\u09CB-\u09CD\u09D7\u09E2\u09E3\u0A01-\u0A03\u0A3C\u0A3E-\u0A42\u0A47\u0A48\u0A4B-\u0A4D\u0A51\u0A70\u0A71\u0A75\u0A81-\u0A83\u0ABC\u0ABE-\u0AC5\u0AC7-\u0AC9\u0ACB-\u0ACD\u0AE2\u0AE3\u0B01-\u0B03\u0B3C\u0B3E-\u0B44\u0B47\u0B48\u0B4B-\u0B4D\u0B56\u0B57\u0B62\u0B63\u0B82\u0BBE-\u0BC2\u0BC6-\u0BC8\u0BCA-\u0BCD\u0BD7\u0C01-\u0C03\u0C3E-\u0C44\u0C46-\u0C48\u0C4A-\u0C4D\u0C55\u0C56\u0C62\u0C63\u0C82\u0C83\u0CBC\u0CBE-\u0CC4\u0CC6-\u0CC8\u0CCA-\u0CCD\u0CD5\u0CD6\u0CE2\u0CE3\u0D02\u0D03\u0D3E-\u0D44\u0D46-\u0D48\u0D4A-\u0D4D\u0D57\u0D62\u0D63\u0D82\u0D83\u0DCA\u0DCF-\u0DD4\u0DD6\u0DD8-\u0DDF\u0DF2\u0DF3\u0E31\u0E34-\u0E3A\u0E47-\u0E4E\u0EB1\u0EB4-\u0EB9\u0EBB\u0EBC\u0EC8-\u0ECD\u0F18\u0F19\u0F35\u0F37\u0F39\u0F3E\u0F3F\u0F71-\u0F84\u0F86\u0F87\u0F90-\u0F97\u0F99-\u0FBC\u0FC6\u102B-\u103E\u1056-\u1059\u105E-\u1060\u1062-\u1064\u1067-\u106D\u1071-\u1074\u1082-\u108D\u108F\u135F\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17B6-\u17D3\u17DD\u180B-\u180D\u18A9\u1920-\u192B\u1930-\u193B\u19B0-\u19C0\u19C8\u19C9\u1A17-\u1A1B\u1B00-\u1B04\u1B34-\u1B44\u1B6B-\u1B73\u1B80-\u1B82\u1BA1-\u1BAA\u1C24-\u1C37\u1DC0-\u1DE6\u1DFE\u1DFF\u20D0-\u20F0\u2DE0-\u2DFF\u302A-\u302F\u3099\u309A\uA66F-\uA672\uA67C\uA67D\uA802\uA806\uA80B\uA823-\uA827\uA880\uA881\uA8B4-\uA8C4\uA926-\uA92D\uA947-\uA953\uAA29-\uAA36\uAA43\uAA4C\uAA4D\uFB1E\uFE00-\uFE0F\uFE20-\uFE26])/g;
    string = string.replace(grapheme_extend, '$2$1'); // Temporarily reverse

    return string.split('').reverse().join('');
}

function paintUserBook()
{
	for (id in usBook)
	{
		var pr = '';
		var status_b = usBook[id][0];
		var st_b = usBook[id][1];
		var en_b = usBook[id][2];
		var tdays=Math.abs(eval(en_b-st_b))/(60*60*24);
		tdays = Math.floor(tdays);
		var tdate = st_b;

		for (var i=0; i<=tdays; i++)
		{
			if(i != 0)
			{
				tdate = eval(tdate) + 86400;
			}

			if(status_b == 'process')
			{
				pr = 'pr';
			}
			else
			{
				pr = '';
			}

			if(i == 0)
			{
				$(day_prefix + tdate).addClass(pr+'checkin');
				if( $(day_prefix + tdate).hasClass(pr+'checkout') )
				{
					$(day_prefix + tdate).removeClass().addClass(pr+'booked');
				}
				else if( $(day_prefix + tdate).hasClass('checkout') )
				{
					$(day_prefix + tdate).removeClass().addClass('bprcheckin');
				}
			}
			else if (i == tdays)
			{
				$(day_prefix + tdate).addClass(pr+'checkout');
				if( $(day_prefix + tdate).hasClass(pr+'checkin') )
				{
					$(day_prefix + tdate).removeClass().addClass(pr+'booked');
				}
				else if( $(day_prefix + tdate).hasClass('checkin') )
				{
					$(day_prefix + tdate).removeClass().addClass('bprcheckout');
				}
			}
			else
			{
				$(day_prefix + tdate).removeClass().addClass(pr+'booked');
			}
		}
	}

	// pain close days
	for (cd in closeRange)
	{
		var st_close = closeRange[cd][0];
		var end_close = closeRange[cd][1];
		var cdays=Math.abs(eval(end_close-st_close))/(60*60*24);
		var cdate = st_close;
		cdays = Math.floor(cdays);


		for (var j=0; j<=cdays; j++)
		{
			if( j != 0 )
			{
				cdate = eval(cdate) + 86400;
			}

			if( j == 0 )
			{
				if( $(day_prefix + cdate).hasClass('checkout') )
				{
					$(day_prefix + cdate).removeClass().addClass('bclosein');
				}
				else if( $(day_prefix + cdate).hasClass('prcheckout') )
				{
					$(day_prefix + cdate).removeClass().addClass('pclosein');
				}
				else if( $(day_prefix + cdate).hasClass('available') )
				{
					$(day_prefix + cdate).removeClass().addClass('closein');
				}
			}
			else if ( j == cdays )
			{
				if( $(day_prefix + cdate).hasClass('checkin') )
				{
					$(day_prefix + cdate).removeClass().addClass('bcloseout');
				}
				else if( $(day_prefix + cdate).hasClass('prcheckin') )
				{
					$(day_prefix + cdate).removeClass().addClass('pcloseout');
				}
				else if( $(day_prefix + cdate).hasClass('available') )
				{
					$(day_prefix + cdate).removeClass().addClass('closeout');
				}
			}
			else
			{
				$(day_prefix + cdate).removeClass().addClass('closed');
			}
		}
	}
}

function cangeDates(mode)
{
	booking_mask('set');
	$('#calendar_load').stop().animate({opacity: 0.4});
	xajax_getDates(listing_id, mode);
}

var booking_mask = function(mode){

	var booking_calendar_width = $('#booking_calendar').width();
	var booking_calendar_height = $('#calendar_map').height();

	if ( mode == 'set' )
	{
		$('#calendar_load').css({height: booking_calendar_height, width: booking_calendar_width, marginTop: -booking_calendar_height});
	}
	else if ( mode == 'reset' )
	{
		$('#calendar_load').css({height: 0, width: 0});
	}
}
