<?php
  require '../dbconnector.php';
  require '../libvplanbot.php';
  include '../includes/head.php';
  session_start();
?>
  <title> WebEditVplanBot </title>
</head>

<body>
  <?php if (logged_in()) : ?>
  <h1> Datenbankeingabe, Lehrer </h1>
  <?php


    $ttstmt = $pdo->query(
      "SELECT `ID`, `abbr`, `full_name`, `gender` FROM `teachers` ORDER BY abbr"
    );
    echo'<table border= 1 solid black> <thead> <tr> <th> Kürzel </th> <th> Name </th> <th> Bearbeiten </th> </tr> </thead>';

    while($row = $ttstmt->fetch()) {
          echo '<tr> <td>' . htmlspecialchars($row['abbr']) . '</td>';

          echo'<td>';
          switch ($row['gender']) {
            case '1':
                echo 'Herr ';
              break;
            case '2':
                echo 'Frau ';
              break;
            default:
                echo 'Lehrer ';
              break;
          }

          echo htmlspecialchars($row['full_name']) . '</td>';
          echo '<td> <a href="editteacher.php?tid=' . htmlspecialchars($row['ID']) . '" style="text-decoration: none; color: red "> Edit </a> </td> </tr>';

    }
    echo '</table>';

    echo '<h3> Lehrer hinzufügen </h3>';
    ?>

    <div>
      <form action="teacherinsert.php" method="post">
        <label for="tabbrIN"> Lehrerkürzel </label>
        <input id="tabbrIN" type="text" name="tabbrin" required="required" />
        <p>  <fieldset>
          <input type="radio" value="nth" name="tgenderin" required="required" />
          <label> Keine Angabe </label>
          <input type="radio" value="m" name="tgenderin" />
          <label> Herr </label>
          <input type="radio" value="f" name="tgenderin" />
          <label> Frau </label>
        </fieldset> </p>
        <label for="tfull_nameIN" > Nachname </label>
        <input id="tfull_nameIN" type="text" name="tfullnamein" required="required" /> <br /> <br />
        <input type="Submit" value="Daten einfügen" />
      </form>
    </div>



<?php else : ?>
<h1> ANMELDUNG FEHLT!!! </h1>
<?php endif; ?>

<?php
include '../includes/footer.php';
?>
