(function($){var F=$.fancybox;F.helpers.media={beforeLoad:function(opts,obj){var href=obj.href||"",type=false,rez;if(rez=href.match(/(youtube\.com|youtu\.be)\/(v\/|u\/|embed\/|watch\?v=)?([^#\&\?]*).*/i)){href="//www.youtube.com/embed/"+rez[3]+"?autoplay=1&autohide=1&fs=1&rel=0&enablejsapi=1";type="iframe"}else if(rez=href.match(/vimeo.com\/(\d+)\/?(.*)/)){href="//player.vimeo.com/video/"+rez[1]+"?hd=1&autoplay=1&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1";type="iframe"}else if(rez= href.match(/metacafe.com\/watch\/(\d+)\/?(.*)/)){href="//www.metacafe.com/fplayer/"+rez[1]+"/.swf?playerVars=autoPlay=yes";type="swf"}else if(rez=href.match(/dailymotion.com\/video\/(.*)\/?(.*)/)){href="//www.dailymotion.com/swf/video/"+rez[1]+"?additionalInfos=0&autoStart=1";type="swf"}else if(rez=href.match(/twitvid\.com\/([a-zA-Z0-9_\-\?\=]+)/i)){href="//www.twitvid.com/embed.php?autoplay=0&guid="+rez[1];type="iframe"}else if(rez=href.match(/twitpic\.com\/(?!(?:place|photos|events)\/)([a-zA-Z0-9\?\=\-]+)/i)){href= "//twitpic.com/show/full/"+rez[1];type="image"}else if(rez=href.match(/(instagr\.am|instagram\.com)\/p\/([a-zA-Z0-9_\-]+)\/?/i)){href="//"+rez[1]+"/p/"+rez[2]+"/media/?size=l";type="image"}else if(rez=href.match(/maps\.google\.com\/(\?ll=|maps\/?\?q=)(.*)/i)){href="//maps.google.com/"+rez[1]+""+rez[2]+"&output="+(rez[2].indexOf("layer=c")?"svembed":"embed");type="iframe"}if(type){obj.href=href;obj.type=type}}}})(jQuery);