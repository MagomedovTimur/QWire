<?php

    session_start();
    if ($_SESSION['logged'] !== TRUE){
        header("Location: /login");
        die();
    }

?>


<!doctype html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>QWire</title>

        <link href="/src/styles/bootstrap.min.css" rel="stylesheet">
        <link href="/admin/monitoring/index.css" rel="stylesheet">
        <link href="/admin/networks/index.css" rel="stylesheet">
        <link href="/admin/clients/index.css" rel="stylesheet">
        <link href="/src/styles/global.css" rel="stylesheet">
        <link href="/alert/index.css" rel="stylesheet">

        <link rel="stylesheet" type="text/css" href="/src/styles/font-awesome.css">

        <!-- TODO make local font-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300&display=swap" rel="stylesheet">

    </head>
    <body>

        <div class="container-fluid menuHover">
            <div class="row">
                <div class="logo col-1">QWire</div>

                <div class="col-1 menuButton menuButtonActive">Monitoring</div>

                <div class="col-1 menuButton">Networks</div>

                <div class="col-1 menuButton">Clients</div>


                <div class="col-1"></div>
                <div class="col-1"></div>
                <div class="col-1"></div>
                <div class="col-1"></div>
                <div class="col-1"></div>
                <div class="col-1"></div>
                <div class="col-1"></div>

                <div class="col-1 menuButtonLogout">
                    <i class="fa fa-sign-out"></i>
                </div>
            </div>
        </div>

        <div class="contentHover mt-5 p-4">
            <div class="container-fluid content">
                    <?php

                        include "monitoring/index.php";
                        include "clients/index.php";
                        include "networks/index.php";
                    
                    ?>
            </div>
        </div>

        <div class="loader mt-5">
            <div class="loader__tile"></div>
            <div class="loader__tile"></div>
            <div class="loader__tile"></div>
            <div class="loader__tile"></div>
            <div class="loader__tile"></div>
        </div>

        <!-- Alert dialog box -->
		<?php include "../alert/index.php";?>

        <script src="/src/scripts/jquery-3.7.0.min.js"></script>
        <script src="/src/scripts/bootstrap.min.js"></script>
        <script src="/alert/index.js"></script>
        <script src="index.js"></script>
        <script src="/admin/clients/index.js"></script>
        <script src="/admin/networks/index.js"></script>
        <script src="/admin/monitoring/index.js"></script>
    </body>
</html>
