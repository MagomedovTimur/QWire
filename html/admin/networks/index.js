var currentNATbutton;

$(document).on({
    mouseenter: function () {
        $( this ).addClass('networkCardActive');
        $( this ).children('.networkCardButtonsBoxDiv').children('.networkCardButtonsBox').addClass('networkCardButtonsBoxActive');
        $( this ).children('.networkSettings').addClass('networkSettingsActive');
    },
    mouseleave: function () {
        $( this ).removeClass('networkCardActive');
        $( this ).children('.networkCardButtonsBoxDiv').children('.networkCardButtonsBox').removeClass('networkCardButtonsBoxActive');
        $( this ).children('.networkSettings').removeClass('networkSettingsActive');
    }
}, ".networkCard");

$( ".networksHoverAddNetwork" ).on( "click", function() {

  $( ".networkCancelButton" ).on( "click", function() {

    $('.networkCardAdd').fadeOut(500);
  
    setTimeout(() => {
      $('.networksHoverAddNetwork ').fadeIn(500);
    }, 500);

  } );
  


  $('.networksHoverAddNetwork ').fadeOut(500);
  
  setTimeout(() => {
    $('.networkCard ').fadeIn(500);
  }, 500);
  
} );

$(document).ready(function() {

  $('.networkName ').keypress(function(event) {

    if (event.keyCode == 13) {
        event.preventDefault();
        $(this).parent().children('.networkAssignedNetworks').children('.networkNetworkName').focus();
    }
  });

  $('.networkPort ').keypress(function(event) {

    if (event.keyCode == 13) {
        event.preventDefault();
    }
  });

});

$(document).on('click', '.networkOptions', function() {

  var svg0 = $(this).children('svg')[0];
  var svg1 = $(this).children('svg')[1];

  if (svg0.classList.contains('networkSVGDisabled')) {
    svg0.classList.remove('networkSVGDisabled');
  }
  else{
    svg0.classList.add('networkSVGDisabled');
  }

  if (svg1.classList.contains('networkSVGDisabled')) {
    svg1.classList.remove('networkSVGDisabled');
  }
  else{
    svg1.classList.add('networkSVGDisabled');
  }

    $(this).toggleClass('networkOptionsActive');

} );

