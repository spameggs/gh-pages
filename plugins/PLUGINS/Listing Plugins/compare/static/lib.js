flCompare.itemTpl = ' \
<div class="item hide" id="compare_{id}"> \
	<div class="left"> \
		<a target="_blank" href="{href}"> \
			<img src="{src}" title="{title}" alt="{title}" /> \
		</a> \
	</div> \
	<div class="right"> \
		<div onclick="flCompare.remove(false, \'{id}\', true)" class="remove" title="'+ flCompare.phrases['remove'] +'"></div> \
		<a target="_blank" href="{href}"><b>{title}</b></a> \
		<div class="category">{category}</div> \
	</div> \
</div>';
$(document).ready(function(){$("#compare_listings_tab").click(function(){if(flCompare.visible){if(rlLangDir=="rtl")$("#compare_listings_fixed").animate({left:0});else $("#compare_listings_fixed").animate({right:0});flCompare.visible=false}else{if(rlLangDir=="rtl")$("#compare_listings_fixed").animate({left:272});else $("#compare_listings_fixed").animate({right:272});flCompare.visible=true}});var ids=readCookie("compare_listings");if(ids)$.getJSON(flCompare.requestUrl,{request:"compareList",ids:ids},
function(response){if(response&&response.length>0&&response!="false"){for(var i=0;i<response.length;i++){var html=flCompare.itemTpl.replace(/{id}/gi,response[i].ID).replace(/{href}/gi,response[i].href).replace(/{src}/gi,response[i].Main_photo?rlConfig["files_url"]+response[i].Main_photo:rlConfig["tpl_base"]+"img/no-picture.jpg").replace(/{title}/gi,response[i].Listing_title).replace(/{category}/gi,response[i].category);$("#compare_listings_area div.body").append(html);$("div#compare_"+response[i].ID).fadeIn();
$("a#compare_icon_"+response[i].ID).removeClass("add_to_compare").addClass("remove_from_compare");$("a#compare_icon_"+response[i].ID).attr("title",flCompare.phrases["remove"])}flCompare.counter=response.length;$("#compare_listings_tab span.counter").html("("+flCompare.counter+")");flCompare.navigation();$("#compare_listings_area div.body").scrollTop($("#compare_listings_area > div").height())}});$("a.add_to_compare, a.remove_from_compare").unbind("click").click(function(){flCompare.action(this)})});
flCompare.action=function(obj,details){var id=$(obj).attr("accesskey");var ids=readCookie("compare_listings");if(ids){ids=ids.split(",");if(ids.indexOf(id)>=0){if(details)flCompare.remove(false,id,true);else flCompare.remove(obj,id);return}else{ids.push(id);flCompare.add(obj,id,details)}}else{ids=new Array;ids.push(id);flCompare.add(obj,id,details)}createCookie("compare_listings",ids.join(","),93);flCompare.open()};
flCompare.add=function(obj,id,details){if(details){var img=$(obj).closest("td.side_bar").find("div.photos div.preview a img");var src=img.length>0?$(img).attr("src"):rlConfig["tpl_base"]+"img/no-picture.jpg";var title=$("div#content td.content h1:first").html();var href=document.location.href;var cat=$(obj).closest("td.side_bar").find("ul.statistics li:first a").wrap("<span />").parent().html();$(obj).removeClass("add").addClass("remove");$(obj).find("a:first").text(flCompare.phrases["remove"])}else{var item=
$(obj).closest("div.item");var img=$(item).find("td.photo img");var link=img.length>0?$(img).parent():$(item).find("td.fields table td.value:first a");var title=img.length>0?$(img).attr("alt"):$(link).html();var src=img.length>0?$(img).attr("src"):rlConfig["tpl_base"]+"img/no-picture.jpg";var href=$(link).attr("href");var cat=$(item).find("a.cat_caption").parent().html();$(obj).removeClass("add_to_compare").addClass("remove_from_compare");$(obj).attr("title",flCompare.phrases["remove"])}flCompare.counter++;
$("#compare_listings_tab span.counter").html("("+flCompare.counter+")");var html=flCompare.itemTpl.replace(/{id}/gi,id).replace(/{href}/gi,href).replace(/{src}/gi,src).replace(/{title}/gi,title).replace(/{category}/gi,cat);$("#compare_listings_area div.body").append(html);$("div#compare_"+id).fadeIn();flCompare.navigation();$("#compare_listings_area div.body").scrollTop($("#compare_listings_area > div").height())};
flCompare.remove=function(obj,id,nav,table){if(!table){if($("li.compare-icon span.remove").length>0){$("li.compare-icon span.remove").removeClass("remove").addClass("add");$("li.compare-icon span a:first").text(flCompare.phrases["add"])}else{$(nav?"a#compare_icon_"+id:obj).removeClass("remove_from_compare").addClass("add_to_compare");$(nav?"a#compare_icon_"+id:obj).attr("title",flCompare.phrases["add"])}$("div#compare_"+id).fadeOut(function(){$(this).remove();flCompare.navigation()});var ids=readCookie("compare_listings");
if(ids){ids=ids.split(",");if(ids.indexOf(id)>=0)ids.splice(ids.indexOf(id),1);createCookie("compare_listings",ids.join(","),93)}flCompare.counter--;$("#compare_listings_tab span.counter").html("("+flCompare.counter+")")}var selector=!table?" > tbody > tr:not(.deny)":"";if($("table.compare"+selector).length>0)if($.browser.msie&&$.browser.version=="9.0")$("table.compare td.fields-content tr.in td.listing_"+id).hide(function(){$(this).remove();if($("table.compare td.fields-content table.table tr.in:first td").length<=
0){$("table.compare").parent().fadeOut(function(){$("span#compare_no_data").fadeIn()});$("a.compare_fullscreen").fadeOut()}});else $("table.compare td.fields-content tr.in td.listing_"+id).fadeOut(function(){$(this).remove();if($("table.compare td.fields-content table.table tr.in:first td").length<=0){$("table.compare").parent().fadeOut(function(){$("span#compare_no_data").fadeIn()});$("a.compare_fullscreen").fadeOut()}})};
flCompare.open=function(){if(!flCompare.visible){if(rlLangDir=="rtl")$("#compare_listings_fixed").animate({left:272});else $("#compare_listings_fixed").animate({right:272});flCompare.visible=true}};flCompare.navigation=function(){var length=$("#compare_listings_area div.body div.item").length;if(length)$("#compare_listings_area div.body span.info").hide();else $("#compare_listings_area div.body span.info").show();if(length>1)$("#compare_listings_area div.button").fadeIn();else $("#compare_listings_area div.button").fadeOut()};
flCompare.fixSizes=function(){$("table.compare td.fields-column td.item").each(function(){var add=0;var column_height=parseInt($(this).height())+add;var index=$("table.compare td.fields-column td.item").index(this);var line_height=parseInt($("table.compare div.scroll table tr.in:eq("+index+") td.item:first").height())+add;line_height=line_height?line_height:0;if(column_height>line_height)$("table.compare div.scroll table tr.in:eq("+index+") td.item:first").height(column_height);else if(column_height<
line_height)$(this).height(line_height)})};
flCompare.fieldHover=function(){$("table.compare td.fields-column td.item:not(:first)").mouseenter(function(){var index=$("table.compare td.fields-column td.item").index(this);$(this).addClass("hover");$("table.compare div.scroll table tr.in:eq("+index+") td.item").addClass("hover");flCompare.highlight($("table.compare div.scroll table tr.in:eq("+index+") td.item"),"add")}).mouseleave(function(){var index=$("table.compare td.fields-column td.item").index(this);$(this).removeClass("hover");$("table.compare div.scroll table tr.in:eq("+
index+") td.item").removeClass("hover");flCompare.highlight($("table.compare div.scroll table tr.in:eq("+index+") td.item"),"remove")});$("table.compare div.scroll table tr.in:not(:first)").mouseenter(function(){var index=$("table.compare div.scroll table tr.in").index(this);$(this).find("td.item").addClass("hover");$("table.compare td.fields-column table tr.header:eq("+index+") td.item").addClass("hover");flCompare.highlight($(this).find("td.item"),"add")}).mouseleave(function(){var index=$("table.compare div.scroll table tr.in").index(this);
$(this).find("td.item").removeClass("hover");$("table.compare td.fields-column table tr.header:eq("+index+") td.item").removeClass("hover");flCompare.highlight($(this).find("td.item"),"remove")})};
flCompare.highlight=function(row,action){if(!row)return;$(row).each(function(item){if(item>0&&trim($(this).html())==trim($(row).parent().find("td.item:first").html())&&$(this).html()!="-")if(action=="add"){$(this).addClass("same");$(row).parent().find("td.item:first").addClass("same")}else{$(this).removeClass("same");$(row).parent().find("td.item:first").removeClass("same")}})};
flCompare.removeFromTable=function(){$("table.compare > tbody > tr:not(.deny) td.fields-content td.side_bar div.remove").each(function(){$(this).flModal({caption:flCompare.phrases["warning"],content:flCompare.phrases["notice"],prompt:"flCompare.remove(false, '"+$(this).attr("id").split("_")[3]+"', true)",width:"auto",height:"auto"})});$("table.compare > tbody > tr.deny td.fields-content td.side_bar div.remove").each(function(){$(this).flModal({caption:flCompare.phrases["warning"],content:flCompare.phrases["notice"],
prompt:"xajax_removeSavedItem('"+$(this).attr("id").split("_")[3]+"')",width:"auto",height:"auto"})})};
flCompare.modeSwitcher=function(){$("a.compare_fullscreen").click(function(){var date=new Date;var button='<a class="button compare_default" title="" href="javascript:void(0)"><span>'+flCompare.phrases["compare_default_view"]+"</span></a>";$("body > *").hide();$("body").append('<div class="compare_fullscreen_area hide"><div class="compare_header"><table class="sTable"><tr><td class="lalign"><h1>'+flCompare.phrases["compare_comparison_table"]+'</h1></td><td class="ralign">'+button+'</td></tr></table></div><div class="compare_body"></div><div id="footer" class="compare_footer"><table class="sTable"><tr><td class="lalign"><img alt="" src="'+
rlConfig["tpl_base"]+'img/logo.png" /></td><td class="ralign" valign="top"><span>&copy; '+date.getFullYear()+", "+flCompare.phrases["powered_by"]+' </span><a title="'+flCompare.phrases["powered_by"]+" "+flCompare.phrases["copy_rights"]+'" href="'+rlConfig["seo_url"]+'">'+flCompare.phrases["copy_rights"]+"</a></td></tr></table></div></div>");$("body > div.compare_fullscreen_area > div.compare_body").append($("table.compare").parent());$("body > div.compare_fullscreen_area").fadeIn();$("a.compare_default").unbind("click").click(function(){$("body > div.compare_fullscreen_area").fadeOut(function(){$("#controller_area").prepend($("body > div.compare_fullscreen_area > div.compare_body > div.highlight"));
$("body > *").show();flCompare.fixSizes();$(this).remove()})});flCompare.fixSizes()})};