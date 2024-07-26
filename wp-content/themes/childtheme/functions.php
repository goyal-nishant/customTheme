<?php

/* Custom Post Type Start */

    function create_posttype() {

        register_post_type( 'location',
        array(
        'labels' => array(
        'name' => __( 'locations' ),
        'singular_name' => __( 'locations' )
        ),  
        'public' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'locations'),
        ));
    }
    add_action( 'init', 'create_posttype' );
    
    
    function create_location_taxonomy() {
        $labels = array(
            'name'              => _x('Location Categories', 'taxonomy general name'),
            'singular_name'     => _x('Location Category', 'taxonomy singular name'),
            'search_items'      => __('Search Location Categories'),
            'all_items'         => __('All Location Categories'),
            'parent_item'       => __('Parent Location Category'),
            'parent_item_colon' => __('Parent Location Category:'),
            'edit_item'         => __('Edit Location Category'),
            'update_item'       => __('Update Location Category'),
            'add_new_item'      => __('Add New Location Category'),
            'new_item_name'     => __('New Location Category Name'),
            'menu_name'         => __('Location Categories'),
        );
    
        $args = array(
            'hierarchical'      => true, 
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'location-category'),
        );
    
        register_taxonomy('location_category', array('location'), $args);
    }
    add_action('init', 'create_location_taxonomy');

$terms = get_the_terms(get_the_ID(), 'location_category');
if ($terms && !is_wp_error($terms)) {
    echo '<ul>';
    foreach ($terms as $term) {
        echo '<li>' . esc_html($term->name) . '</li>';
    }
    echo '</ul>';
}


add_action('wp_ajax_filter_locations', 'filter_locations');
add_action('wp_ajax_nopriv_filter_locations', 'filter_locations');

function filter_locations() {
    $term_id = isset($_GET['term_id']) ? intval($_GET['term_id']) : 0;

    $args = array(
        'post_type' => 'location',
        'posts_per_page' => -1,
    );

    if ($term_id && $term_id !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'location_category',
                'field'    => 'term_id',
                'terms'    => $term_id,
            ),
        );
    }

    $query = new WP_Query($args);
    $output = '';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $address = get_post_meta(get_the_ID(), 'address', true);
            $icon = get_post_meta(get_the_ID(), 'icon_url', true);

            $geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=AIzaSyA8ga5NZSYzrU2SnCqDssEB-kizBQzVipg';
            $response = wp_remote_get($geocode_url);
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body);

            if ($data->status === 'OK') {
                $latitude = $data->results[0]->geometry->location->lat;
                $longitude = $data->results[0]->geometry->location->lng;

                $output .= '<button data-lat="' . esc_attr($latitude) . '" data-lng="' . esc_attr($longitude) . '" data-icon="' . esc_url($icon) . '">' . get_the_title() . '</button>';
            } else {
                $output .= '<button>' . get_the_title() . ' (Address not found)</button>';
            }
        }
    } else {
        $output = '<p>No locations found.</p>';
    }

    wp_reset_postdata();

    echo $output;
    wp_die();
}



function enqueue_custom_styles() {
    wp_enqueue_style('custom-map-style', get_stylesheet_directory_uri() . '/map.css');
}

add_action('wp_enqueue_scripts', 'enqueue_custom_styles');


function get_location_buttons() {
    $args = array(
        'post_type' => 'location', 
        'posts_per_page' => -1 
    );
    
    $query = new WP_Query($args);
    $output = '';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $address = get_post_meta(get_the_ID(), 'address', true); 
            $icon = get_post_meta(get_the_ID(), 'icon_url', true); 


            $geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=AIzaSyA8ga5NZSYzrU2SnCqDssEB-kizBQzVipg';
            $response = wp_remote_get($geocode_url);
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body);

            if ($data->status === 'OK') {
                $latitude = $data->results[0]->geometry->location->lat;
                $longitude = $data->results[0]->geometry->location->lng;

                $output .= '<button data-lat="' . esc_attr($latitude) . '" data-lng="' . esc_attr($longitude) . '" data-icon="' . esc_url($icon) . '">' . get_the_title() . '</button>';
            } else {
                $output .= '<button>' . get_the_title() . ' (Address not found)</button>';
            }
        }
    } else {
        $output = '<p>No locations found.</p>';
    }
    
    wp_reset_postdata();
    
    return $output;
}

