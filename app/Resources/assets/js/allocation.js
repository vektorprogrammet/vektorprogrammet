/**
 * Created by jorgenvalstad on 03.10.2016.
 */
$.get("/kontrollpanel/api/assistants", function (data) {

})

function getAvailableDays(assistant) {
    // NOTE: The select id is not unique if two people have the same name.
    var spaceless_name = assistant['name'].split(' ').join('');
    var select = $('<select>').attr('id', spaceless_name);
    var availability = assistant['availability'];
    for (var i in availability) {
        if (availability[i]) {
            select.append($('<option>', {
                value: i,
                text: i
            }));
        }
    }
    select.change(function () {
        updateAvailableSchools(select.val())
    })
    return select
}
$.get("/kontrollpanel/api/allocated_assistants", function (data) {
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

$.get("/kontrollpanel/api/schools_and_days", function (data) {
    var schools = JSON.parse(data);
    console.log(schools);
});
/*
* Update the select options based on the chosen day
* */
function updateAvailableSchools(day) {

}