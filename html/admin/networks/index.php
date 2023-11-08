<script>
    <?php exec('ls /sys/class/net/', $availableInterfaces);?>
    var availableInterfaces = '<?php echo(implode(",",$availableInterfaces)); ?>';
    availableInterfaces = availableInterfaces.split(',');
</script>

<div class="row networkCardContainer" style="display: none;">

    <?php
        $configFiles = '';

        exec('ls /etc/wireguard/', $configFiles);

        # Leave only config files
        $i = 0;

        while (count($configFiles) > $i) {

            if (!preg_match("/^[0-9a-zA-Z]+\.conf$/m", $configFiles[$i])) {
                unset($configFiles[$i]);
                $configFiles = array_values($configFiles);
            }

            $i++;
        }



        # Bootstrappig networks
        $i = 0;

        while (count($configFiles) > $i) {

                # Get config
                $config = file_get_contents('/etc/wireguard/' . $configFiles[$i]);

                # Config vars
                #Name
                $configName = substr($configFiles[$i], 0, -5);

                #Network address
                preg_match("/^Address = .*$/m", $config, $matches);
                $configAddress = substr($matches[0], 10);

                #Network port
                preg_match("/^ListenPort = .*$/m", $config, $matches);
                $configPort = substr($matches[0], 13);          

                # If net is active
                preg_match("/^#ACTIVE=.$/m", $config, $matches);
                $configIfActive = substr($matches[0], 8);    
                if ($configIfActive === '1') {
                    $configIfActive = '
                            <span class="networkOptions activeNet networkOptionsActive">
                                <svg class="networkSVGDisabled" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg>
                                Active
                            </span>
                        ';
                }
                else {
                    $configIfActive = '
                        <span class="networkOptions activeNet">
                            <svg class="" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512" class="networkSVGDisabled"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg>
                            Active
                        </span>
                    ';
                }

                # NAT Interface
                preg_match("/-o [a-zA-Z0-9]+ /m", $config, $matches);
                if (count($matches) !== 1) {
                    $configNATInterface = '<span class="networkAddNATInterface">None</span>';
                }
                else{
                    $configNATInterface = substr($matches[0], 3, -1);     
                    $configNATInterface = '<span class="networkAddNATInterface" style="background-color: var(--myRed); border: 1px solid var(--myRed);">'. $configNATInterface .'</span>';                 
                }

                #User allowed networks
                preg_match("/^#USER_ALLOWED_NETS=.*$/m", $config, $matches);
                $userAllowedNets = substr($matches[0], 19);
                $userAllowedNets = explode(',', $userAllowedNets);
                $userAllowedNetsHTML = '';
                $j = 0;
                if ($userAllowedNets[0] !== '') {
                    while (count($userAllowedNets) > $j) {
                        $userAllowedNetsHTML .= '<span class="networkAllowedNetwork" contentEditable="true">'. $userAllowedNets[$j] .'</span>';
                        $j++;
                    }
                }

                #User disallowed networks
                preg_match("/^#USER_DISALLOWED_NETS=.*$/m", $config, $matches);
                $userDisallowedNets = substr($matches[0], 22);
                $userDisallowedNets = explode(',', $userDisallowedNets);
                $userDisallowedNetsHTML = '';
                $j = 0;
                if ($userDisallowedNets[0] !== '') {
                    while (count($userAllowedNets) > $j) {
                        $userDisallowedNetsHTML .= '<span class="networkDisallowedNetwork" contentEditable="true">'. $userDisallowedNets[$j] .'</span>';
                        $j++;
                    }
                }

                # LAN Access
                preg_match("/^#ACTIVE=.*$/m", $config, $matches);
                $configLAN = substr($matches[0], 8);

                if ($configLAN === '1') {
                    $configLAN = '<span class="networkOptions lanAccess networkOptionsActive"><svg class="" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512" class="networkSVGDisabled"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>LAN access</span>';
                }
                else {
                    $configLAN = '<span class="networkOptions lanAccess"><svg class="networkSVGDisabled" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512" class=""><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>LAN access</span>';
                }

                # Paste to HTML
                echo '
                        <div class="col-4">
                            <div class="col-12 networkCard '.$configName.'Card">

                                <div class="networkName col-12 text-center">' . $configName . '</div>

                                <div class="col-12 p-2 networkAssignedNetworks">
                                    <span style="padding-left: 0.5rem;margin-left: 0.1em;">Network address:</span>
                                    <span class="networkAddress" contentEditable="true">' . $configAddress . '</span>
                                </div>

                                <div class="col-12 p-2 networkAssignedInterface">
                                    <span style="padding-left: 0.5rem;margin-left: 0.1em;">NAT interface:</span>
                                    '.$configNATInterface.'
                                </div>

                                <div class="col-12 p-2 networkAssignedPort">
                                    <span style="padding-left: 0.5rem;margin-left: 0.1em;">VPN port:</span>
                                    <span class="networkPort" contentEditable="true">'. $configPort .'</span>
                                </div>

                                <div class="col-12 p-2 networkAllowedNetworks">
                                    <span style="padding-left: 0.5rem;margin-left: 0.1em;">Allowed networks:</span></br>
                                    '.$userAllowedNetsHTML.'
                                    <span class="networkAddAllowedNetwork">+</span>
                                </div>

                                <div class="col-12 p-2 networkDisallowedNetworks">
                                    <span style="padding-left: 0.5rem;margin-left: 0.1em;">Disallowed networks:</span></br>
                                    '.$userDisallowedNetsHTML.'
                                    <span class="networkAddDisallowedNetwork">+</span>
                                </div>
                                
                                <div class="col-12 pt-2 p-1 pb-3 networkSettings">
                                    '. $configLAN . $configIfActive .'
                                </div>

                                <div class="networkCardButtonsBoxDiv">
                                    <div class="pt-2 pb-2 row networkCardButtonsBox">

                                        <div class="col-4 networkCardButton text-center networkChengeButton">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                                        </div>
                                        <div class="col-4 networkCardButton text-center networkResetButton">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 384 512"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                                        </div>
                                        <div class="col-4 networkCardButton text-center networkDeleteButton" onclick="removeNetwork(\''.$configName.'\')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.3rem" height="1.3rem" fill="#FEFCFB" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                ';

            $i++;
        }
    ?>



    <div class="col-4 newNetworkHover">
        <div class="networksHoverAddNetwork text-center">
            <span style=" display:table-cell;vertical-align:middle;">+</span>
        </div>

        <div class="col-12 networkCard networkCardAdd" style="display: none;">

            <div class="networkName col-12 text-center" contentEditable="true">Enter name here</div>
        
            <div class="col-12 p-2 networkAssignedNetworks">
                <span style="padding-left: 0.5rem;margin-left: 0.1em;">Network address:</span>
                <span class="networkAddress" contentEditable="true">192.168.1.1/24</span>
            </div>
        
            <div class="col-12 p-2 networkAssignedInterface">
                <span style="padding-left: 0.5rem;margin-left: 0.1em;">NAT interface:</span>
                <span class="networkAddNATInterface">None</span>
            </div>
        
            <div class="col-12 p-2 networkAssignedPort">
                <span style="padding-left: 0.5rem;margin-left: 0.1em;">VPN port:</span>
                <span class="networkPort" contentEditable="true">51820</span>
            </div>

            <div class="col-12 p-2 networkAllowedNetworks">
                <span style="padding-left: 0.5rem;margin-left: 0.1em;">Allowed networks:</span></br>
                <span class="networkAddAllowedNetwork">+</span>
            </div>

            <div class="col-12 p-2 networkDisallowedNetworks">
                <span style="padding-left: 0.5rem;margin-left: 0.1em;">Disallowed networks:</span></br>
                <span class="networkAddDisallowedNetwork">+</span>
            </div>

            <div class="col-12 pt-2 p-1 pb-3 networkSettings">
                <span class="networkOptions lanAccess networkOptionsActive">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512" class="networkSVGDisabled"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>
                    <svg class="" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg>
                    LAN access
                </span>                <span class="networkOptions activeNet networkOptionsActive">
                    <svg class="networkSVGDisabled" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                    Active
                </span>
            </div>
        
            <div class="networkCardButtonsBoxDiv">
                <div class="pt-2 pb-2 row networkCardButtonsBox">
        
                    <div class="col-6 networkCardButton text-center networkCreateButton">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                    </div>
                    
                    <div class="col-6 networkCardButton text-center networkCancelButton">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.3rem" height="1.3rem" fill="#FEFCFB" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                    </div>
        
                </div>
            </div>
        
            </div>
            </div>
    </div>

    <div id="NATInterfaceDropdown" class="dropdown-content">
    </div>

</div>