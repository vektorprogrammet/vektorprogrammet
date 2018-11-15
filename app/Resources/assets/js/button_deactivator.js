(function onclickDisable(){
    $("form").submit(function(e){
        var $submitButtons = $("button", this);
        $submitButtons.each(function() {
            if(!this.disabled && this.type === 'submit'){
                disableElement($(this));
            }
        });
    });

    function disableElement($ele){
        var outerWidth = $ele.outerWidth();
        $ele.css("width", outerWidth);
        $ele.empty();
        $ele.attr('disabled', true);
        $ele.append($('<i class="fal fa-spin fa-spinner-third"></i>'));
    }
})();
