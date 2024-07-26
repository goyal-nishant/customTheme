<?php 


function create_custom_post_type() {
    $labels = array(
        'name'                  => _x( 'Books', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Book', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Books', 'text_domain' ),
        'name_admin_bar'        => __( 'Book', 'text_domain' ),
        'archives'              => __( 'Book Archives', 'text_domain' ),
        'attributes'            => __( 'Book Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Book:', 'text_domain' ),
        'all_items'             => __( 'All Books', 'text_domain' ),
        'add_new_item'          => __( 'Add New Book', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Book', 'text_domain' ),
        'edit_item'             => __( 'Edit Book', 'text_domain' ),
        'update_item'           => __( 'Update Book', 'text_domain' ),
        'view_item'             => __( 'View Book', 'text_domain' ),
        'view_items'            => __( 'View Books', 'text_domain' ),
        'search_items'          => __( 'Search Book', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into book', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this book', 'text_domain' ),
        'items_list'            => __( 'Books list', 'text_domain' ),
        'items_list_navigation' => __( 'Books list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter books list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Book', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        'taxonomies'            => array( 'genre', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,		
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type( 'book', $args );

}
add_action( 'init', 'create_custom_post_type', 0 );


function delete_book_attachments() {
    // Arguments for fetching posts of custom post type 'books'
    $args = array(
        'post_type'      => 'book',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    );

    $book_posts = new WP_Query($args);

    if ($book_posts->have_posts()) {
        while ($book_posts->have_posts()) {
            $book_posts->the_post();
            $post_id = get_the_ID();

            $attachments = get_attached_media('', $post_id);

            foreach ($attachments as $attachment) {
                wp_delete_attachment($attachment->ID, true);
            }
        }
    }

    wp_reset_postdata();
}


function remove_images_from_custom_posts() {
    $args = array(
        'post_type' => 'book',
        'posts_per_page' => -1, 
    );

    $books_query = new WP_Query($args);

    if ($books_query->have_posts()) {
        while ($books_query->have_posts()) {
            $books_query->the_post();

            $post_id = get_the_ID();

            $post_content = get_post_field('post_content', $post_id);

            $updated_content = remove_images_from_content($post_content);

            wp_update_post(array(
                'ID'           => $post_id,
                'post_content' => $updated_content,
            ));
        }
    }

    wp_reset_postdata();
}

function remove_images_from_content($content) {
    $updated_content = preg_replace('/<img[^>]+>/', '', $content);
    return $updated_content;
}


function get_url() {
	if(isset($_GET['url'])) {
		// delete_book_attachments();
        // remove_images_from_custom_posts();
	}
}
get_url();


add_action( 'init', function() {
    add_rewrite_rule( '^delete-book-attachments/?$', 'index.php?delete_book_attachments=1', 'top' );

    add_rewrite_rule( '^remove-images-from-custom-posts/?$', 'index.php?remove_images_from_custom_posts=1', 'top' );

    flush_rewrite_rules();
} );


add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'delete_book_attachments';
    $query_vars[] = 'remove_images_from_custom_posts';
    return $query_vars;
} );


add_action( 'template_redirect', function() {
    if ( get_query_var( 'delete_book_attachments' ) ) {
        delete_book_attachments();
        exit;
    }

    if ( get_query_var( 'remove_images_from_custom_posts' ) ) {
        remove_images_from_custom_posts();
        exit;
    }
} );



add_action( 'init', function() {
    add_rewrite_rule( '^custom-action/?$', 'index.php?custom_action=1', 'top' );
    flush_rewrite_rules();
} );

add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'custom_action';
    return $query_vars;
} );

add_action( 'template_redirect', function() {
    if ( $action = get_query_var( 'custom_action' ) ) {
        if ( $action === 'delete_book_attachments' ) {
            delete_book_attachments();
            remove_images_from_custom_posts();
        }
        exit;
    }
} );
    

