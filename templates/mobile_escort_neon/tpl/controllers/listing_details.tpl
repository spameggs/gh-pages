<!-- listing details -->

{rlHook name='mobileListingDetailsTopTpl'}

<div id="width_tracker"></div>

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

<!-- listing tab -->
<div id="area_listing" class="tab_area">

	<!-- listing photos -->
	{if $listing_type.Photo}
		<div class="photos">
		{if !empty($photos)}
			<div id="thumbnails">
				<div class="prev"></div>
				<div class="next"></div>
				
				<div id="scroll">
					<ul class="inner">
					{foreach from=$photos item='photo' key='pgKey' name='photosF'}
						<li><a href="{$smarty.const.RL_URL_HOME}files/{$photo.Photo}"><img src="{$smarty.const.RL_URL_HOME}files/{$photo.Thumbnail}" alt="{if $photo.Description}$photo.Description|replace:"'":'&#39;'|replace:'"':'&quot'}{else}{$pageInfo.name}{/if}" /></a></li>
					{/foreach}
					</ul>
				</div>
			</div>
			
			<script type="text/javascript" src="{$rlTplBase}js/klass.min.js"></script>
			<script type="text/javascript" src="{$rlTplBase}js/code.photoswipe.jquery-3.0.5.min.js"></script>
			<script type="text/javascript" src="{$rlTplBase}js/photo_gallery.js"></script>
		{else}
			<div>{$lang.no_listing_photos}</div>
		{/if}
		</div>
		<div class="box_shadow"></div>
	{/if}
	<!-- listing photos end -->

	{if $seller_info.Type == 'personal' && $config.messages_module && ($isLogin || (!$isLogin && $config.messages_allow_free))}
		<div style="padding: 0px 10px 15px;">
			<input id="contact_owner_btn" class="tall" type="button" value="{$lang.contact_owner}" />
		</div>
		
		<div class="hide form contact_owner" id="contact_owner">
			<div class="form_caption">{$lang.contact_owner}</div>
			<form onsubmit="xajax_contactOwner($('#contact_name').val(), $('#contact_email').val(), $('#contact_phone').val(), $('#contact_message').val(), $('#contact_code_security_code').val(), '{$listing_data.ID}');$(this).find('input[type=submit]').val('{$lang.loading}');return false;" action="post" name="contact_owner">
			<table class="submit">
			<tr>
				<td class="name">{$lang.name} <span class="red">*</span></td>
				<td class="field"><input type="text" class="text" id="contact_name" value="{$account_info.First_name} {$account_info.Last_name}" /></td>
			</tr>
			<tr>
				<td class="name">{$lang.mail} <span class="red">*</span></td>
				<td class="field"><input type="email" class="text" id="contact_email" value="{$account_info.Mail}" /></td>
			</tr>
			<tr>
				<td class="name">{$lang.contact_phone}</td>
				<td class="field"><input type="text" class="text" id="contact_phone" /></td>
			</tr>
			<tr>
				<td colspan="2">
					{$lang.message} <span class="red">*</span>
					<textarea class="text" id="contact_message" rows="6" cols=""></textarea>
				</td>	
			</tr>
			<tr>
				<td>
					<span class="field">{$lang.security_code} <span class="red">*</span></span>
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
			$('#contact_owner_btn').click(function(){
				$('#contact_owner').show();
				var poss = $('#contact_owner').position();
				$('html,body').scrollTop(poss.top+20);
			});
		});
		
		{/literal}
		</script>
	{/if}
	
	{rlHook name='listingDetailsPreFields'}
	
	<!-- listing info -->
	{foreach from=$listing item='group'}
		{if $group.Group_ID}
			{assign var='value_counter' value='0'}
			{foreach from=$group.Fields item='group_values' name='groupsF'}
				{if $group_values.value == '' || !$group_values.Details_page}
					{assign var='value_counter' value=$value_counter+1}
				{/if}
			{/foreach}
	
			{if !empty($group.Fields) && ($smarty.foreach.groupsF.total != $value_counter)}<!-- new thing -->
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id=$group.ID name=$group.name style='fg'}
			
			<div class="listing_group">
				<table>
				{foreach from=$group.Fields item='item' key='field' name='fListings'}
				{if !empty($item.value) && $item.Details_page}
				<tr id="df_field_{$item.Key}">
					<td class="name">{$item.name}:</td>
					<td class="value">{$item.value}</td>
				</tr>
				{/if}
				{/foreach}
				</table>
			</div>
			
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}
			{/if}
		{else}
			<div class="listing_group">
				<table >
				{assign value=$group.Fields.0 var='item'}
				{if !empty($item.value) && $item.Details_page}
				<tr id="df_field_{$item.Key}">
					<td class="name">{$item.name}</td>
					<td class="value">{$item.value}</td>
				</tr>
				{/if}
				</table>
			</div>
		{/if}
	{/foreach}
	<!-- listing info end -->

	{rlHook name='listingDetailsPostFields'}

