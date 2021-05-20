
function loadMap () {
    var map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 48.856614, lng: 2.3522219 },
        zoom: 8,
      });

      const geocoder = new google.maps.Geocoder();
      document.getElementById("submit").addEventListener("click", () => {
        geocodeAddress(geocoder, map);
      });
      
    //add Marker
    /*  var marker = new google.maps.Marker({
        position: {lat: 48.856614, lng: 2.3522219},
        map: map
      }); */

}
function geocodeAddress(geocoder, resultsMap) {
    for (i = 0; i<10; i++){
    const address = document.getElementById("address[i]").value;
    document.write(address);
    }
    /*geocoder.geocode({ address: address }, (results, status) => {
    if (status === "OK") {
        resultsMap.setCenter(results[0].geometry.location);
        new google.maps.Marker({
            map: resultsMap,
            position: results[0].geometry.location,
        });
    } 
    else {
      alert("Geocode was not successful for the following reason: " + status);
    }
  });*/
}