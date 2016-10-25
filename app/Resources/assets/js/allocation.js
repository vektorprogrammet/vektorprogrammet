/**
 * Created by jorgenvalstad on 03.10.2016.
 */

var school_availability;

$.get("/kontrollpanel/api/assistants", function (data) {

})

function getAvailableDays(assistant) {
    // NOTE: The select id is not unique if two people have the same name.
    var name = assistant['name'];
    var spaceless_name = string_remove_space(name);
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
        updateAvailableSchools(select.val(), name);
    })
    select.addClass("allocation_select");
    return select;
}


function generateSchoolSelect(assistant) {
    var name = assistant['name'];
    var spaceless_name = string_remove_space(name);
    var school_select_id = spaceless_name.concat("SchoolSelect");
    var select = $('<select>').attr('id', school_select_id);
    select.addClass("allocation_select");
    return select;
}


function generateGroupSelect(assistant) {
    var name = assistant['name'];
    var spaceless_name = string_remove_space(name);
    var select = $('<select>');
    select.addClass("allocation_select");
    select.append($('<option>', {value: "Bolk 1", text: "Bolk 1"}));
    select.append($('<option>', {value: "Bolk 2", text: "Bolk 2"}));
    select.append($('<option>', {value: "Dobbel", text: "Dobbel"}));
    return select;
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
                // One column for school selection
                .append($('<td>')
                    .append(generateSchoolSelect(assistant))
                )
                // One column for group selection
                .append($('<td>')
                    .append(generateGroupSelect(assistant))
                )
            );
    }
});

$.get("/kontrollpanel/api/schools_and_days", function (data) {
    school_availability = JSON.parse(data);
});
/*
* Update the select options based on the chosen day
* */
function updateAvailableSchools(day, assistant_name) {
    var school_select_id = string_remove_space(assistant_name).concat("SchoolSelect");
    var school_select = $("#".concat(school_select_id));
    school_select.find('option').remove();
    for(var i in school_availability[day]) {
        school_select.append($('<option>', {
            value: school_availability[day][i],
            text: school_availability[day][i]
        }));
    }

}
/*
* Remove space from string
* */
function string_remove_space(str) {
    return str.split(' ').join('');
}