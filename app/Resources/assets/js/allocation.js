/**
 * Created by jorgenvalstad on 03.10.2016.
 */
$.get("/kontrollpanel/api/assistants", function (data) {
    console.log(data);
})
$.get("/kontrollpanel/api/allocated_assistants", function (data) {
    console.log(data);

    var row = $("tr");
    var col = $("td");
    var select = $("select");
    var option =

    var assistants = JSON.parse(data);
    for (var assistant in assistants);
        $(/*class/id/tag*/).append(assistants[assistant].name);
    }




})