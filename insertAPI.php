    <?php
        require "database/config.php";

        //Establish the connection
        $conn = mysqli_init();
        mysqli_ssl_set($conn,NULL,NULL,$sslcert,NULL,NULL);
        if(!mysqli_real_connect($conn, $host, $username, $password, $db_name, 3306, MYSQLI_CLIENT_SSL)){
            die('Failed to connect to MySQL: '.mysqli_connect_error());
        }


        $res = mysqli_query($conn, "SHOW TABLES LIKE 'Temperatures'");
   
        if (mysqli_num_rows($res) <= 0) {
            //Create table if it does not exist
            //print("la table n'existe pas, on demande la création.")
            $sql = file_get_contents("database/schema_final.sql");
            if(!mysqli_query($conn, $sql)){
                die('La création de la table températures a échoué');
            } // sinon la création de la table a fonctionné
        } 

        $res = mysqli_query($conn, "INSERT INTO Temperatures (dateYMD, temperature, humidity) VALUES ('20240111', '27.4', '42.0')");        
        

        //Close the connection
        mysqli_close($conn);

    ?>


