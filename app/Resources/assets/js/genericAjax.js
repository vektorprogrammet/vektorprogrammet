/*
 Provides a function to do ajax calls to the database.
 */
(function ($) {

    // Takes in options
    $.fn.genericAjax = function (options) {

        // Default settings
        var settings = $.extend({
            confirmation: {
                modal: null,
                acceptButton: null,
                cancelButton: null,
                jsConfirmDialogMessage: 'Er du sikker på at du vil redigere rettighetene til brukeren?',
                disable: false
            }
        }, options);


        // Checks if a custom Foundation modal dialog is specified
        var customModal = settings.confirmation.modal && settings.confirmation.acceptButton;

        // Holds the clicked button, with the entity id, for use with custom modal
        var button = null;

        // Adds callbacks to the custom modal buttons
        if (customModal) {
            settings.confirmation.acceptButton.click(function () {
                settings.confirmation.modal.foundation('reveal', 'close');
                requestAjax(button);
                button = null;
            });
            if (settings.confirmation.cancelButton) {
                settings.confirmation.cancelButton.click(function () {
                    settings.confirmation.modal.foundation('reveal', 'close');
                });
            }
        }

        // Adds a callback to the selected buttons, each holding an id = an entity id
        this.click(function () {
            // Open custom modal dialog
            if (customModal) {
                button = this;
                settings.confirmation.modal.foundation('reveal', 'open');
                // Open default javascript confirmation dialog
            } else if (!settings.confirmation.disable) {
                if (confirm(settings.confirmation.confirmMessage)) {
                    requestAjax(this);
                }
                // Skip dialog
            } else {
                requestAjax(this);
            }
        });


        function requestAjax(button) {

            // The route is the button id
            var role = button.id;
            // the entityId is the name of the button
            var id = parseInt(button.name);

            var pathName = 'profile_change_role';
            if (role == 'profile_activate_user') {
                pathName = 'profile_activate_user';
            } else if (role == 'profile_deactivate_user') {
                pathName = 'profile_deactivate_user';
            }

            $.ajax({
                type: 'POST',
                url: Routing.generate(pathName, {id: id}),
                data: {role: role},
                cache: false,
                success: function (response) {

                    if (response.success) {
                        alert("Endringene ble lagret.");
                    }
                    else {
                        alert("Det oppstod en feil.");
                    }
                }
            });
        }

        // Return to enable function chaining
        return this;
    };

}(jQuery));
