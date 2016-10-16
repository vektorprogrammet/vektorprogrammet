/**
 * Created by jorgenvalstad on 03.10.2016.
 */
$.get("/kontrollpanel/api/assistants", function (data) {
    console.log(data);
})
$.get("/kontrollpanel/api/allocated_assistants", function (data) {
    console.log(data);

    //var row = $("tr");
    //var col = $("td");
    //var select = $("select");
    //var option =

    //Adding a row in allocation_table for each assistant
    var assistants = JSON.parse(data);
    for (var assistant in assistants) {
        $('.allocation_table')
            .append($('<tr>')
                .append($('<td>')
                    .text(assistants[assistant].name)
                )
                .append($('<td>')
                    .append($('<select>').addClass('allocation_select')
                        .append($('<option>')

                        )
                    )
                )
                .append($('<td>')
                    .append($('<select>').addClass('allocation_select')
                        .append($('<option>')

                        )
                    )
                )
                .append($('<td>')
                    .append($('<select>').addClass('allocation_select')
                        .append($('<option>')

                        )
                    )
                )
            );
    }

})