</div>
<!-- listing tab -->

<!-- seller info tab -->
{if $seller_info.Type != 'personal'}
	<div id="area_seller" class="tab_area hide">
		<div class="padding">
			<table class="sTable">
			<tr>
				<td rowspan="2" valign="top" style="width: 100px">
					<div class="img_border">
						{if $seller_info.Own_page}<a title="{$lang.visit_owner_page}" href="{$seller_info.Personal_address}">{/if}
						<img title="{$seller_info.Full_name}" alt="{$seller_info.Full_name}" {if empty($seller_info.Photo)}style="width: 110px;"{/if} src="{if !empty($seller_info.Photo)}{$smarty.const.RL_URL_HOME}files/{$seller_info.Photo}{else}{$rlTplBase}img/account.gif{/if}" />
						{if $seller_info.Own_page}</a>{/if}
					</div>
					<div class="clear"></div>
				</td>
				<td valign="top">
					<div class="caption" style="height: 42px;">
						{$seller_info.Full_name}
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<ul class="item_stats">
						{if $seller_info.Own_page}
							{if $seller_info.Listings_count > 1}<li><a title="{$lang.other_owner_listings}" href="{$seller_info.Personal_address}#listings">{$lang.other_owner_listings}</a> <span class="counter">({$seller_info.Listings_count})</span></li>{/if}
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
			<form onsubmit="xajax_contactOwner($('#contact_name').val(), $('#contact_email').val(), $('#contact_phone').val(), $('#contact_message').val(), $('#contact_code_security_code').val(), '{$listing_data.ID}');$(this).find('input[type=submit]').val('{$lang.loading}');return false;" action="post" name="contact_owner">
			<table class="submit">
			<tr>
				<td class="name">{$lang.name} <span class="red">*</span></td>
				<td class="field"><input type="text" class="text" id="contact_name" value="{$account_info.First_name} {$account_info.Last_name}" /></td>
			</tr>
			<tr>
				<td class="name">{$lang.mail} <span class="red">*</span></td>
				<td class="field"><input type="email" class="text" id="contact_email" value="{$account_info.Mail}" /></td>
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
			{if $seller_info.Display_email}
			<tr>
				<td class="name">{$lang.mail}:</td>
				<td class="value">{encodeEmail email=$seller_info.Mail}</td>
			</tr>
			{/if}
			
			{foreach from=$seller_info.Fields item='field'}
			{if !empty($field.name) && !empty($field.value)}
			<tr id="si_field_{$field.Key}">
				<td class="name">{$field.name}:</td>
				<td class="value">{$field.value}</td>
			</tr>
			{/if}
			{/foreach}
			</table>
		</div>
	</div>
{/if}
<!-- seller info tab end -->

