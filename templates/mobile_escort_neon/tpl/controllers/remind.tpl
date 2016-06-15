<!-- remind password page -->

<div class="highlight">
	{if $change}
		<!-- change password form -->
		{assign var='replace' value=`$smarty.ldelim`username`$smarty.rdelim`}
		{$lang.set_new_password_hint|replace:$replace:$profile_info.Full_name}
		
		<form action="{$rlBase}{if $config.mod_rewrite}{$pages.remind}.html?hash={$smarty.get.hash}{else}?page={$pages.remind}&amp;hash={$smarty.get.hash}{/if}" style="margin-top: 20px;" method="post">
			<input type="hidden" name="change" value="1" />
			
			<div class="field">{$lang.new_password}</div>
			<input type="password" name="profile[password]" value="{$smarty.post.password}" id="new_password" maxlength="40" />
			
			<div class="field">{$lang.password_repeat}</div>
			<input type="password" name="password_repeat" maxlength="40" />
			
			<div class="button"><input type="submit" value="{$lang.change}" /></div>
		</form>
		
		<!-- change password form end -->
	{else}
		<!-- request password change form -->
		
		<form action="{$rlBase}{if $config.mod_rewrite}{$pages.remind}.html{else}?page={$pages.remind}{/if}" method="post">
			<input type="hidden" name="request" value="1" />
			
			<div class="field">{$lang.mail}</div>
			<input type="text" name="email" value="{$smarty.post.email}" maxlength="100" />
			
			<div class="button"><input type="submit" value="{$lang.remind}" /></div>
		</form>
		
		<!-- request password change form end -->
	{/if}
</div>

<!-- remind password page end -->