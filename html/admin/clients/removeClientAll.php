<?php

    session_start();
    if ($_SESSION['logged'] !== TRUE){
        header("Location: /login");
        die();
    }

    # Get user input
    $clientName = $_POST['clientName'];

    # Check escape sequence for client name
    if (!preg_match("/^[a-zA-Z0-9]+$/m", $clientName)) {
        echo('Something went wrong');
        die();
    }


    # Get all config files
    exec('ls /etc/wireguard/ | grep .conf', $availableVPNs);

    for ($i=0; $i < count($availableVPNs); $i++) { 

        # Get current config
        $currentConf = file_get_contents('/etc/wireguard/' . $availableVPNs[$i]);

        # Chang current VPN config
        $newConfig = preg_replace('/#START PEER '.$clientName.'\n.*\n.*\n.*\n.*\n.*\n\n/m', '', $currentConf);

        # Remove client certificates
        exec("rm /etc/wireguard/".$availableVPNs[$i]."/client_keys/".$clientName."_PrivateKey");
        exec("rm /etc/wireguard/".$availableVPNs[$i]."/client_keys/".$clientName."_PublicKey");

        # Write changes to config
        $configFile = fopen("/etc/wireguard/". $availableVPNs[$i], "w") or die("Unable to open file!");
        fwrite($configFile, $newConfig);
        fclose($configFile);

        exec('sudo wg-quick down '.$VPNName);
        sleep(0.5);
        exec('sudo wg-quick up '.$VPNName);
    }


    echo('Success');