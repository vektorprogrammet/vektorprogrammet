/**
 * Created by jorgenvalstad on 03.10.2016.
 */
$.get("/kontrollpanel/api/assistants", function (data) {
    console.log(data);

})

function getAvailableDays(assistant) {
    // NOTE: The select id is not unique if two people have the same name.
    var select = $('<select>').attr('id', assistant['name']);
    var availability = assistant['availability'];
    for (var i in availability) {
        if (availability[i]) {
            select.append($('<option>', {
                value: i,
                text: i
            }));
        }
    }
    return select
}
$.get("/kontrollpanel/api/allocated_assistants", function (data) {
    console.log(data);

    //Adding a row in allocation_table for each assistant
    var assistants = JSON.parse(data);
    for (var i in assistants) {
        var assistant = assistants[i];
        var name = assistant.name;
        $('.allocation_table')
            .append($('<tr>')
                // One column for name
                .append($('<td>')
                    .text(name)
                )
                // One column for day selection
                .append($('<td>')
                    .append(getAvailableDays(assistant))
                )
            );
    }

});