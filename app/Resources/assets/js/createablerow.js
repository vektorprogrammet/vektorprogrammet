/**
 * Adds functionality to make add a row of data to a table
 * provided route and data object.
 */

(function ($) {
    $.fn.createablerow = function(route, options, data){
        // Options and data are optional.
        // An id given by the button id is always sent together
        // with the rest of data's contents

        // TODO, Warning: has not yet been tested with a provided data parameter

        if(!data){
            var data = {};
        }

        // Default settings
        var settings = $.extend({
            confirmation: {
                modal: null,
                acceptButton: null,
                cancelButton: null,
                jsConfirmDialogMessage: 'Er du sikker på at du vil legget til denne raden?',
                disable: false
            }
        }, options);

        // Check if custom modal dialogue is specified
        var customModal = settings.confirmation.modal && settings.confirmation.acceptButton;

        // Holds the clicked button, with the entity id, for use with custom modal
        var button = null;

        // Adds callbacks to the custom modal buttons
        if (customModal) {
            settings.confirmation.acceptButton.on("click", function (e) {
                e.preventDefault();
                settings.confirmation.modal.foundation('reveal', 'close');
                requestCreate(button);
                button = null;
            });
            if (settings.confirmation.cancelButton) {
                settings.confirmation.cancelButton.on("click", function (e) {
                    e.preventDefault();
                    settings.confirmation.modal.foundation('reveal', 'close');
                });
            }
        }

        this.on("click", function (e) {
            e.preventDefault();
            if (customModal) {
                button = this;
                // Open custom modal dialog
                settings.confirmation.modal.foundation('reveal', 'open');
                if (settings.confirmation.message) {
                    settings.confirmation.message.html("Vil du legge til " + this.getAttribute("name") + " som vikar?");
                }
                // Open default javascript confirmation dialog
            } else if (!settings.confirmation.disable) {
                if (confirm(settings.confirmation.confirmMessage)) {
                    requestCreate(this);
                }
                // Skip dialog
            } else {
                requestCreate(this);
            }
        });

        // Takes a button with an id = an entity id and performs an ajax request to the specified route to delete the entity
        function requestCreate(button) {
            $.extend(data,{ id: parseInt(button.id) });
            $.ajax({
                type: 'POST',
                url: Routing.generate(route, data),
                cache: false,
                success: function (response) {
                    // Redirect to substitutes page if insert succeeded
                    if (response.success) {
                        window.location.replace("vikar/" + response.department
                            + "?semester=" + response.semester
                            + "&newsub=" + response.subId);
                    } else {
                        alert(response.cause);
                    }
                }
            });
        }

        // Return to enable function chaining
        return this;
    };
}(jQuery));