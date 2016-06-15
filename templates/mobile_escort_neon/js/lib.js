$(document).ready(function(){
	//navigator.geolocation.getCurrentPosition(foundLocation, noLocation);
});

var glCoords = new Array();
var glAddress = new Array();
var glAddressString = '';
var foundLocation = function(position){
	if ( position.coords.latitude && position.coords.longitude )
	{
		glCoords['latitude'] = position.coords.latitude;
		glCoords['longitude'] = position.coords.longitude;
	}
	//alert(glCoords['latitude'] + ',' +glCoords['longitude']);
	if ( position.address )
	{
		glAddress['country'] = position.address.country;
		glAddress['countryCode'] = position.address.countryCode;
		glAddress['region'] = position.address.region;
		glAddress['city'] = position.address.city;
		glAddress['street'] = position.address.street;
		glAddress['streetNumber'] = position.address.streetNumber;
		
		for ( var i in glAddress )
		{
			if ( glAddress[i] )
			{
				glAddressString += glAddress[i]+', ';
			}
		}
		
		glAddressString = glAddressString.substr(0, glAddressString.length-2);
		//alert(glAddressString)
	}
}
var noLocation = function(){
	alert('netu')
}
/**
*
* cookies functions
* 
**/
function createCookie( name, value, days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else
	{
		var expires = "";
	}
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	
	for(var i=0;i < ca.length;i++)
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name)
{
	createCookie(name,"",-1);
}

/**
*
* languages selector handler
* 
**/
$(document).ready(function(){
	$('#languages > div > div > span').click(function(){
		$('#languages > div > ul').toggle();
	});
	
	$(document).click(function(event){
		var close = true;
		
		$(event.target).parents().each(function(){
			if ( $(this).attr('id') == 'languages' )
			{
				close = false;
			}
		});
		
		if ( close )
		{
			$('#languages > div > ul').hide();
		}
	});
});

/**
*
* languages selector handler
* 
**/
var main_menu = function(){
	/* clear */
	$('#main_menu_more li').remove();
	$('#main_menu li:not(:visible)').show();
	$('#main_menu li.last').removeClass('last').width('auto').find('a span.center').width('auto');
	
	/* build menu */
	var width = $('#main_menu').width(),
		buttonWidth = $('#main_menu li.more').width(),
		workWidth = width - buttonWidth,
		countWidth = 0,
		countItems = $('#main_menu li:not(:last)').length,
		border = false,
		effected = false;
		
	$('#main_menu li:not(:last)').each(function(index){
		countWidth += $(this).width();
		index++;
		
		var rest = countItems != index ? 80 : 0;
		
		if ( workWidth - countWidth < rest )
		{
			effected = true;
			
			if ( !border && countItems != index )
			{
				var newWidth = workWidth - (countWidth - $(this).width());
				$(this).width(newWidth).addClass('last');
				$(this).find('a span.center').width(newWidth - 30); //28 is padding, 2 is borders
				border = true;
			}
			else
			{
				$('#main_menu_more').append('<li></li>');
				$('#main_menu_more li:last').html($(this).find('a').parent().html());
				$(this).hide();
			}
		}
		
		if ( effected )
		{
			$('#main_menu li.more').show();
		}
		else
		{
			$('#main_menu li.more').hide();
		}
	});
	
	if ( !$('#main_menu_more li').length )
	{
		$('#main_menu_more').hide();
		$('#main_menu li.more').removeClass('more_active');
	}
}

$(document).ready(function(){
	main_menu();
	
	$(window).resize(function(){
		main_menu();
	});
	
	$('#main_menu li.more').click(function(){
		$(this).toggleClass('more_active');
		$('#main_menu_more').toggle();
	});
	
	/* scroll top */
	//$(window).scrollTop(0);
});

