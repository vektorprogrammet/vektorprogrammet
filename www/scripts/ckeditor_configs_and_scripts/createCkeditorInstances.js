//jQuery script for inserting clickable images that starts ckeditor on a html element.
//TODO: Trekk formattering av knapper ut av funksjonene og putt de enten øverst her eller i separat fil
//slik at man slipper å lese gjennom hele koden for å finne hvor man endrer på utseendet.

/**
 *
 *
 * todo:  dokumentasjon
 */
function createCkeditorInstances(){
    CKEDITOR.disableAutoInline = true;
    $("div.editable").each(	function() {
        $(this).attr('contenteditable', 'true');
        $(this).ckeditor({
                customConfig: "ckeditor_static_text_config.js"
        }
        );
    });
}