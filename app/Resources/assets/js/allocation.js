/**
 * Created by jorgenvalstad on 03.10.2016.
 */
$.get("/kontrollpanel/api/assistants", function (data) {
    console.log(data);

})

function getAvailableDays(assistant) {
    var select = $('<select>');
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
                .append($('<td>')
                    .text(name)
                )
                .append($('<td>')
                    .append(getAvailableDays(assistant))
                )
            );
    }

});