$( ".networkCreateButton" ).on( "click", function() {

    var networkCreateBox = $(this).parent().parent().parent();

    var networkName = networkCreateBox.children('.networkName').html();

    var networkAddress = networkCreateBox.children('.networkAssignedNetworks').children('.networkAddress').html();

    var networkNATInterface = networkCreateBox.children('.networkAssignedInterface').children('.networkAddNATInterface').html();

    var networkVPNPort = networkCreateBox.children('.networkAssignedPort').children('.networkPort').html();

    var networkAllowedNetworks = '';
    var allowedNetworksLength = networkCreateBox.children('.networkAllowedNetworks').children('.networkAllowedNetwork').length;
    for (let index = 0; index < allowedNetworksLength; index++) {
        networkAllowedNetworks += ',' + (networkCreateBox.children('.networkAllowedNetworks').children('.networkAllowedNetwork')[index]).innerHTML;
    }
    networkAllowedNetworks = networkAllowedNetworks.slice(1);



    var networkDisallowedNetworks = '';
    var disallowedNetworksLength = networkCreateBox.children('.networkDisallowedNetworks').children('.networkDisallowedNetwork').length;
    for (let index = 0; index < disallowedNetworksLength; index++) {
        networkDisallowedNetworks += ',' + (networkCreateBox.children('.networkDisallowedNetworks').children('.networkDisallowedNetwork')[index]).innerHTML;
    }
    networkDisallowedNetworks = networkDisallowedNetworks.slice(1);

    if (networkCreateBox.children('.networkSettings').children('.lanAccess').hasClass('networkOptionsActive')) {
        var networkLANAccess = 1; 
    }
    else{
        var networkLANAccess = 0;
    }

    if (networkCreateBox.children('.networkSettings').children('.activeNet').hasClass('networkOptionsActive')) {
        var networkActive = 1; 
    }
    else{
        var networkActive = 0;
    }

    $.post( "/admin/networks/networkCreate.php", { networkName: networkName,networkAddress: networkAddress,networkNATInterface: networkNATInterface,networkPort: networkVPNPort,networkAllowedNetworks: networkAllowedNetworks,networkDisallowedNetworks: networkDisallowedNetworks,lanAccess: networkLANAccess,activeOption:  networkActive}, function(data){

    if(data.includes('Success')){

        availableVPNs.push(networkName);

        $('.networkCardAdd').fadeOut(500);

        if(networkNATInterface == 'None'){
            networkNATInterface = '<span class="networkAddNATInterface">None</span>';
        }
        else{
            networkNATInterface = '<span class="networkAddNATInterface" style="background-color: var(--myRed); border: 1px solid var(--myRed);">'+networkNATInterface+'</span>';
        }

        var networkAllowedNetworksHTML = '';
        networkAllowedNetworks = networkAllowedNetworks.split(',');

        if (networkAllowedNetworks[0] != '') {
            var i = 0;
            while (networkAllowedNetworks.length > i) {
                networkAllowedNetworksHTML += '<span class="networkAllowedNetwork" contentEditable="true">'+ networkAllowedNetworks[i] +'</span>';
            i++;
            }
        }

        var networkDisallowedNetworksHTML = '';
        networkDisallowedNetworks = networkDisallowedNetworks.split(',');

        if (networkDisallowedNetworks[0] != 0) {
            var i = 0;
            while (networkDisallowedNetworks.length > i) {
                networkDisallowedNetworksHTML += '<span class="networkDisallowedNetwork" contentEditable="true">' + networkDisallowedNetworks[i] + '</span>';
                i++;
            }
        }



        if (networkActive == 1) {
            networkActive = `<span class="networkOptions activeNet networkOptionsActive">
                                <svg class="networkSVGDisabled" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg>
                                Active
                            </span>`;
        }
        else{
            networkActive = `<span class="networkOptions activeNet">
                                <svg class="" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512" class="networkSVGDisabled"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg>
                                Active
                            </span>`;
        }

        if(networkLANAccess == 1){
            networkLANAccess = `<span class="networkOptions lanAccess networkOptionsActive"><svg class="" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512" class="networkSVGDisabled"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>LAN access</span>`;
        }
        else{
            networkLANAccess = `<span class="networkOptions lanAccess"><svg class="networkSVGDisabled" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512" class=""><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>LAN access</span>`;
        }

        var newNetHTML = `
            <div class="col-4">
                <div class="col-12 networkCard `+ networkName + `Card">

                    <div class="networkName col-12 text-center">` + networkName + `</div>

                    <div class="col-12 p-2 networkAssignedNetworks">
                        <span style="padding-left: 0.5rem;margin-left: 0.1em;">Network address:</span>
                        <span class="networkAddress" contentEditable="true">` + networkAddress + `</span>
                    </div>

                    <div class="col-12 p-2 networkAssignedInterface">
                        <span style="padding-left: 0.5rem;margin-left: 0.1em;">NAT interface:</span>
                        ` + networkNATInterface + `
                    </div>

                    <div class="col-12 p-2 networkAssignedPort">
                        <span style="padding-left: 0.5rem;margin-left: 0.1em;">VPN port:</span>
                        <span class="networkPort" contentEditable="true">` + networkVPNPort + `</span>
                    </div>

                    <div class="col-12 p-2 networkAllowedNetworks">
                        <span style="padding-left: 0.5rem;margin-left: 0.1em;">Allowed networks:</span></br>
                        ` + networkAllowedNetworksHTML + `
                        <span class="networkAddAllowedNetwork">+</span>
                    </div>

                    <div class="col-12 p-2 networkDisallowedNetworks">
                        <span style="padding-left: 0.5rem;margin-left: 0.1em;">Disallowed networks:</span></br>
                        ` + networkDisallowedNetworksHTML + `
                        <span class="networkAddDisallowedNetwork">+</span>
                    </div>
                    
                    <div class="col-12 pt-2 p-1 pb-3 networkSettings">
                        ` + networkLANAccess + networkActive +`
                    </div>

                    <div class="networkCardButtonsBoxDiv">
                        <div class="pt-2 pb-2 row networkCardButtonsBox">

                            <div class="col-4 networkCardButton text-center networkChengeButton">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                            </div>
                            <div class="col-4 networkCardButton text-center networkResetButton">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 384 512"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
                            </div>
                            <div class="col-4 networkCardButton text-center networkDeleteButton" onclick="removeNetwork('` + networkName + `')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.3rem" height="1.3rem" fill="#FEFCFB" class="bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/></svg>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        `;

        setTimeout(() => {
            $('.networksHoverAddNetwork').fadeIn(500);
            $(newNetHTML).insertBefore('.newNetworkHover');
        }, 500);

        return;

    }else{
        getAlert(data, 0);
        return;
    }

    });

});

