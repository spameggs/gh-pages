var categoryFilterClass=function(){var self=this;this.moreFilters=function(){$("div.filter-area ul").each(function(){if($(this).find("li.hide").length>0){$(this).next().after('<ul class="hide"></ul>');$(this).next().next().append($(this).find("li.hide").show())}});$("div.filter-area a.more").click(function(){$("div.other_filters_tmp").remove();var pos=$(this).offset();var sub_cats=$(this).next().html();var tmp='<div class="other_filters_tmp side_block"><div class="block_bg"><ul></ul></div></div>';
$("body").append(tmp);$("div.other_filters_tmp div ul").html(sub_cats);var rest=rlLangDir=="ltr"?0:$("div.other_filters_tmp").width();var side=rlLangDir=="ltr"?"Left":"Right";$("div.other_filters_tmp").css({top:pos.top,left:pos.left-rest,display:"block"});var width=$(this).width()+5;$("div.other_filters_tmp div").css("margin"+side,width+"px")});$(document).click(function(event){if($(event.target).closest(".other_filters_tmp").length<=0&&$(event.target).attr("class")!="dark_12 more")$("div.other_filters_tmp").remove()})};
this.checkbox=function(obj,empty){obj.parent().find("ul li input").click(function(){self.checkboxAction(obj,empty)});this.checkboxAction(obj,empty)};this.checkboxAction=function(obj,empty){var values=new Array;obj.parent().find("ul li").each(function(){if($(this).find("input").is(":checked"))values.push($(this).find("input").val())});if(values.length>0){var href=obj.find("a:first").attr("accesskey");href=href.replace("[replace]",values.join(","));obj.find("a:first").attr("href",href);obj.find("span").hide();
obj.removeClass("dark single");obj.find("a:first").show();if(empty){obj.find("a:last").hide();obj.find("span").html(cf_apply_filter)}}else{obj.find("a:first").hide();obj.addClass("dark single");obj.find("span").show();if(empty){obj.find("a:last").show();obj.removeClass("single");obj.find("span").html(cf_remove_filter)}}}};var categoryFilter=new categoryFilterClass;