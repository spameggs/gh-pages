<!-- user navigation bar -->

<div class="user_navbar">
	{if $isLogin}
		{assign var='account_area_phrase' value='blocks+name+account_area'}
		<a title="{$lang.$account_area_phrase}" href="{$rlBase}{if $config.mod_rewrite}{$pages.login}.html{else}?page={$pages.login}{/if}">{$lang.$account_area_phrase}</a>
	{else}
		<a title="{$lang.create_account}" href="{$rlBase}{if $config.mod_rewrite}{$pages.registration}.html{else}?page={$pages.registration}{/if}">{$lang.registration}</a>
		<span class="divider">/</span>
		<a title="{$lang.login}" href="{$rlBase}{if $config.mod_rewrite}{$pages.login}.html{else}?page={$pages.login}{/if}">{$lang.login}</a>
	{/if}
</div>

<!-- user navigation bar end -->