<!-- distance (search by distance) -->

{if isset($dealer.sbd_distance)}
	{if !$sbd_unit}
		{if $config.sbd_default_units == 'miles'}
			{assign var='sbd_unit' value='mi'}
		{else}
			{assign var='sbd_unit' value='km'}
		{/if}
	{/if}
	
	{assign var='sbd_key_short' value='sbd_'|cat:$sbd_unit|cat:'_short'}
	{assign var='sbd_key' value='sbd_'|cat:$sbd_unit}
	{if $sbd_unit == 'km'}
		{assign var='sbd_distance' value=$dealer.sbd_distance*1.609344}
	{else}
		{assign var='sbd_distance' value=$dealer.sbd_distance}
	{/if}
	<span class="icon sbd-target" title="{$lang.sbd_distance}">{$sbd_distance|round:1} <span title="{$lang.$sbd_key}">{$lang.$sbd_key_short}</span></span>
{/if}

<!-- distance (search by distance) end -->