/**
*
* jQuery modal window plugin by Flynax 
*
**/
(function($){
	$.flModal = function(el, options){
		var base = this;
		var lock = false;
		var direct = false;
		
		// access to jQuery and DOM versions of element
		base.$el = $(el);
		base.el = el;
		
		base.objHeight = 0;
		base.objWidth = 0;
		base.sourceContent = false;

		// add a reverse reference to the DOM object
		base.$el.data("flModal", base);

		base.init = function(){
			base.options = $.extend({},$.flModal.defaultOptions, options);

			// initialize working object id
			if ( $(base.el).attr('id') )
			{
				base.options.id = $(base.el).attr('id');
			}
			else
			{
				$(base.el).attr('id', base.options.id);
			}
			
			// add mask on click
			if ( base.options.click )
			{
				base.$el.click(function(){
					base.mask();
					base.loadContent();
				});
			}
			else
			{
				base.mask();
				base.loadContent();
			}
		};

		base.mask = function(){
			var width = $(document).width();
			var height = $(document).height();
			
			var dom = '<div id="modal_mask"><div id="modal_block" class="modal_block"></div></div>';
			
			$('body').append(dom);
			$('#modal_mask').width(width);
			$('#modal_mask').height(height);
			$('#modal_block').width(base.options.width).height(base.options.height);
			
			// on resize document
			$(window).unbind('resize').resize(function(){
				base.resize();
			});
			
			if ( base.options.scroll )
			{
				$(window).unbind('scroll').scroll(function(){
					base.scroll();
				});
			}
		};
		
		base.resize = function(){
			if ( lock )
				return;

			var width = $(window).width();
			var height = $(document).height();
			$('#modal_mask').width(width);
			$('#modal_mask').height(height);
			
			var margin = ($(window).height()/2)-base.objHeight + $(window).scrollTop();
			$('#modal_block').stop().animate({marginTop: margin});
			
			var margin = base.objWidth * -1;
			$('#modal_block').stop().animate({marginLeft: margin});
		};
		
		base.scroll = function(){
			if ( lock )
				return;

			var margin = ($(window).height()/2)-base.objHeight + $(window).scrollTop();
			$('#modal_block').stop().animate({marginTop: margin});
		};
		
		base.loadContent = function(){
			/* load main block source */
			var dom = '<div class="inner"><div class="modal_content"></div><div class="close" title="'+lang['close']+'"></div></div>';
			$('div#modal_block').html(dom);
			
			var track_margin = base.options.height - 72;
			$('#modal_block div.small_track').css('margin-top', track_margin+'px');
			
			/* load content */
			var content = '';
			var caption_class = base.options.type ? ' '+base.options.type : '';
			base.options.caption = base.options.type && !base.options.caption ? lang[base.options.type] : base.options.caption;
			
			/* save source */
			if ( base.options.source )
			{
				if ( $(base.options.source + ' > div.tmp-dom').length > 0 )
				{
					base.sourceContent = $(base.options.source + ' > div.tmp-dom');
					direct = true;
				}
				else
				{
					base.sourceContent = $(base.options.source).html();
				}
			}
			
			/* build content */
			content = base.options.caption ? '<div class="caption'+caption_class+'">'+ base.options.caption + '</div>': '';
			content += base.options.content ? base.options.content : '';
			
			/* clear soruce objects to avoid id overload */
			if ( base.options.source && !direct )
			{
				$(base.options.source).html('');
				content += !base.options.content ? base.sourceContent : '';
			}
			
			$('div#modal_block div.inner div.modal_content').html(content);
			
			if ( base.options.source && direct )
			{
				$('div#modal_block div.inner div.modal_content').append(base.sourceContent);
			}
			
			if ( base.options.prompt )
			{
				var prompt = '<div class="prompt"><input name="ok" type="button" value="Ok" /><input name="close" type="button" value="'+lang['cancel']+'" /></div>';
				$('div#modal_block div.inner div.modal_content').append(prompt);
			}
			
			if ( base.options.ready )
			{
				base.options.ready();
			}
			
			$('#modal_block input[name=close]').click(function(){
				base.close();
			});
			
			if ( base.options.prompt )
			{
				$('#modal_block div.prompt input[name=close]').click(function(){
					base.close();
				});
				$('#modal_block div.prompt input[name=ok]').click(function(){
					var func = base.options.prompt;
					func += func.indexOf('(') < 0 ? '()' : '';
					eval(func);
					base.close();
				});
			}
			
			/* set initial sizes */
			base.objHeight = $('#modal_block').height()/2;
			base.objWidth = $('#modal_block').width()/2;
			
			var setTop = ($(window).height()/2) - base.objHeight + $(window).scrollTop();
			$('#modal_block').css('marginTop', setTop);
			var setLeft = base.objWidth * -1;
			$('#modal_block').css('marginLeft', setLeft);
			
			$('#modal_mask').click(function(e){
				if ( $(e.target).attr('id') == 'modal_mask' )
				{
					base.close();
				}
			});
			
			$('#modal_block div.close').click(function(){
				base.close();
			});
		};
		
		base.close = function(){
			lock = true;
			
			$('#modal_block').animate({opacity: 0});
			$('#modal_mask').animate({opacity: 0}, function(){
				$(this).remove();
				$('#modal_block').remove();
				
				if ( base.options.source )
				{
					$(base.options.source).append(base.sourceContent);
				}
				
				lock = false;
			});
		};
		
		// run initializer
		base.init();
	};

	$.flModal.defaultOptions = {
		scroll: true,
		type: false,
		width: 340,
		height: 230,
		source: false,
		content: false,
		caption: false,
		prompt: false,
		click: true,
		ready: false
	};

	$.fn.flModal = function(options){
		return this.each(function(){
			(new $.flModal(this, options));
		});
	};

})(jQuery);

