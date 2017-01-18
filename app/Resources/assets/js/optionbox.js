/**
 * Adds a toggle button and provides toggle functionality to a simple toggle options box.
 * Requires a div container with class option-box which holds a div with class container and/or footer.
 * Also requires the CSS which is specified under options box in app.scss, and font awesome.
 */
(function ($) {
    $.fn.optionbox = function () {

        return this.each(function () {
            // The div which holds the toggle icon
            var div = $("<div></div>").addClass("toggle-caret");

            // The font awesome icon
            var i = $("<i></i>").addClass("fa");

            var optionBox = $(this);

            // Add the icon to the option box
            optionBox.prepend(div.prepend(i));

            // Make sure the right icon is used, depending on the state of the box (closed or not)
            if (optionBox.hasClass("closed")) {
                i.addClass("fa-caret-down");
            } else {
                i.addClass("fa-caret-up");
            }

            // Add click functionality to the icon, and change it depending on the state of the box
            div.click(function () {
                if (optionBox.hasClass("closed")) {
                    optionBox.removeClass("closed");
                    i.addClass("fa-caret-up");
                    i.removeClass("fa-caret-down");
                } else {
                    optionBox.addClass("closed");
                    i.addClass("fa-caret-down");
                    i.removeClass("fa-caret-up");
                }
            });
        });

    };
}(jQuery));
