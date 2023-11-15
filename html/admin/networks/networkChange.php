<?php

session_start();
if ($_SESSION['logged'] !== TRUE){
    header("Location: /login");
    die();
}
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
include 'subnetCalculator.php';

function checkEscapeSeq($networkName,$networkAddress,$natInterface,$networkPort,$networkAllowedNets,$networkDisallowedNets,$ifLANRouting,$ifActive)
{

    # Network name
    if (!preg_match("/^[a-zA-Z0-9]+$/m", $networkName)) {
        echo('Network name should contain only numbers and letters!');
        die();
    }
    
    # Network address
    if (!preg_match("/^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/(3[0-2]|[1-2][0-9]|[1-9])$/m", $networkAddress)) {
        if($networkAddress !== '0.0.0.0/0'){
            echo('Network address should be address/mask');
            die();
        }
    }

    # Network port
    if (preg_match("/^[0-9]+$/m", $networkPort)) {
        if(intval($networkPort) <= 0 || intval($networkPort) >= 65536){
            echo('Port should be in range 1-65535');
            die();
        }
    }
    else{
        echo('Port should be in range 1-65535');
        die();
    }

    # Allowed networks
    if (strpos($networkAllowedNets, '0.0.0.0/0') !== false) {
        $networkAllowedNets = '0.0.0.0/0';
    } else {
        if (!preg_match("/^(((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/((3[0-2]|[1-2][0-9]|[1-9]),))+((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/((3[0-2]|[1-2][0-9]|[1-9]))$/m", $networkAllowedNets)) {
            if (!preg_match("/^(((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/((3[0-2]|[1-2][0-9]|[1-9])))$/m", $networkDisallowedNets) && $networkDisallowedNets !== '') {
                echo('Wrong disallowed networks format');
                die();
            }
        }
    }

    # Disallowed networks
    if (strpos($networkDisallowedNets, '0.0.0.0/0') !== false) {
            echo('Disallowed net 0.0.0.0/0 is incorrect');
            die();
    } else {
        if (!preg_match("/^(((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/((3[0-2]|[1-2][0-9]|[1-9]),))+((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/((3[0-2]|[1-2][0-9]|[1-9]))$/m", $networkDisallowedNets) && $networkDisallowedNets !== '') {
            if (!preg_match("/^(((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/((3[0-2]|[1-2][0-9]|[1-9])))$/m", $networkDisallowedNets) && $networkDisallowedNets !== '') {
                echo('Wrong disallowed networks format');
                die();
            }
        }
    }

    # NAT interface
    if (!preg_match("/^[a-zA-Z0-9]+$/m", $natInterface)) {
        echo('NAT interface should contain only numbers and letters!');
        die();
    }

    # Active option
    if (!preg_match("/^[0-1]$/m", $ifActive)) {
        echo('Active must me 1 or 0!');
        die();
    }

    # LAN routing option
    if (!preg_match("/^[0-1]$/m", $ifLANRouting)) {
        echo('LAN must me 1 or 0!');
        die();
    }

    return;
}

function allowedIPsCalculator($networkAddress, $networkAllowedNets, $networkDisallowedNets, $ifLANRouting){

    

    # Calculating wireguard VPN interface net address
    $networkAddressSplitArr = explode('/', $networkAddress);
    $subCalculator = new IPv4\SubnetCalculator($networkAddressSplitArr[0], $networkAddressSplitArr[1]);
    $wireguardAdapterIPNet = ($subCalculator->getIPAddressRange())[0] . '/' . $subCalculator->getNetworkSize();


    # If there is LAN routing => allow whole network, if not => allow only gateway
    if($ifLANRouting === '1'){
        if (!preg_match("/".preg_quote($wireguardAdapterIPNet, '/')."/m", $networkAllowedNets)) {
            $networkAllowedNets.= ',' . $wireguardAdapterIPNet;
        }
    }else{
        $networkDisallowedNets = $wireguardAdapterIPNet;
    }

    # Formatting networks not to have , at the start
    if ($networkAllowedNets[0] === ',') {
        $networkAllowedNets = substr($networkAllowedNets, 1);
    }
    if ($networkDisallowedNets[0] === ',') {
        $networkDisallowedNets = substr($networkDisallowedNets, 1);
    }

    $AllowedIPs = '';

    if($networkDisallowedNets === ''){

        $AllowedIPs =  $networkAllowedNets;

    }else{

        $execOutputArr = null;
        exec('python3 wireguard-ip-calculator.py -a '. $networkAllowedNets .' -d ' . $networkDisallowedNets . ' 2>&1', $execOutputArr);
        foreach ($execOutputArr as $line) {
            $AllowedIPs .= "$line\n";
        }
        
        $AllowedIPs = substr($AllowedIPs, 13);
        
    }

    return $AllowedIPs;
}

