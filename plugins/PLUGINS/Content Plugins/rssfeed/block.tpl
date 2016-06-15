<!-- rss feed block tpl -->

<div id="{$block.Key}_box">{$lang.loading}</div>
<script type="text/javascript">//<![CDATA[
{literal}
$(function(){
	{/literal}
	var rssfeed_url = '{$url}';
	var rssfeed_number = '{$number}';
	var rss_request = '{$smarty.const.RL_PLUGINS_URL}rssfeed/request.php';
	var rss_static = '{$smarty.const.RL_PLUGINS_URL}rssfeed/static/';
	var rss_not_found = '{$lang.rssfeed_not_found}';
	var rss_open_in_new_window = '{$lang.rssfeed_not_found}';
	var rss_obj = '{$block.Key}_box';
	{literal}
	
	if ( rss_request && rssfeed_number )
	{
		$.getJSON(rss_request, {number: rssfeed_number, url: rssfeed_url}, function(response){
			if ( response && response.total > 0 )
			{
				var html = '<ul class="hide">';
				for (var i=1; i<=response.total; i++ )
				{
					var style = i != response.total ? 'padding: 0 0 10px;' : '';
					html += '<li style="'+ style +'"><a title="'+ response.data[i].title +'" href="'+ response.data[i].link +'">'+ response.data[i].title +'</a> <a target="_blank" title="'+ rss_open_in_new_window +'" href="'+ response.data[i].link +'"><img style="width: 10px;height: 14px;background: url('+ rss_static +'gallery.png) 0 0 no-repeat;" src="'+ rlConfig['tpl_base'] +'img/blank.gif" alt="'+ rss_open_in_new_window +'" /></a></li>';
				}
				html += '</ul>';
				$('#'+rss_obj).html(html);
				$('#'+rss_obj).find('ul').fadeIn();
			}
			else
			{
				$('#'+rss_obj).html(rss_not_found);
			}
		});
	}
	else
	{
		$('#'+rss_obj).html(rss_not_found);
	}
})

{/literal}
//]]>
</script>

<!-- rss feed block tpl end -->