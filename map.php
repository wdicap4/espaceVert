<!DOCTYPE html>
<html>

<head>
    <title> Espaces Verts Map </title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style type="text/css">
        .container {
            height: 450px;
        }

        #map {
            height: 100%;
            width: 70%;
            border: 5px solid green;
            margin-left: auto;
            margin-right: auto;
            display: block;
        }
    </style>
</head>


<body style="background-color: #E7E895; ">
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
    
    $categorie_params = $_GET["pelouse"];
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    include 'acceuil.php';
    $target = find_all_categories();
    $result = array_unique($target);
    //var_dump($result);
    //var_dump($categorie_params);

    //************************** DATES ********************************** */
    if (isset($_POST["submit_date"])) {
        $date_min = $_POST["date_min"];
        $date_max = $_POST["date_max"];
        $search_result = date_comparaison($date_min, $date_max, $categorie_params);
        if ($search_result == NULL) {
            echo "Aucun resultat <br>";
        }
    }

    //************************** PERIMETRES ********************************** */
    if (isset($_POST["submit_perimetre"])) {

        if ($_POST["perimetre_max"] != NULL && $_POST["perimetre_min"] == NULL) {
            $perimetre = intval($_POST["perimetre_max"]);
            $search_result = perimeter_comparaison_max($perimetre, $categorie_params);
            if ($search_result == NULL) {
                echo "Aucun resultat <br>";
            }
        } elseif ($_POST["perimetre_max"] == NULL && $_POST["perimetre_min"] != NULL) {
            $perimetre = intval($_POST["perimetre_min"]);
            $search_result = perimeter_comparaison_min($perimetre, $categorie_params);
            if ($search_result == NULL) {
                echo "Aucun resultat <br>";
            }
        } else {
            echo "<script>alert(\"N'entrez pas min et max en meme temps\")</script>";
        }
    }

    //************************** SURFACES ********************************** */
    if (isset($_POST["submit_surface"])) {

        if ($_POST["surface_max"] != NULL && $_POST["surface_min"] == NULL) {
            $surface = intval($_POST["surface_max"]);
            $search_result = superficie_comparaison_max($surface, $categorie_params);
            if ($search_result == NULL) {
                echo "Aucun resultat <br>";
            }
        } elseif ($_POST["surface_max"] == NULL && $_POST["surface_min"] != NULL) {
            $surface = intval($_POST["surface_min"]);
            $search_result = superficie_comparaison_min($surface, $categorie_params);
            if ($search_result == NULL) {
                echo "Aucun resultat <br>";
            }
        } else {
            echo "<script>alert(\"N'entrez pas min et max en meme temps\")</script>";
        }
    }

    //************************** OUVETURE HORAIRES ********************************** */
    if (isset($_POST["submit_ouverture"])) {
        $ouverture = $_POST["ouverture"];
        $search_result = ouverture_24h($ouverture, $categorie_params);
        if ($search_result == NULL) {
            echo "Aucun resultat <br>";
        }
    }


    //************************** CLOTURES ********************************** */
    if (isset($_POST["submit_cloture"])) {
        $cloture = $_POST["cloture"];
        $search_result = presence_cloture($cloture, $categorie_params);
        if ($search_result == NULL) {
            echo "Aucun resultat <br>";
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ?>

    <div class="container">
        <center> Espaces Verts en Ile de France </center>
        <div id="map"></div>
        <div class="input">
            <form method="POST">
                <label style="color: #5D5D19;font-weight: bold;font-size: 16px; padding: 15px;;"> Entrer date min et max </label> <br>
                <input type="number" min="1700" max="2021" step="1" name="date_min" value="1990" />
                <input type="number" min="1700" max="2021" step="1" name="date_max" value="2000" />
                <input type="submit" name="submit_date" value="Envoyer...">
            </form> <br>

            <form method="POST">
                <label for="input" style="color: #5D5D19;font-weight: bold;font-size: 16px; padding: 15px;">Entrer un périmètre en mètre </label> <br>
                <input type="number" name="perimetre_max" placeholder="Perimetre superieur " />
                <input type="number" name="perimetre_min" placeholder="Perimetre inferieur " />
                <input type="submit" name="submit_perimetre" value="Envoyer...">
            </form> <br>

            <form method="POST">
                <label style="color: #5D5D19;font-weight: bold;font-size: 16px; padding: 15px;"> Entrer une surface en mètre carré </label> <br>
                <input type="number" name="surface_max" placeholder="Surface superieure " />
                <input type="number" name="surface_min" placeholder="Surface inferieure " />
                <input type="submit" name="submit_surface" value="Envoyer...">
            </form> <br>

            <form method="POST">
                <label for="select" style="color: #5D5D19;font-weight: bold;font-size: 16px; padding: 15px;"> Ouverture 24h/24h</label> <br>
                <select id="select" name="ouverture" required>
                    <option>selectionnez</option>
                    <option value="Oui">Oui</option>
                    <option value="Non">Non</option>
                </select>
                <input type="submit" name="submit_ouverture" value="Envoyer...">
            </form> <br>


            <form method="POST">
                <label for="select" style="color: #5D5D19;font-weight: bold;font-size: 16px; padding: 15px;">Présence de clotures</label><br>
                <select id="select" name="cloture" required>
                    <option>selectionnez</option>
                    <option value="Oui">Oui</option>
                    <option value="Non">Non</option>
                </select>
                <input type="submit" name="submit_cloture" value="Envoyer...">
            </form><br>
        </div>

        
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

                for (i = 0; i < locations.length; i++) {
                    const geocoder = new google.maps.Geocoder();
                    const infowindow = new google.maps.InfoWindow();
                    locate = locations[i][1];
                    mytitle = locations[i][0];
                    geocodeAddress(geocoder, map, infowindow, locate, mytitle);
                }
            }

            function geocodeAddress(geocoder, resultsMap, infowindow, locate, mytitle) {
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
                            icon: 'arbre.png'
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


</html>