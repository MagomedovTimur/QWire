<?php

session_start();
if ($_SESSION['logged'] !== TRUE){
    header("Location: /login");
    die();
}



# Get raw "free -t" command output 
$execOutputArr = null;
exec("free -t 2>&1", $execOutputArr);

# Get memory line
$usageArr = [];
foreach ($execOutputArr as $line) {
    if(preg_match("/^Mem:.*/m", $line)){
        $memoryString = $line;
    }
}

# Remove multiple whitespaces
$memoryString = preg_replace('/\s+/', ' ', $memoryString);

# Trim Mem:
$memoryString =  substr($memoryString, strpos($memoryString, ' ')+1);

# Find total memory
$memoryTotal  = substr($memoryString, 0, strpos($memoryString, ' '));

# Trim total memory
$memoryString =  substr($memoryString, strpos($memoryString, ' ')+1);

# Find used memory
$memoryUsed  = substr($memoryString, 0, strpos($memoryString, ' '));


$memoryTotalMB = round($memoryTotal/1024);
$memoryUsedMB = round($memoryUsed/1024);

$memoryUsedPercentage = round($memoryUsed/$memoryTotal*100, 1);

$result = [$memoryTotalMB . 'MB', $memoryUsedMB . 'MB', $memoryUsedPercentage . '%'];
$result = json_encode($result);
echo($result);


