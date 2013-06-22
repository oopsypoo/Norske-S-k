function initialize(x,y, area_title) 
{
	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(x, y);//skal være bodø
	var myOptions = { zoom: 7,
							center: latlng,
							mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var mcontainer = document.getElementById("map_container");
    var marker = new google.maps.Marker({
      	position: latlng,
      	title: area_title
 		 });

    mcontainer.innerHTML = "<div id=\"map_canvas\"><div>";
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    marker.setMap(map);
}

function codeAddress(adress_string) 
{
	//var address = document.getElementById("address").value;
	geocoder = new google.maps.Geocoder();
	var myOptions = { zoom: 7,
							mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var mcontainer = document.getElementById("map_container");
  	mcontainer.innerHTML = "<div id=\"map_canvas\"><div>";
  	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
 	
	geocoder.geocode( { 'address': adress_string}, function(results, status) 
	{
      if (status == google.maps.GeocoderStatus.OK) 
      {
      map.setZoom(7);
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            											map: map,
            											position: results[0].geometry.location,
            											title: adress_string 
            											}
        );
        marker.setMap(map);
        	
      } 
      else 
      {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }