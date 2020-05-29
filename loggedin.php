<?php
session_start();

$users = [];

$username = ($SESSION['username']);
$password = ($SESSION['password']);

$database = new PDO("mysql:host=localhost;dbname=php-labb-2", "root", "root");

if (isset($_POST['updateUser'])) {
  updateUser();
}

if (isset($_POST['updateactivities'])) {
  updateActivities();
}

//Funktion att koppla till databasen samt skriva ut vår data
function displayData()
{
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
                                        -- LIMIT 10");
    $getUsers->execute();

    $users = $getUsers->fetchAll();

    //Skriv ut datan från vår databas
    foreach ($users as $user) {
      //spara id:t till vår andra select nedan
      $id = $user['user_id'];
      //Börja skriva ut en tabell
      echo '<tr>';
      echo '<td>' . $user['full_name'] . '</td>';
      if ($user['paid_the_fee']) {
        echo '<td>Ja';
      } else {
        echo '<td>Nej';
      }
      echo '<form method="post">
                  <input type="hidden" name="user_id" value="' . $id . '">';
      if ($user['paid_the_fee']) {
        echo '<input type="submit" value="❌" name="updateUser">';
      } else {
        echo '<input type="submit" value="✔️" name="updateUser">';
      }
      echo '</form></td>';
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
      foreach ($activities as $activity) {
        if (count($activities) > 1 && count($activities) > $loop) {
          echo $activity['activity_name'] . ', ';
          $loop++;
        } else {
          echo $activity['activity_name'];
        }
      }
      echo '</td>';
      //Skriver ut en knapp för att ta bort användare
      echo '<td><form method="post">
                  <input type="hidden" name="userId" value="' . $id . '">
                  <input type="submit" value="❌" name="deleteUser">
                </form></td>';
      // Fetcha alla aktiveter
      $getAll = $database->prepare("SELECT *
                                      FROM activities");
      $getAll->execute();
      $allActivities = $getAll->fetchAll();
      echo '<td><form method="post">
      <input type="hidden" name="user_id" value="' . $id . '">';
      foreach ($allActivities as $allActivity) {
        echo '<input type="checkbox" id="' . $allActivity['activity_id'] . '" name="activities[]" value="' . $allActivity['activity_id'] . '">
              <label for="' . $allActivity['activity_name'] . '" style="font-size: 15px">' . $allActivity['activity_name'] . '</label>';
      }
      echo '<input type="submit" name="updateactivities">';
      echo '</form></td>';
      //Avsluta vår html tabell
      echo '</tr>';
    }
  }
  //om vi får något error
  catch (PDOException $e) {
    echo "⚠️ Connection failed: " . $e->getMessage();
  }
  $database = null;
}