<!-- map tab -->
{if $config.map_module && $location}
<div id="area_map" class="tab_area hide">

	<div id="map" style="width: {if empty($config.map_width)}100%{else}{$config.map_width}px{/if}; height: {if empty($config.map_height)}300px{else}{$config.map_height}px{/if}"></div>
		
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false{if $smarty.const.RL_LANG_CODE != '' && $smarty.const.RL_LANG_CODE != 'en'}&amp;language={$smarty.const.RL_LANG_CODE}{/if}"></script>
	<script type="text/javascript" src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&amp;key={$config.google_map_key}"></script>
	<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.flmap.js"></script>
	<script type="text/javascript">//<![CDATA[
	{literal}
	
	var map_exist = false;
	$(document).ready(function(){
		$('div.tabs ul li').click(function(){
			if ( !map_exist && $(this).attr('id') == 'tab_map' )
			{
				$('#map').flMap({
					addresses: [
						[{/literal}'{if $location.direct}{$location.direct}{else}{$location.search}{/if}', '{$location.show}', '{if $location.direct}direct{else}geocoder{/if}'{literal}]
					],
					phrases: {
						hide: '{/literal}{$lang.hide}{literal}',
						show: '{/literal}{$lang.show}{literal}',
						notFound: '{/literal}{$lang.location_not_found}{literal}'
					},
					zoom: {/literal}{$config.map_default_zoom}{if $config.map_amenities && $amenities},{literal}
					localSearch: {
						caption: '{/literal}{$lang.local_amenity}{literal}',
						services: [{/literal}
							{foreach from=$amenities item='amenity' name='amenityF'}
							['{$amenity.Key}', '{$amenity.name}', {if $amenity.Default}'checked'{else}false{/if}]{if !$smarty.foreach.amenityF.last},{/if}
							{/foreach}
						{literal}]
					}
					{/literal}{/if}{literal}
				});
				map_exist = true;
			}
		});
	});
	
	{/literal}
	//]]>
	</script>
</div>
{/if}
<!-- map tab end -->

<!-- video tab -->
{if !empty($videos)}
	<div id="area_video" class="tab_area hide">
		{include file='blocks'|cat:$smarty.const.RL_DS|cat:'video_grid.tpl'}
	</div>
{/if}
<!-- video tab end -->

<!-- tell a friend tab -->
<div id="area_tell" class="tab_area hide">
	<table class="sTable">
	<tr>
		<td style="width: 110px;">
			<span class="field">{$lang.friend_name} <span class="red">*</span></span>
		</td>
		<td>
			<input class="text" type="text" id="friend_name" maxlength="50" value="{$smarty.post.friend_name}" />
		</td>
	</tr>
	<tr>
		<td>
			<span class="field">{$lang.friend_email} <span class="red">*</span></span>
		</td>
		<td>
			<input class="text" type="email" id="friend_email" maxlength="100" value="{$smarty.post.friend_email}" />
		</td>
	</tr>
	<tr>
		<td>
			<span class="field">{$lang.your_name}</span>
		</td>
		<td>
			<input class="text" type="text" id="your_name" maxlength="30" value="{$account_info.First_name} {$account_info.Last_name}" />
		</td>
	</tr>
	<tr>
		<td>
			<span class="field">{$lang.your_email}</span>
		</td>
		<td>
			<input class="text" type="email" id="your_email" maxlength="30" value="{$account_info.Mail}" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="field">{$lang.message} <span class="red">*</span></div>
			<textarea class="text" id="message" rows="6" cols="30">{$smarty.post.message}</textarea>
		</td>
	</tr>
	{if $config.security_img_tell_friend}
	<tr>
		<td>
			<span class="field">{$lang.security_code} <span class="red">*</span></span>
		</td>
		<td>
			{include file='captcha.tpl' no_caption=true}
		</td>
	</tr>
	{/if}
	<tr>
		<td></td>
		<td>
			<input onclick="xajax_tellFriend($('#friend_name').val(), $('#friend_email').val(), $('#your_name').val(), $('#your_email').val(), $('#message').val(), $('#security_code').val(), '{$listing_data.ID}');$('#tf_loading').fadeIn('normal');" style="margin: 0; width: 100px;" class="button" type="button" name="finish" value="{$lang.send}" />
			<span class="loading" id="tf_loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		</td>
	</tr>
	</table>
</div>
<!-- tell a friend tab end -->

<script type="text/javascript">//<![CDATA[
{literal}

var active_tab = 'listing';
var map_showed = true;
var name = '';

$(document).ready(function(){
	$('table.tabs td.item').click(function(){
		name = $(this).attr('abbr');

		$('table.tabs td[abbr='+active_tab+']').removeClass('active');
		$(this).addClass('active');
		
		$('#'+active_tab+'_tab').hide();
		$('#'+name+'_tab').show();
		
		active_tab = name;
	});
});

{/literal}

//]]>
</script>

{rlHook name='mobileListingDetailsBottomTpl'}

<!-- listing details end -->