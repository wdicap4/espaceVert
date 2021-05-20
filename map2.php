<!DOCTYPE html>
<html>

<head>
    <title> Espaces Verts Map </title>
    <!--<script type"text/javascript" src="./mapapi.js"></script>-->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style type="text/css">
        .container {
            height: 700px;
            width: 100%;
        }

        #map {
            height: 100%;
        }
    </style>
</head>


<body>

<header>
      <nav>
        <ul>
          <li id="logo"><a href="#">Espaces verts</a></li>
          
          <li><a href="#contact">Nous contacter</a></li>
        </ul>
      </nav>
      
        


    </header>
    <div><button></button></div><br><br><br></div>
    <?php
    
    include 'acceuil.php';
//************************** DATES ********************************** */
    if (isset($_POST["submit_date"])) {
        $date_min = $_POST["date_min"];
        $date_max = $_POST["date_max"];
        $search_result = date_comparaison($date_min, $date_max);
        
    }

//************************** PERIMETRES ********************************** */
    if (isset($_POST["submit_perimetre"])) {

        if  ($_POST["perimetre_max"] != NULL && $_POST["perimetre_min"] == NULL) {
            $perimetre = intval($_POST["perimetre_max"]);
            $search_result = perimeter_comparaison_max($perimetre);
            
        }

        elseif ($_POST["perimetre_max"] == NULL && $_POST["perimetre_min"] != NULL) {
            $perimetre = intval($_POST["perimetre_min"]);
            $search_result = perimeter_comparaison_min($perimetre);
        }

        else {
            echo "<script>alert(\"N'entrez pas min et max en meme temps\")</script>";
        }
    }

//************************** SURFACES ********************************** */
    if (isset($_POST["submit_surface"])) {

        if  ($_POST["surface_max"] != NULL && $_POST["surface_min"] == NULL) {
            $surface = intval($_POST["surface_max"]);
            $search_result = superficie_comparaison_max($surface);
        }

        elseif ($_POST["surface_max"] == NULL && $_POST["surface_min"] != NULL) {
            $surface = intval($_POST["surface_min"]);
            $search_result = superficie_comparaison_min($surface);
        }
        
        else {
            echo "<script>alert(\"N'entrez pas min et max en meme temps\")</script>";
        }
    }

//************************** OUVETURE HORAIRES ********************************** */
    if (isset($_POST["submit_ouverture"])) {
        $ouverture = $_POST["ouverture"];
        $search_result = ouverture_24h($ouverture);
    }

    
//************************** CLOTURES ********************************** */
    if (isset($_POST["submit_cloture"])) {
        $cloture = $_POST["cloture"];
        $search_result = presence_cloture($cloture);
    }

    ?>

   

    <br>
        <input id="address" type="textbox" value="Paris" />
        <input id="submit" type="button" value="Geocode" />
    </br><br>
    

    <form method="POST">
        <label> Entrer date min et max : </label><br>
        <input type="number" min="1700" max="2021" step="1" name="date_min" value="1990"/>
        <input type="number" min="1700" max="2021" step="1" name="date_max" value="2000" />
        <input type="submit" name="submit_date" value="Envoyer...">
    </form><br>
    
    <form method="POST">
        <label for="input">Entrer un périmètre en mètre :</label><br>
        <input type="number" name="perimetre_max"  placeholder="Perimetre superieur "/>
        <input type="number" name="perimetre_min"  placeholder="Perimetre inferieur "/>
        <input type="submit" name="submit_perimetre" value="Envoyer...">
    </form><br>

    <form method="POST">
        <label > Entrer une surface en mètre carré :</label><br>
        <input type="number" name="surface_max"  placeholder="Surface superieure "/>
        <input type="number" name="surface_min"  placeholder="Surface inferieure "/>
        <input type="submit" name="submit_surface" value="Envoyer...">
    </form><br>

    <form method="POST">    
        <label for="select"> Ouverture 24h/24h</label><br>
        <select id="select" name="ouverture" required>
            <option>selectionnez</option>
			<option value="Oui">Oui</option>
			<option value="Non">Non</option>
		</select>
        <input type="submit" name="submit_ouverture" value="Envoyer...">
    </form><br>

    
    <form method="POST">    
        <label for="select">Présence de clotures</label>
        <select id="select" name="cloture" required>
            <option>selectionnez</option>
			<option value="Oui">Oui</option>
			<option value="Non">Non</option>
		</select>
        <input type="submit" name="submit_cloture" value="Envoyer...">
    </form><br><br>


    <div class="container">
      <h1><center> Espaces Verts en Ile de France </center><br></h1>  
        <div id="map"></div>
        <script type="text/javascript">

            function loadMap() {  
                var locations = <?php echo json_encode($search_result) ?>;
                var map = new google.maps.Map(document.getElementById("map"), {
                    center: {
                        lat: 48.856614,
                        lng: 2.3522219
                    },
                    zoom: 8,
                });
                
                for (i=0; i<locations.length; i++ ){
                    const geocoder = new google.maps.Geocoder();
                    const infowindow = new google.maps.InfoWindow();
                    locate = locations[i][1];
                    mytitle = locations[i][0];
                    geocodeAddress(geocoder, map, infowindow, locate, mytitle);
                }
            }

            function geocodeAddress(geocoder, resultsMap, infowindow ,locate, mytitle) {
                const address = locate;
                geocoder.geocode({
                    address: address
                }, (results, status) => {
                    if (status === "OK") {
                        resultsMap.setZoom(12);
                        resultsMap.setCenter(results[0].geometry.location);
                        const marker = new google.maps.Marker({
                            //title: results[0].formatted_address,
                            map: resultsMap,
                            position: results[0].geometry.location,
                        });
                        //infowindow.setContent(results[0].formatted_address);
                        infowindow.setContent(mytitle);
                        infowindow.open(map, marker);
                    }
                });
            }
        </script>
    </div>


    <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBAPupxRydmJA1OE3I7zb-bmD2cCEh8wVk&callback=loadMap">
    </script>

</body>

<footer>
      
      <div id="deuxiemeTrait"></div>
      <div id="copyrightEtIcons">
        <div id="copyright">
          <span>© Pelli wilfried(); 2021</span>
        </div>
        <div id="icons">
          <a href="http://www.twitter.fr"><i class="fa fa-twitter"></i></a>
          <a href="http://www.facebook.fr"><i class="fa fa-facebook"></i></a>
          <a href="http://www.instagram.com"><i class="fa fa-instagram"></i></a>

        </div>
      </div>

    </footer>

</html>