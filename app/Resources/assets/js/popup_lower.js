window.onload = function() {

    $(".cta").on('click', function() {
        $(this).removeClass("popupAttentionUp");
    });


    $(".popup-dismiss").on('click', function() {
        $(".popup").css("display", "none");
    });



    $(".popup").css("visibility", "visible");
    runAnimationIn();

    var scrollFromTop = true;
    var scrollFromBot = true;


    $(window).scroll(function() {
        if(window.scrollY==0){
            if(scrollFromBot) runAnimationUp();
            scrollFromBot = false;
        }else{
            scrollFromBot = true;
        }

        if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if(scrollFromTop) runAnimationDown();
            scrollFromTop = false;
        }else{
            scrollFromTop = true;
        }


    });


    function runAnimationUp() {

        $(".cta").addClass("popupAttentionUp");

        window.setTimeout(function(){
            $(".cta").removeClass("popupAttentionUp");
        }, 500);

    }

    function runAnimationDown(){
        $(".cta").addClass("popupAttentionDown");

        window.setTimeout(function(){
            $(".cta").removeClass("popupAttentionDown");
        }, 500);

    }

    function runAnimationIn(){
        $(".cta").addClass("transitionIn");

        window.setTimeout(function(){
            $(".cta").removeClass("transitionIn");
        }, 500);

    }

};

