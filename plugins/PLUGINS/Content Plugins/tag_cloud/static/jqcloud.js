!function(e){"use strict";e.fn.jQCloud=function(t,i){var n=this,a=n.attr("id")||Math.floor(1e6*Math.random()).toString(36),o={width:n.width(),height:n.height(),center:{x:(i&&i.width?i.width:n.width())/2,y:(i&&i.height?i.height:n.height())/2},delayedMode:t.length>50,shape:!1,encodeURI:!0};i=e.extend(o,i||{}),n.addClass("jqcloud").width(i.width).height(i.height),"static"===n.css("position")&&n.css("position","relative");var r=function(e){4===e.length&&(e=jQuery.map(/\w+/.exec(e),function(e){return e+e}).join(""));var t=/(\w{2})(\w{2})(\w{2})/.exec(e);return[parseInt(t[1],16),parseInt(t[2],16),parseInt(t[3],16)]},s=function(e){return"#"+jQuery.map(e,function(e){var t=e.toString(16);return t=1===t.length?"0"+t:t}).join("")},l=function(e,t){return jQuery.map(r(e.end),function(i,n){return(i-r(e.start)[n])/t})},d=function(e,t,i){var n=jQuery.map(r(e.start),function(e,n){var a=Math.round(e+t[n]*i);return a>255?a=255:0>a&&(a=0),a});return s(n)},c=function(){for(var o=function(e,t){var i=function(e,t){return Math.abs(2*e.offsetLeft+e.offsetWidth-2*t.offsetLeft-t.offsetWidth)<e.offsetWidth+t.offsetWidth&&Math.abs(2*e.offsetTop+e.offsetHeight-2*t.offsetTop-t.offsetHeight)<e.offsetHeight+t.offsetHeight?!0:!1},n=0;for(n=0;n<t.length;n++)if(i(e,t[n]))return!0;return!1},r=0;r<t.length;r++)t[r].weight=parseFloat(t[r].weight,10);t.sort(function(e,t){return e.weight<t.weight?1:e.weight>t.weight?-1:0});var s="rectangular"===i.shape?18:2,c=[],p=i.width/i.height,u=function(r,u){var f=a+"_word_"+r,h=6.28*Math.random(),m=0,g=0,v=0,b=5,w="",_="",y="";u.html=e.extend(u.html,{id:f}),u.html&&u.html["class"]&&(w=u.html["class"],delete u.html["class"]),t[0].weight>t[t.length-1].weight&&(b=Math.round(9*((u.weight-t[t.length-1].weight)/(t[0].weight-t[t.length-1].weight)))+1);for(var x=t[0].count,k=t[0].count,C=0;C<t.length;C++)x=t[C].count<x?t[C].count:x,k=t[C].count>k?t[C].count:k;var S=k-x;0===S&&(S=1);var T=l(i.color,S),I=d(i.color,T,u.count);if(y=e("<span>").attr(u.html).css("font-size",u.weight).css("color",I),u.link?("string"==typeof u.link&&(u.link={href:u.link}),i.encodeURI&&(u.link=e.extend(u.link,{href:encodeURI(u.link.href).replace(/'/g,"%27")})),_=e("<a>").attr(u.link).text(u.text)):_=u.text,y.append(_),u.handlers)for(var $ in u.handlers)u.handlers.hasOwnProperty($)&&"function"==typeof u.handlers[$]&&e(y).bind($,u.handlers[$]);n.append(y);var D=y.width(),F=y.height(),j=i.center.x-D/2,M=i.center.y-F/2,E=y[0].style;for(E.position="absolute",E.left=j+"px",E.top=M+"px";o(document.getElementById(f),c);){if("rectangular"===i.shape)switch(g++,g*s>(1+Math.floor(v/2))*s*(0===v%4%2?1:p)&&(g=0,v++),v%4){case 1:j+=s*p+2*Math.random();break;case 2:M-=s+2*Math.random();break;case 3:j-=s*p+2*Math.random();break;case 0:M+=s+2*Math.random()}else m+=s,h+=(0===r%2?1:-1)*s,j=i.center.x-D/2+m*Math.cos(h)*p,M=i.center.y+m*Math.sin(h)-F/2;E.left=j+"px",E.top=M+"px"}c.push(document.getElementById(f)),e.isFunction(u.afterWordRender)&&u.afterWordRender.call(y)},f=function(a){return a=a||0,n.is(":visible")?(a<t.length?(u(a,t[a]),setTimeout(function(){f(a+1)},10)):e.isFunction(i.afterCloudRender)&&i.afterCloudRender.call(n),void 0):(setTimeout(function(){f(a)},10),void 0)};i.delayedMode?f():(e.each(t,u),e.isFunction(i.afterCloudRender)&&i.afterCloudRender.call(n))};return setTimeout(function(){c()},10),n}}(jQuery);