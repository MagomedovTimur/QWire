$(document).on('click', '.clientDeleteButton', function() {

    var currentClient = $(this).parent().parent().parent().children('.clientName').html();
    var clientContainer = $(this).parent().parent().parent().parent();

    $.post( "/admin/clients/removeClientAll.php", { clientName: currentClient}, function(data){

        if(data.includes('Success')){

            clientContainer.fadeOut(500);
            console.log($(this).parent().parent().parent());

            setTimeout(() => {
                clientContainer.remove();
                console.log('Success');
                return;
            }, 500);

        }else{
            console.log('error');
            return;
        }

    });

});


$(document).on("mouseenter", ".clientCard", function() {
    $( this ).addClass('clientCardActive');
    $( this ).children('.clientCardButtonsBoxDiv').children('.clientCardButtonsBox').addClass('clientCardButtonsBoxActive');
    $( this ).children('.clientSettings').addClass('clientSettingsActive');

});

$(document).on("mouseleave", ".clientCard", function() {
    $( this ).removeClass('clientCardActive');
    $( this ).children('.clientCardButtonsBoxDiv').children('.clientCardButtonsBox').removeClass('clientCardButtonsBoxActive');
    $( this ).children('.clientSettings').removeClass('clientSettingsActive');
});


$(document).on('click', '.clientConfigButton', function() {
    blockUserInput('block');
    var clientName = $(this).parent().parent().parent().children('.clientName').html();

    $.post( "/admin/clients/getClient.php", {clientName: clientName}, function(data){

        
        
        if(data =! '[]'){

            var clientVPNs = jQuery.parseJSON(data);
            $('.clientsDialog').html('<div class="row pl-5 mt-5 pt-5"><h1 class="col-11">'+clientName+'</h1><h1 class="col-1 text-center exitClientDialog" style="cursor: pointer;" onclick="exitClientDialog();">✕</h1></div>');

            var clientVPNs = jQuery.parseJSON(data);

            for (let i = 0; i < clientVPNs.length; i++) {

                console.log((clientVPNs[i][1]).replace(/\n+/g, '<br>'));

                var currentVPNHTML = `
                <div class="row clientConfigDialog p-4 mb-4">

                    <h3 class="col-12">`+clientVPNs[i][0]+`</h3>
                    <div id="qrcode" style="width: 14rem !important; padding: 0;"></div>

                    <div class="p-3 clientConfigText">`+ (clientVPNs[i][1]).replace(/\n+/g, '<br>')+`</div>

                    <div class="p-3 clientConfigButtonsBox">
                        <div class="clientConfigDialogButton text-center clientConfigDialogButtonCopyConfig" style="margin-top: 3%;">Copy config</div>
                        <div class="clientConfigDialogButton text-center clientConfigDialogButtonDownloadConfig" style="margin-top: 3%;">Download config</div>
                    </div>

                </div>
                `;                

                $('.clientsDialog').append(currentVPNHTML);
                
            }

            $('.clientCardContainer').fadeOut(200);

            setTimeout(function() {

                $('.menuHover').css('background-color', 'transparent');
                $('.menuButton').fadeOut(400);
                $('.logo').fadeOut(400);
                $('.menuButtonLogout').fadeOut(400);

                setTimeout(function() {
                
                    $('.clientsDialog').fadeIn(200);
                    blockUserInput('unblock');

                }, 500);
            

            }, 500);

            console.log('Success');
            return;

        }else{
            getAlert('There are no VPNs assigned to this client', 0);
            blockUserInput('unblock');
            return;
        }

    });


} );

function exitClientDialog() {
    $('.clientsDialog').fadeOut(200);
  $('.clientsNetworksDialog').fadeOut(200);
  
  setTimeout(function() {

    $('.menuHover').css('background-color', 'var(--myRed)');
    $('.menuButton').fadeIn(400);
    $('.logo').fadeIn(400);
    $('.menuButtonLogout').fadeIn(400);

    setTimeout(function() {
    
      $('.clientCardContainer').fadeIn(200);

    }, 500);
  

  }, 500);
}

$(document).on('click', '.clientNetworkAdd', function() {

    if(availableVPNs.length == 0){
        getAlert('There are no available VPNs', 0)
        return;
    }

    refreshVPNs($(this).parent().parent().children('.clientName').html(), $(this).parent().parent().children('.clientAssignedNetworks').html());

    $('.clientCardContainer').fadeOut(200);

    setTimeout(function() {
   
        $('.menuHover').css('background-color', 'transparent');
        $('.menuButton').fadeOut(400);
        $('.logo').fadeOut(400);
        $('.menuButtonLogout').fadeOut(400);



        setTimeout(function() {
        
            $('.clientsNetworksDialog').fadeIn(200);
    
        }, 500);
  
    }, 500);
} );


