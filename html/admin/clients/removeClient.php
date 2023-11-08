<?php

    session_start();
    if ($_SESSION['logged'] !== TRUE){
        header("Location: /login");
        die();
    }

    # Get user input
    $VPNName = $_POST['VPNName'];
    $clientName = $_POST['clientName'];    

    # Check escape sequence for VPN and client names
    if (!preg_match("/^[a-zA-Z0-9]+$/m", $VPNName)) {
        echo('Something went wrong');
        die();
    }
    if (!preg_match("/^[a-zA-Z0-9]+$/m", $clientName)) {
        echo('Something went wrong');
        die();
    }

    # Check if config file exists
    if (!file_exists('/etc/wireguard/' . $VPNName . '.conf')) {
        echo('Something went wrong');
        die();
    }

    # Get current VPN config
    $currentConf = file_get_contents('/etc/wireguard/' . $VPNName . '.conf');
    
    # Changing current VPN config
    $newConfig = preg_replace('/#START PEER '.$clientName.'\n.*\n.*\n.*\n.*\n.*\n\n/m', '', $currentConf);

    # Writ changes to config
    $configFile = fopen("/etc/wireguard/". $VPNName .".conf", "w") or die("Unable to open file!");
    fwrite($configFile, $newConfig);
    fclose($configFile);

    # Remov client certificates
    exec("rm /etc/wireguard/".$VPNName . ".conf/client_keys/".$clientName."_PrivateKey");
    exec("rm /etc/wireguard/".$VPNName . ".conf/client_keys/".$clientName."_PublicKey");

    exec('sudo wg-quick down '.$VPNName);
    sleep(0.5);
    exec('sudo wg-quick up '.$VPNName);

    echo('Success');