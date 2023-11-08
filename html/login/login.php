<?php

    include '../config.php';

    session_start();
    if ($_SESSION['logged'] === TRUE){
        die();
    }

    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    if ($configUsername === $inputUsername && $configPassword === $inputPassword) {
        $_SESSION['logged'] = TRUE;
        echo('Success');
        die();
    }
    else{
        echo('Username or password you provided is incorrect');
        die();
    }

    echo('Something went wrong');
    die();
?>