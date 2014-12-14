jQuery(document).ready(function(){
	geocoder = new google.maps.Geocoder();
	
		jQuery('#geotag-search-button').click(function(){
			//alert(jQuery('#geotag-search').val());
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
    } else {
      alert('Geocode was not successful for the following reason: ' + status);
   }
  });
}