<?php
require 'dbconnector.php';
require 'discordconnector.php';
require 'libvplanbot.php';
require __DIR__ . '/config/config.php';


// seems to be dangerous?
$sitesVisited  = array(); // an array, which includes all the sites, we have already visited

$emergencystop = 0; // if the loop runs too many times, we stop the program
///// Database prepare statements following

$daycomins = $pdo->prepare("INSERT INTO `daycom` (hash, com_date) VALUE (:hash, :com_date)");
$daycomsel = $pdo->prepare("SELECT hash FROM `daycom` WHERE hash = :hash");


$vsave      = $pdo->prepare("INSERT INTO `vertretungen` (klasse,stunde,s_lehrer,lehrer,s_fach,fach,s_raum,raum,v_date,bem) VALUES (:klasse,:stunde,:s_lehrer,:lehrer,:s_fach,:fach,:s_raum,:raum,:v_date,:bem)");
$chkausfall = $pdo->prepare("SELECT lehrer, fach, raum, bem, verl FROM `vertretungen`
  WHERE
  klasse = :klasse
  AND
  s_lehrer = :s_lehrer
  AND
  s_fach = :s_fach
  AND
  s_raum = :s_raum
  AND
  stunde = :stunde
  AND
  v_date = :v_date
  ORDER BY rc_date
  DESC
  LIMIT 1
");

$getrt = $pdo->prepare("SELECT `full_name`, `gender` FROM `teachers`
    WHERE
    `abbr` LIKE  :abbr
    LIMIT 1
");

$getrs = $pdo->prepare("SELECT `full_name` FROM `subjects`
        WHERE
        `abbr` LIKE  :abbr
");

///// END prepared stmts
///// CLEARUP DB
$pdo->query('DELETE FROM vertretungen WHERE v_date < NOW() - INTERVAL 3 MONTH;');
$pdo->query('DELETE FROM daycom WHERE com_date < NOW() - INTERVAL 3 MONTH');

//start program
$vecho = '';

foreach (SUBFOLDERS as $subfolder) { // for each untis web frame
    $subfile = 'subst_001.htm'; // set the subfile
    $siteurl = BASEURL . $subfolder . $subfile; // create url
    while (!in_array($subfolder . $subfile, $sitesVisited)) { // while: run if we have not already visited this page
        // make the links and get the page
        $siteurl = BASEURL . $subfolder . $subfile; // set the site
        echo 'Parsing ' . $siteurl . ' ...' . PHP_EOL;

        $vplancontent = get_site_content($siteurl);

        $vertdate = get_site_date($vplancontent); // the first thing we get from preg match is our date

        $additionalInfo = get_daycom($vplancontent);
        if (empty($additionalInfo)) {
            echo 'No comment found for this day' . PHP_EOL;
        } else {
            $daycomment = '';
            foreach ($additionalInfo as $daycom) {
                $daycomment .= $daycom[1];
            }


            $daycomhash = md5($daycomment . $vertdate->format('Y-m-d'));
            $daycomsel->execute(array(
                'hash' => $daycomhash,
            ));
            $selresult = $daycomsel->fetch();
            if (!$selresult || !$selresult['hash'] == $daycomhash) {
                $daycomins->execute(array(
                    'hash' => $daycomhash,
                    'com_date' => $vertdate->format('Y-m-d'),
                ));
                $todisc[] = '**__BEMERKUNG FÜR DEN ' . $vertdate->format('d.m.Y') . '__**' . PHP_EOL . replace_mdown($daycomment);
            }
        }



        if (parse_site_content($vplancontent)) { // the given class is found on the site

            echo 'Found something!' . PHP_EOL;
            $classrows = parse_site_content($vplancontent);

            foreach ($classrows as $vfield) { // foreach row
                // first make the variables "beautiful"
                $subject    = $vfield[4];
                $wassubject = $vfield[5];
                $teacher    = $vfield[3];
                $wasteacher = $vfield[2];
                $hour       = $vfield[1];
                $room       = $vfield[6];
                $wasroom    = $vfield[7];
                $comment    = $vfield[8];
                // convert abbreviations into complete words

                // get real teachers name
                $getrt->execute(array(
                    'abbr' => $teacher
                ));

                if ($dbteacher = $getrt->fetch()) {
                    $rteachername = $dbteacher["full_name"];
                    $rtgender     = $dbteacher["gender"];
                } else {
                    $rteachername = $teacher;
                    $rtgender     = -1;
                }

                $getrt->execute(array(
                    'abbr' => $wasteacher
                ));

                if ($dbteacher = $getrt->fetch()) {
                    $wrteachername = $dbteacher["full_name"];
                    $wrtgender     = $dbteacher["gender"];
                } else {
                    $wrteachername = $wasteacher;
                    $wrtgender     = -1;
                }

                //get real subjects name

                $getrs->execute(array(
                    'abbr' => $subject
                ));

                if ($dbsubject = $getrs->fetch()) {
                    $rsubjectname = $dbsubject["full_name"];
                } else {
                    $rsubjectname = $subject;
                }

                $getrs->execute(array(
                    'abbr' => $wassubject
                ));

                if ($dbsubject = $getrs->fetch()) {
                    $wrsubjectname = $dbsubject["full_name"];
                } else {
                    $wrsubjectname = $wassubject;
                }

                $day = $dayow[$vertdate->format('w')];
                if ((preg_match('/.*---.*/', $teacher) || preg_match('/[\s]*[\s]*/', $teacher)) && preg_match('/.*---.*/', $subject) && preg_match('/.*---.*/', $room)) {
                    $vecho = 'Am ' . $day . ', den ' . $vertdate->format('d.m.Y') . ' enfällt ' . htmlspecialchars($wrsubjectname) . ' bei ';
                    $vecho .= tgender($wrtgender);
                    $vecho .= htmlspecialchars($wrteachername) . ' in Stunde ' . htmlspecialchars($hour) . '.';
                // if there are three ---, we know that we do not have school
                } elseif ($room != $wasroom && $wasteacher == $teacher && $subject == $wassubject) {
                    $vecho = 'Am ' . $day . ', den ' . $vertdate->format('d.m.Y') . ' in Stunde ' . htmlspecialchars($hour) . ' findet ' . htmlspecialchars($rsubjectname) . ' bei ';
                    $vecho .= tgender($wrtgender);
                    $vecho .= htmlspecialchars($rteachername) . ' einmalig in Raum ' . htmlspecialchars($room) . ' statt. ';
                // if the room changes, but everything else stays
                } elseif ($room == $vfield[7] && $wasteacher != $teacher && $subject == $wassubject) {
                    $vecho = 'Am ' . $day . ', den ' . $vertdate->format('d.m.Y') . " wird " . htmlspecialchars($rsubjectname) . " in Stunde " . htmlspecialchars($hour) . " durch ";
                    $vecho .= tgender($rtgender);
                    $vecho .= htmlspecialchars($rteachername) . ' vertreten.';
                // if the teacher changes, but everything else stays
                } elseif ($room == $vfield[7] && $wasteacher == $teacher && $subject == $wassubject) {
                    $vecho = 'Am ' . $day . ', den ' . $vertdate->format('d.m.Y') . ' findet ' . htmlspecialchars($rsubjectname) . ' in Stunde ' . htmlspecialchars($hour) . ' statt.';
                // everything is the same. Why is it shown??? Ok. just output it.
                } else {
                    $vecho = 'Am ' . $day . ', den ' . $vertdate->format('d.m.Y') . ' wird das Fach ' . htmlspecialchars($wrsubjectname) . ', Stunde ' . htmlspecialchars($hour) . ' bei ';
                    $vecho .= tgender($wrtgender);
                    $vecho .= htmlspecialchars($wrteachername) . ' (Raum ' . htmlspecialchars($wasroom) . ') vertreten. An diesem Tag wird deshalb ';
                    $vecho .= tgender($rtgender);
                    $vecho .= htmlspecialchars($rteachername) . ' in Raum ' . htmlspecialchars($room);
                    // if everything changes


                    if (empty($subject)) { // if the subject is empty
                        $vecho .= ' kein Fach';
                    } elseif ($subject == '???') {
                        $vecho .= ' Unbekanntes Fach';
                    } else {
                        $vecho .= ' das Fach ' . htmlspecialchars($rsubjectname);
                    }
                    $vecho .= ' unterrichten.';

                    if (!empty($comment) || !empty($transfer)) {
                        $vecho .= PHP_EOL . PHP_EOL . '• • • **Weitere Infos** • • •'  . PHP_EOL;


                        if (!empty($comment)) {
                            $vecho .= '**Bemerkung:** ' . replace_mdown($comment);
                        }
                    }
                }
                echo 'Got: "' . htmlspecialchars($vecho) . '"' . PHP_EOL;
                $chkausfall->execute(array(
                    'klasse' => OURCLASS,
                    'stunde' => $hour,
                    's_lehrer' => $wasteacher,
                    's_fach' => $wassubject,
                    's_raum' => $wasroom,
                    'v_date' => $vertdate->format("Y-m-d"),
                ));

                $vrow = $chkausfall->fetch(PDO::FETCH_ASSOC); // get last db entry from vertretungen
                if (!$vrow || !($vrow['lehrer'] == $teacher && $vrow['fach'] == $subject && $vrow['raum'] == $room && $vrow['bem'] == md5($comment))) { // if this thing was already insert to our db, we do not have to send it to discord.
                  // if db empty or if output is different then prepare to send to dc
                  $todisc[] = $vecho;
                    $vsave->execute(array(
                      'klasse' => OURCLASS,
                      'stunde' => $hour,
                      's_lehrer' => $wasteacher,
                      'lehrer' => $teacher,
                      'fach' => $subject,
                      's_fach' => $wassubject,
                      'raum' => $room,
                      's_raum' => $wasroom,
                      'v_date' => $vertdate->format("Y-m-d"),
                      'bem' => md5($comment),
                    ));
                } else {
                    // now insert it into our database, sothat we do not get a 2nd message if already sent

                    echo 'Already sent to Discord.' . PHP_EOL;
                    // check the database, if the "vertretung was already output" -> already sent to discord
                }
            }


        }
        // save the current parsed site in an array
        $sitesVisited[] = $subfolder . $subfile;
        // prepare next site
        // get the meta tag
        // refreshregex: if the meta tag isset, we must also look at the other pages
        $refreshregex   = '/<meta http-equiv=\"refresh\" content=\".*; URL=(.*?)\">/';
        // check if it is on the site
        preg_match($refreshregex, $vplancontent, $reffile);
        $subfile = $reffile[1];
        $emergencystop++; // if loop runs too often
        if ($emergencystop > 100) {
            throw new \Error("Die Schleife lief zu lange.", 1);
        }
    }
}
if (!empty($todisc)) {
    $sendtext = '';
    foreach ($todisc as $vertr) { // get all vertr and save them in our var to be sent to discord.
        $sendtext .= '**- - -**' . PHP_EOL . $vertr . PHP_EOL . '**- - -**' . PHP_EOL;
    }
    $sendtext .= PHP_EOL;
    $vertnum = count($todisc); // how many verts to be sent to DC
    echo 'Sending '. $vertnum .' items to Discord...' . PHP_EOL;
    send_to_discord($sendtext, $webhook);
} else {
    echo 'Nothing to be sent to Discord.' . PHP_EOL;
}
