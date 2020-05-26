<?php
session_start(); 

$users = []; 

$username= ($SESSION['username']);
$password= ($SESSION['password']);

$database = new PDO("mysql:host=localhost;dbname=php-labb-2", "root","root"); 
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

<form action="index.php" method="post">
<input type="submit" name="log-out" value="Logga ut">
</form>
<br>


<div class="welcomeAdmin">
<?php
// Skriver ut alla användare
    if(isset($users)){
        echo 'Välkommen!';
    }
    ?>
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




