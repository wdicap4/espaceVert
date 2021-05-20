


<!DOCTYPE html>
<html >
<body>
    <form method="GET">
        <input  type="submit" value="Importer les données de l'API" name="submit"/>
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

    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client -> projet_ynov;
    $delete = $database ->drop();
    $collection=connection();

    $data = file_get_contents($URL1);
    $json_data = json_decode($data, true);
    //var_dump($json_data);
        foreach ($json_data['records'] as $document) {
            $collection->insertOne([$document]);
    }


//Import des données JSON vers MONGODB DATABASE
    function import_json_data ($url) {
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $database = $client -> projet_ynov;
        $delete = $database ->drop();
        $collection=connection();

        $data = file_get_contents($url);
        $json_data = json_decode($data, true);
        //var_dump($json_data);
            foreach ($json_data['records'] as $document) {
                $collection->insertOne([$document]);
        }
    }

    if(isset($_GET["submit"])){
        import_json_data($URL1);
    }
?>

</body>
</html>


