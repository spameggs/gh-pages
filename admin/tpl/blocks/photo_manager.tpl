<!-- photos manager -->

<div class="dark">{$lang.max_file_size_caption} <b>{$max_file_size} MB</b></div>
				
{assign var='width' value=$config.pg_upload_thumbnail_width+4}
{assign var='height' value=$config.pg_upload_thumbnail_height-50+4}

<div id="fileupload">
	<form action="{$smarty.const.RL_LIBS_URL}upload/admin.php" method="post" enctype="multipart/form-data">
		<span class="files canvas"></span>
		<span title="{$lang.add_photo}" class="draft fileinput-button">
			{$lang.add_photo}
			{assign var='replace' value=`$smarty.ldelim`count`$smarty.rdelim`}
			{if $allowed_photos}<span class="allowed">{$lang.allowed_count|replace:$replace:$allowed_photos}</span>{/if}
			<input type="file" name="files[]" multiple />
		</span>

		<div><input type="button" class="start" value="{$lang.upload}" /></div>
	</form>
</div>

<style type="text/css">
div#fileupload span.hover
{literal}{{/literal}
	width: {if $config.pg_upload_thumbnail_width}{$config.pg_upload_thumbnail_width}{else}120{/if}px;
	height: {if $config.pg_upload_thumbnail_height}{$config.pg_upload_thumbnail_height}{else}90{/if}px;
{literal}}{/literal}
</style>

{literal}
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
	<span {/literal}style="height: {$config.pg_upload_thumbnail_height+30}px;"{literal} class="template-upload fade item active">
		<span class="preview"><span class="fade"></span></span><span class="start"></span>
		<img src="{/literal}{$rlTplBase}{literal}img/blank.gif" class="cancel" alt="{/literal}{$lang.delete}{literal}" title="{/literal}{$lang.delete}{literal}" />
		<span class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></span>
		<div class="photo_navbar"></div>
	</span>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
	<span {/literal}style="height: {$config.pg_upload_thumbnail_height+30}px;"{literal} class="template-download fade item active">
		<img class="thumbnail" src="{%=file.thumbnail_url%}" />
		<img data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}" src="{/literal}{$rlTplBase}{literal}img/blank.gif" class="delete" alt="{/literal}{$lang.delete}{literal}" title="{/literal}{$lang.delete}{literal}" />
		<img src="{/literal}{$rlTplBase}{literal}img/blank.gif" alt="" class="loaded" />
		<div class="photo_navbar" id="navbar_{%=file.id%}">
			<span class="primary">
				<span class="dark_12{% if (!file.primary) { %} hide{% } %}">{/literal}<b>{$lang.primary}</b>{literal}</span>
				<a class="brown_12{% if (file.primary) { %} hide{% } %}" onclick="xajax_makeMain({%=file.listing_id%}, {%=file.id%})" href="javascript:void(0)" title="{/literal}{$lang.set_primary}{literal}">{/literal}{$lang.set_primary}{literal}</a>
			</span>
			<input class="hide" type="text" name="description" value="{%=file.description%}" />
			<input onclick="xajax_editDesc({%=file.id%}, $(this).prev().val())" class="accept hide" type="button" name="accept" />
			{% if ( file.is_crop ) { %}<img id="crop_photo_{%=file.id%}" dir="{%=file.original%}" title="{/literal}{$lang.crop_photo}{literal}" src="{/literal}{$rlTplBase}{literal}img/blank.gif" class="crop" alt="" />{% } %}
			<img title="{/literal}{$lang.manage_description}{literal}" src="{/literal}{$rlTplBase}{literal}img/blank.gif" class="edit" alt="" />
		</div>
	</span>
{% } %}
</script>
{/literal}

<script type="text/javascript">
var photo_allowed = {if $plan_info.Image_unlim}undefined{else}{if $plan_info.Image}{$plan_info.Image}{else}0{/if}{/if};
var photo_client_max_size = 10*1024*1024;
var photo_max_size = {if $max_file_size}{$max_file_size|regex_replace:'/[\D]/':''}{else}2{/if}*1024*1024;
var photo_width = {if $config.pg_upload_thumbnail_width}{$config.pg_upload_thumbnail_width}{else}120{/if};
var photo_height = {if $config.pg_upload_thumbnail_height}{$config.pg_upload_thumbnail_height}{else}90{/if};
var photo_orig_width = {if $config.pg_upload_large_width}{$config.pg_upload_large_width}{else}800{/if};
var photo_orig_height = {if $config.pg_upload_large_height}{$config.pg_upload_large_height}{else}600{/if};
var photo_auto_upload = {if $config.img_auto_upload}true{else}false{/if};
var photo_listing_id = {if $listing.ID}{$listing.ID}{else}false{/if};
var photo_user_crop = {if $config.img_crop_interface}true{else}false{/if};
var sort_save = false;
lang['error_maxFileSize'] = "{$lang.error_maxFileSize}";
lang['error_acceptFileTypes'] = "{$lang.error_acceptFileTypes}";
lang['uploading_completed'] = "{$lang.uploading_completed}";
lang['upload'] = "{$lang.upload}";
lang['picture_preparing'] = "{$lang.picture_preparing}";
lang['upload_file'] = "{if $lang.upload_file}{$lang.upload_file}{else}File:{/if}";
lang['upload_no_preview_available'] = "{if $lang.upload_no_preview_available}{$lang.upload_no_preview_available}{else}No preview available<br /> in IE browsers{/if}";
var ph_empty_error = "{$lang.crop_empty_coords}";
var ph_too_small_error = "{$lang.crop_too_small}";
</script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/jquery.ui.widget.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/tmpl.min.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/load-image.min.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/canvas-to-blob.min.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/bootstrap.min.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/jquery.fileupload.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/jquery.fileupload-fp.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/jquery.fileupload-ui.js"></script>
<script type="text/javascript" src="{$smarty.const.RL_LIBS_URL}upload/main.js"></script>

<!-- photos manager end -->