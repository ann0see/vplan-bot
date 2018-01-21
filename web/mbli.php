<?php
require '../dbconnector.php';
require '../libvplanbot.php';
session_start();

if (logged_in()) {
    echo "Welcome to our system";
    sleep(3);
    
    exit;
} else {
    echo "Vous n'êtes pas connecté";
}