function iptablesCalculator($AllowedIPsArray, $natInterface){

    $iptablesPostUp = '';
    $iptablesPostDown = '';


    foreach ($AllowedIPsArray as &$allowedNetwork) {
        $iptablesPostUp .= 'iptables -A FORWARD -i %i -d '. str_replace(array("\n", "\r"), '', $allowedNetwork) .' -j ACCEPT; ';
        $iptablesPostDown .= 'iptables -D FORWARD -i %i -d '. str_replace(array("\n", "\r"), '', $allowedNetwork) .' -j ACCEPT; ';
    }

    # Drop by defult
    $iptablesPostUp .='iptables -A FORWARD -i %i -j DROP;';
    $iptablesPostDown .='iptables -D FORWARD -i %i -j DROP;';

    if ($natInterface !== 'None') {
        $iptablesPostUp .= 'iptables -t nat -A POSTROUTING -o '.$natInterface.' -j MASQUERADE; ';
        $iptablesPostDown .= 'iptables -t nat -D POSTROUTING -o '.$natInterface.' -j MASQUERADE; ';
    }

    return [$iptablesPostUp, $iptablesPostDown];

}

function generateCerts($networkName){

    exec('mkdir /etc/wireguard/'.$networkName.'/');
    exec('mkdir /etc/wireguard/'.$networkName.'/server_keys/');
    exec('mkdir /etc/wireguard/'.$networkName.'/client_keys/');
    exec('wg genkey > /etc/wireguard/'.$networkName.'/server_keys/Server_PrivateKey');
    exec('wg pubkey < /etc/wireguard/'.$networkName.'/server_keys/Server_PrivateKey > /etc/wireguard/'.$networkName.'/server_keys/Server_PublicKey');

}

function checkCollisions($networkName, $networkAddress, $networkPort){

    # Get all VPNs' filenames
    exec('ls /etc/wireguard/ | grep .conf', $availableVPNs);

    unset($availableVPNs[array_search($networkName . '.conf', $availableVPNs)]);
    $availableVPNs = array_values($availableVPNs);

    for ($i=0; $i < count($availableVPNs); $i++) { 
        
        $currentConf = file_get_contents('/etc/wireguard/' . $availableVPNs[$i]);

        # Check port
        if (preg_match("/^ListenPort = ". $networkPort ."$/m", $currentConf)) {
            echo('Network with this port already exists');
            die();
        }

        # Find current wireguard VPN interface address
        preg_match("/^Address = .*$/m", $currentConf, $matches);
        $currentNetworkAddress = substr($matches[0], 10);

        # Calculate current wireguard VPN interface net address
        $networkAddressSplitArr = explode('/', $currentNetworkAddress);
        $subCalculatorCurrent = new IPv4\SubnetCalculator($networkAddressSplitArr[0], $networkAddressSplitArr[1]);

        # Calculate new wireguard VPN interface net address
        $networkAddressSplitArr = explode('/', $networkAddress);
        $subCalculatorNew = new IPv4\SubnetCalculator($networkAddressSplitArr[0], $networkAddressSplitArr[1]);

        # If there is address collision => error
        foreach ($subCalculatorCurrent->getAllIPAddresses() as $ipAddressCurrent) {
            foreach ($subCalculatorNew->getAllIPAddresses() as $ipAddressNew) {
                if ($ipAddressCurrent === $ipAddressNew) {
                    echo('Network with this address cannot be created');
                    die();
                }
            }
        }


    }

    return;
}

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
$networkName = $_POST['networkName'];
$networkAddress = $_POST['networkAddress'];
$natInterface = $_POST['networkNATInterface'];
$networkPort = $_POST['networkPort'];
$networkAllowedNets = $_POST['networkAllowedNetworks'];
$networkDisallowedNets = $_POST['networkDisallowedNetworks'];
$ifLANRouting = $_POST['lanAccess'];
$ifActive = $_POST['activeOption'];

