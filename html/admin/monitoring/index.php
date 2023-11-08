<div class="row monitoringContainer">
    <div class="col-2">

        <fieldset class="col-12  px-3 pb-2">
            <legend class="w-auto float-none">CPU load</legend>
            <div>
                1 minute:   <span class="CPU1m">-</span><br>
                5 minutes:   <span class="CPU5m">-</span><br>
                15 minutes:   <span class="CPU15m">-</span>
            </div>
        </fieldset>

        <fieldset class="col-12 mt-4  px-3 pb-2">
            <legend class="w-auto float-none">RAM utilization</legend>
            <div>
                Total	:   <span class="RAMTotal">-</span><br>
                Used	:   <span class="RAMUsed">-</span><br>
                utilization	:   <span class="RAMPercentage">-</span>
            </div>
        </fieldset>

        <fieldset class="col-12 mt-4  px-3 pb-2">
            <legend class="w-auto float-none">VPN summary</legend>
            <div>
                Total VPNs			:   <span class="VPNsTotal">-</span><br>
                Active VPNs		    :   <span class="VPNsActive">-</span><br>
                Total clients		:   <span class="ClientsTotal">-</span><br>
                Connected clients 	:   <span class="ClientsActive">-</span><br>
            </div>
        </fieldset>
    </div>

    <fieldset class="col-10  px-3 pb-2 interfacesSummary">

        <legend class="col-12 w-auto float-none" ><h4>Interfaces summary</h4></legend>

        <div class="row">
            <div class="col-2 text-center">
                Name
            </div>
            <div class="col-2 text-center">
                IP
            </div>
            <div class="col-2 text-center">
                utilization/s
            </div>
            <div class="col-2 text-center">
                Packets/s
            </div>
            <div class="col-2 text-center">
                State
            </div>
            <div class="col-2 text-center">
                Users/Connected
            </div>
            <hr>
        </div>

    </fieldset>

</div>