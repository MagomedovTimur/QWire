<?php 


session_start();
if ($_SESSION['logged'] !== TRUE){
    header("Location: /login");
    die();
}

# Get user input
$networkName = $_POST['networkName'];

# If network name incorrect => die
if (!preg_match("/^[a-zA-Z0-9]+$/m", $networkName)) {
    echo('Something went wrong');
    die();
}

# Stop and disable wireguard interface
exec('sudo wg-quick down ' . $networkName);
exec('sudo systemctl disable wg-quick@' . $networkName);



$flagFile = 0;
$flagDir = 0;

# Delete config file
if(file_exists('/etc/wireguard/' . $networkName . '.conf'))
{
    unlink('/etc/wireguard/' . $networkName . '.conf');
    $flagFile = 1;
}

# Delete certificates and VPN's directory
if (is_dir('/etc/wireguard/' . $networkName)) {
    exec('rm -rf /etc/wireguard/' . $networkName);
    $flagDir = 1;
}

# If file, certificates and directory are deleted => Success
if ($flagDir == 1 && $flagFile == 1) {
    echo('Success');
}
else
{
    echo('Something went wrong');
}