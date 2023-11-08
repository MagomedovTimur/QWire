<?php
session_start();
if ($_SESSION['logged'] !== TRUE){
    header("Location: /login");
    die();
}

# Get user input
$clientName = $_POST['clientName'];

# Check escape sequence client name
if (!preg_match("/^[a-zA-Z0-9]+$/m", $clientName)) {
    echo('Something went wrong');
    die();
}

# Get all config files
exec('ls /etc/wireguard/ | grep .conf', $availableVPNs);


$resultArray = array();

for ($i=0; $i < count($availableVPNs); $i++) { 

    $currentConf = file_get_contents('/etc/wireguard/' . $availableVPNs[$i]);

    if(preg_match("/^#START PEER ". $clientName ."$/m", $currentConf)){

        $configString = "";

        # Gett VPN interface network mask
        preg_match("/^Address = .*$/m", $currentConf, $matches);
        $interfaceNetwork = substr($matches[0], strpos($matches[0], '/'));

        # Get peer IP
        preg_match("/^#PEER_IP=.*$/m", $currentConf, $matches);
        $interfaceAddress = substr($matches[0], 9) . $interfaceNetwork;

        # Get client's public key
        $interfaceKey = file_get_contents('/etc/wireguard/' . substr($availableVPNs[$i], 0, -5) . '/client_keys/' . $clientName . '_PrivateKey');
        $interfaceKey = preg_replace("/\n/", '', $interfaceKey);

        # Get server public key
        $peerKey = file_get_contents('/etc/wireguard/' . substr($availableVPNs[$i], 0, -5) . '/server_keys/Server_PublicKey');
        $peerKey = preg_replace("/\n/", '', $peerKey);

        # Get VPN's AllowedIPs for clients
        preg_match("/^#AllowedIPs=.*$/m", $currentConf, $matches);
        $peerAllowedIPs = substr($matches[0], 12);

        # Get server's public IP address and VPN's port
        exec('curl ifconfig.me', $peerEndpoint);
        preg_match("/^ListenPort = .*$/m", $currentConf, $matches);
        $peerEndpoint = $peerEndpoint[0] . ":" . substr($matches[0], 13);

        # Config assembly   
        $configString .= "[Interface]\n";
        $configString .= "PrivateKey=" . $interfaceKey . "\n";
        $configString .= "Address=" . $interfaceAddress . "\n\n";

        $configString .= "[Peer]\n";
        $configString .= "PublicKey=" . $peerKey . "\n";
        $configString .= "AllowedIPs=" . $peerAllowedIPs . "\n";
        $configString .= "Endpoint=" . $peerEndpoint;


        array_push($resultArray, [substr($availableVPNs[$i], 0, -5), $configString]);

    }
}



$configJSON = json_encode($resultArray);
echo($configJSON);
