<?php
session_start();
require '../dbconnector.php';
require '../libvplanbot.php'
?>

<!DOCTYPE html>

<head>
  <meta charset="utf-8">
  <title> Index </title>
</head>

<body>
  <?php
    if (empty($_POST['user']) && empty($_POST['pw'])) {
      die('ey Diggah! füll ma die Felder aus, Brudahh');
    }
    else {
      $username = $_POST["user"];
      $passwort = $_POST["pw"];
    }
    $pwhash = '$2y$10$ZW0VdFrqe0r.opwF2jZbI.PIYzuFa1qAFXsLtMJCCmMtG8d9PLlD6';
    if ($username === 'Dccwe' && password_verify($passwort, $pwhash)) {
      echo "You're logged in";
      $useragent = md5($_SERVER['HTTP_USER_AGENT']);
      $_SESSION['UA'] = $useragent;
      $_SESSION['Logged_in'] = TRUE;
      header("Location: wedit.php");

    } else {
      echo 'Wrong p@$$w0r1, please try again later';
    }

  ?>
</body>
