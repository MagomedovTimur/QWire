function getCPULoad() {

    $.get( "/admin/monitoring/getCPULoad.php", function( data ) {

        var CPUArr = jQuery.parseJSON(data);

        $('.CPU1m').html(CPUArr[0]);
        $('.CPU5m').html(CPUArr[1]);
        $('.CPU15m').html(CPUArr[2]);

    });

    return;
}
function getRAMUsage() {
    $.get( "/admin/monitoring/getRAMUsage.php", function( data ) {

        var RAMArr = jQuery.parseJSON(data);

        $('.RAMTotal').html(RAMArr[0]);
        $('.RAMUsed').html(RAMArr[1]);
        $('.RAMPercentage').html(RAMArr[2]);

    });
}

function getVPNSumm() {
    $.get( "/admin/monitoring/getVPNsSummary.php", function( data ) {

        var VPNsArr = jQuery.parseJSON(data);

        $('.VPNsTotal').html(VPNsArr[0]);
        $('.VPNsActive').html(VPNsArr[1]);
        $('.ClientsTotal').html(VPNsArr[2]);
        $('.ClientsActive').html(VPNsArr[3]);

    });
}

function getIntSumm() {
    $.get( "/admin/monitoring/getInterfaceUsage.php", function( data ) {

        

        var IntArr = jQuery.parseJSON(data);
        var result = '<div class="row interfaceSummaryRow">';

        for (let i = 0; i < IntArr.length; i++) {
            for (let k = 0; k < IntArr[i].length; k++) {
                result += '<div class="col-2 text-center">'+IntArr[i][k]+ '</div>';
            }
        }
        result += '</div>';

        $('.interfaceSummaryRow').remove();
        $('.interfacesSummary').append(result);
       

    });

    return;
}


$('document').ready(function(){

    setInterval(function () {
        getCPULoad();
    }, 2000)

    setInterval(function () {
      getIntSumm();
    }, 2000)

    setInterval(function () {
        getRAMUsage();
    }, 2000)

    setInterval(function () {
        getVPNSumm();
    }, 2000)

});

