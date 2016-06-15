if ( sbdConfig.defaultDistance > 15000 && sbdConfig.defaultDistance <= 25000 )
{
	sbd_set_zoom -= 1;
}
else if ( sbdConfig.defaultDistance > 25000 && sbdConfig.defaultDistance <= 55000 )
{
	sbd_set_zoom -= 2;
}
else if ( sbdConfig.defaultDistance > 55000 )
{
	sbd_set_zoom -= 3;
}

$(document).ready(function(){
	/* adapt sorting links */
	$('.grid_navbar .sorting a').each(function(){
		var href = $(this).attr('href');
		$(this).attr('href', 'javascript:void(0)');
		$(this).attr('accesskey', href.split('=')[1]);
	});
	
	$('#map').flMap({
		phrases: {
			hide: '{/literal}{$lang.hide}{literal}',
			show: '{/literal}{$lang.show}{literal}',
			notFound: '{/literal}{$lang.location_not_found}{literal}'
		},
		scrollWheelZoom: false,
		emptyMap: true,
		zoom: sbd_set_zoom,
		ready: sbdHandler
	});
	
	sbdBase = $.flMap.get();
});

var sbdHandler = function(base){
	sbdControls(sbdConfig.start);
	
	/* controls listener */
	$('select[name=country]').change(function(){
		$('#sbd_zip').val('');
	});
	
	$('select[name=distance_unit]').change(function(){
		sbdConfig.unit = $(this).val();
		sbdSetLabel(sbdConfig.circle.getRadius());
		sbdSearch();
	});
	
	$('a.sbd_control').click(function(){
		sbdControls();
	});
	
	$('#sbd_zip').focus(function(){
		if ( $(this).val() == sbdConfig.zip_code )
		{
			$(this).val('');
		}
	}).blur(function(){
		if ( !$(this).val() )
		{
			$(this).val(sbdConfig.zip_code);
		}
	});
};

var sbdControls = function(start){
	//var country = $('select[name=country] option:selected').html() || $('input[name=country]').val();
	var country_code = $('select[name=country] option:selected').val() || $('input[name=country]').val();
	var zip = $('input#sbd_zip').val().replace(/\W/g, '');
	var geouser = $('input[name=sbd_geouser]').val();
	sbdConfig.unit = $('select[name=distance_unit] option:selected').val() || $('input[name=distance_unit]').val();

	if ( start || !zip )
	{
		geocoder.geocode({'address': start}, function(results, status){
			if ( status.toLowerCase() == 'ok' )
			{
				var lat = results[0].geometry.location.lat();
				var lng = results[0].geometry.location.lng();
				var response_zip, response_country;
				
				if ( results[0].address_components )
				{
					for ( var i = 0; i < results[0].address_components.length; i++ )
					{
						if ( results[0].address_components[i]['types'][0] == 'postal_code' )
						{
							response_zip = results[0].address_components[i]['long_name'];
						}
						else if ( results[0].address_components[i]['types'][0] == 'country' )
						{
							response_country = results[0].address_components[i]['short_name'];
						}
					}
					
					if ( $('select[name=country] option:selected').val() == response_country )
					{
						if ( lat && lng )
						{
							var center = new google.maps.LatLng(lat, lng);
							sbdBase.map.setCenter(center);
							sbdSetCircle();
						}
				
						if ( response_zip )
						{
							$('#sbd_zip').val(response_zip);
						}
					}
					else
					{
						printMessage('warning', sbdConfig.prLocationNotFound);
						$('#sbd_zip').val('');
						sbdSetDefault();
					}
				}
				else
				{
					printMessage('warning', sbdConfig.prLocationNotFound);
					$('#sbd_zip').val('');
					sbdSetDefault();
				}
			}
			else
			{
				printMessage('warning', sbdConfig.prLocationNotFound);
				$('#sbd_zip').val('');
				sbdSetDefault();
			}
		});
	}
	else
	{
		if ( zip && country_code && geouser)
		{
			 $.ajax({
             url: "http://api.geonames.org/postalCodeSearchJSON",
             dataType: 'json',
			 traditional: true,
             data: {
	         postalcode: zip,
	         country: country_code.toLowerCase(),
             username: geouser,
             style: 'SHORT',
             maxRows: 1
          },
          success: getLocation
        });	
		$('a.sbd_control span.center').html(lang['loading']);
		}
		else
		{
			setTimeout('sbdSetDefault()', 100);
		}
	}
}

