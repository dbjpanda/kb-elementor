

( function( $ ) {
    "use strict";

    var KeTocHeadings = function( $scope, $ ) {

        var navSelector = '.ke-toc-headings-nav';
        var navBlock = $(navSelector);

        var contentSelector = navBlock.attr('data-content-selector');
        var contentBlock = $(contentSelector);

        var headingSelectors = navBlock.attr('data-heading-selectors').split(' ').toString();


        /**
         * Add id attribute to each headings
         * @param  string id
         */
        contentBlock.find(headingSelectors).each(function () {
            var heading = $(this);
            var id = heading.attr('id');

            if (id === undefined || id === '') {
                id = heading.text()
                    .trim()
                    .toLowerCase()
                    .replace(/\s+/g, '-')     // Replace spaces with -
                    .replace(/&/g, '-and-')   // Replace & with 'and'
                    .replace(/[^\w\-]+/g, '') // Remove all non-word chars
                    .replace(/\-\-+/g, '-')   // Replace multiple - with single -;
                    .replace(/^-|-$/g,"");    // Replace dash from beginning and end
            }
            heading.attr('id', id);
            // heading.prepend(anchorById(id));
        });


        // @toDo remove above code with this plain vanilla js if performance matters
        // var headings = contentBlock.find(headingSelectors);
        // var headingMap = {};
        //
        // Array.prototype.forEach.call(headings, function (heading) {
        //     var id = heading.id ? heading.id : heading.textContent.trim().toLowerCase()
        //         .split(' ').join('-').replace(/[\!\@\#\$\%\^\&\*\(\)\:]/ig, '');
        //     headingMap[id] = !isNaN(headingMap[id]) ? ++headingMap[id] : 0;
        //     if (headingMap[id]) {
        //         heading.id = id + '-' + headingMap[id]
        //     } else {
        //         heading.id = id
        //     }
        // });


        /**
         * Prepend an anchor with the provided ID
         * @param  string id
         */
        // function anchorById (id) {
        //     var anchor = $('<a></a>');
        //
        //     anchor.attr({
        //         href: '#' + id,
        //         class: 'header-link'
        //     });
        //
        //     anchor.html( '<svg aria-hidden="true" height="12" width="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8"><path d="M5.88.03c-.18.01-.36.03-.53.09-.27.1-.53.25-.75.47a.5.5 0 1 0 .69.69c.11-.11.24-.17.38-.22.35-.12.78-.07 1.06.22.39.39.39 1.04 0 1.44l-1.5 1.5c-.44.44-.8.48-1.06.47-.26-.01-.41-.13-.41-.13a.5.5 0 1 0-.5.88s.34.22.84.25c.5.03 1.2-.16 1.81-.78l1.5-1.5c.78-.78.78-2.04 0-2.81-.28-.28-.61-.45-.97-.53-.18-.04-.38-.04-.56-.03zm-2 2.31c-.5-.02-1.19.15-1.78.75l-1.5 1.5c-.78.78-.78 2.04 0 2.81.56.56 1.36.72 2.06.47.27-.1.53-.25.75-.47a.5.5 0 1 0-.69-.69c-.11.11-.24.17-.38.22-.35.12-.78.07-1.06-.22-.39-.39-.39-1.04 0-1.44l1.5-1.5c.4-.4.75-.45 1.03-.44.28.01.47.09.47.09a.5.5 0 1 0 .44-.88s-.34-.2-.84-.22z" /></svg>' );
        //
        //     return anchor;
        // }


        /**
         *  Tocbot configuration
         */
        tocbot.init({
            // Where to render the table of contents.
            tocSelector: '.ke-toc-headings-nav',

            // Where to grab the headings to build the table of contents.
            contentSelector: '.ke-toc-headings-content',

            // Which headings to grab inside of the contentSelector element.
            headingSelector: headingSelectors,

            orderedList: false,

            // Refer https://github.com/tscanlin/tocbot/issues/111
            headingsOffset: 1,

            // Smooth scrolling enabled.
            scrollSmooth: navBlock.attr('data-smooth-scroll-enabled'),

            // Smooth scroll duration.
            scrollSmoothDuration: navBlock.attr('data-scroll-duration'),

            extraListClasses: 'ke-toc-headings-nav-text'
        });



        navBlock.prepend('<p class="ke-toc-title">' + navBlock.attr('data-title') + '<i class="' + navBlock.attr('data-title-icon-active') + '"></i></p>');

        $(".ke-toc-title > i").click(function(){

            // Show/Hide TOC. For animation use toggle(linear or swing)
            $('ul', navBlock).toggle();

            // Change Icon
            $(this).toggleClass(navBlock.attr('data-title-icon-inactive'));
        });



        // Show/Hide Collapsible button
        // $(".ke-toc-title-icon").click(function(){
        //     $(this).toggleClass($(this).attr('data-icon'));
        //     navBlock.toggle('swing');
        //     $(this).closest('div').css("display", "inline-block");
        // });



        // Create a placeholder for original toc position if not exist
        var placeHolder = $('.ke-toc-headings-nav-placeholder');

        if( ! placeHolder.length){
            navBlock.closest('.elementor-widget').after('<span class="ke-toc-headings-nav-placeholder"></span>');
        }

        if ( navBlock.attr('data-stick-to-content') === 'yes' ){

            contentBlock.prepend(navBlock.closest('.elementor-widget'));
            navBlock.closest('.elementor-widget').css("float", navBlock.attr('data-stick-to-content-alignment'));
        }
        else{
            placeHolder.prepend(navBlock.closest('.elementor-widget'));
        }

    };


    // Call `KeTocHeadings` only when `skin1` is ready
    $( window ).on( 'elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/toc-headings.skin1', KeTocHeadings );
    } );

    // Call `KeTocHeadings` only when `skin2` is ready
    $( window ).on( 'elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/toc-headings.skin2', KeTocHeadings );
    } );

} )( jQuery );
