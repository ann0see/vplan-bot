<?php
require '../dbconnector.php';

require '../libvplanbot.php';

include '../includes/head.php';

session_start();
?>
  <title> Edit teacher </title>
</head>

<body>
  <?php

if (logged_in()): ?>
  <h1> Lehrer bearbeiten</h1>
  <?php
  try
    {
    if (empty($_GET['parm']))
      {
      $parm = 'undef';
      }
      else
      {
      $parm = $_GET['parm'];
      }

    switch ($parm)
      {
    case 'insert':
      if (empty($_POST['tid']) || !is_numeric($_POST['tid']))
        {
        throw new Exception("Tid wurde nicht korrekt übertragen.");
        }
        else
        {
        $tid = $_POST['tid'];
        }
      $teacherUP = $pdo->prepare("UPDATE teachers SET abbr = :abbr, full_name = :full_name, gender = :gender WHERE ID = :id");
      if (empty($_POST['tabbrup']))
        {
        throw new Exception('Feld Lehrerkürzel nicht ausgefüllt');
        }

      if (empty($_POST['tfullnameup']))
        {
        throw new Exception('Feld Name nicht ausgefüllt');
        }

      if (empty($_POST['tgenderup']))
        {
        throw new Exception('Feld Geschlecht ist nicht ausgefüllt');
        }

      switch ($_POST['tgenderup'])
        {
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

      // update the Database

      $teacherUP->execute(
        array (
        'abbr' => $_POST['tabbrup'],
        'full_name' => $_POST['tfullnameup'],
        'gender' => $gender,
        'id' => $tid,
      )
      );
      echo 'Update erfolgreich.';
      echo '<meta http-equiv="refresh" content="1; URL=wedit.php" />';

      break;

      case 'delask':
      $token = md5(uniqid(mt_rand(), true));
      $_SESSION['token'] = $token;

      if (empty($_GET['tid']) || !is_numeric($_GET['tid'])) {
        throw new Exception("tid not set");
      }
      echo '<form action="editteacher.php?parm=deleteteacher" method="post">
      <p>
      <label> please enter <code>JA, ICH will löschen!!</code> </label>
      <input name="confirmation" type="text" value="nein, ich will nicht löschen!" />
      <input type="hidden" value="'.htmlspecialchars($_GET['tid']).'" name="tid" />
      <input type="hidden" value="'.htmlspecialchars($token).'"  name="token" />
      </p>
      <p>
      <input type="Submit" value="löschen now!"
      </p>
      </form>';

      break;
    case 'deleteteacher':
    
      if ($_SESSION['token'] === $_POST['token']) {
        unset($_SESSION['token']);
        if (!empty($_POST['tid']) || is_numeric($_POST['tid'])) {
          $delteacher = $pdo->prepare("DELETE FROM teachers WHERE id= :tid LIMIT 1");
          $delteacher->execute(
            array('tid'=>$_POST['tid'])
          );
         echo 'Gelöscht';
        }
      } else {
        unset($_SESSION['token']);
        throw new \Exception("Token ist falsch. Haben Sie diese Aktion schon einmal durchgeführt? [?]", 1);
      }
    break;
    default:
    if (empty($_GET['tid']) || !is_numeric($_GET['tid']))
      {
      throw new Exception("Tid wurde nicht korrekt übertragen.");
      }
      else
      {
      $tid = $_GET['tid'];
      }
      $gtstmt = $pdo->prepare("
        SELECT full_name, gender, abbr FROM teachers WHERE ID = :id
      ");
      $gtstmt->execute(
        array(
          'id'=>$tid
        )
      );

      $myTeacher = $gtstmt->fetch();



      echo '
      <div>
        <form action="editteacher.php?parm=insert" method="post">
          <label for="tabbrup"> Lehrerkürzel </label>
          <input id="tabbrup" type="text" name="tabbrup" required="required" value="'. htmlspecialchars($myTeacher['abbr']) .'" />
          <p>
          <fieldset>
          <legend >
          Geschlecht
          </legend>';
          $gnthselected = $gmselected = $gfselected = '';
          switch ($myTeacher['gender']) {
            case '1':
                $gmselected = 'checked="checked"';
              break;

            case '2':
                $gfselected = 'checked="checked"';
              break;


            default:
                $gnthselected = 'checked="checked"';
              break;
          }
          echo'
            <input type="radio" value="nth" name="tgenderup" required="required" ' . $gnthselected .' />
            <label> Keine Angabe </label>
            <input type="radio" value="m" name="tgenderup" '. $gmselected . ' />
            <label> Herr </label>
            <input type="radio" value="f" name="tgenderup"  '. $gfselected . ' />
            <label> Frau </label>
          </fieldset>
          </p>
          <label for="tfull_nameup" > Nachname </label>
          <input id="tfull_nameup" type="text" name="tfullnameup" required="required" value="'. htmlspecialchars($myTeacher['full_name']) .'"/> <br /> <br />
          <input type="hidden" name="tid" value="'.htmlspecialchars($tid).'"/>
          <p>
          <input type="Submit" value="Daten updaten" />
          </p>
        </form>
      </div>
     ';

      echo '<a href=editteacher.php?parm=delask&tid=' . htmlspecialchars($tid) . '>Delete</a>';
      break;
      }
    }

  catch (Exception $e)
    {
    echo 'Unfortunately something went wrong. Please try again later!';
    echo $e->getMessage();
    }

?>

<?php
else: ?>
<h1> Do's jemand nich` ang`meldet, odr? </h1>
<?php
endif; ?>
<?php
include '../includes/footer.php';

?>
