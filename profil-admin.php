<?php

    function connectToDatabase(){
        $hostname = "localhost";
        $username = "admin";
        $password = "admin";

        try {

                $database = new PDO("mysql:host=$hostname;dbname=php-labb-2", $username, $password);
            
                $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                echo '<tr>
                        <th>Namn</th>
                        <th>Betalat medlemsavgift?</th>
                        <th>Aktiviteter</th>
                      </tr>';

                //Fetch
                $getUsers = $database->prepare("SELECT `full_name`, `paid_the_fee`, `activity_name` , `number_of_activities`
                                                FROM `users` 
                                                INNER JOIN `user_activities` ON `user_activities`.`ua_user` = `users`.`user_id`
                                                INNER JOIN `activities` ON `activities`.`activity_id` = `user_activities`.`ua_activity`
                                                -- WHERE
                                                ORDER BY `paid_the_fee` DESC, `full_name` ASC 
                                                LIMIT 10");
                $getUsers->execute();

                $users = $getUsers->fetchAll();
                foreach ($users as $user) {
                    echo '<tr>';
                    echo '<td>' . $user['full_name'] . '</td>';
                    if($user['paid_the_fee']){
                        echo '<td>Ja</td>';
                    }
                    else{
                        echo '<td>Nej</td>';
                    }
                    echo '<td>' . $user['activity_name'] . '</td>';
                    echo '</tr>';
                }

                //var_dump($users);

            }
         catch(PDOException $e)
     
            {
     
            echo "Connection failed: " . $e->getMessage();
     
            }
     
            $database = null;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboration 2</title>
</head>
<body>
    <div>

        <h2>Labb 2</h2>
        <p>Låt oss testa att hämta data från databasen!</p>
        <form method="post">
            <input type="submit" value="Connect to Database" name="connectToDatabase">
        </form>

        <table>
            <?php
            if (isset($_POST['connectToDatabase'])) {
                connectToDatabase();
            }
            ?>
        </table>
    </div>

</body>
</html>