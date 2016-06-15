<tr>
	<td class="name">{$lang.category_icon}</td>
	<td class="field">
    	<div id="category_icon_upload">
		    <table class="sTable">
		        <tr>
					<td>
						{assign var='click_dom' value='<a href="'|cat:$rlBase|cat:'index.php?controller=settings&amp;group='|cat:$group_id|cat:'">'|cat:$lang.here|cat:'</a>'}
						<input class="file" type="file" name="icon"/> &nbsp;| <small>{$lang.category_icon_notice|replace:'[width]':$config.categories_icons_width|replace:'[height]':$config.categories_icons_height|replace:'[here]':$click_dom}</small>
					</td>
				</tr>
			</table>
		</div>
		
		{if !empty($sPost.icon)}                                                    
			<div id="gallery">                                                         
				<div style="margin: 1px 0 4px 0;">
					<fieldset style="margin: 0 0 10px 0;">
						<legend id="legend_details" class="up" onclick="fieldset_action('details');">{$lang.current_icon}</legend>
						<div id="fileupload" class="ui-widget">
							<span class="item active template-download" style="width: {math equation="x + y" x=$config.categories_icons_width y=4}px; height: {math equation="x + y" x=$config.categories_icons_width y=4}px;">   
								<img src="{$smarty.const.RL_FILES_URL}{$sPost.icon}" style="border: 2px solid #D0D0D0; border-radius: 5px 5px 5px 5px; display: block; height: {math equation="x + y" x=$config.categories_icons_width y=4}; width: {math equation="x + y" x=$config.categories_icons_width y=4}px;" alt="{$lang.category_icon}" />   
								<img title="Delete" alt="Delete" class="delete" src="{$rlTplBase}/img/blank.gif" onclick="xajax_deleteIcon('{$sPost.key}');" />   
							</span>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="loading" id="photos_loading" style="width: 100%;"></div>
		{/if}
	</td>
</tr>