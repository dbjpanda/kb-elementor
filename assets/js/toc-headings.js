

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


        /**
         *  Tocbot configuration
         */
        tocbot.init({
            // Where to render the table of contents.
            tocSelector: '.ke-toc-headings-nav',

            // Where to grab the headings to build the table of contents.
            contentSelector: contentSelector,

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

        // Prepend show/hide icon to toc nav block
        // @toDo Don't insert empty tag
        navBlock.prepend('<p class="ke-toc-title">' + navBlock.attr('data-title') + '<i class="' + navBlock.attr('data-title-icon-active') + '"></i></p>');

        // Show/hide animation
        $(".ke-toc-title > i").click(function(){

            // Show/Hide TOC. For animation use toggle(linear or swing)
            $('ul', navBlock).toggle();

            // Change Icon
            $(this).toggleClass(navBlock.attr('data-title-icon-inactive'));
        });


        // Create a placeholder for original toc position if not exist
        // @toDo Don't insert tag anywhere except elimentor's editor page
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
