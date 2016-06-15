<!-- accounts tpl -->

{if $account_type}
	
	<!-- account details -->
	{if $account}
		<!-- tabs -->
		<div class="tabs">
			<ul>
				{foreach from=$tabs item='tab' name='tabF'}
					{if $tab.key != 'tell_friend'}
					<li {if $smarty.foreach.tabF.first}class="active first"{/if} id="tab_{$tab.key}">
						<span class="center"><span>{$tab.name}</span></span>
					</li>
					{/if}
				{/foreach}
			</ul>
		</div>
		<div class="clear"></div>
		<!-- tabs end -->
		
		<!-- account details -->
		<div id="area_details" class="tab_area">
			<div class="padding">
				<table class="sTable">
				<tr>
					<td rowspan="2" valign="top" style="width: 100px">
						<div class="img_border">
							{if $account.Own_page}<a title="{$lang.visit_owner_page}" href="{$account.Personal_address}">{/if}
							<img class="img" title="{$account.Full_name}" alt="{$account.Full_name}" {if empty($account.Photo)}style="width: 110px;"{/if} src="{if !empty($account.Photo)}{$smarty.const.RL_URL_HOME}files/{$account.Photo}{else}{$rlTplBase}img/account.gif{/if}" />
							{if $account.Own_page}</a>{/if}
						</div>
						<div class="clear"></div>
					</td>
					<td valign="top">
						<div class="caption" style="height: 42px;">
							{$account.Full_name}
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<ul class="item_stats">
							{if $account.Own_page}
								{if $account.Listings_count > 1}<li><a title="{$lang.other_owner_listings}" onclick="tabsSwitcher('td[abbr=listings]')" href="javascript:void(0)">{$lang.other_owner_listings}</a> <span class="counter">({$account.Listings_count})</span></li>{/if}
							{/if}
							{rlHook name='mobileSellerinfoAfterStat'}
						</ul>
		
						<div style="padding: 0 10px;">
							<a id="contactOwnerBtn" class="button" href="javascript:void(0)">
								{$lang.contact_owner}
							</a>
						</div>
					</td>
				</tr>
				</table>
			</div>
				
			<div class="hide form contact_owner" id="contact_owner">
				<div class="form_caption">{$lang.contact_owner}</div>
				<form onsubmit="xajax_contactOwner($('#contact_name').val(), $('#contact_email').val(), $('#contact_phone').val(), $('#contact_message').val(), $('#contact_code_security_code').val(), '{$listing_data.ID}');$(this).find('input[type=submit]').val('{$lang.loading}');return false;" name="contact_owner">
				<table class="submit">
				<tr>
					<td class="name">{$lang.name} <span class="red">*</span></td>
					<td class="field"><input type="text" class="text" id="contact_name" value="{$account_info.First_name} {$account_info.Last_name}" /></td>
				</tr>
				<tr>
					<td class="name">{$lang.mail} <span class="red">*</span></td>
					<td class="field"><input type="text" class="text" id="contact_email" value="{$account_info.Mail}" /></td>
				</tr>
				<tr>
					<td class="name">{$lang.contact_phone}</td>
					<td class="field"><input type="text" class="text" id="contact_phone" /></td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="name">{$lang.message} <span class="red">*</span></div>
						<textarea class="text" id="contact_message" rows="6" cols=""></textarea>
					</td>	
				</tr>
				<tr>
					<td>
						<span class="name">{$lang.security_code} <span class="red">*</span></span>
					</td>
					<td>
						{include file='captcha.tpl' no_caption=true captcha_id='contact_code' }
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input class="tall" type="submit" name="finish" value="{$lang.send}" />
						<a class="cancel" onclick="$('#contact_owner').hide();" href="javascript:void(0);">{$lang.cancel}</a>
					</td>
				</tr>
				</table>
				</form>
			</div>
				
			<script type="text/javascript">
			{literal}
			
			$(document).ready(function(){
				$('#contactOwnerBtn').click(function(){
					$('#contact_owner').show();
					var poss = $('#contact_owner').position();
					$('html,body').scrollTop(poss.top+20);
				});
			});
			
			{/literal}
			</script>
		
			<div class="padding listing_group" style="padding-top: 20px;">
				<table>
				{if $account.Display_email}
				<tr>
					<td class="name">{$lang.mail}:</td>
					<td class="value"><a class="static" href="mailto:{$account.Mail}">{$account.Mail}</a></td>
				</tr>
				{/if}
				
				{if $account.Fields}
					{foreach from=$account.Fields item='field'}
					{if !empty($field.name) && !empty($field.value)}
					<tr id="si_field_{$field.Key}">
						<td class="name">{$field.name}:</td>
						<td class="value">{$field.value}</td>
					</tr>
					{/if}
					{/foreach}
				{/if}
				</table>
			</div>
			
			<script type="text/javascript">
			var current_page = {if $pInfo.current}{$pInfo.current}{else}false{/if};
			var sorting_mode = {if $sorting_mode}true{else}false{/if};
			{literal}
			
			$(document).ready(function(){
				/* switch to listings tab */
				if ( flynax.getHash() == 'listings' || current_page > 0 || sorting_mode )
				{
					tabsSwitcher('td[abbr=listings]')
				}
			});
			
			{/literal}
			</script>
			
		</div>
		<!-- account details end -->
		
		<!-- account listings -->
		<div id="area_listings" class="tab_area hide">
			{if !empty($listings)}
			
				<!-- listings -->
				<div id="listings">
					<ul>
						{foreach from=$listings item='listing' key='key' name='listingsF'}
							{include file='blocks'|cat:$smarty.const.RL_DS|cat:'listing.tpl'}
						{/foreach}
					</ul>
				</div>
				<!-- listings end -->

				<!-- paging block -->
				{paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page custom=$account.Own_address full=true}
				<!-- paging block end -->
			
			{else}
				<div class="padding">{$lang.no_dealer_listings}</div>
			{/if}
		</div>
		<!-- account listings end -->
		
		<!-- map -->
		<div id="area_map" class="tab_area hide">
			<div class="highlight">
				<div id="map" style="width: {if empty($config.map_width)}100%{else}{$config.map_width}px{/if}; height: {if empty($config.map_height)}300px{else}{$config.map_height}px{/if}"></div>
			</div>
				
			<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false{if $smarty.const.RL_LANG_CODE != '' && $smarty.const.RL_LANG_CODE != 'en'}&amp;language={$smarty.const.RL_LANG_CODE}{/if}"></script>
			<script type="text/javascript" src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&amp;key={$config.google_map_key}"></script>
			<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.flmap.js"></script>
			<script type="text/javascript">//<![CDATA[
			{literal}
			
			var map_exist = false;
			$(document).ready(function(){
				$('.tabs li').click(function(){
					if ( !map_exist && $(this).attr('id') == 'tab_map' )
					{
						$('#map').flMap({
							addresses: [
								['{/literal}{$location.search}{literal}', '{/literal}{$location.show}{literal}', 'geocoder']
							],
							phrases: {
								hide: '{/literal}{$lang.hide}{literal}',
								show: '{/literal}{$lang.show}{literal}',
								notFound: '{/literal}{$lang.location_not_found}{literal}'
							},
							zoom: {/literal}{$config.map_default_zoom}{literal},
							localSearch: {
								caption: '{/literal}{$lang.local_amenity}{literal}',
								services: [
									['hospital', 'Hospital', 'checked'],
									['school', 'School'],
									['cafe', 'Cafe', 'checked'],
									['pizza', 'Pizza'],
									['Burger-King', 'Burger King', 'checked']
								]
							}
						});
						map_exist = true;
					}
				});
			});
			
			{/literal}
			//]]>
			</script>
		</div>
		<!-- map -->
	
	<!-- accounts search -->
	{else}
		<!-- tabs -->
		<div class="tabs">
			<ul>
				{foreach from=$tabs item='tab' name='tabF'}
					{if $tab.key != 'tell_friend'}
					<li {if $smarty.foreach.tabF.first}class="active first"{/if} id="tab_{$tab.key}">
						<span class="center"><span>{$tab.name}</span></span>
					</li>
					{/if}
				{/foreach}
			</ul>
		</div>
		<div class="clear"></div>
		<!-- tabs end -->
			
		<!-- characters tab -->
		<div id="area_characters" class="tab_area">
			
			<div id="characters_line">
				{foreach from=$alphabet item='character'}
					<a href="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$character}.html{else}?page={$pageInfo.Path}&amp;character={$character}{/if}" {if $character == $char}class="active"{/if}>{$character}</a>
				{/foreach}
			</div>
			
			<!-- dealers list -->
			{if !empty($alphabet_dealers)}
			
				<div class="dealers">
					<ul>
					{foreach from=$alphabet_dealers item='dealer' name='dealersF'}
						{include file='blocks'|cat:$smarty.const.RL_DS|cat:'dealer.tpl'}
					{/foreach}
					</ul>
				</div>
				
				<!-- paging block -->
				{paging calc=$pInfo.calc_alphabet total=$alphabet_dealers|@count current=$pInfo.current per_page=$config.dealers_per_page url=$char var='character'}
				<!-- paging block end -->
			{else}
				<div class="padding">{if $search_results != 'search'}{$lang.no_dealers}{/if}</div>
			{/if}
			<!-- dealers list end -->
		</div>
		<!-- characters tab end -->
		
		<!-- advanced search tab -->
		<div id="area_search" class="hide tab_area">
			{if $search_results == 'search'}
				{if $dealers}
					<div class="dealers">
						<ul>
						{foreach from=$dealers item='dealer' name='dealersF'}
							{include file='blocks'|cat:$smarty.const.RL_DS|cat:'dealer.tpl'}
						{/foreach}
						</ul>
					</div>
					
					<!-- paging block -->
					{paging calc=$pInfo.calc total=$dealers|@count current=$pInfo.current per_page=$config.dealers_per_page url=$search_results_url}
					<!-- paging block end -->
				{else}
					<div class="padding">{$lang.no_dealers_found}</div>
				{/if}
			{else}
				<form method="post" action="{$rlBase}{if $config.mod_rewrite}{$pageInfo.Path}/{$search_results_url}.html{else}?page={$pageInfo.Path}&amp;{$search_results_url}{/if}">
					<input type="hidden" name="search" value="true" />
					{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fields_search.tpl'}
				
					<div style="padding: 20px 10px 0;">
						<input class="tall" type="submit" name="search" value="{$lang.search}" />
					</div>
				</form>
				
				<script type="text/javascript">
				var phrase_from = "{$lang.from}";
				var phrase_to = "{$lang.to}";
				{literal}
				
				$(document).ready(function(){
					flynax.fromTo(phrase_from, phrase_to);
				});
				
				{/literal}
				</script>
			{/if}
		</div>
		<!-- advanced search tab end -->
		
		{if $alphabet_mode}
			<script type="text/javascript">
				{literal}
				$(document).ready(function(){
					$('.tabs li#tab_characters').trigger('click');
				});
				{/literal}
			</script>
		{elseif $search_results}
			<script type="text/javascript">
				{literal}
				$(document).ready(function(){
					$('li#tab_search').trigger('click');
				});
				{/literal}
			</script>
		{/if}
		
		<script type="text/javascript">
		{literal}
		$(document).ready(function(){
			if ( flynax.getHash('modify') )
			{
				$('.tabs li#tab_search').trigger('click');
			}
		});
		{/literal}
		</script>
	{/if}

{/if}

<!-- accounts tpl end -->