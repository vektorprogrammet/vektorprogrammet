(function onclickDisable(){
    var $submitButtons = $("form button");
    $submitButtons.click(function() {
        var btn = $(this);
        var form = btn.closest('form');
        var isValid = form.get(0).checkValidity();
        if (!isValid || btn.hasClass('ignore-auto-disable')) {
          return;
        }
        if(!btn.hasClass('disabled') && this.type === 'submit'){
          btn.addClass('disabled').addClass('not-clickable');
          btn.click(function(e){
              e.preventDefault()
            })
        }
    });
})();
