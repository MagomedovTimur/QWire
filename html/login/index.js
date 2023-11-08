$(document).ready(function() { 

    $('.loginForm').removeClass('loginFormClose');
    $( ".myInput" ).removeClass('myInputClose');

});

$( ".loginButton" ).on( "click", function() {

    var inputUsername = $('*[placeholder="Username"]').val();
    var inputPassword = $('*[placeholder="Password"]').val();

    $('.loginForm').addClass('loginFormClose');
    $( ".myInput" ).addClass('myInputClose');

    $.post( "/login/login.php", { username: inputUsername, password: inputPassword }, function(data){

        if(data.includes('Success')){

            $( ".logo" ).addClass('logoClose');

            setTimeout(function(){
                window.location.replace("/admin/");
            }, 3300);

        }else{
            setTimeout(function(){
                getAlert('Incorrect username and/or password', 0);
                $('.loginForm').removeClass('loginFormClose');
                $( ".myInput" ).removeClass('myInputClose');
            }, 2200);
        }

    });


});



$( ".myInput" ).on( "keypress", function() {
    if ( event.which == 13 ) {
    event.preventDefault();
    $('.loginButton').click();
  }
} );