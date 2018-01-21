<?php
$dayow = array(
  'Sonntag',
  'Montag',
  'Dienstag',
  'Mittwoch',
  'Donnerstag',
  'Freitag',
  'Samstag',
  );
  // Array includes all days 0 = Sunday, 6 = Saturday.

  // functions for the bot
  // check if logged in (web edit of teachers)
  function logged_in()
  {
      if ($_SESSION['Logged_in'] && $_SESSION['UA'] = md5($_SERVER['HTTP_USER_AGENT'])) {
          return true;
      } else {
          return false;
      }
  }

  // sends the message to discord
  function send_to_discord($discmessage, $webhook)
  {
      $discordrequest = curl_init();
      $discmessagepost = array('content' => $discmessage);
      curl_setopt($discordrequest, CURLOPT_POSTFIELDS, $discmessagepost);
      curl_setopt($discordrequest, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($discordrequest, CURLOPT_URL, $webhook);
      curl_setopt($discordrequest, CURLINFO_HEADER_OUT, true);
      $response = curl_exec($discordrequest);
      curl_close($discordrequest);
  }

  // gets the site content
  function get_site_content($siteurl)
  {
    $vplanrequest = curl_init($siteurl); // make the curl connectionobject
    curl_setopt_array($vplanrequest, VPLANCURL);
    $vplancontent = utf8_encode(curl_exec($vplanrequest));
    curl_close($vplanrequest);
    // clean site
    $vplancontent = preg_replace('~&nbsp;~u', "", $vplancontent); // replaces/deletes non breaking spaces
    $vplancontent = trim(preg_replace("~[[:blank:]]+~"," ", $vplancontent));
    $vplancontent = preg_replace('~<br>~', " ", $vplancontent);
    return $vplancontent;
  }

  // gets date writen on the site
  function get_site_date($vplancontent)
  {
    $dayregex = '/(([1-9]|0[1-9]|[12][0-9]|3[01])[-\.]([1-9]|0[1-9]|1[012])[-\.](\d{4})) .*(tag|woch), Woche [A|B]/'; //the regex which gets the day
    preg_match($dayregex, $vplancontent, $datear);

      // make the date object for the day, we are currently parsing
      $vertdate = new DateTime($datear[1]);
      // calculate written day of week
      return $vertdate;
  }

  // parses site content
  function parse_site_content($vplancontent)
  {

    $classregex = '/<tr.*><td.*>.*' . OURCLASS . '.*<\/td><td.*>(.*?)<\/td><td.*>(.*?)<\/td><td.*>(.*?)<\/td><td.*>(.*?)<\/td><td.*>(.*?)<\/td><td.*>(.*?)<\/td><td.*>(.*?)<\/td><td.*>(.*?)<\/td><\/tr>/';
    preg_match_all($classregex, $vplancontent, $classrows, PREG_SET_ORDER);

    return $classrows;
  }

  // specifies teachers gender for $wrtgender and $rtgender
  function tgender($tgender)
  {
    switch ($tgender) {
        case 1:
            $vecho = 'Herrn ';
            break;
        case 2:
            $vecho = 'Frau ';
            break;
        default:
            $vecho = 'Lehrer ';
            break;
    }

    return $vecho;
  }

  // parses
  function get_daycom($vplancontent) {
      $additinforegex = '/<tr class=["|\'|`]info["|\'|`]><td class=["|\'|`]info["|\'|`] colspan=["|\'|`]2["|\'|`]>(.*?)<\/td><\/tr>/';
      preg_match_all($additinforegex, $vplancontent, $additinfo, PREG_SET_ORDER);
      //
      return $additinfo;
  }

  function replace_mdown($vplancontent) {
      $vplancontent = preg_replace('~\*~', '\\*', $vplancontent);
      $vplancontent = preg_replace('~´~', '\\´', $vplancontent);
      $vplancontent = preg_replace('~_~', '\\_', $vplancontent);

      return $vplancontent;
  }
