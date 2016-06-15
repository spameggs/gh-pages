$(document).ready(function(){
	/* enable photo gallery */
	$('div#scroll ul li a').photoSwipe();
	
	/* thumbnails bar handler */
	var photosHandler = function(){
		var width = $('#scroll ul.inner').width(),
			sidePadding = 5,
			itemWidth = $('#scroll ul.inner li:first').width(),
			itemsCount = $('#scroll ul.inner li').length,
			itemsPerSlideTmp = Math.floor(width / itemWidth),
			itemsPerSlide = Math.floor(width / (itemWidth + (sidePadding * ((itemsPerSlideTmp - 1) / itemsPerSlideTmp)))),
			slides = Math.ceil(itemsCount / itemsPerSlide),
			sidePaddingNew = (width - (itemsPerSlide * itemWidth)) / (itemsPerSlide - 1),
			currentSlide = 0,
			setPadding = rlLangDir == 'ltr' ? 'paddingRight' : 'paddingLeft',
			plusSign = rlLangDir == 'ltr' ? '+' : '-',
			minusSign = rlLangDir == 'ltr' ? '-' : '+';
		
		/* reset position */
		$('div#scroll ul.inner').css({marginLeft: 0});
			
		/* the work width mutchs or wider then items slide bar, return */
		if ( width >= (itemsCount * (itemWidth + sidePadding) - sidePadding) )
		{
			/* reset padding */
			$('#scroll ul.inner li').css(setPadding, sidePadding+'px');	
			
			/* hide navigation */
			$('#thumbnails div.prev,#thumbnails div.next').hide();
			
			return;
		}
		
		/* show navigation */
		$('#thumbnails div.prev').hide();
		$('#thumbnails div.next').show();
		
		/* set new padding */
		$('#scroll ul.inner li').css(setPadding, sidePaddingNew+'px');
		
		/* slideRight */
		var slide = function(sign){
			if ( (currentSlide == 0 && sign == '-') || (currentSlide == (slides - 1) && sign == '+') )
				return;
			
			eval('currentSlide'+sign+sign);
			var position = ((width + sidePaddingNew) * currentSlide) * -1;
			
			if ( rlLangDir == 'ltr' )
			{
				$('div#scroll ul.inner').animate({
					marginLeft: position
				});
			}
			else
			{
				$('div#scroll ul.inner').animate({
					marginRight: position
				});
			}
			
			if ( currentSlide == 0 )
			{
				$('#thumbnails div.prev').hide();
				$('#thumbnails div.next').show();
			}
			else if ( currentSlide == (slides - 1) )
			{
				$('#thumbnails div.prev').show();
				$('#thumbnails div.next').hide();
			}
			else
			{
				$('#thumbnails div.prev').show();
				$('#thumbnails div.next').show();
			}
		}
		$('#thumbnails div.next').unbind('click').bind('click', function(){
			slide('+');
		});
		$('#scroll ul.inner').unbind('swipeleft').bind('swipeleft', function(){
			slide(plusSign);
		});
		
		/* slideLeft */
		$('#thumbnails div.prev').unbind('click').bind('click', function(){
			slide('-');
		});
		$('#scroll ul.inner').unbind('swiperight').bind('swiperight', function(){
			slide(minusSign);
		});
	}
	
	photosHandler();
	
	$(window).orientationchange(function(){
		photosHandler();
	});
});