$( ".networkChengeButton" ).on( "click", function() {

    var networkCreateBox = $(this).parent().parent().parent();

    var networkName = networkCreateBox.children('.networkName').html();

    var networkAddress = networkCreateBox.children('.networkAssignedNetworks').children('.networkAddress').html();

    var networkNATInterface = networkCreateBox.children('.networkAssignedInterface').children('.networkAddNATInterface').html();

    var networkVPNPort = networkCreateBox.children('.networkAssignedPort').children('.networkPort').html();

    var networkAllowedNetworks = '';
    var allowedNetworksLength = networkCreateBox.children('.networkAllowedNetworks').children('.networkAllowedNetwork').length;
    for (let index = 0; index < allowedNetworksLength; index++) {
        networkAllowedNetworks += ',' + (networkCreateBox.children('.networkAllowedNetworks').children('.networkAllowedNetwork')[index]).innerHTML;
    }
    networkAllowedNetworks = networkAllowedNetworks.slice(1);



    var networkDisallowedNetworks = '';
    var disallowedNetworksLength = networkCreateBox.children('.networkDisallowedNetworks').children('.networkDisallowedNetwork').length;
    for (let index = 0; index < disallowedNetworksLength; index++) {
        networkDisallowedNetworks += ',' + (networkCreateBox.children('.networkDisallowedNetworks').children('.networkDisallowedNetwork')[index]).innerHTML;
    }
    networkDisallowedNetworks = networkDisallowedNetworks.slice(1);

    if (networkCreateBox.children('.networkSettings').children('.lanAccess').hasClass('networkOptionsActive')) {
        var networkLANAccess = 1; 
    }
    else{
        var networkLANAccess = 0;
    }

    if (networkCreateBox.children('.networkSettings').children('.activeNet').hasClass('networkOptionsActive')) {
        var networkActive = 1; 
    }
    else{
        var networkActive = 0;
    }

    $.post( "/admin/networks/networkChange.php", { networkName: networkName,networkAddress: networkAddress,networkNATInterface: networkNATInterface,networkPort: networkVPNPort,networkAllowedNetworks: networkAllowedNetworks,networkDisallowedNetworks: networkDisallowedNetworks,lanAccess: networkLANAccess,activeOption:  networkActive}, function(data){

    if(data.includes('Success')){

        return;

    }else{
        return;
    }

    });

});

function changeNATInterface(button, text) {
    if (text == 'None') {
        button.css('background-color', 'transparent');
        button.css('border', '1px solid white');
    }
    else{
        button.css('background-color', 'var(--myRed)');
        button.css('border', '1px solid var(--myRed)');
    }
    button.html(text);
}

$(document).on('click', '.networkAddNATInterface', function(e) {
    currentNATbutton = $(this);
    document.getElementById("NATInterfaceDropdown").innerHTML = '<span onclick="changeNATInterface(currentNATbutton, \'None\')" class="dropdownNATInterface my-1" style="background-color: transparent; border: 1px solid white">None</span>';
 
    for (let i = 0; i < availableInterfaces.length; i++) {
        document.getElementById("NATInterfaceDropdown").innerHTML = document.getElementById("NATInterfaceDropdown").innerHTML + '<span onclick="changeNATInterface(currentNATbutton, \''+availableInterfaces[i]+'\')" class="networkAddNATInterfaceDropdown my-1">'+availableInterfaces[i]+'</span>';
    }

    var x = e.pageX;
    var y = e.pageY;

    $("#NATInterfaceDropdown").css("left", x);
    $("#NATInterfaceDropdown").css("top", y);

    document.getElementById("NATInterfaceDropdown").classList.toggle("dropdown-show");

});

window.onclick = function(event) {
  if (!event.target.matches('.networkAddNATInterface')) {
        document.getElementById("NATInterfaceDropdown").classList.remove("dropdown-show");
  }
}

$(document).on('click', '.networkAddAllowedNetwork', function() {
    $(this).before('<span class="networkAllowedNetwork" contenteditable="true">0.0.0.0/0</span>');
} );


$(document).on('click', '.networkAddDisallowedNetwork', function() {
    $(this).before('<span class="networkDisallowedNetwork" contenteditable="true">0.0.0.0/0</span>');
} );

$('body').on('keyup', '.networkAllowedNetwork', function(e) {
        if((e.key == 'Backspace' || e.key == 'Delete') && $( this ).html() == '') {
        $(this).remove();
    }
});