function addNewUser($username, $password, $firstName, $lastName, $payment, $activity1, $activity2, $activity3)
{
  //logga in i databasen
  $dbHostname = "localhost";
  $dbUsername = "root";
  $dbPassword = "root";

  //Räkna hur många aktiviteter användaren är med i
  $activities = array();
  if ($activity1) {
    array_push($activities, $activity1);
  }
  if ($activity2) {
    array_push($activities, $activity2);
  }
  if ($activity3) {
    array_push($activities, $activity3);
  }
  $numberOfActivities = count($activities);

  try {
    //koppla upp oss till databasen
    $database = new PDO("mysql:host=$dbHostname;dbname=php-labb-2", $dbUsername, $dbPassword);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //lägg till användaren
    $database->exec("INSERT
                       INTO `users`(`username`, `password`, `full_name`, `paid_the_fee`, `number_of_activities`)
                       VALUES ('$username', '$password', '$firstName $lastName', '$payment', '$numberOfActivities')");

    //lägg till användarens aktiviteter
    if ($numberOfActivities > 0) {
      $userId = $database->lastInsertId();
      foreach ($activities as $activity) {
        $database->exec("INSERT
                               INTO `user_activities`(`ua_user`, `ua_activity`)
                               VALUES ('$userId', '$activity')");
      }
    }

    //ladda om sidan så användaren kan se resultatet direkt
    header("Refresh:0");
  } catch (PDOException $e) {

    echo "⚠️ Connection failed: " . $e->getMessage();
  }
}

//Ta bort en användare
function deleteUser($userId)
{
  $hostname = "localhost";
  $username = "root";
  $password = "root";
  try {
    //koppla upp oss till databasen
    $database = new PDO("mysql:host=$hostname;dbname=php-labb-2", $username, $password);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //ta bort användaren
    $database->exec("DELETE `users`, `user_activities`
                             FROM `users`
                             INNER JOIN `user_activities` ON `users`.`user_id` = `user_activities`.`ua_user`
                             WHERE `user_id`= $userId");

    //ladda om sidan så användaren kan se resultatet direkt
    header("Refresh:0");
  } catch (PDOException $e) {
    echo "⚠️ Connection failed: " . $e->getMessage();
  }
}

function updateUser()
{
  $hostname = "localhost";
  $username = "root";
  $password = "root";

  try {
    $database = new PDO("mysql:host=$hostname;dbname=php-labb-2", $username, $password);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "UPDATE users SET paid_the_fee = !paid_the_fee WHERE user_id = :user_id";
    $sth = $database->prepare($sql);
    $sth->execute([':user_id' => $_POST['user_id']]);
  } catch (PDOException $e) {

    echo "⚠️ Connection failed: " . $e->getMessage();
  }
}

function updateActivities()
{
  $hostname = "localhost";
  $username = "root";
  $password = "root";

  try {
    $database = new PDO("mysql:host=$hostname;dbname=php-labb-2", $username, $password);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "DELETE FROM user_activities WHERE ua_user = :user_id";
    $sth = $database->prepare($sql);
    $sth->execute([':user_id' => $_POST['user_id']]);
  } catch (PDOException $e) {
    echo "⚠️ Connection failed: " . $e->getMessage();
  }
  try {
    $database = new PDO("mysql:host=$hostname;dbname=php-labb-2", $username, $password);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($_POST['activities'] as $activity) {
      $sql = "INSERT INTO user_activities (ua_user, ua_activity) VALUES (:user, :activity)";
      $sth = $database->prepare($sql);
      $sth->execute([':user' => $_POST['user_id'], ':activity' => $activity]);
    }
  } catch (PDOException $e) {
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
  <header style="position:absolute;left:0;top:0;overflow:auto;width:100%;height:10px;background-color: rgb(255, 92, 92);"></header>

  <div class="welcomeAdmin">
    <?php
    // Skriver ut alla användare
    if (isset($users)) {
      echo 'Välkommen!';
    }
    ?>

    <table>
      <?php
      //visa datan högst upp
      if (isset($users)) {
        displayData();
      }
      ?>
    </table>

    <!--Knapp för att skapa nya användare under datan-->
    <form method="post">
      <input type="submit" value="Lägg till ny användare" name="newUser">
    </form>

    <?php
    //formulär för att skapa ny användare
    if (isset($_POST['newUser'])) {
      echo '
        <form method="post">
      
        <p>Användarnamn och lösenord*</p>
        <input type="text" placeholder="Användarnamn" name="username" required>
        <input type="password" placeholder="Lösenord" name="password" required>
      
        <p>Namn*</p>
        <input type="text" placeholder="Förnamn" name="firstName" required>
        <input type="text" placeholder="Efternamn" name="lastName" required>
      
        <p>Har betalt medlemsavgiften?*</p>
        <select name="payment">
            <option value="0">Nej</option>
            <option value="1">Ja</option>
        </select>
      
        <p>Aktiviteter</p>
        <input type="checkbox" name="activity1" value="1">
        <label for="activity1">Fotboll</label>
        <input type="checkbox" name="activity2" value="2">
        <label for="activity2">Skidor</label>
        <input type="checkbox" name="activity3" value="3">
        <label for="activity3">Gymnastik</label>

        <br>
        <p>* = Obligatoriskt</p>
        <br>
      
        <input type="submit" value="Skicka" name="addNewUser">
        </form>
        ';
    }

    //kallar på funktionen att skapa den nya användaren
    if (isset($_POST['addNewUser'])) {
      unset($_POST['newUser']);
      addNewUser($_POST['username'], $_POST['password'], $_POST['firstName'], $_POST['lastName'], $_POST['payment'], $_POST['activity1'], $_POST['activity2'], $_POST['activity3']);
    }

    //kallar på funktionen att ta bort användaren
    if (isset($_POST['deleteUser'])) {
      deleteUser($_POST['userId']);
    }
    ?>

    <!--Knapp att logga ut från sidan-->
    <form action="index.php" method="post">
      <input type="submit" name="log-out" value="Logga ut">
    </form>

  </div>


  <?php
  // Log out 
  if (isset($_POST['log-out'])) {
    session_destroy();
    header('location: index.php');
  }
  ?>
</body>

</html>