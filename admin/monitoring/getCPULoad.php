<?php

session_start();
if ($_SESSION['logged'] !== TRUE){
    header("Location: /login");
    die();
}

# Get raw uptime command output 
$execOutputArr = null;
exec('uptime 2>&1', $execOutputArr);
$loadStr = $execOutputArr[0];

# Trimm raw string to get CPU load separated by comma+space
$loadStr = substr($loadStr, strpos($loadStr, ': ')+2);

# Convert CPU load string to array with load value
$loadArr = explode(', ', $loadStr);

# Convert result to JSON and send to user
$loadArr = json_encode($loadArr);
echo($loadArr);
