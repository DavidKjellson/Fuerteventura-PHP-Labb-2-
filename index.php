<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css">
  
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
  <title>Svalan</title>
</head>
<body>
  

<?php
// Log in
if(isset($_POST['signin'])){

  $username = $_POST['username'];
  $password = $_POST['password'];

  $_SESSION['username'] = $username;
  $_SESSION['password'] = $password;

  $database = new PDO("mysql:host=localhost;dbname=php-labb-2", "root","root");
?>

<div class="wrong">
  <?php
if($username=="user" && $password=="user"){
  header('location: loggedin.php');
} 
else {
  echo 'Fel användarnamn eller lösenord';
}
}
?>
</div>


<div class="sidenav">
         <div class="login-main-text">
           <form action="index.php" method="post">
            <h1>Välkommen till idrottsföreningen Svalan </h1>
            <p>Inloggningssidan för admins</p>
         </div>
      </div>
      <div class="main">
         <div class="col-md-6 col-sm-12">
            <div class="login-form">

                  <div class="form-group">
                     <label>Username</label>
                     <input type="text" class="form-control" name="username" placeholder="Username">
                  </div>

                   <div class="form-group">
                     <label>Password</label>
                     <input type="password" name="password" class="form-control" placeholder="**********">
                  </div> 
                  <button type="submit" name="signin"  value="SIGN IN" class="btn btn-black">Login</button> 
               </form>
               </div>
            </div>
         </div>
</body>
</html>







<!-- <div class="createAccunt"> 

<h1>Create Account Here</h1>
<form action="index.php" method="post">
<input type="text" name="username" placeholder="username"><br><br>
<input type="text" name="email" placeholder="example@example.com"><br><br>
<input type="text" name="password" placeholder="**********"><br><br>

<input type="submit" name="signup" value="SIGN UP">
</form>
</div> -->


<!-- <div class="signIn">

<h1>Log In Here</h1>
<form action="index.php" method="post">
<input type="text" name="email" placeholder="example@example.com"><br><br>
<input type="text" name="password" placeholder="**********"><br><br>
<input type="submit" name="signin" value="SIGN IN">

</form>
</div> -->

<!-- 

//SIGN UP FORM !!!!!!!!!!!!
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


// if(isset($_POST['signup'])){
//   // include 'db.php';

// try {
//   $pdoConnect = new PDO("mysql:host=localhost;dbname=php-labb-2", "root","root");
// } catch (PDOException $exc) {
//   echo $exc->getMessage();
//   exit();
// }

//  $username = $_POST['username'];
//  $email = $_POST['email'];
//  $password = $_POST['password'];


//  $pdoQuery = "INSERT INTO `users`(`username`, `password`, `email`) VALUES (:username, :password, :email)";

//  $pdoResult = $pdoConnect->prepare($pdoQuery);

//  $pdoExec = $pdoResult->execute(array(":username"=>$username, ":password"=>$password, ":email"=>$email));

//  if($pdoExec) 
//  {
//    echo 'Data Inserted';
//  } else {
//    echo 'Data Not inserted';
//  }
// } -->
