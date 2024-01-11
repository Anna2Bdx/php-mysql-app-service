<?php
    echo ("hello world!");
    require "database/config.php";
    $request_method = $_SERVER["REQUEST_METHOD"];
    echo("on est au moins là");

    function getTemp()
    {
        echo ("on est entré dans la fonction");
        //Establish the connection
        $conn = mysqli_init();
        mysqli_ssl_set($conn,NULL,NULL,$sslcert,NULL,NULL);
        if(!mysqli_real_connect($conn, $host, $username, $password, $db_name, 3306, MYSQLI_CLIENT_SSL)){
            //die('Failed to connect to MySQL: '.mysqli_connect_error());
            echo("on n'a pas réussi à se connecter à la base");
        }
        $query = "SELECT * FROM Temperatures";
        $response = array();
        echo("on balance la requête");
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;
        }
        //header('Content-Type: application/json');
        //echo json_encode($response, JSON_PRETTY_PRINT);
    }

    switch($request_method)
    {
        case 'GET':
            echo ("on a bien reconnu la demande de GET");
            getTemp();
            break;
        default:
            // Requête invalide
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

    
?>