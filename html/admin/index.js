var $loader = document.querySelector(".loader");

function blockUserInput(action) {

    if (action == 'block') {
        $('body').addClass('bodyBlocked');
        return;
    }
    if (action == 'unblock') {
        $('body').removeClass('bodyBlocked');
        return;
    }

    $('body').addClass('bodyBlocked');

    window.setTimeout(function () {

        $('body').removeClass('bodyBlocked');

    }, action);

}

$(document).ready(function() { 

    $('.menuButton').fadeTo(500, 1);

    $('.menuButtonLogout').fadeTo(500, 1);
});

$('.menuButton').on( "click", function() {
    blockUserInput('block');
    var buttonHTML = $(this).html(); 

    $('.menuButton').removeClass('menuButtonActive');
    $(this).addClass('menuButtonActive');

      $loader.classList.add("loader--active");

        window.setTimeout(function () {

            $('.clientCardContainer').fadeOut(1);
            $('.networkCardContainer').fadeOut(1);
            $('.monitoringContainer').fadeOut(1);


            switch ( buttonHTML ) {
                case 'Clients':
                    $('.clientCardContainer').fadeIn(1);
                    break;
                case 'Networks':
                    $('.networkCardContainer').fadeIn(1);
                    break;
                case 'Monitoring':
                    $('.monitoringContainer').fadeIn(1);
                    break;
                default:
                    break;
            }

            $loader.classList.remove("loader--active");
            
            window.setTimeout(function () {blockUserInput('unblock');}, 1000);
        }, 1000);

} );


$('.menuButtonLogout').on( "click", function() {
    
    
    $.post( "/logout/index.php", {}, function(data){
        
        $('.contentHover').fadeOut();
        $('.menuButton').fadeTo(500, 0);
        $('.menuButtonLogout').fadeTo(500, 0);

        setTimeout(function(){

            $('.menuHover').css("transform", `translate(0, -50%)`);
            $('.menuHover').css('top','40%');

            setTimeout(function(){
                window.location.replace("/login/");
            }, 1700);

        }, 500);

    });
} );

