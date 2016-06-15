<!-- testimonials box -->
<div class="testimonials{if $text_dir == 'right'} t-rtl{/if}">
	{if !$block.Tpl}
		<div class="side_block">
			<div class="header"><p class="dark">{$block.name}</p> <a class="button add-testimonial" title="{$lang.testimonials_add_testimonial}" href="{$rlBase}{if $config.mod_rewrite}{$pages.testimonials}.html#add-testimonial{else}?page={$pages.testimonials}#add-testimonial{/if}"><span></span></a></div>
	{/if}
	{if $testimonial_box}
		<div>
			<div class="area hlight">
				<div class="quotes"></div>
				{$testimonial_box.Testimonial|truncate:320:'...':false|nl2br|regex_replace:'/(https?\:\/\/[^\s]+)/':'<a href="$1">$1</a>'}
			</div>
			<div class="bottom">
				{if !$block.Tpl}
					<div class="triangle"></div>
				{/if}
				<span class="raed-more">
					<a title="{$lang.testimonials_read_more_title}" href="{$rlBase}{if $config.mod_rewrite}{$pages.testimonials}.html{else}?page={$pages.testimonials}{/if}">{$lang.testimonials_read_more}</a>
				</span>
				<span class="author" title="{$lang.testimonials_author}: {$testimonial_box.Author}">{$testimonial_box.Author}</span>
			</div>
		</div>
	{else}
		<div class="info">{$lang.testimonials_no_testimonials}</div>
	{/if}
	{if !$block.Tpl}
		</div>	
	{/if}
</div>
<script type="text/javascript">
{literal}
$(function(){
	var color = $('.testimonials div.hlight').css('background-color');
	$('.testimonials div.triangle').css('border-{/literal}{if $text_dir == 'right'}top{else}right{/if}{literal}-color', color);
});
{/literal}
</script>
<!-- testimonials box end -->