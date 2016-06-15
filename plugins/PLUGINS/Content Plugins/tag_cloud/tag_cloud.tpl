<!-- tags page tpl -->

{if !$tag_cloud}
	<span class="info">{$lang.tc_cloud_empty}</span>
{elseif $config.tc_box_type == 'simple'}

	<div id="cloud">
		{foreach from=$tag_cloud item='tag'}
			<a {if $config.tc_tag_new_page}target="_blank"{/if} href="{$smarty.const.SEO_BASE}{if $config.mod_rewrite}{$pages.tags}/{$tag.Path}{if $config.tc_urls_postfix}.html{else}/{/if}{else}?page={$pages.tags}&tag={$tag.Path}{/if}" style="font-size:{$tag.Size}px;">{$tag.Tag}</a>
		{/foreach}
	</div>

{elseif $config.tc_box_type == 'gradient'}
	<div id="cloud">
		{foreach from=$tag_cloud item='tag'}
			<a {if $config.tc_tag_new_page}target="_blank"{/if} href="{$smarty.const.SEO_BASE}{if $config.mod_rewrite}{$pages.tags}/{$tag.Path}{if $config.tc_urls_postfix}.html{else}/{/if}{else}?page={$pages.tags}&tag={$tag.Path}{/if}" rel="{$tag.Count}">{$tag.Tag}</a>
		{/foreach}
	</div>

	<script type="text/javascript">
		{literal}
			$.fn.tagcloud.defaults = {
				size: {start: {/literal}{$config.tc_minsize}, end: {$config.tc_maxsize}{literal}, unit: 'px'},
				color: {start: {/literal}'{$config.tc_jquery_gradient_start}', end: '{$config.tc_jquery_gradient_end}'{literal}}
			};

			$(document).ready(function(){
				$('#cloud a').tagcloud();
			});	
		{/literal}
	</script>

{elseif $config.tc_box_type == 'circle'}
	<script type="text/javascript">
		var word_list = [
			{foreach from=$tag_cloud item='tag' name="tagsLoop"}
			{if $config.mod_rewrite}
				{assign var="tag_link" value=$smarty.const.SEO_BASE|cat:$pages.tags|cat:"/"|cat:$tag.Path}
			{else}
				{assign var="tag_link" value=$smarty.const.SEO_BASE|cat:"?page="|cat:$pages.tags|cat:"&tag="|cat:$tag.Path}
			{/if}

			{literal}
				{text: "{/literal}{$tag.Tag}", weight: {$tag.Size}, count: {$tag.Count}{literal},
					html: {title: "{/literal}{$tag.Tag}{literal}"/*, "class": "custom-class"*/},
					link: {href: "{/literal}{$tag_link}{if $config.mod_rewrite}{if $config.tc_urls_postfix}.html{else}/{/if}{/if}", {if $config.tc_tag_new_page}target: "_blank"{/if}
				{literal}}}
			{/literal}
			{if !$smarty.foreach.tagsLoop.last},{/if}
			{/foreach}
		];
		{literal}
		$(document).ready(function(){
			$("#cloud").jQCloud( word_list, {
				width: $('#cloud').width(),
				height: '{/literal}{$config.tc_jquery_circle_height}{literal}',
				size: {start: {/literal}{$config.tc_minsize}, end: {$config.tc_maxsize}{literal}},
				color: {start: {/literal}'{$config.tc_jquery_gradient_start}', end: '{$config.tc_jquery_gradient_end}'}{literal}
			});
		});
		{/literal}
	</script>

	<div id="cloud">&nbsp;</div>
{/if}

<!-- tags page tpl -->
