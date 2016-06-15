<!-- field-bound box tpl -->
{assign var='fbb_list_mode' value=false}
{if $icons_position == 'left' && $columns_number == 1 && $show_count && $tpl_settings.ffb_list}
	{assign var='fbb_list_mode' value=true}
{/if}
{if !empty($options)}
	<div class="field_bound_box categories{if $fbb_list_mode} fbb_list{/if}">
		<ul>
			<li>
			<table class="fixed">
			<tr>
			{foreach from=$options item='option' name='fCats'}
				<td valign="top">
					<div class="item">
						{if $fbb_list_mode}<div class="fbb-name">{/if}
						{if ($icons_position == 'left' || $icons_position == 'top') && $option.Icon}
							<div style="{if $icons_position == 'left'}display: inline;{else}display: block;{/if}">
									<a class="cat_icon" title="{$lang[$option.pName]}" href="{$rlBase}{if $config.mod_rewrite}{$path}/{$option.Key}{if $html_postfix}.html{else}/{/if}{else}?page={$pages.listings_by_field}&amp;{$path}={$option.Key}{/if}">
									<img src="{$smarty.const.RL_URL_HOME}files/{$option.Icon}" title="{$lang[$option.pName]}" alt="{$lang[$option.pName]}" />
								</a>
							</div>
						{/if}
						<a class="category" title="{$lang[$option.pName]}" href="{$rlBase}{if $config.mod_rewrite}{$path}/{$option.Key}{if $html_postfix}.html{else}/{/if}{else}?page={$pages.listings_by_field}&amp;{$path}={$option.Key}{/if}">{$lang[$option.pName]}</a>
						{if $fbb_list_mode}</div>{/if}

						{if $fbb_list_mode && $show_count}
							<div class="fbb-counter">
								<span>{$option.Count}</span>
							</div>
						{else}
							{if $show_count}<span>(<b>{$option.Count}</b>)</span>{/if}
						{/if}
						{if ($icons_position == 'right' || $icons_position == 'bottom') && $option.Icon}
							<div style="{if $icons_position == 'right'}display: inline;{else}display: block;{/if}">
									<a class="category cat_icon" title="{$lang[$option.pName]}" href="{$rlBase}{if $config.mod_rewrite}{$path}/{$option.Key}{if $html_postfix}.html{else}/{/if}{else}?page={$pages.listings_by_field}&amp;{$path}={$option.Key}{/if}">
									<img src="{$smarty.const.RL_URL_HOME}files/{$option.Icon}" title="{$lang[$option.pName]}" alt="{$lang[$option.pName]}" />
								</a>
							</div>
						{/if}
					</div>
				</td>
				{if $smarty.foreach.fCats.iteration%$columns_number == 0 && !$smarty.foreach.fCats.last}
				</tr>
				<tr>
				{/if}
			{/foreach}
			</tr>
			</table>
			</li>
		</ul>
		<div class="clear"></div>
	</div>
{/if}
<!-- field-bound box tpl end-->