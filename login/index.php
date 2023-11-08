<?php

    session_start();
    if ($_SESSION['logged'] === TRUE){
        header("Location: /admin");
        die();
    }

?>


<!doctype html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login</title>
        <link href="/src/styles/bootstrap.min.css" rel="stylesheet">
        <link href="/src/styles/global.css" rel="stylesheet">
        <link href="index.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/src/styles/font-awesome.css">
        <link href="/alert/index.css" rel="stylesheet">

        <!-- TODO make local font-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300&display=swap" rel="stylesheet">

    </head>
    <body>

        <div class="container-fluid">
            <div class="row">
                <div class="logo">QWire</div>
                <div class="col-2 p-4 pt-2 loginForm loginFormClose">
                    <input class="col-12 mt-4 text-center myInput myPlaceholder p-2 myInputClose" placeholder="Username">
                    <input class="col-12 mt-4 text-center myInput myPlaceholder p-2 myInputClose" type="password" placeholder="Password">
                    <div class="text-center col-12 mt-4 myButton myInput loginButton p-1 myInputClose">Login</div>
            </div>
        </div>

        <?php include "../alert/index.php";?>


        <script src="/src/scripts/jquery-3.7.0.min.js"></script>
        <script src="/src/scripts/bootstrap.min.js"></script>
        <script src="/alert/index.js"></script>
        <script src="/login/index.js"></script>
    </body>
</html>
