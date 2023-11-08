<?php

    session_start();
    if ($_SESSION['logged'] !== TRUE){
        header("Location: /login");
        die();
    }

    include '../networks/subnetCalculator.php';
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL); 

    function calculateIPaddress($currentIPsArr, $networkAddress){

        # Create subnet calculator + calculate available IP address range
        $networkAddressSplitArr = explode('/', $networkAddress);
        $subCalculator = new IPv4\SubnetCalculator($networkAddressSplitArr[0], $networkAddressSplitArr[1]);


        $clientIPFoundFlag = 0;

        $currentIPsArrTrimmed = [$networkAddressSplitArr[0]];
        if (count($currentIPsArr) !== 0) {
            for ($i=0; $i < count($currentIPsArr); $i++) { 
                if(substr($currentIPsArr[$i], 9) !== ''){
                    array_push($currentIPsArrTrimmed, substr($currentIPsArr[$i], 9));
                }
            }
        }
            
        foreach ($subCalculator->getAllIPAddresses() as $i => $ipAddress) {
        if ($i !== 0){
            $collisionFlag = 0;
            $clientIP = $ipAddress;
            foreach($currentIPsArrTrimmed as $existedIP)
            {
                if ($ipAddress !== $existedIP) {
                    $clientIP = $ipAddress;
                }
                else {
                    $collisionFlag = 1;
                }
            }

            if ($collisionFlag === 0) {
                return $clientIP;
            }

        }
        }

        return;
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


    $currentConf = file_get_contents('/etc/wireguard/' . $VPNName . '.conf');

    if (preg_match("/^#START PEER ". $clientName ."$/m", $currentConf)) {
        echo('User with this name already exists');
        die();
    }



    # Generate client's certificates
    exec("wg genkey > /etc/wireguard/".$VPNName."/client_keys/".$clientName."_PrivateKey");
    exec("wg pubkey < /etc/wireguard/".$VPNName."/client_keys/".$clientName."_PrivateKey > /etc/wireguard/".$VPNName."/client_keys/".$clientName."_PublicKey");


    $currentConf = file_get_contents('/etc/wireguard/' . $VPNName . '.conf');
    $peerPublicKey = file_get_contents("/etc/wireguard/".$VPNName."/client_keys/".$clientName."_PublicKey");

    #Find user's allowed networks for VPN
    preg_match("/^#AllowedIPs=.*$/m", $currentConf, $matches);                    
    $allowedNets = substr($matches[0], 12);

    #Find VPN interface IP address 
    preg_match("/^Address = .*$/m", $currentConf, $matches);
    $networkAddress = substr($matches[0], 10);

    # Find all current clients' IPs
    preg_match_all("/^#PEER_IP=.*$/m", $currentConf, $matches);

    # Calculate unique IP address for new client
    $clientIP = calculateIPaddress($matches[0], $networkAddress);

    # Create new config string
    $newConfPart = "#START PEER ". $clientName ."\n#PEER_IP=". $clientIP ."\n[Peer]\nPublicKey = ". str_replace(array("\n", "\r"), '', $peerPublicKey) ."\nAllowedIPs = ".$allowedNets."\n#END PEER ". $clientName ."\n\n";

    # Append changes to VPN's config
    $configFile = fopen("/etc/wireguard/". $VPNName .".conf", "w") or die("Unable to open file!");
    fwrite($configFile, $currentConf . $newConfPart);
    fclose($configFile);

    exec('sudo wg-quick down '.$VPNName);
    sleep(0.5);
    exec('sudo wg-quick up '.$VPNName);

    echo('Success');