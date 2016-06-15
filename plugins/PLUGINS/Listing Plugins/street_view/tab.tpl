<!-- street view tab content -->

<div id="area_streetView" class="tab_area hide">
	<div class="highlight">

	{assign var='replace' value=`$smarty.ldelim`address`$smarty.rdelim`}
	{assign var='no_loc_phrase' value=$lang.street_view_no_location|replace:$replace:$location.search}

	{if $location.direct || $location.search}
	
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false{if $smarty.const.RL_LANG_CODE != '' && $smarty.const.RL_LANG_CODE != 'en'}&amp;language={$smarty.const.RL_LANG_CODE}{/if}"></script>
		<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}street_view/static/lib.js"></script>
		<script type="text/javascript">
		var stree_view_mode = "{if $location.direct}direct{else}geocoder{/if}";
		var stree_view_point = "{if $location.direct}{$location.direct}{else}{$location.search}{/if}";
		{literal}
		
		$(document).ready(function(){
			var street_view_map = false;
			$(document).ready(function(){
				$('div.tabs li').click(function(){
					if ( !street_view_map && $(this).attr('id') == 'tab_streetView' ) {
						streetViewInit(stree_view_mode, stree_view_point);
						street_view_map = true;
					}
				});
			});
		});
		
		{/literal}
		</script>
		
		<div id="street_view" style="width: {if empty($config.map_width)}100%{else}{$config.map_width}px{/if}; height: {if empty($config.map_height)}300px{else}{$config.map_height}px{/if}"></div>
		<div id="no_street_view" class="hide info">{$no_loc_phrase}</div>
		
	{else}
	
		<div class="info">{$no_loc_phrase}</div>
	
	{/if}
	
	</div>
</div>
<!-- street view tab content end -->