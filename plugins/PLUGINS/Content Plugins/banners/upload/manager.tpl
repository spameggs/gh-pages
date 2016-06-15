<!-- banners upload manager -->

<div class="dark">{$lang.max_file_size_caption} <b>{$max_file_size} MB</b></div>
<div id="fileupload">
	<form onsubmit="return false;" action="{$smarty.const.RL_PLUGINS_URL}banners/upload/admin.php" method="post" encoding="multipart/form-data" enctype="multipart/form-data">
		<span class="files canvas"></span>
		<span title="{$lang.add_photo}" class="draft fileinput-button">
			<span id="size-notice"><b>{$sBox.width}</b> x <b>{$sBox.height}</b></span>
			<input type="file" name="files" style="height:{$sBox.height}px;" />
			<input type="hidden" name="box_width" value="{$sBox.width}" />
			<input type="hidden" name="box_height" value="{$sBox.height}" />
		</span>
	</form>
</div>

{if $new_jquery_version}
{literal}
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
	<span {/literal}style="height: {$sBox.height}px;"{literal} class="template-upload fade item active">
		<span class="preview"><span class="fade"></span></span><span class="start"></span>
		<img src="{/literal}{$rlTplBase}{literal}img/blank.gif" class="cancel" alt="{/literal}{$lang.delete}{literal}" title="{/literal}{$lang.delete}{literal}" />
		<span class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></span>
	</span>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
	<span {/literal}style="height: {$sBox.height}px;"{literal} class="template-download fade item active">
		<img class="thumbnail" src="{%=file.thumbnail_url%}" />
		<img data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}" src="{/literal}{$rlTplBase}{literal}img/blank.gif" class="delete" alt="{/literal}{$lang.delete}{literal}" title="{/literal}{$lang.delete}{literal}" />
		<img src="{/literal}{$rlTplBase}{literal}img/blank.gif" alt="" class="loaded" />
	</span>
{% } %}
</script>
{/literal}

{else}

{literal}
<script id="template-upload" type="text/x-jquery-tmpl">
	<span class="item active template-upload">
		<span class="preview"></span><span class="start"></span>
		<img src="{/literal}{$rlTplBase}{literal}img/blank.gif" class="cancel" alt="{/literal}{$lang.delete}{literal}" title="{/literal}{$lang.delete}{literal}" />
		<span class="progress"></span>
	</span>
</script>
<script id="template-download" type="text/x-jquery-tmpl">
	<span class="item active template-download">
		<img class="thumbnail" src="${thumbnail_url}" />
		<img data-type="${delete_type}" data-url="${delete_url}" src="{/literal}{$rlTplBase}{literal}img/blank.gif" class="delete" alt="{/literal}{$lang.delete}{literal}" title="{/literal}{$lang.delete}{literal}" />
		<img src="{/literal}{$rlTplBase}{literal}img/blank.gif" alt="" class="loaded" />
	</span>
</script>
{/literal}
{/if}

<style type="text/css" title="banners">
div#fileupload span.progress
{literal}{{/literal}
	margin: 0;
{literal}}{/literal}

div#fileupload span.hover
{literal}{{/literal}
	width: {$sBox.width}px;
	height: {$sBox.height}px;
{literal}}{/literal}

div#fileupload span.draft
{literal}{{/literal}
	width: {$sBox.width}px;
	height: {$sBox.height}px;
	line-height: {$sBox.height}px;
	padding: 0;
	margin: 0 10px 5px 0;
	background: #F3F3F3;
{literal}}{/literal}

canvas.new, img.thumbnail
{literal}{{/literal}
	width: {$sBox.width}px;
	height: {$sBox.height}px;
{literal}}{/literal}

div#fileupload span.active, div#fileupload span.hover
{literal}{{/literal}
	width: {$sBox.width+4}px;
	height: {$sBox.height}px;
{literal}}{/literal}

div#fileupload img.loaded
{literal}{{/literal}
	margin: 0 4px 4px;
{literal}}{/literal}
</style>

<script type="text/javascript">
var new_jquery_version = {if $new_jquery_version}true{else}false{/if};
var photo_allowed = 1;
var photo_width, photo_orig_width = {$sBox.width};
var photo_height, photo_orig_height = {$sBox.height};
var photo_max_size, photo_client_max_size = {if $max_file_size}{$max_file_size}{else}2{/if}*1024*1024;
var photo_auto_upload = true;
var client_resize, photo_user_crop = false;

lang['error_maxFileSize'] = "{$lang.error_maxFileSize}";
lang['error_acceptFileTypes'] = "{$lang.error_acceptFileTypes}";
lang['uploading_completed'] = "{$lang.uploading_completed}";
lang['upload'] = "{$lang.upload}";

var ph_empty_error = "{$lang.crop_empty_coords}";
var ph_too_small_error = "{$lang.crop_too_small}";

{literal}
var managePhotoDesc = function() {}
var crop_handler = function() {}

$(document).ready(function(){
	$('#fileupload').fileupload({
		url: rlPlugins +'banners/upload/admin.php',
		maxNumberOfFiles: photo_allowed,
		autoUpload: true
	}).removeClass('ui-widget');

	if ( banner_current_type == 'image' ) {
		$.getJSON(rlPlugins +'banners/upload/admin.php', function (files) {
			if ( new_jquery_version ) {
				$('#fileupload').fileupload('option', 'done').call($('#fileupload'), null, {result: files});
			}
			else {
				var fu = $('#fileupload').data('fileupload');
				fu._adjustMaxNumberOfFiles(-files.length);
				fu._renderDownload(files)
					.appendTo($('#fileupload .files'))
					.fadeIn(function () {
						$(this).show();
				});
			}
		});
	}
});
{/literal}
</script>

{if $new_jquery_version}
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}banners/static/tmpl.min.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}banners/static/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}banners/static/jquery.fileupload.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_PLUGINS_URL}banners/static/jquery.fileupload-ui.js"></script>
{else}
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/jquery.fileupload.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/jquery.fileupload-ui.js"></script>
{/if}

<!-- banners upload manager end -->