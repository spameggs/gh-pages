<!-- send interface -->
<div class="x-hidden" id="statistic">
	<div class="x-window-header">{$lang.massmailer_newsletter_processing}</div>
	<div class="x-window-body" style="padding: 10px 15px;">
		<table class="massmailer">
		<tr>
			<td class="name">
				{$lang.massmailer_newsletter_total}:
			</td>
			<td class="value">
				<label id="total">{$lang.massmailer_newsletter_processing}</label>
			</td>
		</tr>
		<tr>
			<td class="name">
				{$lang.massmailer_newsletter_sents}:
			</td>
			<td class="value">
				<label id="sent">0</label>
			</td>
		</tr>
		</table>
		
		<div id="sending_area">
			<table class="massmailer">
			<tr>
				<td class="name">
					{$lang.massmailer_newsletter_sending}:
				</td>
				<td class="value">
					<label id="sending">{$lang.massmailer_newsletter_processing}</label>
				</td>
			</tr>
			</table>
		</div>
		
		<table class="sTable">
		<tr>
			<td>
				<div class="progress">
					<div id="processing"></div>
				</div>
			</td>
			<td class="counter">
				<div id="loading_percent">0%</div>
			</td>
		</tr>
		</table>
	</div>
</div>
<!-- send interface end -->