/**
 * Created by jorgenvalstad on 03.10.2016.
 */
$.get("/kontrollpanel/api/assistants", function (data) {
    console.log(data);
})

function getAvailableDays(assistant) {
    var select = $('select')

    // TODO: Add available days to select

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
            .append($('<tr>').attr('id', name)
                .append($('<td>')
                    .text(name)
                )
                .append($('td')
                    .append(getAvailableDays(assistant))
                )
            );
    }

});