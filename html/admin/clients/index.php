<script>
    <?php exec('ls /etc/wireguard/ | grep .conf', $availableVPNs);?>
    var availableVPNs = '<?php echo(implode(".",$availableVPNs)); ?>';
    availableVPNs = availableVPNs.split('.');
    availableVPNs = availableVPNs.filter(e => e !== 'conf')
    if (availableVPNs[0] == '') {
        availableVPNs = [];
    }
</script>

<div class="row clientCardContainer" style="display: none;">

   

        <?php 

            $clients = array();
            
            $i = 0;
            exec('ls /etc/wireguard/', $configFiles);
            while (count($configFiles) > $i) {

                if (!preg_match("/^[0-9a-zA-Z]+\.conf$/m", $configFiles[$i])) {
                    unset($configFiles[$i]);
                    $configFiles = array_values($configFiles);
                }

                $i++;
            }

            # Get client + networks array
            $i = 0;

            while (count($configFiles) > $i) {
                # Get config
                $config = file_get_contents('/etc/wireguard/' . $configFiles[$i]);

                preg_match_all('/^#START PEER .*$/m', $config, $matches);
                
                for ($j=0; $j < count($matches[0]); $j++) { 

                    $currentUser = $matches[0][$j];   
                    $currentUser = substr($currentUser, 12);

                    preg_match('/^#NETWORK_NAME=.*$/m', $config, $currentNetwork);
                    $currentNetwork = substr($currentNetwork[0], 14);

                    if ($clients[$currentUser] === NULL) {
                        $clients[$currentUser][0] = $currentNetwork;
                    }
                    elseif ($clients[$currentUser] !== NULL) {
                        array_push($clients[$currentUser], $currentNetwork);
                    }

                }
                            

                $i++;
            }
            

            # Clients creation

            for ($i=0; $i < count($clients); $i++) { 

                $clientNetworkNameHTML = '';
                $currentUser = (array_keys($clients))[$i];

                for ($j=0; $j < count($clients[$currentUser]); $j++) { 
                    $clientNetworkNameHTML .= '<span class="clientNetworkName clientNetworkName'.$clients[$currentUser][$j].'">'.$clients[$currentUser][$j].'</span>';
                }

                echo '
                     <div class="col-2">
                    <div class="col-12 clientCard clientCard'.$currentUser.'">
                        <div class="clientName col-12 text-center">'.$currentUser.'</div>
                        <div class="col-12 p-2 clientAssignedNetworks">
                            <span style="padding-left: 0.5rem;margin-left: 0.1em;">Assigned networks:</span>
                            '.$clientNetworkNameHTML.'
                            <span class="clientNetworkAdd">+</span>
                        </div>

                        <div class="clientCardButtonsBoxDiv">
                            <div class="pt-2 pb-2 row clientCardButtonsBox">
                                <div class="col-6 clientCardButton text-center clientConfigButton" style="border-right: 1px solid var(--myWhite);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.3rem" height="1.3rem" fill="#FEFCFB" class="bi bi-download" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                                </div>
                                <div class="col-6 clientCardButton text-center clientDeleteButton">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1.3rem" height="1.3rem" fill="#FEFCFB" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                                </div>
                            </div>
                        </div>

                    </div>
                    </div>
                ';
            }
        ?> 




    <div class="col-2">
        <div class="clientsHoverAddClient text-center">
            <span style=" display:table-cell;vertical-align:middle;">+</span>
        </div>



        <div class="col-12 clientCard clientAddCard" style="display: none;">
            <div class="clientName col-12 text-center" contenteditable="true">Name</div>
            <div class="col-12 p-2 clientAssignedNetworks">
                <span style="padding-left: 0.5rem;margin-left: 0.1em;">Assigned networks:</span>
                <span class="clientNetworkAddDisabled">-</span>
            </div>

            <div class="clientCardButtonsBoxDiv">
                <div class="pt-2 pb-2 row clientCardButtonsBox">
                    <div class="col-6 clientCardButton text-center clientApplyButton" style="border-right: 1px solid var(--myWhite);">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg>                    </div>
                    <div class="col-6 clientCardButton text-center clientDeleteButton">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.3rem" height="1.3rem" fill="#FEFCFB" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="container clientsDialog" style="display: none;">

</div>

<div class="container clientsNetworksDialog " style="display: none;">
</div>