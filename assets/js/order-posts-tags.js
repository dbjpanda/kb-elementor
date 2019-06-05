jQuery(document).ready(function($) {


    if(jQuery('body').hasClass('wp-admin')) {
        var itemList = jQuery('#the-list');
    }
    else{
        var itemList = jQuery('.ke-toc-posts');
    }

    itemList.sortable({
        update: function(event, ui) {

            if(jQuery('body').hasClass('wp-admin')){

                if(jQuery('tr', this).attr('id').replace(/[^A-Za-z]+/g, '') === 'tag'){
                    var action = 'uc_sort_term_items';
                }
                else {
                    var action = 'uc_sort_post_items';
                }

                var arr = jQuery('tr', this);
                var i, n;
                var order = [];
                for (i = 0, n = arr.length; i < n; i++) {
                    order.push(jQuery(arr[i]).attr('id').match(/\d+/));
                }
                var order = order.toString();

            }
            else{

                if(ui.item.hasClass('Node') ){

                    var action = 'uc_sort_term_items';
                    var arr = jQuery('.Node', this);
                    var i, n;
                    var order = [];
                    for (i = 0, n = arr.length; i < n; i++) {
                        order.push(jQuery('a',arr[i]).attr('data-id'));
                    }
                    var order = order.toString();
                }
                else{
                    var action = 'uc_sort_post_items';
                    var arr = jQuery('li', this);
                    var i, n;
                    var order = [];
                    for (i = 0, n = arr.length; i < n; i++) {
                        order.push(jQuery('a',arr[i]).attr('data-id'));
                    }
                    var order = order.toString();
                }
            }


            jQuery.ajax({
                cache: false,
                url: '/wp-admin/admin-ajax.php',
                type: 'post',
                dataType: 'json',
                data:{
                    action: action, // Tell WordPress how to handle this ajax request
                    order: order // Passes ID's of list items in  1,3,2 format
                },
                error: function(xhr,textStatus,e) {
                    alert(e);
                }

                });

        }
    });

    // Append "Add post" option on frontend
    var term = jQuery('.ke-toc-posts-term');
    if(term.length){
        jQuery(term).each(function() {
            jQuery(this).after( '<a href = \"/wp-admin/post-new.php?post_cat='+jQuery(this).text()+'\" target=\"_blank\"><i class=\"fa fa-plus-circle\" aria-hidden=\"true\"></i></a>' );

        });
    }
    else{
        jQuery('.ke-toc-posts').append( '<li><span class="stm-icon"></span><a class=\"stm-content\" href = \"/wp-admin/post-new.php?post_cat='+jQuery(location).attr("href").match(/[^\/]*$/)[0]+'\" target=\"_blank\">Add post <i class=\"fa fa-plus-circle\" aria-hidden=\"true\"></i></a></li>' );

    }

});

