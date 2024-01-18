<?php
    //echo ("hello world!");
    
    $request_method = $_SERVER["REQUEST_METHOD"];
    //echo("on est au moins là");

    function getTemp()
    {
        require "database/config.php";
        //echo ("on est entré dans la fonction");
        //Establish the connection
        $conn = mysqli_init();
        mysqli_ssl_set($conn,NULL,NULL,$sslcert,NULL,NULL);
        if(!mysqli_real_connect($conn, $host, $username, $password, $db_name, 3306, MYSQLI_CLIENT_SSL)){
            die('Failed to connect to MySQL: '.mysqli_connect_error());
        }

        $auth = false;
       
        /* // debug
        foreach (getallheaders() as $name => $value) {
            echo "$name: $value\n";
        }*/

        // récupération du header pour l'authentification
        /// TODO : passer la valeur de la clé en paramètre global dans la solution
        foreach (getallheaders() as $name => $value) { 
            if ($name=="Authent" && $value==$auth_key) {
                $auth=true;
                break;
            }
        } 

        if ($auth) {
            // réupération du paramètre passé dans l'URL pour le GET
            $date = $_GET["dateYMD"];
            $query = "SELECT * FROM Temperatures where dateYMD=".$date;
            $response = array();
            $result = mysqli_query($conn, $query);
            while($row = mysqli_fetch_array($result))
            {
                $response[] = $row;
            }
            header('Content-Type: application/json');
            echo json_encode($response, JSON_PRETTY_PRINT);    
        } else {
            header('Content-Type: text/plain');
            http_response_code(204);
            echo "wrong authentication";
        }
    }

    function addTemp()
	{
		require "database/config.php";
        //echo ("on est entré dans la fonction");
        //Establish the connection
        $conn = mysqli_init();
        mysqli_ssl_set($conn,NULL,NULL,$sslcert,NULL,NULL);
        if(!mysqli_real_connect($conn, $host, $username, $password, $db_name, 3306, MYSQLI_CLIENT_SSL)){
            die('Failed to connect to MySQL: '.mysqli_connect_error());
        }
        $idMaison = $_POST["idMaison"];
		$dateYMD = $_POST["dateYMD"];
		$temp = $_POST["temp"];
		$humi = $_POST["humi"];
		
		echo $query="INSERT INTO Temperatures( idMaison, dateYMD, temperature, humidity) VALUES(".$idMaison.", '".$dateYMD."', '".$temp."', '".$humi."')";
		if(mysqli_query($conn, $query))
		{
			$response=array(
				'status' => 1,
				'status_message' =>'Data successfully added.'
			);
		}
		else
		{
			$response=array(
				'status' => 0,
				'status_message' =>'ERREUR!.'. mysqli_error($conn)
			);
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}

    switch($request_method)
    {
        case 'GET':
            //echo ("on a bien reconnu la demande de GET");
            getTemp();
            break;
        case 'POST':
            //echo ("on a bien reconnu la demande de GET");
            addTemp();
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

    /* 
    /!\ penser à ajouter l'ID de maison dans toutes les routes et les structures de tables !!
    to do pour la partie Histo :
    - garder une page de visualisation des données
    - essayer de supprimer les doublons dans le code json_encode
    - ne renvoyer que les 12 derniers mois dans le Get (mais complet dans la page de visualisation)
    - faire en sorte que la structure de la table corresponde à ce que l'on attend (similaire au format actuel)
    - faire en sorte que l'insertion corresonde en terme de format
    - enlever tout ce qui sert à rien
    - mettre ce code dans un git privé beaucoup plus propre
    - redéployer une stack complète et déployer ce code
    - faire une reprise d'historique à partir du json !!
    - ajouter une route dans l'API monitoring qui pointe vers ces routes (au moins pour la lecture)
    to do pour la partie intra day :
    - faire un nouveau fichier php
    - faire une structure de table qui corresponde
    - faire une route d'insertion
    - faire une route de calcul (nouveau fichier pour calculer les moyennes)
    - faire une route de lecture
    - faire une route de truncate table
    - faire une page de visualisation
    idem pour la partie unknown, logs, alarmes
    */
?>