<!-- category filter box -->

{assign var='cf_category_id' value=$category.ID}

<div class="filter-area">
	{if $cf_filter|@count > 1}
		<div class="clear-filters">
			<a href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$category.Path}{if $listing_type.Cat_postfix}.html{else}/{/if}{else}?page={$pages[$listing_type.Page_key]}&amp;category={$category.ID}{/if}">{$lang.categoryFilter_remove_filters}<img style="margin: 0 7px;" title="{$lang.categoryFilter_remove_filter}" alt="" class="remove" src="{$rlTplBase}img/blank.gif" /></a>
		</div>
	{/if}
	
	{if $cf_items}
		{foreach from=$cf_items item='cf_item'}
			{assign var='cf_field' value=$cf_item.Items|ctJsonDecode}
			{assign var='cf_name' value=$cf_item.pName}
			{assign var='cf_key' value=$cf_item.Key}
			{assign var='cf_count_items' value=0}
			{assign var='cf_fieldset' value='cf_'|cat:$cf_item.Key}
			{assign var='pass_key' value=$cf_item.Key|replace:'_':'-'}

			{if $cf_item.Key|strpos:"level"}
				{if $cf_item.Key|substr:-1:1 > 1}
					{math equation="x-y" x=$cf_item.Key|substr:-1:1 y=1 assign="prevnumber"}
					{assign var="prevfilter" value=$cf_item.Key|substr:0:-1|cat:$prevnumber}
				{else}
					{assign var="prevfilter" value=$cf_item.Key|substr:0:-7}
				{/if}

				{if !$cf_filter.$prevfilter}
					{continue}
				{/if}
			{/if}
			
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id=$cf_fieldset name=$lang.$cf_name}

			{if isset($cf_filter.$cf_key) && $cf_item.Mode != 'slider'}
				{assign var='active_name' value=false}
				{if $cf_item.Type == 'checkbox'}
				<ul>
				{/if}
				{foreach from=$cf_field item='cf_count' key='cf_name' name='itemF'}
					{if $cf_item.Type == 'radio' || $cf_item.Type == 'select'}
						{if $cf_name == $cf_filter.$cf_key}
							{if $item_names[$cf_item.Key] && !$cf_item.Key|strpos:"level"}
								{assign var='phrase_name' value=$item_names[$cf_item.Key].$cf_name}
							{else}
								{if $cf_item.Condition}
									{assign var='phrase_name' value='data_formats+name+'|cat:$cf_item.Condition|cat:'_'|cat:$cf_name}
									{if !$lang.$phrase_name}
										{assign var='phrase_name' value='data_formats+name+'|cat:$cf_name}
									{/if}
								{else}
									{if $cf_item.Key == 'posted_by'}
										{assign var='phrase_name' value='account_types+name+'|cat:$cf_name}
									{else}
										{assign var='phrase_name' value='listing_fields+name+'|cat:$cf_item.Key|cat:'_'|cat:$cf_name}
									{/if}
								{/if}
							{/if}

							{assign var='active_name' value=$lang.$phrase_name}
						{/if}
					{elseif $cf_item.Type == 'bool'}
						{if $cf_name == $cf_filter.$cf_key}
							{if $item_names[$cf_item.Key]}
								{assign var='phrase_name' value=$item_names[$cf_item.Key].$cf_name}
								{assign var='active_name' value=$lang.$phrase_name}
							{else}
								{if $cf_name}
									{assign var='active_name' value=$lang.yes}
								{else}
									{assign var='active_name' value=$lang.no}
								{/if}
							{/if}
						{/if}
					{elseif $cf_item.Type == 'checkbox'}
						{assign var='ct_current' value=','|explode:$cf_filter.$cf_key|urldecode}
						
						{if $item_names[$cf_item.Key]}
							{assign var='phrase_name' value=$item_names[$cf_item.Key].$cf_name}
						{else}	
							{if $cf_item.Condition}
								{assign var='phrase_name' value='data_formats+name+'|cat:$cf_item.Condition|cat:'_'|cat:$cf_name}
								{if !$lang.$phrase_name}
									{assign var='phrase_name' value='data_formats+name+'|cat:$cf_name}
								{/if}
							{else}
								{assign var='phrase_name' value='listing_fields+name+'|cat:$cf_item.Key|cat:'_'|cat:$cf_name}
							{/if}
						{/if}
						
						<li><label title="{$lang.$phrase_name}"><input {if false !== $cf_name|array_search:$ct_current}checked="checked"{/if} type="checkbox" name="cf_checkbox_{$cf_item.Key}[]" value="{$cf_name}" /> {$lang.$phrase_name}</label></li>
					{else}
						{if $cf_name|replace:'/':' '|replace:':':' ' == $cf_filter.$cf_key|urldecode}
							{*assign var='active_name' value=$cf_name}
						{else*}
							{if $item_names[$cf_item.Key]}
								{assign var='disp_key' value=$cf_filter.$cf_key|urldecode}
								{assign var='disp_key' value=$item_names[$cf_item.Key].$disp_key}
								{assign var='active_name' value=$lang.$disp_key}
							{else}
								{assign var='active_name' value=$cf_filter.$cf_key|urldecode}
							{/if}
						{/if}
					{/if}
				{/foreach}
				
				{if $cf_item.Type == 'checkbox'}
				</ul>
				<div class="cf-apply" id="cf_checkbox_{$cf_item.Key}">
					<a accesskey="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$category.Path}/{foreach from=$cf_filter key='filter_key' item='filter_val'}{if $cf_key != $filter_key|replace:'-':'_'}{$filter_key|replace:'_':'-'}:{$filter_val}/{else}{$pass_key}:[replace]/{/if}{/foreach}{else}?page={$pages[$listing_type.Page_key]}&amp;category={$category.ID}{foreach from=$cf_filter key='filter_key' item='filter_val'}{if $cf_key != $filter_key|replace:'_':'-'}&amp;cf-{$filter_key|replace:'_':'-'}={$filter_val}{else}&amp;cf-{$pass_key}=[replace]{/if}{/foreach}{/if}" title="{$lang.categoryFilter_apply_filter}" href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$category.Path}/{foreach from=$cf_filter key='filter_key' item='filter_val'}{if $cf_key != $filter_key|replace:'_':'-'}{$filter_key|replace:'_':'-'}:{$filter_val}/{else}{$pass_key}:1/{/if}{/foreach}{else}?page={$pages[$listing_type.Page_key]}&amp;category={$category.ID}{foreach from=$cf_filter key='filter_key' item='filter_val'}{if $cf_key != $filter_key|replace:'_':'-'}&amp;cf-{$filter_key|replace:'_':'-'}={$filter_val}{else}&amp;cf-{$pass_key}=1{/if}{/foreach}{/if}">{$lang.categoryFilter_apply_filter}</a>
					<a class="cf-remove hide" href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$category.Path}/{foreach from=$cf_filter key='filter_key' item='filter_val'}{if $filter_key != $cf_key}{$filter_key}:{$filter_val}/{/if}{/foreach}{else}?page={$pages[$listing_type.Page_key]}&amp;category={$category.ID}{foreach from=$cf_filter key='filter_key' item='filter_val'}{if $filter_key != $cf_key}&amp;cf-{$filter_key}={$filter_val}{/if}{/foreach}{/if}"><span class="hide">-{$lang.categoryFilter_apply_filter}</span><img title="{$lang.categoryFilter_remove_filter}" alt="" class="remove" src="{$rlTplBase}img/blank.gif" /></a>
				</div>
				<script type="text/javascript">
				var cf_apply_filter = "{$lang.categoryFilter_apply_filter}";
				var cf_remove_filter = "{$lang.categoryFilter_remove_filter}";
				{literal}
				
				$(document).ready(function(){
					categoryFilter.checkbox($('#cf_checkbox_{/literal}{$cf_item.Key}{literal}'), true);
				});
				
				{/literal}
				</script>
				{/if}
				
				{if !$active_name && $cf_item.Type != 'checkbox'}
					{assign var='active_name' value=$cf_filter.$cf_key|urldecode}
				{/if}
				
				{if $active_name}
					<div class="dark"><span>
						{if $cf_item.Type == 'price'&& $aHooks.currencyConverter && $config.system_currency_position == 'before'}
							{if $curConv_search_key}{$curConv_search_key}{elseif $curConv_country.Currency}{if $curConv_mapping[$curConv_country.Currency]}{$curConv_mapping[$curConv_country.Currency]}{else}{$curConv_country.Currency}{/if}{/if}
						{/if}
						{$active_name}
						{if $cf_item.Type == 'price'&& $aHooks.currencyConverter && $config.system_currency_position == 'after'}
							{if $curConv_search_key}{$curConv_search_key}{elseif $curConv_country.Currency}{if $curConv_mapping[$curConv_country.Currency]}{$curConv_mapping[$curConv_country.Currency]}{else}{$curConv_country.Currency}{/if}{/if}
						{/if}
					</span> <a href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$category.Path}/{foreach from=$cf_filter key='filter_key' item='filter_val'}{if $filter_key != $cf_key}{$filter_key}:{$filter_val}/{/if}{/foreach}{else}?page={$pages[$listing_type.Page_key]}&amp;category={$category.ID}{foreach from=$cf_filter key='filter_key' item='filter_val'}{if $filter_key != $cf_key}&amp;cf-{$filter_key}={$filter_val}{/if}{/foreach}{/if}"><img title="{$lang.categoryFilter_remove_filter}" alt="" class="remove" src="{$rlTplBase}img/blank.gif" /></a></div>
				{/if}
			{else}
				{assign var='tmp_count' value=0}
				{assign var='tmp_items_count' value=0}
				{foreach from=$cf_field item='cf_count' key='cf_name'}
					{if $cf_count->$cf_category_id}
						{assign var='tmp_count' value=$tmp_count+$cf_count->$cf_category_id}
						{assign var='tmp_items_count' value=$tmp_items_count+1}
					{/if}
				{/foreach}
				<ul>
				{if $cf_item.Mode == 'slider'}
					<li>
						{assign var='cF' value=$cf_item.Key}
						<script type="text/javascript">
						var cf_{$cF}_data = new Array();
						{assign var='step' value=1}
						{assign var='item_min' value=false}
						{assign var='item_max' value=false}
						{if $smarty.session.curConv_code}
							{assign var='cf_rate' value=$smarty.session.curConv_code}
						{else}
							{assign var='cf_rate' value=$smarty.cookies.curConv_code}
						{/if}
						
						{foreach from=$cf_field item='cf_count' key='cf_name' name='itemF'}
							{if $cf_item.Type == 'price' && $aHooks.currencyConverter && ($cf_rate)}
								{math assign='cf_name' equation='ceil(a*b)' a=$cf_name b=$curConv_rates[$cf_rate].Rate}
							{/if}
							{if false !== $cf_name|strpos:'|'}
								{assign var='cf_name_exp' value='|'|explode:$cf_name}
								cf_{$cF}_data[{$smarty.foreach.itemF.iteration}] = new Array({$cf_count->$cf_category_id}, '{$cf_name_exp.1}', {$cf_name_exp[0]|round});
							{else}
								cf_{$cF}_data[{$smarty.foreach.itemF.iteration}] = new Array({$cf_count->$cf_category_id}, false, {$cf_name|round} );
							{/if}
							
							{assign var='cf_name' value=$cf_name|intval}
							{if $cf_count->$cf_category_id}
								{if $cf_name > $item_max || !$item_max}
									{assign var='item_max' value=$cf_name}
								{/if}
								{if $cf_name < $item_min || !$item_min}
									{assign var='item_min' value=$cf_name}
								{/if}
							{/if}
						{/foreach}
						</script>
						
						{if is_array($smarty.session.cf_slider_data[$cf_box_id][$cf_key])}
							{assign var='item_min' value=$smarty.session.cf_slider_data[$cf_box_id][$cf_key].min}
							{assign var='item_max' value=$smarty.session.cf_slider_data[$cf_box_id][$cf_key].max}
						{/if}
						
						{assign var='item_min_init' value=$item_min}
						{assign var='item_max_init' value=$item_max}
						
						{math assign='step' equation='ceil((max-min)/limit)' min=$item_min max=$item_max limit=20}
						{if $cf_item.Type == 'price'}
							{if $step >= 2500}
								{assign var='round' value=-3}
							{elseif $step < 2500 && $step >= 100}
								{assign var='round' value=-2}
							{elseif $step < 100 && $step > 10}
								{assign var='round' value=-1}
							{else}
								{assign var='round' value=0}
							{/if}
							
							{assign var='step' value=$step|round:$round}
							{assign var='item_min' value=$item_min|round:$round}
							{assign var='item_max' value=$item_max|round:$round}
						{/if}
						
						{if $item_min > $item_min_init}
							{if $item_min-$step < 0}
								{assign var='item_min' value=0}
							{else}
								{assign var='item_min' value=$item_min-$step}
							{/if}
						{/if}
						
						{if $item_max < $item_max_init}
							{assign var='item_max' value=$item_min+$step}
						{/if}
						
						{if !$step}
							{assign var='step' value=1}
						{/if}
						
						{if isset($cf_filter.$cf_key)}
							{assign var='slider_exp' value='-'|explode:$cf_filter.$cf_key}
							{assign var='slider_min' value=$slider_exp[0]}
							{assign var='slider_max' value=$slider_exp[1]}
							{php}
								global $rlSmarty, $cf_key;
								$cf_filter_slider = $rlSmarty -> get_template_vars('cf_filter');
								$cf_key = $rlSmarty -> get_template_vars('cf_key');;
								if ( $cf_filter_slider[$cf_key] )
								{
									unset($cf_filter_slider[$cf_key]);
								}
								$rlSmarty -> assign('cf_filter_slider', $cf_filter);
							{/php}
						{else}
							{assign var='slider_min' value=$item_min}
							{assign var='slider_max' value=$item_max}
							{assign var='cf_filter_slider' value=$cf_filter}
						{/if}
						
						{if is_numeric($item_min) && is_numeric($item_max)}
							<div class="cf-slider">
								<input type="hidden" value="{$slider_min};{$slider_max}" name="slider_{$cf_item.Key}" />
							</div>
							<div class="cf-apply" id="cf_link_{$cf_item.Key}">
								<a title="{$lang.categoryFilter_apply_filter}" href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$category.Path}/{foreach from=$cf_filter_slider key='filter_key' item='filter_val'}{$filter_key|replace:'_':'-'}:{$filter_val}/{/foreach}{$pass_key}:{$item_min}-{$item_max}/{else}?page={$pages[$listing_type.Page_key]}&amp;category={$category.ID}{foreach from=$cf_filter_slider key='filter_key' item='filter_val'}&amp;cf-{$filter_key|replace:'_':'-'}={$filter_val}{/foreach}&amp;cf-{$pass_key}={$item_min}-{$item_max}{/if}">{$lang.categoryFilter_apply_filter}</a> <span class="counter">(2)</span>
							</div>
							
							<script type="text/javascript">//<![CDATA[
							var slider_{$cF} = '{$cf_item.Key}';
							var slider_{$cF} = slider_{$cF}.replace('-', '\-');
							var slider_pattern_{$cF} = new RegExp(slider_{$cF}+'[\:|\=]([^/]+)');
							var slider_dimension_{$cF} = {if $cf_item.Type == 'price' && $aHooks.currencyConverter && ($smarty.session.curConv_code || $smarty.cookies.curConv_code)}'&nbsp;'+currencyConverter.rates[currencyConverter.config['currency']][1][0]{else}''{/if};
							
							{literal}
							$(document).ready(function(){
								$('input[name=slider_'+slider_{/literal}{$cF}{literal}+']').slider({
									{/literal}
									from: {$item_min},
									to: {$item_max},
									step: {$step},
									skin: 'round_plastic',
									raund: 0,
									limits: true,
									{if $cf_item.Type != 'price'}{literal}format: {format: '#', locale: 'us'},{/literal}{/if}
									{literal}
									dimension: slider_dimension_{/literal}{$cF}{literal},
									onstatechange: function(value){
										var data = value.split(';');
										
										data[0] = parseInt(data[0]);
										data[1] = parseInt(data[1]);
										var total = 0;
										for (var i=0; i<=cf_{/literal}{$cF}{literal}_data.length; i++)
										{
											if ( cf_{/literal}{$cF}{literal}_data[i] )
											{
												var count = parseInt(cf_{/literal}{$cF}{literal}_data[i][0]);
												var price = parseInt(cf_{/literal}{$cF}{literal}_data[i][2]);
												
												if ( price >= data[0] && price <= data[1] && count )
												{
													total += parseInt(count);
												}
												
											}										
										}
										
										
										if ( total > 0 )
										{
											var sign = rlConfig['mod_rewrite'] ? ':' : '=';
											$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' span.counter').html('('+total+')');
											var href = $('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').attr('href');
											href = href.replace(slider_pattern_{/literal}{$cF}{literal}, slider_{/literal}{$cF}{literal}+sign+data[0]+'-'+data[1]);
											$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').attr('href', href);
											
											if ( !$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').is(':visible') )
											{
												$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').next().remove();
												$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').show();
												$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').parent().removeClass('dark single');
											}
										}
										else
										{
											$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' span.counter').html('('+total+')');
											
											if ( $('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').is(':visible') )
											{
												var name = $('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').html();
												$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').after('<span></span>');
												$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' a').hide();
												$('div#cf_link_'+slider_{/literal}{$cF}{literal}+' span:first').html(name).parent().addClass('dark single');
											}
										}
									}
								});
							});
							
							{/literal}
							//]]>
							</script>
						{else}
							<span class="dark single">{$lang.categoryFilter_no_listings}</span>
						{/if}
					</li>
				{else}
					{if $tmp_count}
						{foreach from=$cf_field item='cf_count' key='cf_name' name='itemF'}
							{if $cf_count->$cf_category_id}
								{assign var='disp_phrase' value=false}
								<li {if $cf_count_items >= $cf_item.Items_display_limit}class="hide"{/if}>
									{if ($cf_item.Type == 'radio' || $cf_item.Type == 'select') && $cf_item.Condition != 'years'}
										{if $cf_name}
											{if $item_names[$cf_item.Key] && $item_names[$cf_item.Key].$cf_name}
												{assign var='phrase_name' value=$item_names[$cf_item.Key].$cf_name}
											{else}
												{if $cf_item.Condition}
													{assign var='phrase_name' value='data_formats+name+'|cat:$cf_item.Condition|cat:'_'|cat:$cf_name}
													{if !$lang.$phrase_name}
														{assign var='phrase_name' value='data_formats+name+'|cat:$cf_name}
													{/if}
												{else}
													{if $cf_item.Key == 'posted_by'}
														{assign var='phrase_name' value='account_types+name+'|cat:$cf_name}
													{else}
														{assign var='phrase_name' value='listing_fields+name+'|cat:$cf_item.Key|cat:'_'|cat:$cf_name}
													{/if}
												{/if}
											{/if}
											{assign var='disp_phrase' value=$lang.$phrase_name}
										{/if}
									{elseif $cf_item.Type == 'bool'}
										{if $item_names[$cf_item.Key]}
											{assign var='phrase_name' value=$item_names[$cf_item.Key].$cf_name}
											{assign var='disp_phrase' value=$lang.$phrase_name}
										{else}
											{if $cf_name}
												{assign var='disp_phrase' value=$lang.yes}
											{else}
												{assign var='disp_phrase' value=$lang.no}
											{/if}
										{/if}
									{elseif $cf_item.Type == 'checkbox'}
										{if $cf_name}
											{if $item_names[$cf_item.Key]}
												{assign var='phrase_name' value=$item_names[$cf_item.Key].$cf_name}
											{else}
												{if $cf_item.Condition}
													{assign var='phrase_name' value='data_formats+name+'|cat:$cf_item.Condition|cat:'_'|cat:$cf_name}
													{if !$lang.$phrase_name}
														{assign var='phrase_name' value='data_formats+name+'|cat:$cf_name}
													{/if}
												{else}
													{assign var='phrase_name' value='listing_fields+name+'|cat:$cf_item.Key|cat:'_'|cat:$cf_name}
												{/if}
											{/if}
											
											<label title="{$lang.$phrase_name}"><input type="checkbox" name="cf_checkbox_{$cf_item.Key}[]" value="{$cf_name}" /> {$lang.$phrase_name}</label>
										{/if}
									{elseif $cf_item.Type == 'price'}
										{if $item_names[$cf_item.Key]}
											{assign var='disp_key' value=$item_names[$cf_item.Key].$cf_name}
											{assign var='disp_phrase' value=$lang.$disp_key}
										{else}
											{assign var='disp_phrase' value=$cf_name}
										{/if}
									{else}
										{if $item_names[$cf_item.Key]}
											{assign var='disp_key' value=$item_names[$cf_item.Key].$cf_name}
											{assign var='disp_phrase' value=$lang.$disp_key}
										{else}
											{assign var='disp_phrase' value=$cf_name}
										{/if}
									{/if}
									
									{if $disp_phrase}
										{if $tmp_count == 0 || $tmp_items_count < 1}
											<div class="dark single"><span>{$disp_phrase}</span> <span class="counter">({$cf_count->$cf_category_id})</span></div>
										{else}
											<a title="{$disp_phrase}" href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$category.Path}/{foreach from=$cf_filter key='filter_key' item='filter_val'}{$filter_key|replace:'_':'-'}:{$filter_val}/{/foreach}{$pass_key}:{$cf_name|replace:'/':' '|replace:':':' '}/{else}?page={$pages[$listing_type.Page_key]}&amp;category={$category.ID}{foreach from=$cf_filter key='filter_key' item='filter_val'}&amp;cf-{$filter_key|replace:'_':'-'}={$filter_val}{/foreach}&amp;cf-{$pass_key}={$cf_name|replace:'/':' '|replace:':':' '}{/if}">{$disp_phrase}</a><span class="counter"> ({$cf_count->$cf_category_id})</span>
										{/if}
									{/if}
								</li>
								{assign var='cf_count_items' value=$cf_count_items+1}
							{/if}
						{foreachelse}
							<li class="dark single"><span>{$lang.categoryFilter_no_listings}</span></li>
						{/foreach}
					{else}
						<li class="dark single"><span>{$lang.categoryFilter_no_listings}</span></li>
					{/if}
				{/if}
				</ul>
				{if $cf_count_items > $cf_item.Items_display_limit && $cf_item.Type != 'checkbox'}
					<a class="dark_12 more" href="javascript:void(0)" rel="nofollow">{$lang.categoryFilter_show_more}</a>
				{/if}
				
				{if $cf_item.Type == 'checkbox'}
					<div class="cf-apply" id="cf_checkbox_{$cf_item.Key}">
						<a accesskey="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$category.Path}/{foreach from=$cf_filter key='filter_key' item='filter_val'}{$filter_key|replace:'_':'-'}:{$filter_val}/{/foreach}{$pass_key}:[replace]/{else}?page={$pages[$listing_type.Page_key]}&amp;category={$category.ID}{foreach from=$cf_filter key='filter_key' item='filter_val'}&amp;cf-{$filter_key|replace:'_':'-'}={$filter_val}{/foreach}&amp;cf-{$pass_key}=[replace]{/if}" title="{$lang.categoryFilter_apply_filter}" href="{$rlBase}{if $config.mod_rewrite}{$pages[$listing_type.Page_key]}/{$category.Path}/{foreach from=$cf_filter key='filter_key' item='filter_val'}{$filter_key|replace:'_':'-'}:{$filter_val}/{/foreach}{$pass_key}:1{else}?page={$pages[$listing_type.Page_key]}&amp;category={$category.ID}{/if}">{$lang.categoryFilter_apply_filter}</a>
						<span class="hide">{$lang.categoryFilter_apply_filter}</span>
					</div>
					<script type="text/javascript">
					{literal}
					
					$(document).ready(function(){
						categoryFilter.checkbox($('#cf_checkbox_{/literal}{$cf_item.Key}{literal}'));
					});
					
					{/literal}
					</script>
				{/if}
			{/if}
			
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
		{/foreach}
		
		<script type="text/javascript">categoryFilter.moreFilters();</script>
	{else}
		{$lang.categoryFilter_no_fields_added}
	{/if}
</div>

<!-- category filter box end -->