$('body').on('keyup', '.networkDisallowedNetwork', function(e) {
        if((e.key == 'Backspace' || e.key == 'Delete') && $( this ).html() == '') {
        $(this).remove();
    }
});

function removeNetwork(networkName) {
    $.post( "/admin/networks/networkRemove.php", { networkName: networkName}, function(data){

    if(data.includes('Success')){

        $('.' + networkName + 'Card').parent().fadeOut(500);

        setTimeout(() => {
            getAlert('Network was removed' , 1);
            $('.' + networkName + 'Card').parent().remove();
        }, 500);
        

        availableVPNs = availableVPNs.filter(function(item) {return item !== networkName})
        return;

    }else{
        getAlert(data , 0);
        return;
    }

    });
}

$(document).on('click', '.networkResetButton', function() {

    var rootElement = $(this).parent().parent().parent();

    // Network address
    var newHTML = rootElement.children('.networkAssignedNetworks').children('.networkAddress').attr('default');
    rootElement.children('.networkAssignedNetworks').children('.networkAddress').html(newHTML);

    // NAT interface
    var newHTML = rootElement.children('.networkAssignedInterface').attr('default').replaceAll('\\', '');
    rootElement.children('.networkAssignedInterface').html('<span style="padding-left: 0.5rem;margin-left: 0.1em;">NAT interface:</span>' + newHTML);

    // VPN port
    var newHTML = rootElement.children('.networkAssignedPort').children('.networkPort').attr('default');
    rootElement.children('.networkAssignedPort').children('.networkPort').html(newHTML);

    // Allowed netowrks
    var newHTML = rootElement.children('.networkAllowedNetworks').attr('default').replaceAll('\\', '');
    rootElement.children('.networkAllowedNetworks').html('<span style="padding-left: 0.5rem;margin-left: 0.1em;">Allowed networks:</span></br>' + newHTML + '<span class="networkAddAllowedNetwork">+</span>');

    // Disallowed netowrks
    var newHTML = rootElement.children('.networkDisallowedNetworks').attr('default').replaceAll('\\', '');
    rootElement.children('.networkDisallowedNetworks').html('<span style="padding-left: 0.5rem;margin-left: 0.1em;">Disallowed networks:</span></br>' + newHTML + '<span class="networkAddDisallowedNetwork">+</span>');

    // Network settings
    var newHTML = rootElement.children('.networkSettings').attr('default').replaceAll('\\', '');
    rootElement.children('.networkSettings').html(newHTML);


} );

