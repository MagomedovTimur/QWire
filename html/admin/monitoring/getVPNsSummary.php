<?php

session_start();
if ($_SESSION['logged'] !== TRUE){
    header("Location: /login");
    die();
}

# Get all config files and count them
exec('ls /etc/wireguard/ | grep .conf', $totalVPNs);
$totalVPNs = count($totalVPNs);

# Get active VPN's and count them
exec('sudo wg show all | grep interface:', $activeVPNs);
$activeVPNs = count($activeVPNs);

# Get active clients and count them
exec('sudo wg show all | grep peer:', $activeClients);
$activeClients = count($activeClients);


$allClients = [];

# Get all config files
exec('ls /etc/wireguard/ | grep .conf', $availableVPNs);

for ($i=0; $i < count($availableVPNs); $i++) { 

    # Get current config
    $currentConf = file_get_contents('/etc/wireguard/' . $availableVPNs[$i]);
    preg_match_all( "/^#START PEER .*$/m" , $currentConf, $currentConfClients);
    $currentConfClients = $currentConfClients[0];

    # Trim $START PEER
    for ($j=0; $j < count($currentConfClients); $j++) { 
        $currentConfClients[$j] = substr($currentConfClients[$j], 12);
    }

    # Add client to array if unique
    foreach ($currentConfClients as $currentConfClient) {
        if(!array_search($currentConfClient, $allClients)){
            array_push($allClients, $currentConfClient);
        }
    }

}

$totalClient = count($allClients);

$result = [$totalVPNs, $activeVPNs, $totalClient, $activeClients];
$result = json_encode($result);
echo($result);
