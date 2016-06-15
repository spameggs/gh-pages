<!-- import interface -->
<div class="x-hidden" id="statistic">
	<div class="x-window-header">{$lang.eil_importing_caption}</div>
	<div class="x-window-body" style="padding: 10px 15px;">
		<table class="importing">
		<tr>
			<td class="name">
				{$lang.total_listings}:
			</td>
			<td class="value">
				<label id="total">{$import_details.0.value}</label>
			</td>
		</tr>
		</table>
		<div id="dom_area">
			<table class="importing">
			<tr>
				<td class="name">
					{$lang.eil_total_listings}:
				</td>
				<td class="value">
					<label id="importing">1-{if $import_details.1.value > $import_details.0.value}{$import_details.0.value}{else}{$import_details.1.value}{/if}</label>
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
<!-- import interface end -->