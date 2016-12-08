/**
    This ia backup of configuration file used to define which editor controls are enabled on the editor
    when editing static texts.

    The folder scripts/ckeditor contains files as downloaded from the ckeditor website. If you upgrade
    that installation, you have to copy this file into that folder and remove the .bak-ending. But to be on
     the safe side, backup the entire scripts/ckeditor folder anyways. Just in case there are some adaptions
     there I'm not aware of.

*/


CKEDITOR.editorConfig = function(config){
    config.skin = 'bootstrapck';

    // The toolbar groups arrangement
    config.toolbarGroups = [
        //{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        //{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'links' },
        { name: 'insert' },
        //{ name: 'forms' },
        //{ name: 'tools'},
        { name: 'document',	   groups: ['Sourcedialog', 'mode', 'document', 'doctools' ] },
         //'/',  <= when uncommented this will put the following controls on a new line in the interface
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup'] },
        { name: 'paragraph', groups: [ 'list'] },
        { name: 'others', groups: [ 'Inlinesave', 'Inlinecancel'] },
        { name: 'styles' }

        //{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },

        //{ name: 'colors' }
        //{ name: 'about' }
    ];

    //Removing some buttons that is not needed (?).
    //For a list of the names of the buttons: (may be incomplete..)
    //http://ckeditor.com/forums/CKEditor/Complete-list-of-toolbar-items
    config.removeButtons = 'Underline,Subscript,Superscript,Anchor,Table,SpecialChar,HorizontalRule';

    // Set the most common block elements.
    config.format_tags = 'p;h1;h2;h3;pre';

    //Enable plugins
    config.extraPlugins = 'sourcedialog,inlinesave,inlinecancel';

    // Simplify the dialog windows.
    config.removeDialogTabs = 'image:advanced;link:advanced';

    //UI color
    config.uiColor = '#008CBA';

    //Sets ElFinder as the filebrowser to open when looking for image files to add
    config.filebrowserImageBrowseUrl = "elfinder";

}; 
