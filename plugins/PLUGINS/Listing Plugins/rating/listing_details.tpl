<!-- listing ration block -->
<li id="listing_rating_dom">
	{include file=$smarty.const.RL_PLUGINS|cat:'rating'|cat:$smarty.const.RL_DS|cat:'dom.tpl'}
	{if !$rating_denied && (($config.rating_prevent_visitor && $isLogin) || !$config.rating_prevent_visitor) && (!$config.rating_prevent_owner || ($config.rating_prevent_owner && $listing_data.Account_ID != $account_info.ID))}
		<script type="text/javascript">
		var rating_listing_id = {$listing_data.ID};
		{literal}
		$(document).ready(function(){
			$('ul.lising_rating_ul li').mouseenter(function(){
				var index = $('ul.lising_rating_ul li').index(this) + 1;
				for(var i = 0; i < index; i++)
				{
					$('ul.lising_rating_ul li:eq('+i+')').addClass('hover');
					if ( $('ul.lising_rating_ul li:eq('+i+') div').length > 0 )
					{
						$('ul.lising_rating_ul li div').hide();
					}
				}
			}).mouseleave(function(){
				$('ul.lising_rating_ul li').removeClass('hover');
				$('ul.lising_rating_ul li div').show();
			}).click(function(){
				var stars = $('ul.lising_rating_ul li').index(this) + 1;
				xajax_rate(rating_listing_id, stars);
			});
		});
		{/literal}
		</script>
	{/if}
</li>
<!-- listing ration block end -->