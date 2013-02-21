$(document).ready(function() {
	if(!$.cookie("lat")) {
		getLocation();
	} else {
		getPositionProperties($.cookie("lat"), $.cookie("lon"));
	}

	// bind geo autocomplete to input field
	$(".posContainer").geocomplete()
	.bind("geocode:result", function(event, result) {
		$.cookie("lat", result.geometry.location.lat());
		$.cookie("lon", result.geometry.location.lng());
	});

	// make active navigation tab highlighted
	$($(".nav li")[0]).addClass('active');

	$(".form-search").submit(function() {
		$.cookie("iens", $("#iens").is(':checked'));
		window.location = window.location.href+"search/"+$(".search-query").val()+'?lat='+$.cookie("lat")+'&lon='+$.cookie("lon")+'&name='+$(".posContainer").val()+'&iens='+$("#iens").is(':checked');
		return false;
	});
});

function errorCallback(error) {
	showWarning("There was a problem getting your location; enter a position to search in its vicinity.<br />"+error);
	checkGeoAnswer();
}

function getLocation() {
	var alertContainer = $(".alert-warning");

	if (navigator.geolocation) {
		runWithTimeout(checkGeoAnswer);
		navigator.geolocation.getCurrentPosition(convertPosition, errorCallback);
	} else {
		showWarning("Geolocation is not supported by this browser.");
	}
}

function convertPosition(position) {
	var llcontainer = $(".llcontainer"); 
	$("#geoloader").hide();

	$.cookie("lat", position.coords.latitude);
	$.cookie("lon", position.coords.longitude);

	getPositionProperties(position.coords.latitude, position.coords.longitude);
}

function getPositionProperties (lat, lon) {
	var reqString = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.placefinder%20where%20text%3D%22"+lat+"%2C"+lon+"%22%20and%20gflags%3D%22R%22&format=json";
	
	var request = $.ajax({
		url: reqString,
		type: "GET",
		accepts: 'application/json',
		dataType: "jsonp"
		});
	request.done(function(data) {
			showPosition(data);
		});
	request.fail(function(jqXHR, textStatus) {
			showError(textStatus);
		});
}

function showPosition (data) {
	var posContainer = $(".posContainer"); 
	posContainer.val(data.query.results.Result.city);
}

function checkGeoAnswer () {
	var posContainer = $(".posContainer"); 
	if(posContainer.val() == '') {
		posContainer.attr('placeholder', 'location');
	}
}

function runWithTimeout(func) {
	setTimeout(func, 12000);
}

function showWarning(message) {
	var alertContainer = $(".alert-warning");
	var alertContainerContent = $(".alert-warning .content");
	alertContainerContent.html(message);
	alertContainer.show();
}

function showError(message) {
	var alertContainer = $(".alert-error");
	var alertContainerContent = $(".alert-error .content");
	alertContainerContent.html(message);
	alertContainer.show();
}
