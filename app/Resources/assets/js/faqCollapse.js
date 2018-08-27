(function() {
    var faq = $('#about-faq')[0].children;

    $('.faq h5').click(function(){
        var index = $(this).index();
        $(faq[index + 1]).slideToggle("slow");
    })
})();