/**
*
* favorites handler
* 
**/
$(document).ready(function(){
	flFavoritesHandler();
});

var flFavoritesHandler = function(){
	var ids = readCookie('favorites');
	
	if ( ids )
	{
		ids = ids.split(',');
		
		$('a.add_favorite').each(function(){
			var id = $(this).attr('id').split('_')[1];
			
			if ( ids && ids.indexOf(id) >= 0 )
			{
				$(this).addClass('remove_favorite');
				$(this).attr('title', lang['remove_from_favorites']);
			}
		});
	}
	
	$('a.add_favorite').unbind('click').click(function(){
		var id = $(this).attr('id').split('_')[1];
		var ids = readCookie('favorites');
		
		if ( ids )
		{
			ids = ids.split(',');
			
			if ( ids.indexOf(id) >= 0 )
			{
				ids.splice(ids.indexOf(id), 1);
				
				createCookie('favorites', ids.join(','), 93);
				
				$(this).removeClass('remove_favorite');
				$(this).attr('title', lang['add_to_favorites']);
				
				if ( rlPageInfo['key'] == 'my_favorites' )
				{
					var type = readCookie('grid_mode');
					var parent = $(this).closest('div.item');
					
					$(parent).fadeOut('normal', function(){
						$(this).remove();
						
						if ( $('#listings div.item').length < 1 )
						{
							if ( $('ul.paging').length > 0 )
							{
								var redirect = rlConfig['seo_url'];
								redirect += rlConfig['mod_rewrite'] ? rlPageInfo['path'] +'.html' : 'index.php?page='+ rlPageInfo['path'];
								location.href = redirect;
							}
							else
							{
								var div = '<div class="info">'+lang['no_favorite']+'</div>';
								$('div#controller_area').append(div);
								$('table.grid_navbar').remove();
							}
						}
					});
						
					$('#notice_message').html(lang['notice_removed_from_favorites']);
					$('#notice_obj').fadeIn();
				}
				
				return;
			}
			else
			{
				ids.push(id);
			}
		}
		else
		{
			ids = new Array();
			ids.push(id);
		}
		
		createCookie('favorites', ids.join(','), 93);
		
		$(this).addClass('remove_favorite');
		$(this).attr('title', lang['remove_from_favorites']);
	});
}

/**
*
* home page | favorites listings handler
* 
**/
var item_float = rlLangDir == 'rtl' ? 'right' : 'left';
var item_float_rev = rlLangDir == 'rtl' ? 'left' : 'right';

$(document).ready(function(){
	$(window).orientationchange(function(){
		carouselWidthHandler();
		scrollClick();
	});
	
	carouselWidthHandler();
	scrollClick();
});

var areaWidth = 0;
var carouselWidthHandler = function(){
	areaWidth = parseInt($('#width_tracker').width());
	
	areaWidth = areaWidth > 0 ? areaWidth: 320;
	areaWidth -= 20;
	
	$('div#carousel div.visible').width(areaWidth);
};