function clientsNetworksToggle(clientsNetworksDialogNetwork) {
    
    var currentElem = $( '.'+clientsNetworksDialogNetwork);
    var clientName = $('.ClientDialogName').html();
    var VPNName = clientsNetworksDialogNetwork.slice(28);

    if (currentElem.hasClass('clientsNetworksDialogNetworkActive')) {
        currentElem.children('.clientsNetworksDialogNetworkButton').text( '+' );
        currentElem.removeClass('clientsNetworksDialogNetworkActive');
        $.post( "/admin/clients/removeClient.php", { VPNName: VPNName, clientName: clientName}, function(data){

        if(data.includes('Success')){
            $('.clientCard' + clientName).children('.clientAssignedNetworks').children('.clientNetworkName' + VPNName).remove();
            return;

        }else{
            console.log('error');
            return;
        }

        });
    }
    else{
        currentElem.children('.clientsNetworksDialogNetworkButton').text( '-' );
        currentElem.addClass('clientsNetworksDialogNetworkActive');
        $.post( "/admin/clients/createClient.php", { VPNName: VPNName, clientName: clientName}, function(data){

        if(data.includes('Success')){
            var newClientNetworkName = '<span class="clientNetworkName clientNetworkName'+VPNName+'">'+VPNName+'</span>';
            console.log($('.clientCard' + clientName).children('.clientAssignedNetworks').children('.clientNetworkAdd'));
            $('.clientCard' + clientName).children('.clientAssignedNetworks').children('.clientNetworkAdd').before(newClientNetworkName);
            return;
        }else{
            console.log('error');
            return;
        }

        });
    }
}

$( ".clientsHoverAddClient" ).on( "click", function() {

  $( ".clientDeleteButton" ).on( "click", function() {

    $('.clientAddCard').fadeOut(500);
  
    setTimeout(() => {
      $('.clientsHoverAddClient ').fadeIn(500);
    }, 500);

  } );
  


  $('.clientsHoverAddClient ').fadeOut(500);
  
  setTimeout(() => {
    $('.clientAddCard ').fadeIn(500);
  }, 500);
  
} );                

function refreshVPNs(clientName, networksStr){

    var result = `
    <div class="row pl-5 mt-5 pt-5">
        <h1 class="col-11 ClientDialogName">` + clientName + `</h1>
        <h1 class="col-1 text-center exitClientDialog" style="cursor: pointer;" onclick="exitClientDialog()">✕</h1>
    </div>
    `;

    console.log('sss: ' + availableVPNs.length);

    for (let i = 0; i < availableVPNs.length; i++) {
        var ifActive = '';
        var ifActive2 = '+';

        if (networksStr.includes(availableVPNs[i])) {
            ifActive = 'clientsNetworksDialogNetworkActive';
            ifActive2 = '-';
        }

        result += `
            <div class="row clientsNetworksDialogNetwork clientsNetworksDialogNetwork`+ availableVPNs[i] +` mt-3 p-2 `+ ifActive +`">
                <div class="col-11">
                    ` + availableVPNs[i] + `
                </div>
                <div class="col-1 clientsNetworksDialogNetworkButton text-center" onclick="clientsNetworksToggle('clientsNetworksDialogNetwork`+availableVPNs[i]+`')">
                    `+ifActive2+`
                </div>
            </div>
        `;

        
    }

    $('.clientsNetworksDialog').html( result);

}

$('.clientApplyButton').on('click', function(){

    var clientName = $('.clientAddCard').children('.clientName').html();
    $('.clientAddCard').fadeOut(500);

    var newClientHTML = `

        <div class="col-2">
            <div class="col-12 clientCard clientCard`+clientName+`">
                <div class="clientName col-12 text-center">`+clientName+`</div>
                <div class="col-12 p-2 clientAssignedNetworks">         
                    <span style="padding-left: 0.5rem;margin-left: 0.1em;">Assigned networks:</span>
                    <span class="clientNetworkAdd">+</span>
                </div>

                <div class="clientCardButtonsBoxDiv">
                    <div class="pt-2 pb-2 row clientCardButtonsBox">
                        <div class="col-6 clientCardButton text-center clientConfigButton" style="border-right: 1px solid var(--myWhite);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.3rem" height="1.3rem" fill="#FEFCFB" class="bi bi-download" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"></path><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"></path></svg>
                        </div>
                        <div class="col-6 clientCardButton text-center clientDeleteButton">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.3rem" height="1.3rem" fill="#FEFCFB" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"></path></svg>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    `;

    setTimeout(() => {
        $('.clientsHoverAddClient').fadeIn(500); 
        $('.clientsHoverAddClient').parent().before(newClientHTML);
    }, 500);
  
});


$(document).on('click', '.clientConfigDialogButtonCopyConfig', function(ev) {

    var text = $(this).parent().parent().children('.clientConfigText').html();
    text = text.replace(/<br>+/g, "\n")

    console.log(text);

    const mySmartTextarea = document.createElement('textarea');
    mySmartTextarea.innerHTML = text;
    const parentElement = document.body.appendChild(mySmartTextarea);
    mySmartTextarea.select();
    document.execCommand('copy');
    parentElement.remove();

});

$(document).on('click', '.clientConfigDialogButtonDownloadConfig', function() {

    var text = $(this).parent().parent().children('.clientConfigText').html();
    text = text.replace(/<br>+/g, "\n")

    var VPNName = $(this).parent().parent().children('h3').html();

    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', VPNName + '.conf');

    element.style.display = 'none';
    document.body.appendChild(element);

    element.click();

    document.body.removeChild(element);

});

//window.onload = function(){ 
//    refreshVPNs();
//}