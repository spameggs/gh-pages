<div id="grid"></div>
	<script type="text/javascript">//<![CDATA[
	var reportGrid;
	{literal}
	$(document).ready(function(){
		reportGrid = new gridObj({
			key: 'reportBroken',
			id: 'grid',
			ajaxUrl: rlPlugins + 'reportBrokenListing/admin/reportBrokenListing.inc.php?q=ext',
			defaultSortField: 'Date',
			defaultSortType: 'DESC',
			title: lang['ext_manager'],
			fields: [
				{name: 'ID', mapping: 'ID'},
				{name: 'Listing_ID', mapping: 'Listing_ID', type: 'int'},
				{name: 'Message', mapping: 'Message'},
				{name: 'Account_ID', mapping: 'Account_ID', type: 'int'},
				{name: 'Name', mapping: 'Name', type: 'string'},
				{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
			],
			columns: [{
					header: lang['ext_id'],
					dataIndex: 'ID',
					width: 35,
					fixed: true				
				},{
					header: lang['ext_listing_id'],
					id: 'rlExt_black_bold',
					dataIndex: 'Listing_ID',
					width: 70,
					fixed: true,
					renderer: function(value, param1, row){
						if ( parseInt(value) )
						{
							value = '<a href="'+rlUrlHome+'index.php?controller=listings&action=view&id='+row.data.Listing_ID+'" target="_blank"><img ext:qtip="'+lang['ext_click_to_view_details']+'" class="view grid-icon" src="'+rlUrlHome+'img/blank.gif" alt="" /></a> ' + value;
						}
						return value;
					}
				},{
					header: lang['ext_reportbroken_message'],
					dataIndex: 'Message',
					width: 120,					
					renderer: function (val){
						var display = val.substring(0, 60);
						if ( val.length > 60 )
						{
							display += '...';
						}
						return '<span ext:qtip="'+val.replace(/\n/gi, '<br />')+'">'+display+'</span>';
					}
				},{
					header: lang['ext_reportBroken_report_by'],
					dataIndex: 'Name',
					id: 'rlExt_item',
					width: 30,
					renderer: function (username, ext, row){
						if ( username )
						{
							return "<a target='_blank' ext:qtip='"+lang['ext_click_to_view_details']+"' href='"+rlUrlHome+"index.php?controller=accounts&action=view&userid="+row.data.Account_ID+"'>"+username+"</a>"
						}
						else
						{
							return lang['ext_reportBroken_guest'];
						}
					}
				},{
					header: lang['ext_add_date'],
					dataIndex: 'Date',
					width: 100,	
					fixed: true,
					renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
				},{
					header: lang['ext_reportbroken_delete_listing'],
					dataIndex: 'ID',
					width: 100,
					fixed: true,
					renderer: function(val){	
						return "<center><img class='remove' ext:qtip='"+lang['ext_reportbroken_delete_listing']+"' src='"+rlUrlHome+"img/blank.gif' onclick='rlConfirm( \""+lang['ext_reportBroken_notice_'+delete_mod]+"\", \"xajax_deleteListing\", \""+Array(val)+"\", \"section_load\" )' /></center>";
					}
				},{
					header: lang['ext_actions'],
					dataIndex: 'ID',
					width: 60,
					fixed: true,
					renderer: function(id){
						return "<center><img class='remove' ext:qtip='"+lang['ext_reportbroken_delete']+"' src='"+rlUrlHome+"img/blank.gif' onclick='rlConfirm( \""+lang['ext_notice_delete']+"\", \"xajax_deletereportBrokenListing\", \""+Array(id)+"\", \"section_load\" )' /></center>";
					}
				}
			]
		});
		reportGrid.init();
		grid.push(reportGrid.grid);
	});
	{/literal}
	//]]>
</script>