jQuery(document).ready(function(){
	geocoder = new google.maps.Geocoder();
	
		jQuery('#geotag-search-button').click(function(){
			geocode(jQuery('#geotag-search').val());
		});
});

function geocode(address) 
{
  geocoder.geocode( {address:address}, function(results, status) 
  {
    if (status == google.maps.GeocoderStatus.OK) 
    {
		console.log(results[0]);
		jQuery('#geotag-latitude').val(results[0].geometry.location.k);
		jQuery('#geotag-longitude').val(results[0].geometry.location.D);
		
		var myLatlng = new google.maps.LatLng(results[0].geometry.location.k, results[0].geometry.location.D);
		var mapOptions = {
		  zoom: 8,
		  center: myLatlng
		}
		
		var marker = new google.maps.Marker({
		    position: myLatlng
		});

		// To add the marker to the map, call setMap();
		clearMap();
		map.setCenter(myLatlng);
		marker.setMap(map);
		
		
    } else {
      alert('Geocode was not successful for the following reason: ' + status);
	  clearMap();
	  jQuery('#geotag-latitude').val('');
	  jQuery('#geotag-longitude').val('');
   }
  });
}