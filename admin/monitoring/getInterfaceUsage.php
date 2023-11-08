<?php

session_start();
if ($_SESSION['logged'] !== TRUE){
    header("Location: /login");
    die();
}
function getBaseStats()
{
    # Getting raw "sar -n DEV" command output 
    $execOutputArr = null;
    exec('sar -n DEV 1 1  2>&1', $execOutputArr);

    # Parsing wanted lines and pushing them into array
    $usageArr = [];
    foreach ($execOutputArr as $line) {
        if(!preg_match("/^Average:.*/m", $line) && $line !== ''){
            array_push($usageArr, $line);
        }

    }
    unset($usageArr[0]);unset($usageArr[1]);


    $result = [];
    foreach ($usageArr as $line) {

        # Remove multiple whitespaces
        $line = preg_replace('/\s+/', ' ', $line);

        #Trim date
        $line = substr($line, strpos($line, " ") + 1);

        # Get interface name
        $interfaceName = substr($line, 0, strpos($line, ' '));

        # Trim interface name
        $line = substr($line, strpos($line, " ") + 1);

        # Get interface RX
        $interfaceRX = substr($line, 0, strpos($line, ' ') + 1);

        # Trim interface RX
        $line = substr($line, strpos($line, " ") + 1);

        # Get interface TX
        $interfaceTX = substr($line, 0, strpos($line, ' '));

        # Get interface utilization
        $interfaceUtil = substr($line, strrpos($line, " ") + 1);

        $interfacePackets = $interfaceTX + $interfaceRX;

        array_push($result, [$interfaceName,'', $interfaceUtil, $interfacePackets, 'Up', '-']);

    }

    return $result;

}

function getWireguardStats($baseStats){


    $result = $baseStats;

    # Get all config files
    exec('ls /etc/wireguard/ | grep .conf', $availableVPNs);

    # For all active interfaces
    foreach ($availableVPNs as $currentVPN){
        $collisionFlag = 0;

        # Get current config
        $currentConf = file_get_contents('/etc/wireguard/' . $currentVPN);

        # For all existed VPNs
        foreach ($baseStats as $j => $stat) {

            # If we found active interface from base stats array
            if (preg_match("/^#NETWORK_NAME=".$stat[0]."$/m", $currentConf)) {

                $collisionFlag = 1;

                # Get VPN's IP and insert it to result array
                preg_match("/^Address = .*$/m", $currentConf, $matches);
                $interfaceIP = substr($matches[0], 10);
                $result[$j][1] = $interfaceIP;

                # Get VPN's total client count and insert it to result array
                $clientTotal = substr_count($currentConf, '#START PEER');
                $result[$j][5] = $clientTotal;
            }
        }

        if ($collisionFlag === 0) {
                

                # Get VPN's name
                preg_match("/^#NETWORK_NAME=.*$/m", $currentConf, $matches);
                $interfaceName = substr($matches[0], 14);

                # Get VPN's IP
                preg_match("/^Address = .*$/m", $currentConf, $matches);
                $interfaceIP = substr($matches[0], 10);

                # Get VPN's total client count
                $clientTotal = substr_count($currentConf, '#START PEER');


                array_push($result, [$interfaceName,$interfaceIP,'-','-','Down',$clientTotal . '/0']);
        }


    }

    return $result;

}

function getRemainingStats($baseANDwireguardStats){

    # Get raw "ip -brief address show | awk '{print $1, $3}'" command output 
    $execOutputArr = null;
    exec("ip -brief address show | awk '{print $1, $3}'  2>&1", $execOutputArr);

    # Parse wanted lines and pushing them into array
    $usageArr = [];
    foreach ($execOutputArr as $line) {
        if(!preg_match("/^Average:.*/m", $line) && $line !== ''){
            $lineArr = explode(' ', $line);
            array_push($usageArr, $lineArr);
        }
    }


    foreach($baseANDwireguardStats as $i => $newLine){
        if($newLine[1] == ''){
        
            foreach ($usageArr as $existedLine) {
                if($newLine[0] === $existedLine[0]){
                    $baseANDwireguardStats[$i][1] = $existedLine[1];
                }
            }

        }

    }

    return $baseANDwireguardStats;
}

# Get base interface stats from sar -n DEV 1
$baseStats = getBaseStats();

# Append wireguard specific statistic to wireguard interfaces
$baseANDwireguardStats = getWireguardStats($baseStats);

# Append IP addresses to non-wireguard interfaces
$usageArr = getRemainingStats($baseANDwireguardStats);


$usageArr = json_encode($usageArr);
echo($usageArr);