# Check escape sequences in user input. If incorrect - echo error and die()
checkEscapeSeq($networkName,$networkAddress,$natInterface,$networkPort,$networkAllowedNets,$networkDisallowedNets,$ifLANRouting,$ifActive);

# Check unser input collisions with existing configs
checkCollisions($networkName, $networkAddress, $networkPort);

# Copy raw allowed and disallowed networks for config comment  
$userNetworkAllowedNets = $networkAllowedNets;
$userNetworkDisallowedNets = $networkDisallowedNets;

# Calculate AllowedIPs string for config
$AllowedIPs = allowedIPsCalculator($networkAddress, $networkAllowedNets, $networkDisallowedNets, $ifLANRouting);

# Calculate PostUp and PostDown strings; [0] => PostUp; [1] => PostDown;
$iptablesArr = iptablesCalculator(explode(',', $AllowedIPs), $natInterface);

# Generate directories certs and directories for them 
generateCerts($networkName);

# Cat server private key from created file
$serverPrivateKey = file_get_contents('/etc/wireguard/'.$networkName.'/server_keys/Server_PrivateKey');

# Config assembly
$networkConfigTemplate = "#NETWORK_NAME=".$networkName."\n#ACTIVE=". $ifActive . "\n#LAN=" . $ifLANRouting ."\n#USER_ALLOWED_NETS=" . $userNetworkAllowedNets . "\n#USER_DISALLOWED_NETS=" . $userNetworkDisallowedNets . "\n#AllowedIPs=". $AllowedIPs ."\n[Interface]\nPrivateKey = ".str_replace(array("\n", "\r"), '', $serverPrivateKey)."\nAddress = ". $networkAddress ."\nListenPort = ". $networkPort ."\nPostUp = ". $iptablesArr[0] ."\nPostDown = ". $iptablesArr[1] ."\nSaveConfig = false\nTable = off\n\n";


# Get current config
$originalConfig = file_get_contents('/etc/wireguard/' . $networkName . '.conf');


# Find current wireguard VPN interface config and replace it with new one
preg_match("/^#START PEER .*$/m", $originalConfig, $matches, PREG_OFFSET_CAPTURE);
$networkConfigTemplate .= substr($originalConfig, $matches[0][1]);


# Remake clinets' addresses if new interface address is setted
preg_match("/^Address = .*$/m", $originalConfig, $matches);

if (substr($matches[0], 10) !== $networkAddress) {

    echo('kek');

    # Remove current clients' IPs
    $networkConfigTemplate = preg_replace("/^#PEER_IP=.*$/m", "#PEER_IP=", $networkConfigTemplate);

    # Count all current clients' IPs
    preg_match_all("/^#PEER_IP=.*$/m", $networkConfigTemplate, $matches);

    for ($i=0; $i < count($matches[0]); $i++) { 
        # Find all current clients' IPs
        preg_match_all("/^#PEER_IP=.*$/m", $networkConfigTemplate, $matches);

        # Calculate unique IP address for new client
        $clientIP = calculateIPaddress($matches[0], $networkAddress);

        # Add new IP to client
        $networkConfigTemplate = preg_replace("/^#PEER_IP=$/m", "#PEER_IP=" . $clientIP, $networkConfigTemplate, 1);

    }

    # Calculate unique IP address for new client
    $clientIP = calculateIPaddress($matches[0], $networkAddress);

}


# Write new config to file
exec('rm /etc/wureguard/' . $networkName .".conf");
$configFile = fopen("/etc/wireguard/". $networkName .".conf", "w") or die("Unable to open file!");
fwrite($configFile, $networkConfigTemplate);
fclose($configFile);

# Disable net by default
exec('sudo wg-quick down ' . $networkName);
exec('sudo systemctl disable wg-quick@' . $networkName);

# If VPN is set active => start and enable
if ($ifActive === '1') {
    exec('sudo wg-quick up ' . $networkName);
    exec('sudo systemctl enable wg-quick@' . $networkName);
}


echo('Success');
?>

