/**
 * Created by jorgenvalstad on 03.10.2016.
 */

var schools = [];
var assistants = [];
getSchoolsAndAssistants();

///////////////////////////////
////        Classes        ////
///////////////////////////////
function Assistant(assistantData) {
    this.name = assistantData.name;
    this.group = assistantData.group;
    this.doublePosition = assistantData.doublePosition;
    this.preferredGroup = assistantData.preferredGroup;
    this.assignedSchool = getSchoolByName(assistantData.assignedSchool);
    this.assignedDay = assistantData.assignedDay;
    this.availability = assistantData.availability;
    this.availableSchools = null;

    this.getTableRow = function () {
        return $('<tr>')
        // One column for name
            .append($('<td>')
                .text(this.name)
            )
            // One column for day selection
            .append($('<td>')
                .append(this.getAvailableDaysSelect())
            )
            // One column for group selection
            .append($('<td>')
                .append(this.getGroupSelect())
            )
            // One column for school selection
            .append($('<td>')
                .append(this.getSchoolsSelect())
            );
    };

    this.getAvailableDaysSelect = function () {
        var name = this.name;
        var spaceless_name = string_remove_space(name);
        var select = $('<select>').attr('id', spaceless_name);
        for (var day in this.availability) {
            if (this.availability.hasOwnProperty(day) && this.availability[day]) {
                select.append($('<option>', {
                    value: day,
                    text: norwegian_day(day)
                }));
            }
        }
        select.change($.proxy(function () {
            this.assignedSchool = null;
            this.resetAvailableSchools(getAvailableSchoolsByDay(this.getSelectedGroup(), this.getSelectedDay()));
            updateAllAvailableSchools();
        }, this));
        select.addClass("allocation_select");
        return select;
    };

    this.getSchoolsSelect = function () {
        var spaceless_name = string_remove_space(this.name);
        var school_select_id = spaceless_name.concat("SchoolSelect");
        var select = $('<select>').attr('id', school_select_id);
        select.addClass("allocation_select");
        return select;
    };

    this.getGroupSelect = function () {
        var select = $('<select>').attr('id', string_remove_space(this.name).concat("GroupSelect"));
        select.addClass("allocation_select");
        var preferredGroup = this.preferredGroup;
        if (!this.doublePosition) {
            switch (preferredGroup) {
                case null:
                    select.append($('<option>', {value: "Bolk 1", text: "Bolk 1"}));
                    select.append($('<option>', {value: "Bolk 2", text: "Bolk 2"}));
                    break;
                case 1:
                    select.append($('<option>', {value: "Bolk 1", text: "Bolk 1"}));
                    break;
                case 2:
                    select.append($('<option>', {value: "Bolk 2", text: "Bolk 2"}));
                    break;
            }
        } else {
            select.append($('<option>', {value: "Dobbel", text: "Dobbel"}));
            select.append($('<option>', {value: "Bolk 1", text: "Bolk 1"}));
            select.append($('<option>', {value: "Bolk 2", text: "Bolk 2"}));
        }
        select.change($.proxy(function () {
            this.assignedSchool = null;
            this.resetAvailableSchools();
            updateAllAvailableSchools();
        }, this));
        return select;
    };

    this.getSelectedDay = function () {
        return $('#' + string_remove_space(this.name)).val();
    };

    this.getSelectedGroup = function () {
        return $('#' + string_remove_space(this.name).concat("GroupSelect")).val();
    };

    this.updateAvailableSchools = function () {
        var availableSchools = getAvailableSchoolsByDay(this.getSelectedGroup(), this.getSelectedDay());
        // Don't re-render if available schools did not change. Updating the DOM is very expensive.
        if (!schoolListsAreIdentical(this.availableSchools, availableSchools)) {
            this.resetAvailableSchools();
        }
    };

    this.resetAvailableSchools = function () {
        this.availableSchools = getAvailableSchoolsByDay(this.getSelectedGroup(), this.getSelectedDay());

        var spaceless_name = string_remove_space(this.name);
        var school_select_id = spaceless_name.concat("SchoolSelect");
        var select = $('#' + school_select_id);
        select.empty();
        var option = $('<option>', {value: '', text: 'Velg skole'});
        select.append(option);
        this.availableSchools.forEach(function (school) {
            var option = $('<option>', {value: school.name, text: school.name});
            if (this.assignedSchool !== null && this.assignedSchool.name === school.name) {
                option.attr('selected', true)
            }
            select.append(option);
        }, this);
        select.change($.proxy(function () {
            this.assignedSchool = getSchoolByName(select.val());
            updateAllAvailableSchools();
        }, this))
    };
}

