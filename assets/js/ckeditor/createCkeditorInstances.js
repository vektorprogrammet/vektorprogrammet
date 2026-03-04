//jQuery script for inserting clickable images that starts ckeditor on a html element.
//slik at man slipper å lese gjennom hele koden for å finne hvor man endrer på utseendet.

function createCkeditorInstances(){
    CKEDITOR.disableAutoInline = true;
    $("div.editable").each(	function() {
        $(this).attr('contenteditable', 'true');
        $(this).ckeditor({
                customConfig: "./ckeditor_static_text_config.js"
        }
        );
    });
}
