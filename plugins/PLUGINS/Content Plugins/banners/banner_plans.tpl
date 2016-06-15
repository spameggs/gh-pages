<!-- banner_plans.tpl -->

{php} if ( version_compare($GLOBALS['config']['rl_version'], '4.1.0', '<') ) { {/php}
<ul class="plans">
{foreach from=$plans item='plan' name='plansF' key='plansK'}
<li {if $plan.ID == $smarty.post.plan}class="active"{/if}>
	<table class="sTable">
	<tr>
		<td class="radio">
			{if $plan.ID == $sPost.plan}
				{assign var='sPlan' value=$plan}
			{else}
				{if $smarty.foreach.plansF.first}
					{assign var='sPlan' value=$plan}
				{/if}
			{/if}
			<input {if $sPlan.ID == $plan.ID}checked="checked"{/if} id="plan_{$plan.ID}" type="radio" name="plan" index="{$plansK}" value="{$plan.ID}" />
		</td>
		<td class="label">
			<div>
				<div class="tile" {if $plan.Color}style="background-color: #{$plan.Color};"{/if}></div>
				<div class="bg" {if $plan.Color}style="background-color: #{$plan.Color};"{/if}>
					<div class="price">
                    {if $plan.Price > 0}{if $config.system_currency_position == 'before'}{$config.system_currency}{/if}
                    {if $config.price_decimal == '.'}{$plan.Price}{else}{$plan.Price|replace:".":$config.price_decimal}{/if}
                    {if $config.system_currency_position == 'after'}{$config.system_currency}{/if}{else}{$lang.free}{/if}</div>
					<div class="type">{$lang.banners_planType}</div>
				</div>
			</div>
		</td>
		<td class="info">
			<table class="sTable">
			<tr>
				<td class="caption"><div>{$plan.name}</div></td>
				<td>
					<ul class="features">
						<li class="period" title="{$lang.banners_bannerLiveFor}">
							{if $plan.Period}
								{$plan.Period} {if $plan.Plan_Type == 'period'}{$lang.days}{else}{$lang.banners_liveTypeViews}{/if}
							{else}
								{$lang.unlimited}
							{/if}
						</li>
					</ul>
				</td>
			</tr>
			</table>

			<div class="desc">
				<div class="text">{$plan.des|nl2br}</div>
			</div>
		</td>
	</tr>
	</table>
</li>
{/foreach}
</ul>

{php} } else { {/php}
	<table class="plans">
	{foreach from=$plans item='plan' name='plansF'}
		<tr {if $plan.ID == $smarty.post.plan}class="active"{/if}>
		{assign var='item_disabled' value=false}
		{if $plan.Limit > 0 && $plan.Using == 0 && $plan.Using != ''}
		{assign var='item_disabled' value=true}
		{/if}

		<td class="radio"><input {if $item_disabled}disabled="disabled"{/if} id="plan_{$plan.ID}" type="radio" name="plan" value="{$plan.ID}" {if $plan.ID == $smarty.post.plan}checked="checked"{/if} /></td>
		<td class="label" style="width:200px;">
			<table class="bg">
			<tr>
				<td class="left" {if $plan.Color}style="background-color: #{$plan.Color};"{/if}></td>
				<td class="center" {if $plan.Color}style="background-color: #{$plan.Color};"{/if}>
					<div class="price">
                    {if $plan.Price > 0}{if $config.system_currency_position == 'before'}{$config.system_currency}{/if}
                    {if $config.price_decimal == '.'}{$plan.Price}{else}{$plan.Price|replace:".":$config.price_decimal}{/if}
                    {if $config.system_currency_position == 'after'}{$config.system_currency}{/if}{else}{$lang.free}{/if}
					</div>
					<div class="type">{$lang.banners_planType}</div>
				</td>
				<td class="right">
					<div class="relative">
						<div {if $plan.Color}style="background-color: #{$plan.Color};"{/if}>
							{if $plan.Color}<div class="tile" style="background-color: #{$plan.Color};"></div>{/if}
							<div class="bg"></div>
						</div>
					</div>
				</td>
			</tr>
			</table>
		</td>
		<td class="info">
			<table class="sTable">
			<tr>
				<td class="caption"><div>{$plan.name}</div></td>
				<td>
					<ul class="features">
						<li class="period" title="{$lang.banners_bannerLiveFor}">
							{if $plan.Period}
								{$plan.Period} {if $plan.Plan_Type == 'period'}{$lang.days}{else}{$lang.banners_liveTypeViews}{/if}
							{else}
								{$lang.unlimited}
							{/if}
						</li>
					</ul>
				</td>
			</tr>
			</table>

			<div class="desc">
				<div class="text">{$plan.des|nl2br}</div>
			</div>
		</td>
	</tr>
	{/foreach}
	</table>

{php} } {/php}

<script type="text/javascript">
var selected_plan_id = 0;
var last_plan_id = 0;

{literal}

$(document).ready(function(){
	$('table.plans > tbody > tr').mouseenter(function() {
		$(this).find('ul.features').show();
		$('table.plans > tbody > tr input[name=plan]:checked').closest('tr').removeClass('active');
	}).mouseleave(function() {
		$(this).find('ul.features').hide();
		$('table.plans > tbody > tr input[name=plan]:checked').closest('tr').addClass('active');
	}).click(function() {
		if ( $(this).find('input[name=plan]:not(:disabled)') ) {
			$('table.plans > tbody > tr').removeClass('active');
			$(this).addClass('active');
			planClickHandler($(this).find('input[name=plan]'));
			$(this).find('input[name=plan]').attr('checked', true);
		}
	});

	$('table.plans > tbody > tr:first > td.info').width($('table.plans > tbody > tr:first > td.info').width()-10);

	if ( $('table.plans input[name=plan]:checked').length == 0 ) {
		$('table.plans input[name=plan]:not(:disabled):first').attr('checked', true);
	}

	planClickHandler($('table.plans input[name=plan]:checked'));
	$('table.plans input[name=plan]:checked').closest('tr').addClass('active');
});

var planClickHandler = function(obj) {
	if ( obj.length == 0 ) return;
}

{/literal}
</script>

<!-- banner_plans.tpl end -->