function School(schoolData) {
    this.name = schoolData.name;
    this.capacity = schoolData.capacity;

    this.capacityLeftOnDay = function (group, day) {
        var groupId = group === 'Bolk 1' ? '1' : '2';
        var capacityLeft = JSON.parse(JSON.stringify(this.capacity[groupId][day]));
        assistants.forEach(function (assistant) {
            if (assistant.assignedSchool !== null &&
                assistant.assignedSchool.name === this.name &&
                assistant.getSelectedDay() === day &&
                (assistant.getSelectedGroup() === group || assistant.getSelectedGroup() === 'Dobbel')) {
                capacityLeft--;
            }
        }, this);
        return capacityLeft;
    };

    this.hasCapacityLeftOnDay = function (group, day) {
        if (group === 'Dobbel') {
            return this.capacityLeftOnDay('Bolk 1', day) > 0 && this.capacityLeftOnDay('Bolk 2', day) > 0;
        }
        return this.capacityLeftOnDay(group, day) > 0;
    };
}

///////////////////////////////
////          API          ////
///////////////////////////////
function getSchools() {
    $.get("/kontrollpanel/api/schools", function (data) {
        JSON.parse(data).forEach(function (s) {
            schools.push(new School(s));
        });
        getAssistants();
    });
}

function getAssistants() {
    $.get("/kontrollpanel/api/assistants", function (data) {
        JSON.parse(data).forEach(function (a) {
            assistants.push(new Assistant(a));
        });
        generateAllocationTable();
        generateSchoolOverview();
    });
}

function getSchoolsAndAssistants() {
    getSchools();
}

///////////////////////////////
////       Selectors       ////
///////////////////////////////
function getAvailableSchoolsByDay(group, day) {
    var availableSchools = [];
    schools.forEach(function (school) {
        if (school.hasCapacityLeftOnDay(group, day)) {
            availableSchools.push(school);
        }
    });
    return availableSchools;
}

function getSchoolByName(name) {
    for (var i = 0; i < schools.length; i++) {
        var school = schools[i];
        if (school.name === name) {
            return school;
        }
    }
    return null;
}

///////////////////////////////
////   Helper functions    ////
///////////////////////////////
function generateAllocationTable() {
    //Adding a row in allocation_table for each assistant
    $('.allocation_table tbody').empty();
    assistants.forEach(function (assistant) {
        $('.allocation_table').append(assistant.getTableRow());

        // Initialize the school selector
        var day_select = $("#".concat(string_remove_space(name)));
        // updateAvailableSchools(day_select.val(), name);
        assistant.updateAvailableSchools();
        var school_select = $("#".concat(string_remove_space(name).concat("SchoolSelect")));
        // Set the school and day that the algorithm chose
        if (assistant.assignedDay != null) {
            day_select.val(norwegian_day(assistant.assignedDay));
        }
        if (assistant.assignedSchool != null) {
            school_select.val(assistant.assignedSchool);
        }
    });
}

function generateSchoolOverview() {
    var schoolList = $('#schools');
    schoolList.empty();
    ['1', '2'].forEach(function (group) {
        schoolList.append('<h4>Bolk ' + group + '</h4>');
        schools.forEach(function (school) {
            var p = $('<p>');
            p.append(' ' + school.name + ': ');
            ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'].forEach(function (day) {
                var cap = school.capacity[group][day];
                var capLeft = school.capacityLeftOnDay('Bolk ' + group, day);
                var assistantCount = (cap - capLeft);
                if (cap > 0) {
                    var color = 'text-success';
                    if (assistantCount === 0 || assistantCount % 2 === 1) {
                        color = 'text-warning'
                    }
                    p.append('<span class=' + color + '>' + day + ': ' + (cap - capLeft) + '/' + cap + '</span>' + ', ');
                }
            });
            schoolList.append(p);
        })
    });

}

function updateAllAvailableSchools() {
    assistants.forEach(function (assistant) {
        if (assistant.assignedSchool === null) {
            assistant.updateAvailableSchools();
        }
    });
    generateSchoolOverview();
}

///////////////////////////////
////         Utils         ////
///////////////////////////////
/*
 * Remove space from string
 * */
function string_remove_space(str) {
    return str.split(' ').join('');
}

/*
 Convert english day to norwegian day
 */
function norwegian_day(english_day) {
    switch (english_day) {
        case "Monday":
            return "Mandag";
        case "Tuesday":
            return "Tirsdag";
        case "Wednesday":
            return "Onsdag";
        case "Thursday":
            return "Torsdag";
        case "Friday":
            return "Fredag";
    }
}

function schoolListsAreIdentical(schools1, schools2) {
    if (schools1 === null && schools2 === null) {
        return true;
    }
    if (schools1 === null || schools2 === null) {
        return false;
    }
    if (schools1.length !== schools2.length) {
        return false;
    }
    for (var i = 0; i < schools1.length; i++) {
        var id1 = schools1[i];
        var schools2ContainsSchool1 = false;
        for (var j = 0; j < schools2.length; j++) {
            var id2 = schools2[j];
            if (id1 === id2) {
                schools2ContainsSchool1 = true;
                break;
            }
        }
        if (!schools2ContainsSchool1) {
            return false;
        }
    }
    return true;
}
