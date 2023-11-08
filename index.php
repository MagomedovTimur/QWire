<?php

    session_start();
    if ($_SESSION['logged'] !== TRUE){
        header("Location: /login");
        die();
    }
    else{
        header("Location: /admin");
        die();
    }

?>