$( ".networkChangeButton" ).on( "click", function() {

    var rootElement = $(this).parent().parent().parent();

    var networkName = rootElement.children('.networkName').html();

    var networkAddress = rootElement.children('.networkAssignedNetworks').children('.networkAddress').html();

    var networkNATInterface = rootElement.children('.networkAssignedInterface').children('.networkAddNATInterface').html();

    var networkVPNPort = rootElement.children('.networkAssignedPort').children('.networkPort').html();

    var networkAllowedNetworks = '';
    var allowedNetworksLength = rootElement.children('.networkAllowedNetworks').children('.networkAllowedNetwork').length;
    for (let index = 0; index < allowedNetworksLength; index++) {
        networkAllowedNetworks += ',' + (rootElement.children('.networkAllowedNetworks').children('.networkAllowedNetwork')[index]).innerHTML;
    }
    networkAllowedNetworks = networkAllowedNetworks.slice(1);



    var networkDisallowedNetworks = '';
    var disallowedNetworksLength = rootElement.children('.networkDisallowedNetworks').children('.networkDisallowedNetwork').length;
    for (let index = 0; index < disallowedNetworksLength; index++) {
        networkDisallowedNetworks += ',' + (rootElement.children('.networkDisallowedNetworks').children('.networkDisallowedNetwork')[index]).innerHTML;
    }
    networkDisallowedNetworks = networkDisallowedNetworks.slice(1);

    if (rootElement.children('.networkSettings').children('.lanAccess').hasClass('networkOptionsActive')) {
        var networkLANAccess = 1; 
    }
    else{
        var networkLANAccess = 0;
    }

    if (rootElement.children('.networkSettings').children('.activeNet').hasClass('networkOptionsActive')) {
        var networkActive = 1; 
    }
    else{
        var networkActive = 0;
    }

    $.post( "/admin/networks/networkChange.php", { networkName: networkName,networkAddress: networkAddress,networkNATInterface: networkNATInterface,networkPort: networkVPNPort,networkAllowedNetworks: networkAllowedNetworks,networkDisallowedNetworks: networkDisallowedNetworks,lanAccess: networkLANAccess,activeOption:  networkActive}, function(data){

    if(data.includes('Success')){
        getAlert('Network was changed', 1);

        rootElement.fadeOut(500);
        setTimeout(() => {
            if(networkNATInterface == 'None'){
                networkNATInterface = '<span class="networkAddNATInterface">None</span>';
            }
            else{
                networkNATInterface = '<span class="networkAddNATInterface" style="background-color: var(--myRed); border: 1px solid var(--myRed);">'+networkNATInterface+'</span>';
            }

            var networkAllowedNetworksHTML = '';
            networkAllowedNetworks = networkAllowedNetworks.split(',');

            if (networkAllowedNetworks[0] != '') {
                var i = 0;
                while (networkAllowedNetworks.length > i) {
                    networkAllowedNetworksHTML += '<span class="networkAllowedNetwork" contentEditable="true">'+ networkAllowedNetworks[i] +'</span>';
                i++;
                }
            }

            var networkDisallowedNetworksHTML = '';
            networkDisallowedNetworks = networkDisallowedNetworks.split(',');

            if (networkDisallowedNetworks[0] != 0) {
                var i = 0;
                while (networkDisallowedNetworks.length > i) {
                    networkDisallowedNetworksHTML += '<span class="networkDisallowedNetwork" contentEditable="true">' + networkDisallowedNetworks[i] + '</span>';
                    i++;
                }
            }



            if (networkActive == 1) {
                networkActive = `<span class="networkOptions activeNet networkOptionsActive">
                                    <svg class="networkSVGDisabled" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg>
                                    Active
                                </span>`;
            }
            else{
                networkActive = `<span class="networkOptions activeNet">
                                    <svg class="" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512" class="networkSVGDisabled"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg>
                                    Active
                                </span>`;
            }

            if(networkLANAccess == 1){
                networkLANAccess = `<span class="networkOptions lanAccess networkOptionsActive"><svg class="" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512" class="networkSVGDisabled"><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>LAN access</span>`;
            }
            else{
                networkLANAccess = `<span class="networkOptions lanAccess"><svg class="networkSVGDisabled" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><style>svg{fill:#FEFCFB}</style><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512" class=""><style>svg{fill:#FEFCFB}</style><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"></path></svg>LAN access</span>`;
            }

            // Network address
            rootElement.children('.networkAssignedNetworks').children('.networkAddress').html(networkAddress);
            rootElement.children('.networkAssignedNetworks').children('.networkAddress').attr('default', networkAddress.replace(/(['"])/g, "\\$1"));            

            // NAT interface
            rootElement.children('.networkAssignedInterface').html('<span style="padding-left: 0.5rem;margin-left: 0.1em;">NAT interface:</span>' + networkNATInterface);
            rootElement.children('.networkAssignedInterface').attr('default' , networkNATInterface.replace(/(['"])/g, "\\$1"));  

            // VPN port
            rootElement.children('.networkAssignedPort').children('.networkPort').html(networkVPNPort);
            rootElement.children('.networkAssignedPort').children('.networkPort').attr('default' , networkVPNPort.replace(/(['"])/g, "\\$1"));  

            // Allowed netowrks
            rootElement.children('.networkAllowedNetworks').html('<span style="padding-left: 0.5rem;margin-left: 0.1em;">Allowed networks:</span><br>' + networkAllowedNetworksHTML + '<span class="networkAddAllowedNetwork">+</span>');
            rootElement.children('.networkAllowedNetworks').attr('default' , networkAllowedNetworksHTML.replace(/(['"])/g, "\\$1"));  
            // Disallowed netowrks
            rootElement.children('.networkDisallowedNetworks').html('<span style="padding-left: 0.5rem;margin-left: 0.1em;">Disallowed networks:</span><br>' + networkDisallowedNetworksHTML + '<span class="networkAddDisallowedNetwork">+</span>');
            rootElement.children('.networkDisallowedNetworks').attr('default' , networkDisallowedNetworksHTML.replace(/(['"])/g, "\\$1"));  

            // Network settings
            rootElement.children('.networkSettings').html(networkLANAccess + networkActive);
            rootElement.children('.networkSettings').attr('default' , (networkLANAccess + networkActive).replace(/(['"])/g, "\\$1"));  

            
            rootElement.fadeIn(500);
        }, 500);

        return;

    }else{
        getAlert(data, 0);
        return;
    }

    });

});