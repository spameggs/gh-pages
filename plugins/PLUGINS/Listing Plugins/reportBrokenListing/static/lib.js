$(document).ready(function(){$("a.reportBroken").each(function(){var e=$(this).attr("id").split("_")[1],t=readCookie("reportBrokenListings");if(t&&t.indexOf(e)>=0&&($(this).removeClass("reportBroken").addClass("removeBroken"),$(this).attr("title",lang.reportbroken_remove_in),!$(this).hasClass("icon"))){var n=$(this).find("img");$(this).html(lang.reportbroken_remove_in).append(n)}}),reportBrokenLisitngHandler()});var reportBrokenLisitngID,reportBrokenLisitngHandler=function(){$("a.reportBroken").unbind("click").flModal({source:"#reportBrokenListing_form",width:500,height:"auto",ready:function(){reportBrokenLisitngID=$(this).attr("id").split("_")[1],$("#message_text").textareaCount({maxCharacterSize:rlConfig.reportBroken_message_length,warningNumber:20})}}),$("a.removeBroken").each(function(){$(this).unbind("click").flModal({caption:lang.warning,content:lang.reportbroken_do_you_want_to_delete_list,prompt:"xajax_removeReportBrokenListing("+$(this).attr("id").split("_")[1]+")",width:"auto",height:"auto"})})},reportBrokenLisitngIcon=function(e){var t=readCookie("reportBrokenListings");if(t){if(t=t.split(","),t=$.map(t,function(e){return parseInt(e)}),t.indexOf(e)>=0){if(t.splice(t.indexOf(e),1),createCookie("reportBrokenListings",t.join(","),93),$("a#reportBrokenListing_"+e).removeClass("removeBroken").addClass("reportBroken"),$("a#reportBrokenListing_"+e).attr("title",lang.reportbroken_add_in),!$("a#reportBrokenListing_"+e).hasClass("icon")){var n=$("a#reportBrokenListing_"+e).find("img");$("a#reportBrokenListing_"+e).html(lang.reportbroken_add_in).append(n)}return reportBrokenLisitngHandler(),void 0}t.push(e)}else t=new Array,t.push(e);if(createCookie("reportBrokenListings",t.join(","),93),$("a#reportBrokenListing_"+e).removeClass("reportBroken").addClass("removeBroken"),$("a#reportBrokenListing_"+e).attr("title",lang.reportbroken_remove_in),!$("a#reportBrokenListing_"+e).hasClass("icon")){var n=$("a#reportBrokenListing_"+e).find("img");$("a#reportBrokenListing_"+e).html(lang.reportbroken_remove_in).append(n)}reportBrokenLisitngHandler()};