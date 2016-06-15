{if $coupon_price_info}	
	<table class="table">
		<tr>
			<td class="name">
				{$lang.coupon_code}:
			</td>
			<td class="value">
				<b>{$coupon_code}</b>, <a href="javascript:void(0);" id="diffuse" onClick="diffuse()" class="dark_12">{$lang.coupon_reject}</span>	
			</td>
		</tr>
		<tr>
			<td class="name">
				{$lang.price}:
			</td>
			<td class="value">
				{if $config.system_currency_position == 'before'}{$config.system_currency}{/if} {$coupon_price_info.price} {if $config.system_currency_position == 'after'}{$config.system_currency}{/if}
			</td>
		</tr>
		<tr>
			<td class="name">
				{$lang.coupon_discount}:
			</td>
			<td class="value">
				{$coupon_price_info.discount}
			</td>
		</tr>
		<tr>
			<td class="name">
				{$lang.total}:
			</td>
			<td class="value">
				{if $coupon_price_info.total==0}{$lang.free}{else}{if $config.system_currency_position == 'before'}{$config.system_currency}{/if} {$coupon_price_info.total} {if $config.system_currency_position == 'after'}{$config.system_currency}{/if}{/if}
			</td>
		</tr>
	</table>
{/if}