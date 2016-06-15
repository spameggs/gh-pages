/*
 * jQuery File Upload Plugin JS Example 6.11
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */
$(function () {
	'use strict';

	var test_convas = document.createElement('canvas');
	var client_resize = test_convas.getContext ? true : false;
	var resize_width = photo_orig_width;
	var resize_height = photo_orig_height;
		
	if ( client_resize )
	{
		if ( photo_user_crop )
		{
			resize_width *= 2;
			resize_height *= 2;
		}
		
		photo_max_size = photo_client_max_size;
		var set_max_size = photo_client_max_size/1024/1024;
		$('#fileupload').prev().find('b').html(set_max_size+' MB');
	}
	
	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: $('#fileupload form').attr('action')
	});

	// Enable iframe cross-domain access via redirect option:
//	$('#fileupload').fileupload(
//		'option',
//		'redirect',
//		window.location.href.replace(
//			/\/[^\/]*$/,
//			'/cors/result.html?%s'
//		)
//	);

	 $('#fileupload').fileupload('option', {
		maxFileSize: photo_max_size,
		maxNumberOfFiles: photo_allowed,
		acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
		process: [
			{
				action: 'load',
				fileTypes: /^image\/(gif|jpeg|png)$/,
				maxFileSize: photo_max_size
			},
			{
				action: 'resize',
				maxWidth: resize_width,
				maxHeight: resize_height
			},
			{
				action: 'save'
			}
		]
	});

	// Load existing files:
	$.ajax({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: $('#fileupload').fileupload('option', 'url'),
		dataType: 'json',
		context: $('#fileupload')[0]
	}).done(function (result) {
		if (result && result.length)
		{
			$(this).fileupload('option', 'done').call(this, null, {result: result});
			managePhotoDesc();
			crop_handler();
		}
	});

});

var submit_photo_step = function(){
	/* check for not uploaded photos */
	var not_saved = $('#fileupload span.template-upload').length;
	if ( not_saved > 0 )
	{
		$('#fileupload span.template-upload').addClass('suspended');
		printMessage('warning', lang['unsaved_photos_notice'].replace('{number}', not_saved));
		
		return false;
	}
	else
	{
		return true;
	}
};

var managePhotoDesc = function(){
	$('#fileupload div.photo_navbar img.edit')
		.unbind('click')
		.click(function(){
			var parent = $(this).parent();
			var id = $(parent).attr('id');
			$(parent).find('span.primary, img.edit, img.crop').hide();
			$(parent).find('input').show();
	});
	
	$("#fileupload span.files").sortable({
		items: 'span.item:not(.template-upload)',
		placeholder: 'hover',
		handle: 'img.thumbnail',
		start: function(event, obj){
			$(obj.item).find('div.photo_navbar').hide();
		},
		stop: function(event, obj){
			$(obj.item).find('div.photo_navbar').show();
			/* save sorting */
			var sort = '';
			var count = 0;
			$('#fileupload span.files span.template-download div.photo_navbar').each(function(){
				var id = $(this).attr('id').split('_')[1];
				count++;
				var pos = $('#fileupload span.files span.item').index($(this).parent())+1;
				sort += id+','+pos+';';
			});
			
			if ( sort.length > 0 && count > 1 && sort_save != sort )
			{
				sort_save = sort;
				sort = rtrim(sort, ';');
				xajax_reorderPhoto(photo_listing_id, sort);
			}
		}
	});
};