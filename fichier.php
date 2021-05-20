// your existing PHP...
<html>

  <head>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkUOdZ5y7hMm0yrcCQoCvLwzdM6M8s5qk&libraries=places&v=weekly"></script>
  </head>

  <body>
    <div id="map"></div>
    <script type="text/javascript">
      var locations = <?php echo json_encode($a);?>;
      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 6,
        center: new google.maps.LatLng(22.00, 96.00),
        mapTypeId: google.maps.MapTypeId.ROADMAP
      });

      var infowindow = new google.maps.InfoWindow();
      // var image = '/marker/map.png';
      // var image2 = '/marker/Map1.png';

      var marker1, n;
      for (n = 0; n < locationsb.length; n++) {
        marker1 = new google.maps.Marker({
          position: new google.maps.LatLng(locationsb[n][1], locationsb[n][2]),
          offset: '0',
          // icon: image2,
          title: locationsb[n][4],
          map: map
        });
        google.maps.event.addListener(marker1, 'click', (function(marker1, n) {
          return function() {
            infowindow.setContent(locationsb[n][0]);
            infowindow.open(map, marker1);
          }
        })(marker1, n));
      }

    </script>
  </body>

</html>