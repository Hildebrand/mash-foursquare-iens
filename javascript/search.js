$(document).ready(function() {
	$($(".nav li")[1]).addClass('active');
	
	$("#inputLocation").geocomplete()
	.bind("geocode:result", function(event, result) {
		$.cookie("lat", result.geometry.location.lat(), { path: '/fs/'});
		$.cookie("lon", result.geometry.location.lng(), { path: '/fs/'});

		var searchString = $("#inputSearchstring").val();
		getFoursquareResults(searchString, result.geometry.location.lat(), result.geometry.location.lng());
	});

	$("#iens").click(function () {
		if($("#iens").prop('checked')) {
			$.cookie('iens', true, { path: '/fs/'});
		} else {
			$.cookie('iens', false, { path: '/fs/'});
		}
		var searchString = $("#inputSearchstring").val();
		getFoursquareResults(searchString, $.cookie("lat"), $.cookie("lon"));
	});

	// update map after search string has been changed after a second
	bindChangeListener('#inputSearchstring');

	if($.url().param('name') != $("#inputLocation").val()) {
		getFoursquareResults($("#inputSearchstring").val(), $.cookie("lat"), $.cookie("lon"));
	}
	if($.url().param('iens') == "true") {
		$("#iens").prop('checked', true);
	} else {
		$("#iens").prop('checked', false);
	}

	fillLeftColumnWithSearchResults();
	initializeMap();
});

function getFoursquareResults(searchString, lat, lon) {
	var reqString = 'http://pwnshop.nl/fs/rest/listvenues/'+searchString+'?lat='+lat+'&lon='+lon+'&iens='+$.cookie("iens");
	
	var request = $.ajax({
		url: reqString,
		type: "GET",
		accepts: 'application/json',
		dataType: "json"
		});
	request.done(function(data) {
			searchResults.response.venues.length = 0;
			searchResults = data;
			fillLeftColumnWithSearchResults();
			updateMap();
		});
	request.fail(function(jqXHR, textStatus) {
			searchResults.response.venues.length = 0;
			clearMap();
			fillLeftColumnWithSearchResults();
			console.log(textStatus);
		});
}

function updateMap () {
	clearMap();
	fillMap();
	map.setCenter(new google.maps.LatLng($.cookie("lat"), $.cookie("lon")));
}

function fillLeftColumnWithSearchResults () {
	var navList = $("#searchResultsList");
	$("#searchResultsList li:not(:first)").remove();

	var i;
	for(i = 0; i< searchResults.response.venues.length; i++) {
		var node = $(document.createElement('li'))
			.append($('<a></a>').attr('href', searchResults.response.venues[i].canonicalUrl)
				.html(searchResults.response.venues[i].name+', '+
					searchResults.response.venues[i].location.address+', '+
					searchResults.response.venues[i].location.city)
			);
		navList.append(node);
	}

	$("#searchResultsList .nav-header").html("Search results ("+searchResults.response.venues.length+")");
}

var map;
var geocoder;
var markers = new Array();
var infowindow;

function initializeMap() {
	geocoder = new google.maps.Geocoder();
	infowindow = new google.maps.InfoWindow({
		content: "<span style='font-family: Arial;'></span>"
	});

	geocoder.geocode( { 'address': $.url().param('name')}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			map.setCenter(new google.maps.LatLng($.cookie("lat"), $.cookie("lon")));
			fillMap();
		} else {
			alert("Geocode was not successful for the following reason: " + status);
		}
	});

	var mapOptions = {
		center: new google.maps.LatLng($.cookie("lat"), $.cookie("lon")),
		zoom: 12,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	map = new google.maps.Map(document.getElementById("map_canvas"),
			mapOptions);
}

function fillMap() {
	var i;
	for(i = 0; i< searchResults.response.venues.length; i++) {
		markers[i] = createMarker(new google.maps.LatLng(searchResults.response.venues[i].location.lat,
			searchResults.response.venues[i].location.lng),
			searchResults.response.venues[i].name,
			searchResults.response.venues[i].location.address,
			searchResults.response.venues[i].location.city,
			searchResults.response.venues[i].canonicalUrl,
			searchResults.response.venues[i].rating_food,
			searchResults.response.venues[i].rating_service,
			searchResults.response.venues[i].rating_interior);
	}
}

function clearMap () {
	var i;
	for(i = 0; i<markers.length; i++) {
		markers[i].setMap(null);
	}
	markers.length = 0;
}

function createMarker(pos, t, address, city, url, rating_food, rating_service, rating_interior) {
	var marker = new google.maps.Marker({
		position: pos, 
		map: map,  // google.maps.Map 
		title: t
	});

	google.maps.event.addListener(marker, 'click', function() {
		var markerstring = "<span style='font-family: Arial;'>Name: "+marker.title;
		if(address) {
			markerstring += '<br />Address: '+address;
		}
		if(city) {
			markerstring += '<br />City: '+city;
		}
		markerstring += "<br /><a href='"+url+"'>website</a>";
		if(rating_food) { markerstring += '<br />Rating food: '+rating_food; }
		if(rating_service) { markerstring += '<br />Rating service: '+rating_service; }
		if(rating_interior) { markerstring += '<br />Rating interior: '+rating_interior; }
		markerstring += "</span>";
		infowindow.content = markerstring;
		infowindow.open(map,marker);
    }); 
    return marker;  
}

function bindChangeListener (inputId) {
	$(inputId).bind("input keyup", function (evt) {
		var val = $(this).attr('value');
		// If it's the propertychange event, make sure it's the value that changed.
		if (window.event && event.type == "propertychange" && event.propertyName != "value")
			return;

		// Clear any previously set timer before setting a fresh one
		window.clearTimeout($(this).data("timeout"));

		$(this).data("timeout", setTimeout(function () {
			var searchString = $(inputId).val();
			getFoursquareResults(searchString, $.cookie("lat"), $.cookie("lon"));
		}, 350));
	});
}
