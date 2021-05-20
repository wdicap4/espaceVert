


<!DOCTYPE html>
<html >
<body>
    <form action="import.php" method="GET" target="_blank">
        <button type="submit" value="Pelouse" name="pelouse">Importer vos données</button>
    </form>

<?php 
    require 'vendor/autoload.php';
    $URL1 = "https://opendata.paris.fr/api/records/1.0/search/?dataset=espaces_verts&q=&rows=800";

//VERIFICATION SI LA BASE DE DONNEE EXISTE DEJA ;
    function verif_database () {
        //$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        $client = new MongoDB\Client("mongodb://localhost:27017");
        
        foreach ($client->listDatabases() as $databaseInfo) {
            //var_dump($databaseInfo);
            if ($databaseInfo['name'] == "projet_ynov"){
                $verif = 1;
                return $verif;
            }
            $verif = 0;
        }
        return $verif;
    }

//CREATION DE LA BASE DE DONNEES PROJET_YNOV et de la COLLECTION ESPACE_VERTS
    function create_connection () {
        $client = new MongoDB\Client("mongodb://localhost:27017");
        if ($client){
            $database = $client -> projet_ynov;
           
            if($database){
                $collection = $database -> createCollection("espaces_verts");
            }
        }
    }

//CONNECTION A LA BASE DE DONNEE et COLLECTION
    function connection (){
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $var = verif_database();
        if($var == 0) {
            create_connection();
            $mycollection = $client->projet_ynov->espaces_verts;
            return $mycollection;
        }
        else {
            $mycollection = $client->projet_ynov->espaces_verts;
            return $mycollection;
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// ALL ADDRESSES  ////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function find_all_adresses (){
        $collection=connection();
        $count = 0;
        $search_result = array ();

        $cursor = $collection->find();
        foreach ( $cursor as $doc1)
        {
            //echo "$id: ";
            //var_dump( $value );
            if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_numero']!=null && $doc1[0]['fields']['adresse_libellevoie']) {
                $nom_espace = $doc1[0]['fields']['nom_ev'];
                $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                $result = array();
                $result [] = $nom_espace;
                $result [] = $adresse;

                $search_result[$count] = $result;
                $count++;
            }
        }
        return $search_result;

    }

    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// ALL CATEGORIES  ////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function find_all_categories (){
        $collection=connection();
        $count = 0;
        $search_result = array ();

        $cursor = $collection-> find(['0.fields.categorie' =>  'Pelouse' ]);
        foreach ( $cursor as $doc1)
        {
            $categorie = $doc1[0]['fields']['categorie'];
            $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
            $result = array();
            $result [] = $categorie;
            array_push($search_result, $categorie);
        }
        return $search_result;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// DATES  ////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //Comparaison de dates 
    function date_comparaison ($date_petit, $date_long, $categorie_params){
        $search_result = array();
        $count = 0;
        $collection=connection();
        $date = array('x' => array('$gt' => $date_petit, '$lt' => $date_long));
        $where = array ('$and' => array( array('0.fields.annee_ouverture' => array('$gt' => $date_petit, '$lt' => $date_long)), array('0.fields.categorie' => $categorie_params) ));
        $find = $collection -> find($where);
        foreach ($find as $doc1) {
            $taille = sizeof($doc1[0]['fields']['geom']['coordinates'][0]);
            
            if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_numero']!=null && $doc1[0]['fields']['adresse_libellevoie']) {
                $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                $nom_espace = $doc1[0]['fields']['nom_ev'];
                $date = $doc1[0]['fields']['annee_ouverture'];
                $categorie = $doc1[0]['fields']['categorie'];
                //$superficie = $doc1[0]['fields']['surface_totale_reelle'];
                //$cloture = $doc1[0]['fields']['presence_cloture'];
//                $ouverture = $doc1[0]['fields']['ouvert_ferme'];

                $result = array();
                $result [] = $nom_espace;
                $result [] = $adresse;
                $result [] = $date;

                $search_result[$count] = $result;
                $count++;
            }
            //array_push($search_result, $adresse);
        }
        return $search_result;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// PERIMETRES  ////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function perimeter_comparaison_min ($perimeter_params,  $categorie_params) {
        $collection=connection();
        $search_result = array();
        $count = 0;
        $where = array ('$and' => array( array('0.fields.perimeter' => array('$lt' => $perimeter_params)), array('0.fields.categorie' => $categorie_params) ));
        $find = $collection -> find($where);
        foreach ($find as $doc1) {

           if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_typevoie']!=null && $doc1[0]['fields']['adresse_libellevoie']){
                
                $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                $nom_espace = $doc1[0]['fields']['nom_ev'];
                $perimeter = $doc1[0]['fields']['perimeter'];

                $result = array();
                $result [] = $nom_espace;
                $result [] = $adresse;
                $result [] = $perimeter;

                $search_result[$count] = $result;
                $count++;
            }
        }
        return $search_result;
    }

    function perimeter_comparaison_max ($perimeter_params,  $categorie_params) {
        $collection=connection();
        $search_result = array ();
        $count = 0;
        $where = array ('$and' => array( array('0.fields.perimeter' => array('$gt' => $perimeter_params) ) , array('0.fields.categorie' => $categorie_params) ));
        $find = $collection -> find($where); 
        foreach ($find as $doc1) {
            if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_typevoie']!=null && $doc1[0]['fields']['adresse_libellevoie']){

                $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                $nom_espace = $doc1[0]['fields']['nom_ev'];
                $perimeter = $doc1[0]['fields']['perimeter'];

                $result = array();
                $result [] = $nom_espace;
                $result [] = $adresse;
                $result [] = $perimeter;

                $search_result[$count] = $result;
                $count++;
            }
        }
        return $search_result;
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// SUPERFICIES/ SURFACES ////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function superficie_comparaison_max ($superfice_params,  $categorie_params) {
        $collection=connection();
        $search_result = array();
        $count = 0;

        $where = array ('$and' => array( array('0.fields.surface_totale_reelle' =>  array('$gt' => $superfice_params) ) , array('0.fields.categorie' => $categorie_params) ));
        $find = $collection -> find($where);
        if($find != NULL) {
            foreach ($find as $doc1) {
                if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_typevoie']!=null && $doc1[0]['fields']['adresse_libellevoie']){

                   $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                    $nom_espace = $doc1[0]['fields']['nom_ev'];
                    $superficie = $doc1[0]['fields']['surface_totale_reelle'];

                    $result = array();
                    $result [] = $nom_espace;
                    $result [] = $adresse;
                    $result [] = $superficie;

                    $search_result[$count] = $result;
                    $count++;
                }
            }
        }
    }

    function superficie_comparaison_min ($superfice_params,  $categorie_params) {
        $collection=connection();
        $search_result = array();
        $count = 0;

        $where = array ('$and' => array( array('0.fields.surface_totale_reelle' =>  array('$lt' => $superfice_params) ) , array('0.fields.categorie' => $categorie_params) ));
        $find = $collection -> find($where);
        if($find != NULL) {
            foreach ($find as $doc1) {
                if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_typevoie']!=null && $doc1[0]['fields']['adresse_libellevoie']){

                   $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                    $nom_espace = $doc1[0]['fields']['nom_ev'];
                    $superficie = $doc1[0]['fields']['surface_totale_reelle'];

                    $result = array();
                    $result [] = $nom_espace;
                    $result [] = $adresse;
                    $result [] = $superficie;

                    $search_result[$count] = $result;
                    $count++;
                }
            }
        }
    }

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// CLOTURES ////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function presence_cloture ($cloture_params,  $categorie_params){
        $collection=connection();
        $search_result = array();
        $count = 0;
        if($cloture_params == "Oui"){
            $where = array ('$and' => array( array('0.fields.presence_cloture' =>  $cloture_params ) , array('0.fields.categorie' => $categorie_params) ));
            $find = $collection -> find($where);
            foreach ($find as $doc1) {
                if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_typevoie']!=null && $doc1[0]['fields']['adresse_libellevoie']){

                    $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                   $nom_espace = $doc1[0]['fields']['nom_ev'];
                    $cloture = $doc1[0]['fields']['presence_cloture'];

                    $result = array();
                    $result [] = $nom_espace;
                    $result [] = $adresse;
                    $result [] = $cloture;

                    $search_result[$count] = $result;
                    $count++;
                }
            }
            return $search_result;
        }
        if ($cloture_params == "Non") {

            $where = array ('$and' => array( array('0.fields.presence_cloture' =>  $cloture_params ) , array('0.fields.categorie' => $categorie_params) ));
            $find = $collection -> find($where);
            foreach ($find as $doc1) { 
                if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_typevoie']!=null && $doc1[0]['fields']['adresse_libellevoie']){

                    $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                    $nom_espace = $doc1[0]['fields']['nom_ev'];
                    $cloture = $doc1[0]['fields']['presence_cloture'];

                    $result = array();
                    $result [] = $nom_espace;
                    $result [] = $adresse;
                    $result [] = $cloture;

                    $search_result[$count] = $result;
                    $count++;
                }
            }
            return $search_result;
        }
    }



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// OUVERTURES HORAIRES ////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    function ouverture_24h ($ouverture_params,  $categorie_params){
        $collection=connection();
        $search_result = array();
        $count = 0;
        //OUI
        if($ouverture_params == "Oui") {
            $where = array ('$and' => array( array('0.fields.ouvert_ferme' =>  $ouverture_params ) , array('0.fields.categorie' => $categorie_params) ));
            $find = $collection -> find($where);
            foreach ($find as $doc1) { 
                if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_typevoie']!=null && $doc1[0]['fields']['adresse_libellevoie']){

                    $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                    $nom_espace = $doc1[0]['fields']['nom_ev'];
                    $ouverture = $doc1[0]['fields']['ouvert_ferme'];

                    $result = array();
                    $result [] = $nom_espace;
                    $result [] = $adresse;
                    $result [] = $ouverture;

                    $search_result[$count] = $result;
                    $count++;
                }
            }
            return $search_result;
        }
        //NON
        else {
            $ouverture_params = "Non";
            $where = array ('$and' => array( array('0.fields.ouvert_ferme' =>  $ouverture_params ) , array('0.fields.categorie' => $categorie_params) ));
            $find = $collection -> find($where);
            foreach ($find as $doc1) { 
                if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_typevoie']!=null && $doc1[0]['fields']['adresse_libellevoie']){

                    $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                    $nom_espace = $doc1[0]['fields']['nom_ev'];
                    $ouverture = $doc1[0]['fields']['ouvert_ferme'];

                    $result = array();
                    $result [] = $nom_espace;
                    $result [] = $adresse;
                    $result [] = $ouverture;

                    $search_result[$count] = $result;
                    $count++;
                }
            }
            return $search_result;
        }
    }

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// TYPOLOGIE ESPACES ////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    function typologie_espace ($typologie_params) {
        $collection=connection();
        $search_result = array();
        $count = 0;

        $find = $collection -> find(['0.fields.categorie' =>  $typologie_params ]);

        foreach ($find as $doc1) { 
              if($doc1[0]['fields']['adresse_numero'] != null && $doc1[0]['fields']['adresse_typevoie']!=null && $doc1[0]['fields']['adresse_libellevoie']){
                $adresse = $doc1[0]['fields']['adresse_numero']." ".$doc1[0]['fields']['adresse_typevoie']." ".$doc1[0]['fields']['adresse_libellevoie'].", ".$doc1[0]['fields']['adresse_codepostal'];
                $nom_espace = $doc1[0]['fields']['nom_ev'];
                $categorie = $doc1[0]['fields']['categorie'];

                $result = array();
                $result [] = $nom_espace;
                $result [] = $adresse;
                $result [] = $categorie;

                $search_result[$count] = $result;
                $count++;
            }
        }
        return $search_result;
    }

    if(isset($_GET["submit"])){
        import_json_data($URL1);
    }
?>

</body>
</html>


