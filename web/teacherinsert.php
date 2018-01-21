<?php
  require '../dbconnector.php';
  require '../libvplanbot.php';
  include '../includes/head.php';
  session_start();
?>
  <title> WebEditVplanBot </title>
</head>

<body>
  <?php
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
    try {

      $teacherIN = $pdo->prepare(
      "INSERT INTO `teachers` (abbr, full_name, gender) VALUES (:abbr, :full_name, :gender)"
      );

      if (empty($_POST['tabbrin'])) {
        throw new Exception ('Feld Lehrerkürzel nicht ausgefüllt');
      }
      if (empty($_POST['tfullnamein'])) {
        throw new Exception ('Feld Name nicht ausgefüllt');
      }
      if (empty($_POST['tgenderin'])) {
        throw new Exception ('Feld Geschlecht ist nicht ausgefüllt');
      }
      else {
        switch ($_POST['tgenderin']) {
          case 'nth':
            $gender = 0;
            break;
          case 'm':
            $gender = 1;
            break;
          case 'f':
            $gender = 2;
            break;
          default:
            throw new Exception('Falsche Angabe zum Geschlecht');
            break;
        }
      }

      $teacherIN->execute(
        array('abbr'=> $_POST['tabbrin'],
              'full_name'=> $_POST['tfullnamein'],
              'gender'=> $gender,
          )
        );
       header("Location: wedit.php");

    } catch (Exception $e) {
        echo 'Unfortunately something went wrong. Please try again later!';
    }

?>
