<script type="text/javascript">
var dom_block = '<div class="fieldset"id="fs_coupon"><table class="fieldset_header"><tr><td class="caption">{$lang.coupon_code}</td><td class="line">&nbsp;</td><td class="arrow"></td></tr></table><div class="body"><div><div id="coupon_code"><input class="text w150" id="coupon_code_name" name="coupon_code" value="{$smarty.post.coupon_code}" type="text" maxlength="20" size="20" onkeydown="javascript:if(13==event.keyCode){literal}{{/literal}return false;{literal}}{/literal}" /> <input class="low" id="check_coupon" type="button" style="margin: 0 5px;" value="{$lang.apply}"></div><div id="coupon_code_info"></div></div></div></div>';
var plan_id = '{$plan_info.ID}';
var renew = '{$smarty.get.renew}';
{literal}	
	$(document).ready(function() {
		$('ul#payment_gateways').after(dom_block);
		$('#check_coupon').click(function() {
			plan_id = $("input[name='plan']:checked").val() ? $("input[name='plan']:checked").val() : plan_id ;
			xajax_checkCouponCode($('#coupon_code_name').val(), plan_id, '', renew);$(this).val('Loading...');$('#coupon_code_info').hide();
		});
		$('.plans li:not(.active)').click(function() {		
			xajax_checkCouponCode('', '', 'remove', renew);$(this).val('Loading...');
		});
		
		$('#coupon_code_name').keydown(function(event){
			if(event.keyCode == 13)
			{
				xajax_checkCouponCode($('#coupon_code_name').val(), plan_id, '', renew);$('#check_coupon').val('Loading...');$('#coupon_code_info').hide();
			}
		});
		$('#checkout_submit').click(function(){
			$(this).closest('form').submit();
		});
	});
	function diffuse()
	{
		xajax_checkCouponCode('', '', 'remove', renew);$(this).val('Loading...');
	}
{/literal}
</script>