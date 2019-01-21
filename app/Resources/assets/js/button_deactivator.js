(function onclickDisable(){
    var $submitButtons = $("form button");
    $submitButtons.click(function() {
        var btn = $(this);
        if(!btn.hasClass('disabled') && this.type === 'submit'){
          btn.addClass('disabled');
          btn.click(function(e){
              e.preventDefault()
            })
        }
    });
})();
