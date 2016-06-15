<!-- booking calendar -->

<div class="info">{$lang.booking_start_booking}</div>

{if $navigation.prev || $navigation.next || !$config.booking_calendar_restricted}
<div id="booking_nav">
	{if $navigation.prev || !$config.booking_calendar_restricted}
	<div style="float: left;" id="prevRange" class="hide grey_small">&larr; {$lang.booking_prev}
		<a href="javascript:void(0)" class="static" onclick="cangeDates('-Y');">{$lang.booking_year}</a> /
		<a href="javascript:void(0)" class="static" onclick="cangeDates('-M');">{$lang.booking_month}</a>
	</div>
	{/if}

	{if $navigation.next || !$config.booking_calendar_restricted}
	<div style="float: right;" id="nextRange" class="grey_small">{$lang.booking_next}
		<a href="javascript:void(0)" class="static" onclick="cangeDates('+Y');">{$lang.booking_year}</a> /
		<a href="javascript:void(0)" class="static" onclick="cangeDates('+M');">{$lang.booking_month}</a> &rarr;
	</div>
	{/if}
	<div class="clear"></div>
</div>
{/if}

<table align="center" id="calendar_map">
<tr>
	{foreach from=$BookingDays item='month' name='fMonths'}
	<td valign="top">
		<div style="margin: 5px 3px;">
			<table{if $month.Days.01.Color == 'R'} style="opacity:0.4;"{/if}>
			<tr>
				<td colspan="7" class="monthName">{$month.Name} {$month.Year}</td>
			</tr>
			<tr class="dayName">
				<td>{$lang.booking_monday}</td>
				<td>{$lang.booking_tuesday}</td>
				<td>{$lang.booking_wednesday}</td>
				<td>{$lang.booking_thursday}</td>
				<td>{$lang.booking_friday}</td>
				<td>{$lang.booking_saturday}</td>
				<td>{$lang.booking_sunday}</td>
			</tr>
			<tr>
				{foreach from=$month.Days item='day' name='fDays' key='kDay'}
				<td class="calendar_td">
				{if $day != 'missed'}
					{if $day.Color == 'U'}
						{assign var='book_color' value='unavailable'}
					{elseif $day.Color == 'R'}
						{assign var='book_color' value='restriction'}
					{elseif $day.Color == 'T'}
						{assign var='book_color' value='today'}
					{elseif $day.Color == 'A'}
						{assign var='book_color' value='available'}
					{/if}
					<div class="{$book_color}" {if $day.Color == 'R' &&  $month.Days.01.Color == 'R'}style="opacity:1;"{/if}
					{if $day.Color != 'U' && $day.Color != 'R'}
						title="{$lang.booking_start_booking_title}" id="day_{$day.mktime}" onclick="xSelect('{$day.mktime}');"
					{/if}>
					{if $kDay<10}{$kDay|substr:1:1}{else}{$kDay}{/if}
					</div>
				{/if}
				</td>
				{if $smarty.foreach.fDays.iteration%7 == 0 && !$smarty.foreach.fDays.last}
				</tr><tr>
				{/if}
				{/foreach}
			</tr>
			</table>
		</div>
	</td>
	{if $smarty.foreach.fMonths.iteration%$config.booking_calendar_horizontal == 0 && !$smarty.foreach.fMonths.last}
	</tr><tr>
	{/if}
	{/foreach}
</tr>
</table>

<!-- booking calendar end -->