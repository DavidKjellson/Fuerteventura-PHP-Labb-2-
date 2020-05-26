<?php
session_start(); 

$users = []; 

$username= ($SESSION['username']);
$password= ($SESSION['password']);

$database = new PDO("mysql:host=localhost;dbname=php-labb-2", "root","root"); 

  //Funktion att koppla till databasen samt skriva ut vår data
  function displayData(){
      //Kontot i phpmyadmin
      $hostname = "localhost";
      $username = "root";
      $password = "root";

      try {
        //koppla upp oss till databasen
        $database = new PDO("mysql:host=$hostname;dbname=php-labb-2", $username, $password);
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //Skriv ut kategorierna av vår tabell som ska användas
        echo '<tr>
                <th>Namn</th>
                <th>Betalat medlemsavgift?</th>
                <th>Aktiviteter</th>
              </tr>';

        //Fetcha våra användare
        $getUsers = $database->prepare("SELECT `full_name`, `paid_the_fee`, `user_id`
                                        FROM `users`
                                        WHERE `username`!='admin' -- vi vill inte att admin ska kunna ta bort sitt eget konto
                                        ORDER BY `paid_the_fee` DESC, `full_name` ASC 
                                        LIMIT 10");
        $getUsers->execute();
                
        $users = $getUsers->fetchAll();

        //Skriv ut datan från vår databas
        foreach ($users as $user) {
          //spara id:t till vår andra select nedan
          $id = $user['user_id'];
          //Börja skriva ut en tabell
          echo '<tr>';
          echo '<td>' . $user['full_name'] . '</td>';
          if($user['paid_the_fee']){
            echo '<td>Ja</td>';
          }
          else{
            echo '<td>Nej</td>';
          }
          //Fetcha våra användares aktiviteter
          $getActivities = $database->prepare("SELECT `activity_name`
                                              FROM `users`
                                              INNER JOIN `user_activities` ON `user_activities`.`ua_user` = `users`.`user_id`
                                              INNER JOIN `activities` ON `activities`.`activity_id` = `user_activities`.`ua_activity`
                                              WHERE `user_id`= $id -- använd id:t vi sparade innan
                                              ORDER BY `activity_name` ASC");
          $getActivities->execute();
          $activities = $getActivities->fetchAll();
          //skriv ut aktiviteterna
          echo '<td>';
          //Variabel för att veta om vi behöver skriva ut ett kommatecken eller ej
          $loop = 1;
          foreach($activities as $activity){
            if(count($activities) > 1 && count($activities) > $loop){
              echo $activity['activity_name'] . ', ';
              $loop++;
            }
            else{
              echo $activity['activity_name'];
            }
          }
          echo '</td>';
          //Skriver ut en knapp för att ta bort användare
          echo '<td><form method="post">
                  <input type="hidden" name="userId" value="' . $id . '">
                  <input type="submit" value="❌" name="deleteUser">
                </form></td>';
          //Avsluta vår html tabell
          echo '</tr>';
        }
      }
      //om vi får något error
      catch(PDOException $e){
        echo "⚠️ Connection failed: " . $e->getMessage();
      }
        $database = null;
  }

//Ta bort en användare
function deleteUser($userId){
    $hostname = "localhost";
    $username = "admin";
    $password = "admin";
    try {
            //koppla upp oss till databasen
            $database = new PDO("mysql:host=$hostname;dbname=php-labb-2", $username, $password);
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            //ta bort användaren
            $database->exec("DELETE `users`, `user_activities`
                             FROM `users`
                             INNER JOIN `user_activities` ON `users`.`user_id` = `user_activities`.`ua_user`
                             WHERE `user_id`= $userId");
                             
    }
    catch(PDOException $e){
        
        echo "⚠️ Connection failed: " . $e->getMessage();
    
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
  <title>Document</title>
</head>
<body>

  <div class="welcomeAdmin">
    <?php
    // Skriver ut alla användare
    if(isset($users)){
        echo 'Välkommen!';
    }
    ?>
    
    <table>
      <?php
      if (isset($users)) {
        displayData();
      }
      ?>
    </table>
    
    <?php
    if (isset($_POST['deleteUser'])) {
      deleteUser($_POST['userId']);
    }
    ?>
    
    <form action="index.php" method="post">
      <input type="submit" name="log-out" value="Logga ut">
    </form>

  </div>

    
  <?php
  // Log out 
  if(isset($_POST['log-out'])){ 
    session_destroy();
    header('location: index.php');
  }
  ?>

</body>
</html>




