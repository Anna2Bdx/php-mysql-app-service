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
        $query = "SELECT * FROM Temperatures";
        $response = array();
        //echo("on balance la requête");
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result))
        {
            $response[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($response, JSON_PRETTY_PRINT);
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
		$date = $_POST["dateYMD"];
		$temp = $_POST["temp"];
		$humi = $_POST["humi"];
		
		echo $query="INSERT INTO Temperatures( dateYMD, temperature, humidity) VALUES('".$date."', '".$temp."', '".$humi."')";
		if(mysqli_query($conn, $query))
		{
			$response=array(
				'status' => 1,
				'status_message' =>'Donnée ajoutée avec succès.'
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

    
?>