<?php
    echo ("Starting SQL Script assessment \n");
    
    $request_method = $_SERVER["REQUEST_METHOD"];
    //echo("on est au moins là");

    function applyScript()
    {
        require "database/config.php";
        echo ("GET method detected \n");
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
                echo ("Authentication success \n");
                break;
            }
        } 

        if ($auth) {
            // réupération du paramètre passé dans l'URL pour le GET
            $script = $_GET["script"];
            
            // execute script if exists
            $sql = file_get_contents("database/".$script.".sql");
            if(!mysqli_query($conn, $sql)){
                die('Script execution failed \n');
            }
            echo ("Script executed successfully. \n");
        }
         else {
            header('Content-Type: text/plain');
            http_response_code(204);
            echo "wrong authentication";
        }
    }


    switch($request_method)
    {
        case 'GET':
            //echo ("on a bien reconnu la demande de GET");
            applyScript();
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }


?>