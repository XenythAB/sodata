<link rel="stylesheet" type="text/css" href="https://necolas.github.io/normalize.css/latest/normalize.css">
<link rel="stylesheet" type="text/css" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="wysiwyg/dist/wysiwyg.css" />
<script type="text/javascript" src="wysiwyg/wysiwyg.js"></script>
<style>
body {
    font-family: Arial,Verdana;
}
</style>
<script type="text/javascript">
var editor1_commands;
function ready(fn)
{
    if( document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading" )
        fn();
    else
        document.addEventListener( 'DOMContentLoaded', fn );
}
ready(function() {
    // Buttons
    var htmlparser = document.implementation.createHTMLDocument('');
    htmlparser.body.innerHTML = '<button>Insert text</button>';
    var customButton = htmlparser.body.firstChild;
    // Buttons: https://material.io/tools/icons/
    var buttons = [
        // generic
        customButton,
        // open popup
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/></svg>',
            popup: function( commands, popup, button ) {
                    var smilies = [ 'afraid.png','amorous.png','angel.png','angry.png','bored.png','cold.png','confused.png','cross.png','crying.png','devil.png','disappointed.png','dont-know.png','drool.png',
                                    'embarrassed.png','excited.png','excruciating.png','eyeroll.png','happy.png','hot.png','hug-left.png','hug-right.png','hungry.png','invincible.png','kiss.png','lying.png',
                                    'meeting.png','nerdy.png','neutral.png','party.png','pirate.png','pissed-off.png','question.png','sad.png','shame.png','shocked.png','shut-mouth.png','sick.png','silent.png',
                                    'sleeping.png','sleepy.png','stressed.png','thinking.png','tongue.png','uhm-yeah.png','wink.png','working.png','bathing.png','beer.png','boy.png','camera.png','chilli.png',
                                    'cigarette.png','cinema.png','coffee.png','girl.png','console.png','grumpy.png','in_love.png','internet.png','lamp.png','mobile.png','mrgreen.png','musical-note.png','music.png',
                                    'phone.png','plate.png','restroom.png','rose.png','search.png','shopping.png','star.png','studying.png','suit.png','surfing.png','thunder.png','tv.png','typing.png','writing.png' ];
                    smilies.forEach( function( filename )
                    {
                        var smiley = document.createElement('img');
                        smiley.src = 'wysiwyg/smiley/' + filename;
                        smiley.width = 16;
                        smiley.height = 16;
                        smiley.style.cursor = 'pointer';
                        smiley.onclick = function( e ) {
                            e.preventDefault();
                            e.stopPropagation();
                            var smileyhtml = ' <img src="wysiwyg/smiley/' + filename + '" width="16" height="16">&nbsp; ';
                            commands.insertHTML( smileyhtml ); // 'commands.insertHTML(...).closePopup()' - to close popup
                        };
                        popup.style.textAlign = 'center';
                        popup.style.width = '20em';
                        popup.style.padding = '0.5em';
                        popup.appendChild( smiley );
                        popup.appendChild( document.createTextNode(' ') );
                    });
                },
            attr: { // attributes
                title: 'Smilies',
            },
        },
        // insert image
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>',
            dataurl: function( commands, type,dataurl, button ) {
                    if( ! type.match(/^image/i) )
                        return;
                    commands.insertHTML( ' <img src="'+dataurl+'" style="max-width:100%;max-height:20em;"> <br><br> ' );
                },
            attr: { // attributes
                title: 'Insert image',
            },
        },
        // attach file
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>',
            browse: function( commands, input,file, button ) {
                    button.style.position = 'relative';
                    var badge = button.querySelector('div.badge');
                    if( ! badge ) {
                        badge = document.createElement('div');
                        badge.className = 'badge';
                        badge.style.position = 'absolute';
                        badge.style.top = '-3px';
                        badge.style.right = '-3px';
                        badge.style.fontSize = '0.6em';
                        badge.style.padding = '1px 3px';
                        badge.style.color = 'white';
                        badge.style.backgroundColor = '#fa3e3e';
                        badge.style.borderRadius = '3px';
                        badge.textContent = '1';
                        button.appendChild( badge );
                    }
                    else
                        badge.textContent = parseInt(badge.textContent) + 1;
                },
            attr: { // attributes
                title: 'Attach files',
            },
        },
        // simple buttons
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>',
            action: 'link',
            attr: { // attributes
                title: 'Insert link',
            },
        },
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M15.6 10.79c.97-.67 1.65-1.77 1.65-2.79 0-2.26-1.75-4-4-4H7v14h7.04c2.09 0 3.71-1.7 3.71-3.79 0-1.52-.86-2.82-2.15-3.42zM10 6.5h3c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-3v-3zm3.5 9H10v-3h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>',
            action: 'bold',
            hotkey: 'b',
            attr: { // attributes
                title: 'Bold (Ctrl+B)',
            },
        },
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M10 4v3h2.21l-3.42 8H6v3h8v-3h-2.21l3.42-8H18V4z"/></svg>',
            action: 'italic',
            hotkey: 'i',
            attr: { // attributes
                title: 'Italic (Ctrl+I)',
            },
        },
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 17c3.31 0 6-2.69 6-6V3h-2.5v8c0 1.93-1.57 3.5-3.5 3.5S8.5 12.93 8.5 11V3H6v8c0 3.31 2.69 6 6 6zm-7 2v2h14v-2H5z"/></svg>',
            action: 'underline',
            hotkey: 'u',
            attr: { // attributes
                title: 'Underline (Ctrl+U)',
            },
        },
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M7.24 8.75c-.26-.48-.39-1.03-.39-1.67 0-.61.13-1.16.4-1.67.26-.5.63-.93 1.11-1.29.48-.35 1.05-.63 1.7-.83.66-.19 1.39-.29 2.18-.29.81 0 1.54.11 2.21.34.66.22 1.23.54 1.69.94.47.4.83.88 1.08 1.43.25.55.38 1.15.38 1.81h-3.01c0-.31-.05-.59-.15-.85-.09-.27-.24-.49-.44-.68-.2-.19-.45-.33-.75-.44-.3-.1-.66-.16-1.06-.16-.39 0-.74.04-1.03.13-.29.09-.53.21-.72.36-.19.16-.34.34-.44.55-.1.21-.15.43-.15.66 0 .48.25.88.74 1.21.38.25.77.48 1.41.7H7.39c-.05-.08-.11-.17-.15-.25zM21 12v-2H3v2h9.62c.18.07.4.14.55.2.37.17.66.34.87.51.21.17.35.36.43.57.07.2.11.43.11.69 0 .23-.05.45-.14.66-.09.2-.23.38-.42.53-.19.15-.42.26-.71.35-.29.08-.63.13-1.01.13-.43 0-.83-.04-1.18-.13s-.66-.23-.91-.42c-.25-.19-.45-.44-.59-.75-.14-.31-.25-.76-.25-1.21H6.4c0 .55.08 1.13.24 1.58.16.45.37.85.65 1.21.28.35.6.66.98.92.37.26.78.48 1.22.65.44.17.9.3 1.38.39.48.08.96.13 1.44.13.8 0 1.53-.09 2.18-.28s1.21-.45 1.67-.79c.46-.34.82-.77 1.07-1.27s.38-1.07.38-1.71c0-.6-.1-1.14-.31-1.61-.05-.11-.11-.23-.17-.33H21z"/></svg>',
            action: 'strikethrough',
            hotkey: 's',
            attr: { // attributes
                title: 'Strikethrough (Ctrl+S)',
            },
        },
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path fill-opacity=".36" d="M0 20h24v4H0z"/><path d="M11 3L5.5 17h2.25l1.12-3h6.25l1.12 3h2.25L13 3h-2zm-1.38 9L12 5.67 14.38 12H9.62z"/></svg>',
            action: 'colortext',
            attr: { // attributes
                title: 'Text color',
            },
        },
        {
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M16.56 8.94L7.62 0 6.21 1.41l2.38 2.38-5.15 5.15c-.59.59-.59 1.54 0 2.12l5.5 5.5c.29.29.68.44 1.06.44s.77-.15 1.06-.44l5.5-5.5c.59-.58.59-1.53 0-2.12zM5.21 10L10 5.21 14.79 10H5.21zM19 11.5s-2 2.17-2 3.5c0 1.1.9 2 2 2s2-.9 2-2c0-1.33-2-3.5-2-3.5z"/><path fill-opacity=".36" d="M0 20h24v4H0z"/></svg>',
            action: 'colorfill',
            attr: { // attributes
                title: 'Background color',
            },
        },
        {
            icon: '<span class="fa fa-subscript"></span>', // font-awesome demo
            action: 'subscript',
            attr: { // attributes
                title: 'Subscript',
            },
        },
        {
            icon: '<span class="fa fa-superscript"></span>', // font-awesome demo
            action: 'superscript',
            attr: { // attributes
                title: 'Superscript',
            },
        },
        {
            icon: '<span class="fa fa-list-ol"></span>', // font-awesome demo
            action: 'orderedlist',
            attr: { // attributes
                title: 'Ordered list',
            },
        },
        {
            icon: '<span class="fa fa-list-ul"></span>', // font-awesome demo
            action: 'unorderedlist',
            attr: { // attributes
                title: 'Unordered list',
            },
        },
        {
            icon: '<span class="fa fa-eraser"></span>', // font-awesome demo
            action: 'clearformat',
            attr: { // attributes
                title: 'Remove format',
            },
        },
    ];

    var suggester = function( term, response )
    {
        // Suggestions start with '@'
        if( term[0] != '@' )
            return false;
        // You may want to ask the server ...
        var fill_suggestion = function()
            {
                var suggestions = [];
                var usernames = ['Evelyn','Harry','Amelia','Oliver','Isabelle','Eddie','Editha','Jacob','Emily','George','Edison'];
                usernames.forEach( function( username, index )
                {
                    var re = new RegExp( '^(' + term.substring(1).replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')(.*)$', 'i' );
                    if( term == '@' || re.test(username) )
                    {
                        var codepoint = 0xF600 + index;
                        var fakeavatar = String.fromCharCode( ((codepoint >>>10) & 0x3FF) | 0xD800 ) +
                                         String.fromCharCode( 0xDC00 | (codepoint & 0x3FF) );
                        suggestions.push({
                            label: fakeavatar + ' ' + (term != '@' ? username.replace(re,'$1<b>$2</b>') : username),
                            insert: '<a href="/' + username + '">@' + username + '</a>&nbsp;'
                        });
                    }
                });
                response( suggestions.length ? suggestions : null );  // null = close suggestions
            };
        // simulate: immediate, fast and slow internet
        if( Math.random() < 0.5 )
            fill_suggestion();
        else
            setTimeout( fill_suggestion, Math.random() > 0.9 ? 5000 : 100 );
        return true;
    };

    var interceptenter = function()
    {
        return false;
    };

    editor1_commands = wysiwyg( '#editor1', {
        toolbar: 'top',
        buttons: buttons.slice(1),
        selectionbuttons: buttons.slice(2,3).concat( buttons.slice(4,13) ),
        suggester: suggester,
        interceptenter: interceptenter,
        hijackmenu: true
    });

    // insert text
    document.querySelector('#outside').addEventListener('click', function() {
        editor1_commands.insertHTML( '<i>some text</i>&nbsp;' );
    });
    customButton.addEventListener('click', function() {
        editor2_commands.insertHTML( '<i>some text</i>&nbsp;' );
    });

});
</script>
<div id="editor1" class="wysiwyg">
  <textarea name="editor" placeholder="Write a comment ..."></textarea>
</div>
