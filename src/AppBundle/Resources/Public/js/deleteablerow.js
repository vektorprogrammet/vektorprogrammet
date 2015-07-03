/**
 * Provides delete functionality to a table of entities using ajax.
 * Use on a collection of buttons, each with an id equal to an entity id.
 * Requires a route which deletes the entity with the specified id and returns a json response.
 */
(function ( $ ) {

    // Route is required, options are optional
    $.fn.deleteablerow = function( route, options ) {

        // Default settings
        var settings = $.extend({
            confirmation: {
                modal: null,
                acceptButton: null,
                cancelButton: null,
                jsConfirmDialogMessage: 'Er du sikker på at du vil slette denne raden?',
                disable: false
            }
        }, options);

        // Checks if a custom Foundation modal dialog is specified
        var customModal = settings.confirmation.modal && settings.confirmation.acceptButton;

        // Holds the clicked button, with the entity id, for use with custom modal
        var button = null;

        // Adds callbacks to the custom modal buttons
        if(customModal) {
            settings.confirmation.acceptButton.on("click", function(e) {
                e.preventDefault();
                settings.confirmation.modal.foundation('reveal', 'close');
                requestDelete(button);
                button = null;
            });
            if(settings.confirmation.cancelButton) {
                settings.confirmation.cancelButton.on("click", function(e) {
                    e.preventDefault();
                    settings.confirmation.modal.foundation('reveal', 'close');
                });
            }
        }

        // Adds a callback to the selected buttons, each holding an id = an entity id
        this.on("click", function(e) {
            e.preventDefault();
            // Open custom modal dialog
            if(customModal) {
                button = this;
                settings.confirmation.modal.foundation('reveal', 'open');
            // Open default javascript confirmation dialog
            } else if(!settings.confirmation.disable) {
                if (confirm(settings.confirmation.confirmMessage)) {
                    requestDelete(this);
                }
            // Skip dialog
            } else {
                requestDelete(this);
            }
        });

        // Takes a button with an id = an entity id and performs an ajax request to the specified route to delete the entity
        function requestDelete( button ) {
            var row = $(button).closest('tr');
            var entityId = parseInt(button.id);

            $.ajax({
                type: 'GET',
                url: Routing.generate(route, { id: entityId }),
                cache: false,
                success: function(response) {
                    // Remove the row from the table if the entity was successfully deleted
                    if (response.success) {
                        row.fadeOut('slow', function() {
                            $(this).remove();
                        });
                    } else {
                        alert(response.cause);
                    }
                }
            });
        }

        // Return to enable function chaining
        return this;
    };

}( jQuery ));