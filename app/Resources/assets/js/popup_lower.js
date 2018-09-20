window.onload = function() {

    $(".js-popup-toggle").on('click', function() {
        $("body").toggleClass("popup-active");
    });

    $(".cta").on('click', function() {
        $(this).removeClass("popupAttention");
    });


    $(".popup-dismiss").on('click', function() {
        $(".popup").css("display", "none");
    });

    var firstScroll = true;

    $(window).on('scroll', function() {
        if (firstScroll) {
            firstScroll = false;
            $(".popup").css("visibility", "visible");
            runAnimation();


        }
    });

    var scrollFromTop = true;
    var scrollFromBot = true;
    var itCount = 0;


    $(window).scroll(function() {
        console.log(scrollFromBot, scrollFromTop)

        if(window.scrollY==0){
            if(scrollFromBot) runAnimation();
            scrollFromBot = false;
        }else{
            scrollFromBot = true;
        }

        if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if(scrollFromTop) runAnimation();
            scrollFromTop = false;
        }else{
            scrollFromTop = true;
        }


    });

    function runAnimation() {
        itCount +=1;
        $(".cta.popupAttention").css("animation-iteration-count", itCount);


    }

};