var sbdSetDefault = function(){
	var center = new google.maps.LatLng(37.160317, -34.833988);
	sbdBase.map.setCenter(center);
	sbdBase.map.setZoom(2);
	sbdConfig.defaultDistance = 1000 * 1000;
	sbdSetCircle();
}

function getLocation(data) {
	if (data && data.postalCodes[0].lng && data.postalCodes[0].lat)
	{
		var center = new google.maps.LatLng(data.postalCodes[0].lat, data.postalCodes[0].lng);
		sbdBase.map.setCenter(center);
		sbdBase.map.setZoom(9);
		sbdSetCircle($('#sbd_radius').val());
	} else {
		printMessage('warning', sbdConfig.prZipNotFound);
		$('#sbd_zip').val('');
		sbdSetDefault();
	}	
	$('a.sbd_control span.center').html(sbdConfig.go);
};

var sbdSetCircle = function(radius){
	if ( !sbdConfig.circle )
	{
		var geocoder = new google.maps.Geocoder();
		var populationOptions = {
			strokeColor: "#db8a33",
			strokeOpacity: 0.8,
			strokeWeight: 2,
			fillColor: "#ff9e36",
			fillOpacity: 0.2,
			map: sbdBase.map,
			center: sbdBase.map.getCenter(),
			radius: sbdConfig.defaultDistance,
			editable: true
		};
	
		sbdConfig.circle = new google.maps.Circle(populationOptions);
		
		setTimeout('sbdSetLabel(sbdConfig.defaultDistance)', 1000);
		
		/* add listeners */
		google.maps.event.addListener(sbdConfig.circle, 'radius_changed', function() {
			setTimeout('sbdSetLabel(sbdConfig.circle.getRadius()); sbdSearch();', 155);
		});
		
		google.maps.event.addListener(sbdConfig.circle, 'center_changed', function() {
			setTimeout('sbdSetLabel(sbdConfig.circle.getRadius()); sbdSearch();', 50);
		});
		
		google.maps.event.addListener(sbdBase.map, 'zoom_changed', function() {
			setTimeout('sbdSetLabel(sbdConfig.circle.getRadius())', 150);
		});
		
		/* sorting handler */
		$('.grid_navbar .sorting a').click(function(){
			sbdConfig.sortField = $(this).attr('accesskey');
			var type = $(this).hasClass('asc') || $(this).hasClass('desc');
			sbdConfig.sortType = !type || $(this).hasClass('desc') ? 'asc' : 'desc';
			
			$('td.sorting a').attr('class', '');
			$(this).addClass('active');
			$(this).addClass(sbdConfig.sortType);
			
			xajax_getListings(sbdConfig.circle.getCenter().lat(), sbdConfig.circle.getCenter().lng(), sbdConfig.circle.getRadius(), sbdConfig.unit, sbdConfig.sortField, sbdConfig.sortType);
		});
		
		sbdSearch();
	}
	else
	{
		sbdConfig.circle.setCenter(sbdBase.map.getCenter());
		if (radius)
		{
			var new_radius = radius*1000;
			new_radius = sbdConfig.unit == 'mi' ? new_radius*1.609344 : new_radius;
			sbdConfig.circle.setRadius(new_radius);
			//sbdSetLabel(new_radius);
		}
	}
}

