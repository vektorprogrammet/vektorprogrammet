CKEDITOR.plugins.add( 'inlinecancel',
{
	init: function( editor )
	{
		editor.addCommand( 'inlinecancel',
			{
				exec : function( editor )
				{    
					if(confirm("Avbryt redigering og last inn siden på nytt? Endringer gjort etter siste lagring vil gå tapt."))
						location.reload(true);
				}
			});
		editor.ui.addButton( 'Inlinecancel',
		{
			label: 'Cancel and Reload Page',
			command: 'inlinecancel',
			icon: this.path + 'images/inlinecancel.png'
		} );
	}
} );