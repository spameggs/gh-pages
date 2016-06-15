<!-- paypal subscription unsubscribe icon -->
{if $listing.Subscription_data == 'paypal_subscription'}
	<tr>
		<td class="name">{$lang.paypal_subscription_subscription}:</td>
		<td class="value"><a title="{$lang.paypal_unsubscribe}" href="https://www.{if $config.paypal_subscription_test_mode}sandbox.{/if}paypal.com/cgi-bin/webscr?cmd=_subscr-find&amp;alias={$config.paypal_subscription_account_email|urlencode}">{$lang.paypal_subscription_caption} ({$lang.cancel})</a></td>
	</tr>
{/if}
<!-- paypal subscription unsubscribe icon end -->