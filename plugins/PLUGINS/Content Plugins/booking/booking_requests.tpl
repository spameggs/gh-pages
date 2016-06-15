<!-- booking requests -->

{if $requests}
	{if $config.mod_rewrite}
		{assign var='require_id' value=$smarty.get.request_id}
	{else}
		{assign var='require_id' value=$smarty.get.id}
	{/if}

{if !$require_id}
<div class="highlight">
	{if empty($requests)}
		<div class="info">{$lang.booking_no_new_requests}</div>
	{else}
		<table class="list" id="saved_search">
		<tr class="header">
			<td align="center" class="no_padding" style="width: 15px;">#</td>
			<td class="divider"></td>
			<td>{$lang.listing}</td>
			<td class="divider"></td>
			{if $aHooks.ref == 1}
			<td style="width: 80px;">{$lang.booking_ref_number}</td>
			<td class="divider"></td>
			{/if}
			<td style="width: 100px;">{$lang.booking_author}</td>
			<td class="divider"></td>
			<td style="width: 70px;">{$lang.status}</td>
			<td class="divider"></td>
			<td style="width: 65px;">{$lang.actions}</td>
		</tr>
		{foreach from=$requests item='request' name='requestsF' key='rKey'}
		{assign var='status_key' value=$request.status}
		<tr class="body" id="item_request_{$request.ID}">
			<td class="no_padding" align="center"><span class="text">{$smarty.foreach.requestsF.iteration}</span></td>
			<td class="divider"></td>
			<td>
				{assign var='ltype_key' value='lt_'|cat:$request.ltype}
				<a href="{$rlBase}{if $config.mod_rewrite}{$pages.$ltype_key}/{$request.Path}/{str2path string=$request.title}-l{$request.Listing_ID}.html{else}?page={$pages.$ltype_key}&amp;id={$request.Listing_ID}{/if}" title="{$request.booking_page_details}">{$request.title}</a>
			</td>
			<td class="divider"></td>
			{if $aHooks.ref == 1}
			<td><span class="text">{$request.ref}</span></td>
			<td class="divider"></td>
			{/if}
			<td><span class="text">{$request.Author}</span></td>
			<td class="divider"></td>
			<td id="status_{$request.ID}"><span class="{if $request.status == 'process'}active{elseif $request.status == 'refused'}red{else}deactive{/if}">{if $request.status == 'process'}{$lang.new}{elseif $request.status == 'booked'}{$lang.booking_legend_booked}{else}{$lang.booking_refused}{/if}</span></td>
			<td class="divider"></td>
			<td>
				<img class="search" onclick="location.href='{$rlBase}{if $config.mod_rewrite}{$pages.booking_requests}/{str2path string=$request.title}-r{$rKey}.html{else}?page={$pages.booking_requests}&amp;id={$rKey}{/if}';" title="{$lang.booking_page_details}" alt="" src="{$rlTplBase}img/blank.gif" />
				<img class="del" onclick="rlConfirm( '{$lang.ext_booking_remove_notice}', 'xajax_deleteRequest', Array('{$request.ID}'), 'request_loading' );" alt="{$lang.delete}" title="{$lang.delete}" src="{$rlTplBase}img/blank.gif" />
			</td>
		</tr>
		{/foreach}
		</table>
	{/if}
</div>

<script type="text/javascript">
{literal}
	function removeRequest(id) {
		$('#item_request_'+ id).remove();

		if ( $('table#saved_search tr').length == 1 ) {
			var parent = $('table#saved_search').parent();
			$('table#saved_search').remove();
			$(parent).html('<div class="info">{/literal}{$lang.booking_no_requests}{literal}</div>');
		}
	}
{/literal}
</script>

{else}