var scrollClick = function(){
	var obj = '#carousel div.visible';
	var count = $(obj).find('> ul > li').length;
	var itemWidth = $(obj).find('ul li:first').width();
	var visibleWidth = $(obj).width();
	var margin = 5;
	var poss = 0;
	var activeItem = 0;
	var visible = Math.floor(visibleWidth/itemWidth);
	var perSlide = visible;
	
	var diff = Math.ceil(visibleWidth - (visible*itemWidth));
	
	var newMargin = Math.ceil(diff / (visible > 1 ? visible - 1 : visible));
	
	if ( newMargin > margin )
	{
		$(obj).find('> ul li:not(:last)').css('margin-'+item_float_rev, newMargin+'px');
		margin = newMargin;	
	}
	
	/* back to 0 */
	if ( rlLangDir == 'rtl' )
	{
		$(obj).find('> ul').css({
			marginRight: 0
		});
	}
	else
	{
		$(obj).find('> ul').css({
			marginLeft: 0
		});
	}
	
	var scrollRight = function(){
		if ( (activeItem + visible) >= count )
		{
			return;
		}
		
		poss -= (itemWidth + margin) * perSlide;
		activeItem += perSlide;
		
		if ( rlLangDir == 'rtl' )
		{
			$(obj).find('> ul').animate({
				marginRight: poss
			});
		}
		else
		{
			$(obj).find('> ul').animate({
				marginLeft: poss
			});
		}
	};
	
	var scrollLeft = function(){
		if ( activeItem <= 0 )
		{
			return;
		}
		
		poss += (itemWidth + margin) * perSlide;
		activeItem -= perSlide;
		
		if ( rlLangDir == 'rtl' )
		{
			$(obj).find('> ul').animate({
				marginRight: poss
			});
		}
		else
		{
			$(obj).find('> ul').animate({
				marginLeft: poss
			});
		}
	}
	
	/* scroll right */
	$('.right_nav').unbind('click').click(function(){
		scrollRight();
	});
	
	$('#carousel div.visible').unbind('swipeleft').bind('swipeleft', function(){
		if ( rlLangDir == 'rtl' )
		{
			scrollLeft();
		}
		else
		{
			scrollRight();
		}
	});
	
	/* scroll left */
	$('.left_nav').unbind('click').click(function(){
		scrollLeft();
	});
	
	$('#carousel div.visible').unbind('swiperight').bind('swiperight', function(){
		if ( rlLangDir == 'rtl' )
		{
			scrollRight();
		}
		else
		{
			scrollLeft();
		}
	});
};

/**
*
* tabs click handler
*
* @param object obj - tab object referent
* 
**/
$(document).ready(function(){
	$('div.tabs li').click(function(){
		tabsSwitcher(this);
	});
});

var tabsSwitcher = function(obj){
	var key = $(obj).attr('id').split('_')[1];
	
	$('div.tab_area').hide();
	$('div.tabs li.active').removeClass('active');
	
	$(obj).addClass('active');
	$('div#area_'+key).show();
	
	$('#system_message>div').fadeOut();
};

/**
*
* prompt alert
*
* @param string message - prompt message text
* @param srting method  - javascript method (function)
* @param Array  params  - method (function) params
* @param string load_object  - load object ID
* 
**/
function rlConfirm( message, method, params, load_object )
{
	if (confirm(message))
	{
		var func = method+'('+params+')';
		
		eval(func);
		
		if ( load_object != '')
		{
			$('#'+load_object).fadeIn('normal');
		}
	}
}

