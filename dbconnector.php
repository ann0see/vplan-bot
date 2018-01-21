<?php
    // variables for db connection from ../../config/config.ini
    $config_file = parse_ini_file("config/config.ini",TRUE); //returns multi array
    $protocol = 'mysql';
    $charset = 'utf8';
    $dbname = $config_file['Database']['dbname'];
    $username = $config_file['Database']['username'];
    $password = $config_file['Database']['password'];
    $host = $config_file['Database']['host'];
    if (!empty($config_file['Database']['port'])) {
      $port = $config_file['Database']['port'];
    }

    try {
        if (!empty($port)) { // is port set?
            $dsn = "$protocol:host=$host;port=$port;dbname=$dbname;charset=$charset";
        } else {
            $dsn = "$protocol:host=$host;dbname=$dbname;charset=$charset";
        }
        // Connect to db
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // switch off emulation
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // let pdo throw exceptions
    } catch (PDOException $e) {
        $errmsg = $e->getMessage(); // get an error
        error_log("Datenbankverbindungsfehler: $errmsg", 0); // log error
        echo 'Datenbankverbindungsfehler';
    } finally {
      // unset all variables
      unset($config_file,$protocol,$charset,$dbname,$username,$password,$host,$port,$dsn);
    }

    error_reporting(E_ALL);
  ini_set('display_errors', '1');
