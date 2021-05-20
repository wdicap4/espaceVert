
var geocoder; 
var address = "63 RUE DU MOULIN DE LA POINTE, 75013";

function loadMap () {
    var map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 48.856614, lng: 2.3522219 },
        zoom: 8,
      });
      
      //add Marker
  
  }

function codeAddress (address) {

  geocoder.geocode({ 'address': address }, function (results, status) {
    console.log(results);
    var latLng = {lat: results[0].geometry.location.lat (), lng: results[0].geometry.location.lng ()};
    console.log (latLng);
    if (status == 'OK') {
        var marker = new google.maps.Marker({
            position: latLng,
            map: map
        });
        console.log (map);
    }
    
    else {
        alert('Geocode was not successful for the following reason: ' + status);
    }
});

}