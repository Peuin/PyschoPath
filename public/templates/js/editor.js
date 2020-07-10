/**
 * Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/* exported initSample */

if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
	CKEDITOR.tools.enableHtml5Elements( document );

// The trick to keep the editor in the sample quite small
// unless user specified own height.
CKEDITOR.config.height = '400';
CKEDITOR.config.width = 'auto';

var initSample = ( function() {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get( 'bbcode' );

	return function() {
		var editorElement = CKEDITOR.document.getById( 'ckeditor' );
		// Depending on the wysiwygare plugin availability initialize classic or inline editor.
		if ( wysiwygareaAvailable ) {
			CKEDITOR.replace( 'ckeditor' );
		} else {
			editorElement.setAttribute( 'contenteditable', 'true' );
			CKEDITOR.inline( 'ckeditor' );

			// TODO we can consider displaying some info box that
			// without wysiwygarea the classic editor may not work.
		}
	};

	function isWysiwygareaAvailable() {
		// If in development mode, then the wysiwygarea must be available.
		// Split REV into two strings so builder does not replace it :D.
		if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
			return true;
		}

		return !!CKEDITOR.plugins.get( 'wysiwygarea' );
	}
} )();

var initBasic = ( function() {
    var wysiwygareaAvailable = isWysiwygareaAvailable(),
        isBBCodeBuiltIn = !!CKEDITOR.plugins.get( 'bbcode' );

    return function() {
        var editorElement = CKEDITOR.document.getById( 'ckeditor' );
        // Depending on the wysiwygare plugin availability initialize classic or inline editor.
        if ( wysiwygareaAvailable ) {
            CKEDITOR.replace( 'ckeditor' , {
                // Define the toolbar groups as it is a more accessible solution.
                //toolbarGroups: [
                //    {"name":"basicstyles","groups":["basicstyles"]},
                //    {"name":"links","groups":["links"]},
                //    //{"name":"paragraph","groups":["list","blocks"]},
                //    //{"name":"document","groups":["mode"]},
                //    {"name":"insert","groups":["insert"]},
                //    {"name":"styles","groups":["styles"]},
                //],
                // Remove the redundant buttons from toolbar groups defined above.
                removeButtons: 'Save,NewPage,DocProps,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Strike,Subscript,Superscript,RemoveFormat,NumberedList,BulletedList,Outdent,Indent,Blockquote,CreateDiv,BidiLtr,BidiRtl,Link,Unlink,Anchor,CreatePlaceholder,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,InsertPre,Styles,UIColor,Language,ShowBlocks,button1,button2,button3,oembed,MediaEmbed,About'
            } );
        } else {
            editorElement.setAttribute( 'contenteditable', 'true' );
            CKEDITOR.inline( 'ckeditor' , {
                // Define the toolbar groups as it is a more accessible solution.
                toolbarGroups: [
                    {"name":"basicstyles","groups":["basicstyles"]},
                    {"name":"links","groups":["links"]},
                    {"name":"paragraph","groups":["list","blocks"]},
                    {"name":"document","groups":["mode"]},
                    {"name":"insert","groups":["insert"]},
                    {"name":"styles","groups":["styles"]},
                    {"name":"about","groups":["about"]}
                ],
                // Remove the redundant buttons from toolbar groups defined above.
                removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar'
            } );
            // TODO we can consider displaying some info box that
            // without wysiwygarea the classic editor may not work.
        }

    };

    function isWysiwygareaAvailable() {
        // If in development mode, then the wysiwygarea must be available.
        // Split REV into two strings so builder does not replace it :D.
        if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
            return true;
        }

        return !!CKEDITOR.plugins.get( 'wysiwygarea' );
    }
} )();

