<?php
/**
 * Paekakariki Online.
 *
 * This file adds a template for 'listing' pages.
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */

//// move default page header into main entry
remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );

//// switch out header for custom header
remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );
add_action( 'genesis_before_content_sidebar_wrap', 'pae_online_general_banner_header' );
function pae_online_general_banner_header() {
    $image = get_field('banner_image');
    $title = get_the_title();
    pae_online_banner_header($image, $title);
}

$listing = build_listing(get_post($post->ID));

remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'pae_online_post_header' );
function pae_online_post_header() {
    global $listing;
    if ($listing->has_content) {
        echo "<div class='post_header'>";
        genesis_do_post_title();
        echo "</div>";
    }
}

add_filter( 'genesis_post_meta', 'sp_post_meta_filter' );
function sp_post_meta_filter($post_meta) {
    
    $post_meta = do_shortcode('Last modified: [post_modified_date], [post_modified_time]');
    $post_meta = $post_meta . "<br />" . do_shortcode('Date published: [post_date]');
    
    return $post_meta;
}

add_action( 'genesis_after_entry_content', 'pae_single_listing_widget' );
function pae_single_listing_widget() {
     global $listing;
     if ($listing->has_content) 
        $title = "";
     else 
        $title = get_the_title();

	 render_single_listing_widget(array(
        'title' => $title,
        'show_services' => true // show all the services for this listing
     ));
}


// Run Genesis.
genesis();