/**
* notices/errors handler
*
* @param string type - message type: error, notice, warning
* @param string/array message - message text
* @param string/array fields - error fields names, array or through comma
*
**/
var printMessageTimer = false;
var printMessage = function(type, message, fields, direct){
	
	var types = new Array('error', 'notice', 'warning');
	var height = 0;
	
	if ( types.indexOf(type) < 0 )
		return;
		
	if ( typeof(message) == 'object' )
	{
		var tmp = '<ul>';
		for( var i=0; i<message.length; i++ )
		{
			tmp += '<li>'+message[i]+'</li>';
		}
		tmp += '</ul>';
		message = tmp;
	}
	
	$('input,select,textarea,table.error').removeClass('error');
	
	/* highlight error fields */
	if ( fields )
	{
		if ( typeof(fields) != 'object' )
		{
			fields = fields.split(',');
		}

		for ( var i = 0; i<fields.length; i++ )
		{
			if ( !fields[i] )
				continue;

			if ( trim(fields[i]) != '' )
			{
				if ( fields[i].charAt(0) == '#' )
				{
					$(fields[i]).addClass('error');
				}
				else
				{
					var selector = 'input[name^="'+fields[i]+'"]:last,select[name="'+fields[i]+'"],textarea[name="'+fields[i]+'"]';
					
					if ( $(selector).length > 0 && $(selector).attr('type') != 'radio' && $(selector).attr('type') != 'checkbox' )
					{
						$(selector).addClass('error');
					}
					else
					{
						if ( $(selector).attr('type') == 'radio' || $(selector).attr('type') == 'checkbox' )
						{
							$(selector).closest('table').addClass('error');
						}
						else
						{
							//$('input[name="'+fields[i]+'[1]"],select[name="'+fields[i]+'[1]"],textarea[name="'+fields[i]+'][1]"').parent().addClass('error');
						}
					}
				}	
			}
		}
	}
	
	/* print error in direct mode */
	if ( direct )
	{
		var html = ' \
			<div class="'+type+' hide"> \
				<div class="inner"> \
					<div class="icon"></div> \
					<div class="message">'+message+'</div> \
				</div> \
			</div> \
		';
		
		$('#system_message').html(html);
		$('#system_message div.'+type).fadeIn();
		
		$('input.error,select.error,textarea.error').focus(function(){
			$(this).removeClass('error');
		});
		$('table.error').click(function(){
			$(this).removeClass('error');
		});
		
		return;
	}
	
	/* print errors */
	if ( $('body>div.'+type).length > 0 )
	{
		$('body>div.'+type+' div.message').fadeOut(function(){
			$(this).html(message).fadeIn();
			height = $('body>div.'+type).height() * -1;
			
			clearTimeout(printMessageTimer);
			printMessageTimer = setTimeout('close()', 30000);
		});
	}
	else
	{
		$('body>div.error, body>div.notice, body>div.warning, #system_message>div.error, #system_message>div.notice, #system_message>div.warning').fadeOut('fast', function(){
			$(this).remove();
		});
		
		clearTimeout(printMessageTimer);
		
		var html = ' \
			<div class="'+type+'"> \
				<div class="inner"> \
					<div class="icon"></div> \
					<div class="message">'+message+'</div> \
					<div class="close" title="'+lang['close']+'"></div> \
				</div> \
			</div> \
		';
		
		$('body').prepend(html);
			height = $('body>div.'+type).height() * -1;
		$('body>div.'+type).css('margin-top', height).show().animate({marginTop: 0}, function(){
			printMessageTimer = setTimeout('close()', 30000);
		});
	}
	
	$('body>div.'+type).unbind('mouseenter').unbind('mouseleave').mouseenter(function(){
		clearTimeout(printMessageTimer);
	}).mouseleave(function(){
		printMessageTimer = setTimeout('close()', 30000);
	});
	
	/* close */
	$('body>div.'+type+' div.close').unbind('click').click(function(){
		close();
	});
	
	$('input.error,select.error,textarea.error').focus(function(){
		$(this).removeClass('error');
	});
	$('table.error').click(function(){
		$(this).removeClass('error');
	});
	
	this.close = function(){
		$('body>div.'+type).animate({marginTop: height}, 'fast', function(){
			$('body>div.'+type).remove();
		});
		clearTimeout(printMessageTimer);
	};
};

/**
*
* trim string
*
* @param string str - string for trim
* @param string chars - chars to be trimmed
*
* @return trimmed string
* 
**/
function trim(str, chars)
{
	return ltrim(rtrim(str, chars), chars);
}

/**
*
* left trim string
*
* @param string str - string for trim
* @param string chars - chars to be trimmed
*
* @return trimmed string
* 
**/
function ltrim(str, chars)
{
	if ( !str )
		return;
		
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}

/**
*
* right trim string
*
* @param string str - string for trim
* @param string chars - chars to be trimmed
*
* @return trimmed string
* 
**/
function rtrim(str, chars)
{
	if ( !str )
		return;
		
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

/**
*
* hide or show the object (via jQuery effect) by ID, and hide all objects by html path
*
* @param srting id - field id
* @param srting path - html path
* 
**/
function show( id, path )
{
	if (path != undefined)
	{
		$(path).slideUp('fast');
	}

	if ( $( '#'+id ).css('display') == 'block' )
	{
		$( '#'+id ).slideUp('normal');
	}
	else
	{
		$( '#'+id ).slideDown('slow');
	}
}

/**
*
* escape or replace quotes
*
* @param string str - string for replacing
* @param bool to - replace if true and escape if false
* 
**/
function quote( str, to )
{
	if (!to)
	{
		return str.replace(/'/g, "").replace(/"/g, "");
	}
	else
	{
		var to_single = '&rsquo;';
		var to_double = '&quot;';
		
		return str.replace(/'/g, to_single).replace(/"/g, to_double).replace(/\n/g, '<br />' );
	}
}
