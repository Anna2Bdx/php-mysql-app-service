<?php
    //echo ("hello world!");
    
    $request_method = $_SERVER["REQUEST_METHOD"];
    //echo("on est au moins là");

    function getLogs()
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
                    // affichage des 50 derniers logs
                    $query = "SELECT * FROM (SELECT * FROM Logs where idMaison=".$maison." ORDER BY logtimestamp DESC LIMIT 50) lastT ORDER BY logtimestamp ASC";
                    break;
                case 2 :
                    // affichage des 50 derniers logs uniquement pour les logs de niveau 0 / majeurs
                    $query = "SELECT * FROM (SELECT * FROM Logs where idMaison=".$maison." AND loglevel=0 ORDER BY logtimestamp DESC LIMIT 50) lastT ORDER BY logtimestamp ASC";
                    break;
                default:
                    // affichage de toutes les data
                    $query = "SELECT * FROM Logs where idMaison=".$maison;
                    break;
            }
            
            $response = array();
            $result = mysqli_query($conn, $query);
            while($row = mysqli_fetch_array($result))
            {
                //$response[] = $row;
                $response[] = array(
                    'id' => (int)$row['id'],
                    'maison' => $row['idMaison'],
                    'TimeStamp' => $row['logtimestamp'],
                    'level' => (int)$row['loglevel'],
                    'content' => $row['logcontent']
                );
            }
            header('Content-Type: application/json');
            echo json_encode($response, JSON_PRETTY_PRINT);    
        } else {
            header('Content-Type: text/plain');
            http_response_code(204);
            echo "wrong authentication";
        }
    }

    function addLogs()
	{
        require "database/config.php";
        $auth = false;
       
        /* // debug
        foreach (getallheaders() as $name => $value) {
            echo "$name: $value\n";
        }*/

        // récupération du header pour l'authentification
        foreach (getallheaders() as $name => $value) { 
            if ($name=="Authent" && $value==$auth_key) {
                $auth=true;
                echo("Authentication success \n");
                break;
            }
        } 

        if ($auth) {
            //echo ("on est entré dans la fonction");
            //Establish the connection
            $conn = mysqli_init();
            mysqli_ssl_set($conn,NULL,NULL,$sslcert,NULL,NULL);
            if(!mysqli_real_connect($conn, $host, $username, $password, $db_name, 3306, MYSQLI_CLIENT_SSL)){
                die('Failed to connect to MySQL: '.mysqli_connect_error());
            }
            $idMaison = (int)$_POST["idMaison"];
            $logtimestamp = $_POST["timestamp"];
            $loglevel = (int) $_POST["level"];
            $logcontent = $_POST["content"];
            
            echo("hello world");

            /*echo $query="INSERT INTO Logs( idMaison, loglevel, logcontent) VALUES(".$idMaison.", ".$loglevel.", '".$logcontent."')";
            if(mysqli_query($conn, $query))
            {
                $response=array(
                    'status' => 1,
                    'status_message' =>'\n Data successfully added.'
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
            echo json_encode($response);*/
        } else {
            header('Content-Type: text/plain');
            http_response_code(204);
            echo "wrong authentication";
        }
	}

    switch($request_method)
    {
        case 'GET':
            //echo ("on a bien reconnu la demande de GET");
            getLogs();
            break;
        case 'POST':
            //echo ("on a bien reconnu la demande de GET");
            addLog();
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

    /* 
    /!\ penser à ajouter l'ID de maison dans toutes les routes et les structures de tables !!
    to do pour la partie Histo :
    - valider les paramètres d'appel du POST
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
    idem pour la partie unknown, logs, alarmes
    */
?>