var sbdSetLabel = function(radius){
	radius = radius/1000;
	var unit = sbdConfig.prKmShort;
	
	if ( sbdConfig.unit == 'mi' )
	{
		radius /= 1.609344;
		unit = sbdConfig.prMiShort;
	}
	radius = Math.round(radius*10)/10;
	radius += ' '+unit;
	
	var el = $('#map>div>div>div>div:eq(2)>div:eq(1)>div>div:eq(3)');
	var pos = $(el).position();

//	if ( !pos )
//		return;
		
	var top = pos.top + 17;
	var left = pos.left;
	
	if ( !sbdConfig.label )
	{
		var label = '<div class="sbd_label">'+radius+'</div>';
		
		$('#map>div>div>div:first').append(label);
		left = pos.left - ($('#map div.sbd_label').width() / 2);
		$('#map div.sbd_label').css({
			top: top,
			left: left
		});
		sbdConfig.label = true;
	}
	else
	{
		$('#map div.sbd_label').html(radius).css({
			top: top,
			left: left - ($('#map div.sbd_label').width() / 2)
		});
	}
};

var sbdSearch_in_progress = false;
var sbdSearch = function(){
	if ( sbdSearch_in_progress )
		return;

	/* set loading */
	$('#sbd_count').html(lang['loading']);
	sbdSearch_in_progress = true;
	xajax_getListings(sbdConfig.circle.getCenter().lat(), sbdConfig.circle.getCenter().lng(), sbdConfig.circle.getRadius(), sbdConfig.unit);
};

var sbdSetMarker = function(lat, lng, id)
{
	if ( sbdConfig.idsOnMap.indexOf(id) < 0 )
	{
		var latLng = new google.maps.LatLng(lat, lng);
	
		var marker = new google.maps.Marker({
			position: latLng,
			map: sbdBase.map
		});
		
		google.maps.event.addListener(marker, 'click', function(){
			sbdAttachInfo(id);
		});
		
		// save marker pos
		sbdConfig.markers[id] = marker;
		sbdConfig.idsOnMap.push(id);
		
		// base.attachInfo(marker, i);
		sbdBase.bounds.extend(latLng);
	}
}

var sbdClearMarker = function(ids)
{
	if ( ids == 'all' )
	{
		ids = new Array();
	}
	else
	{
		ids = ids.split(',');
		
		for( var i = 0; i < ids.length; i++ )
		{
			ids[i] = parseInt(ids[i]);
		}
	}
	
	var to = sbdConfig.idsOnMap.length;
	var tmp = new Array();
	
	for ( var i = 0; i < to; i++ )
	{
		var pos = ids.indexOf(sbdConfig.idsOnMap[i]);
		
		if ( pos < 0 )
		{
			sbdConfig.markers[sbdConfig.idsOnMap[i]].setMap(null);
		}
		else
		{
			tmp.push(sbdConfig.idsOnMap[i]);
		}
	}
	
	sbdConfig.idsOnMap = tmp;
};

var sbdPaging = function()
{
	$('ul#sbd_paging li').unbind('click').click(function(){
		if ( $(this).hasClass('active') )
			return;
		
		if ( $(this).find('a').attr('accesskey') )
		{
			var page = $(this).find('a').attr('accesskey');
		}
		else
		{
			var page = parseInt($(this).find('a').html());
		}
		
		sbdConfig.page = page;
		xajax_getListings(sbdConfig.circle.getCenter().lat(), sbdConfig.circle.getCenter().lng(), sbdConfig.circle.getRadius(), sbdConfig.unit, sbdConfig.sortField, sbdConfig.sortType, page);
	});
};

/* attache info to the marker */
var sbdAttachInfo = function(id){
	// close all windows
	if ( sbdConfig.windowinfo.length > 0 )
	{
		for ( var i=0; i < sbdConfig.windowinfo.length; i++ )
		{
			sbdConfig.windowinfo[i].close();
		}
	}
	
	// load/open window
	if ( !sbdConfig.markers[id].flInfowindow )
	{
		var infowindow = new google.maps.InfoWindow({
			content: lang['loading'],
			size: new google.maps.Size(50,50)
		});
		
		sbdConfig.windowinfo.push(infowindow);
		sbdConfig.markers[id].flInfowindow = infowindow;
		infowindow.open(sbdBase.map, sbdConfig.markers[id]);
		
		$.get(sbdConfig.requestUrl, {request: 'sbd', id: id}, function(response){
			sbdConfig.markers[id].flInfowindow.setContent(response);
		});
	}
	else
	{
		sbdConfig.markers[id].flInfowindow.open(sbdBase.map, sbdConfig.markers[id]);
	}
}