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
            if (isset($_GET["idMaison"])){
                $maison = (int)$_GET["idMaison"];
            } else {
                $maison = 1;
            }
            
            $query = "";
            $mode = (int)$_GET["mode"];
            switch($mode) {
                case 1: 
                    // affichage des 30 dernier jours
                    $query = "SELECT * FROM Temperatures where idMaison=".$maison." ORDER BY dateYMD DESC LIMIT 30";
                    break;
                case 2:
                    // affichage d'un point par mois (avec calcul de la moyenne des moyennes, du min et du max)
                    $query = "SELECT MAX(idMaison) AS idMaison, MAX(dateYMD) AS dateYMD, dateYear, dateMonth,AVG(minT) AS minT, AVG(maxT) AS maxT,AVG(avgT) AS avgT,AVG(minH) AS minH,AVG(maxH) AS maxH,AVG(avgH) AS avgH FROM Temperatures /*WHERE idMaison=1*/ GROUP BY dateYear, dateMonth";
                default:
                    // affichage de toutes les data
                    $query = "SELECT * FROM Temperatures where idMaison=".$maison;
                    break;
            }
            
            $response = array();
            $result = mysqli_query($conn, $query);
            while($row = mysqli_fetch_array($result))
            {
                //$response[] = $row;
                $response[] = array(
                    'maison' => $row['idMaison'],
                    'date' => $row['dateYMD'],
                    'dateYear' => $row['dateYear'],
                    'dateMonth' => $row['dateMonth'],
                    'minT' => (float)$row['minT'],
                    'maxT' => (float)$row['maxT'],
                    'avgT' => (float)$row['avgT'],
                    'minH' => (float)$row['minH'],
                    'maxH' => (float)$row['maxH'],
                    'avgH' => (float)$row['avgH']
                );
            }
            header('Content-Type: application/json');
            echo ('{"measures":');
            echo json_encode($response, JSON_PRETTY_PRINT);  
            echo ('}');  
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
		$minT = $_POST["minT"];
        $maxT = $_POST["maxT"];
        $avgT = $_POST["avgT"];
        $minH = $_POST["minH"];
        $maxH = $_POST["maxH"];
        $avgH = $_POST["avgH"];
		
		
		echo $query="INSERT INTO Temperatures( idMaison, dateYMD, dateYear, dateMonth, dateDay, minT, maxT, avgT, minH, maxH, avgH) VALUES(".$idMaison.", '".$dateYMD."', ".substr($dateYMD,0,4).", ".substr($dateYMD,5,2).", ".substr($dateYMD,-2).", ".$minT.", ".$maxT.", ".$avgT.", ".$minH.", ".$maxH.", ".$avgH." )";
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
    - Modes de lecture :
        Un mode ALL (par défaut)
        Un mode les 30 derniers jours enregistrés
        Un mode 1 point par mois (avec calcul de la moyenne group by "year/month")
    - ajouter plusieurs modes (dernier mois, 1 valeur par mois (moyenne), avec ALL si non valorisé)
    - valider les paramètres d'appel du GET et du POST
    - mettre ce code dans un git privé beaucoup plus propre
    - redéployer une stack complète et déployer ce code
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