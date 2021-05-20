<!DOCTYPE html>
<html>

<head>
    <title>Recherche d'une espace'</title>
    <style type="text/css">
        .container {
            height: 700px;
            width: 70%;
        }

        #map {
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <center> Espaces Verts en Ile de France </center>

        <div>
            <input id="search" type="text" placeholder="Paris" class="nom_espace"/>
            <input id="submit" type="button" value="Geocode" />
        </div>

        <div id="map"></div>

        <?php 
            include 'acceuil.php';
            $tableau = find_all_adresses();
        ?>

        <script>
            var locations = <?php echo json_encode($tableau) ?>;
            $("#search").autocomplete({
                source: ['Senegal', 'Paris', 'Hollande']
            });
        
        </script>

        <script type="text/javascript">
            function loadMap() {
                var map = new google.maps.Map(document.getElementById("map"), {
                    center: {
                        lat: 48.856614,
                        lng: 2.3522219
                    },
                    zoom: 8,
                });

                const geocoder = new google.maps.Geocoder();
                document.getElementById("submit").addEventListener("click", () => {
                    geocodeAddress(geocoder, map);
                });
            }

            function geocodeAddress(geocoder, resultsMap) {
                const address = document.getElementById("address").value;
                geocoder.geocode({
                    address: address
                }, (results, status) => {
                    if (status === "OK") {
                        resultsMap.setCenter(results[0].geometry.location);
                        new google.maps.Marker({
                            map: resultsMap,
                            position: results[0].geometry.location,
                        });
                    } else {
                        alert("Geocode was not successful for the following reason: " + status);
                    }
                });
            }
        </script>
    </div>

    <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBAPupxRydmJA1OE3I7zb-bmD2cCEh8wVk&callback=loadMap">
    </script>

</body>

</html>