<!-- testimonial item -->
<div class="item{if $text_dir == 'right'} t-rtl{/if}">
	<div class="area hlight">
		<div class="quotes"></div>
		{$testimonial.Testimonial|regex_replace:'/(https?\:\/\/[^\s]+)/':'<a href="$1">$1</a>'}
	</div>
	<div class="bottom">
		<div class="triangle"></div>
		<span class="raed-more">
			<span class="dark_12">{$testimonial.Date|date_format:'%d %b.'}</span>
		</span>
		<span class="author" title="{$lang.testimonials_author}: {$testimonial.Author}">{$testimonial.Author}</span>
	</div>
</div>
<!-- testimonial item end -->