<div class="highlight">
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='booking_details' name=$lang.booking_page_details}
	<table class="submit">
		{if $aHooks.ref == 1}
		<tr>
			<td class="name">{$lang.booking_ref_number}:</td>
			<td class="field">{$requests.ref_number}</td>
		</tr>
		{/if}
		<tr>
			<td class="name">{$lang.booking_checkin}:</td>
			<td class="field">{$requests.From|date_format:$smarty.const.RL_DATE_FORMAT}</td>
		</tr>
		<tr>
			<td class="name">{$lang.booking_checkout}:</td>
			<td class="field">{$requests.To|date_format:$smarty.const.RL_DATE_FORMAT}</td>
		</tr>
		<tr>
			<td class="name">{$lang.booking_req_status}:</td>
			<td class="field" id="owRes">{$requests.Stat}</td>
		</tr>
		<tr>
			<td class="name">{$lang.booking_amount}:</td>
			<td class="field">{$defPrice.currency} {str2money string=$requests.Amount}</td>
		</tr>
	</table>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}

	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_header.tpl' id='client_details' name=$lang.booking_client_details}
	<table class="submit">
		{foreach from=$requests.fields key='fKey' item='field'}
		{assign var='field_value' value=$field.value}
		{if $field.Type == 'bool'}
			{if $field.value == '1'}
				{assign var='field_value' value=$lang.yes}
			{else}
				{assign var='field_value' value=$lang.no}
			{/if}
		{/if}
		<tr>
			<td class="name">{$field.name}:</td>
			<td class="field">
				{if $field.Condition == 'isUrl'}
					<a class="static" href="{$field_value}" title="{$field_value}">{$field_value}</a>
				{else}
					{$field_value}
				{/if}
			</td>
		</tr>
		{/foreach}
	</table>
	{include file='blocks'|cat:$smarty.const.RL_DS|cat:'fieldset_footer.tpl'}

	{if $requests.Stat == $lang.booking_processed}
	<div id="owner_actions">
		<div id="asf_res">
			<input style="margin-top: 5px;" type="button" class="button" onclick="ownerResult('1', 'accept');" value="{$lang.booking_accept}" />
			<input style="margin-top: 5px;" type="button" class="button" onclick="ownerResult('1', 'refuse');" value="{$lang.booking_refuse}" />
		</div>

		<div id="refuse_ansfer" class="hide" style="margin-top: 10px;">
			<span class="blue_middle">{$lang.booking_request_ansfer_area}<span class="red">*</span></span><div class="clear"></div>
			<textarea rows="5" cols="" id="textarea_ansfer"></textarea>
			<div>
				<input style="margin-top: 5px;float: left;" type="button" id="accept_btn" class="button hide" onclick="ownerResult('2', 'accept');" value="{$lang.booking_accept}" />
				<input style="margin-top: 5px;float: left;" type="button" id="refuse_btn" class="button hide" onclick="ownerResult('2', 'refuse');" value="{$lang.booking_refuse}" />
				<input style="margin: 5px 0 0 4px;float: left;" type="button" class="button" onclick="ownerResult('cancel');" value="{$lang.booking_button_cancel}" />
				<div class="clear"></div>
				<div>
				</div>

				<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}jquery/jquery.textareaCounter.js"></script>
				<script type="text/javascript">
				var require_id = '{$require_id}';
				{literal}

				$(document).ready(function(){
					$('#textarea_ansfer').textareaCount({
						'maxCharacterSize': 255,
						'warningNumber': 20
					})
				});

				function ownerResult( step, ask )
				{
					if( step == '1' )
					{
						$('#asf_res, #accept_btn, #refuse_btn').slideUp('fast');
						$('#refuse_ansfer, #'+ask+'_btn').slideDown('fast');
					}
					else if( step == 'cancel' )
					{
						$('#asf_res').fadeIn('fast');
						$('#refuse_ansfer, #accept_btn, #refuse_btn').slideUp('fast');
					}
					else
					{
						xajax_ownerResult( require_id, ask, $('#textarea_ansfer').val() );
					}
				}
				{/literal}
				</script>
			</div>
			{/if}

		</div>
		{/if}
{else}
	<div class="highlight">
		<div class="info">{$lang.booking_no_requests}</div>
	</div>
{/if}

<!-- booking requests end -->
