
( function( $ ) {

    var KeTocPosts = function( $scope, $ ) {

        var $this = $("#ke-toc-posts");

        // Convert html list to tree using Simple Tree Menu Jquery library
        $($this).simpleTreeMenu();

        // Expand the index till the post title
        // @toDo Find a better way to fix it in case of multiple same titles.
        $title = $.trim($('#kb-title').text());
        $($this).simpleTreeMenu('searchForTitle', $title);

        $(".ke-toc-posts-post").click(function(){

            // Use ajax to fetch post
            if ( $($this).attr("data-ajax-enable") === "yes") {
                event.preventDefault();

                $('li').removeClass('selected');
                $(this).parent().addClass('selected');


                post_id = $(this).attr("data-id");
                api_end_point_url = '/wp-json/wp/v2/posts/' + post_id;

                $.ajax({
                    url: api_end_point_url, success: function (result) {

                        $('#kb-content').html(result.content.rendered);
                        $('#kb-title').html(result.title.rendered);

                        if (history.pushState) {
                            window.history.pushState("object or string", "Title", result.link);
                        } else {
                            document.location.href = "/new-url";
                        }
                    }
                });

            }

        });

    };



    // Add Js file to elementor edit page
    $( window ).on( 'elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/ke-toc-posts.default', KeTocPosts );
    } );
} )( jQuery );



