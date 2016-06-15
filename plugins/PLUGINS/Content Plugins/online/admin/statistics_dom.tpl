<table class="fixed">
<tr>
	<td>
		<table class="stat">
		<tr class="header">
			<td colspan="3">{$lang.online_statistics_text}</td>
		</tr>
		<tr>
			<td class="line"><div>{$lang.online_count_last_hour_text}</div></td>
			<td class="counter"><b>{$onlineStatistics.lastHour}</b></td>
		</tr>
		<tr>
			<td class="line"><div>{$lang.online_count_last_day_text}</div></td>
			<td class="counter"><b>{$onlineStatistics.lastDay}</b></td>
		</tr>
		
		<tr class="header">
			<td colspan="3" style="padding-top: 7px;">{$lang.online_count_all_text|replace:'[number]':$onlineStatistics.total}</td>
		</tr>
		
		<tr>
			<td class="line"><div>{$lang.online_count_users_text}</div></td>
			<td class="counter"><b>{$onlineStatistics.users}</b></td>
		</tr>
		<tr>
			<td class="line"><div>{$lang.online_count_guests_text}</div></td>
			<td class="counter"><b>{$onlineStatistics.guests}</b></td>
		</tr>
		</table>
	</td>
</tr>
</table>