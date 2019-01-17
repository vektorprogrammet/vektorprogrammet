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
        $ele.attr('disabled', true);
    }
})();
