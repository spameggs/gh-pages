<!-- grid -->
<div id="grid"></div>
<script type="text/javascript">//<![CDATA[
var testimonialsGrid;
{literal}
$(document).ready(function(){
	testimonialsGrid = new gridObj({
		key: 'testimonials',
		id: 'grid',
		ajaxUrl: rlPlugins + 'testimonials/admin/testimonials.inc.php?q=ext',
		defaultSortField: 'ID',
		title: lang['testimonials_manager'],
		remoteSortable: false,
		fields: [
			{name: 'Author', mapping: 'Author', type: 'string'},
			{name: 'Account_ID', mapping: 'Account_ID', type: 'int'},
			{name: 'Status', mapping: 'Status', type: 'string'},
			{name: 'Testimonial', mapping: 'Testimonial', type: 'string'},
			{name: 'Date', mapping: 'Date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
			{name: 'Email', mapping: 'Email', type: 'string'},
			{name: 'ID', mapping: 'ID'}
		],
		columns: [
			{
				header: lang['ext_name'],
				dataIndex: 'Author',
				width: 15,
				id: 'rlExt_item_bold',
				renderer: function(author, obj, row) {
					if ( row.data.Account_ID ) {
						var out = "<a target='_blank' ext:qtip='"+lang['ext_click_to_view_details']+"' href='"+rlUrlHome+"index.php?controller=accounts&action=view&userid="+row.data.Account_ID+"'>"+author+"</a>"
					}
					else {
						var out = author;
					}
					
					return out;
				}
			},{
				header: "{/literal}{$lang.testimonials_testimonial}{literal}",
				dataIndex: 'Testimonial',
				width: 60,
				editor: new Ext.form.TextArea({
					allowBlank: false
				}),
				renderer: function(val){
					return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
				}
			},{
				header: lang['ext_date'],
				dataIndex: 'Date',
				fixed: true,
				width: 100,
				renderer: Ext.util.Format.dateRenderer(rlDateFormat.replace(/%/g, '').replace('b', 'M'))
			},{
				header: lang['ext_status'],
				dataIndex: 'Status',
				fixed: true,
				width: 100,
				editor: new Ext.form.ComboBox({
					store: [
						['active', lang['ext_active']],
						['approval', lang['ext_approval']]
					],
					displayField: 'value',
					valueField: 'key',
					typeAhead: true,
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus:true
				}),
				renderer: function(val){
					return '<span ext:qtip="'+lang['ext_click_to_edit']+'">'+val+'</span>';
				}
			},{
				header: lang['ext_actions'],
				width: 80,
				fixed: true,
				dataIndex: 'ID',
				sortable: false,
				renderer: function(id, obj, row) {
					var out = "<center>";
					out += "<img class='remove' ext:qtip='"+lang['ext_delete']+"' src='"+rlUrlHome+"img/blank.gif' onClick='rlConfirm( \""+lang['testimonials_ext_delete_notice']+"\", \"xajax_deleteTestimonial\", \""+Array(id)+"\" )' />";
					out += "</center>";
					
					return out;
				}
			}
		]
	});		
	testimonialsGrid.init();
	grid.push(testimonialsGrid.grid);
});
{/literal}
//]]>
</script>
<!-- grid end -->