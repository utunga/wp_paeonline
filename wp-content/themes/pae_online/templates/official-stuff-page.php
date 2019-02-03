<?php
/**
 * Paekakariki Online
 *
 * Template Name: Official Stuff Main Page
 *
 * This file adds the A-Z page to the Paekakariki Online theme
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */

add_filter( 'body_class', 'pae_online_official_stuff_body_class' );
/**
 * Add landing page body class to the head.
 *
 * @param  array $classes Array of body classes.
 * @return array $classes Array of body classes.
 */
function pae_online_official_stuff_body_class( $classes ) {

	$classes[] = 'official-stuff-page';

	return $classes;

}

add_action( 'wp_enqueue_scripts', 'pae_onlinedequeue_skip_links' );
/**
 * Dequeue Skip Links Script.
 *
 * @return void
 */
function pae_onlinedequeue_skip_links() {

	wp_dequeue_script( 'skip-links' );

}

//// move default page header into main entry
remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );

add_filter( 'genesis_post_info', 'pae_online_post_info' );
function pae_online_post_info($post_info) {
}

//// switch out header for custom header
remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );
add_action( 'genesis_before_content_sidebar_wrap', 'pae_online_general_banner_header' );
function pae_online_general_banner_header() {
    $image = get_field('banner_image');
    $title = get_the_title();
    pae_online_banner_header($image, $title);
}

// ut sidebar over on the side 
remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
add_action( 'genesis_sidebar', 'official_stuff_sidebar' );
function official_stuff_sidebar() {
    genesis_widget_area( 'official-stuff-sidebar', array(
        'before' => '<div class="widget-area official-stuff-sidebar">',
        'after'  => '</div>',
    ) );
}

// put sidebar at bottom
remove_action( 'genesis_after_entry', 'genesis_do_sidebar' );
add_action( 'genesis_after_entry', 'official_stuff_lower_sidebar' ); 
function official_stuff_lower_sidebar() {
    genesis_widget_area( 'official-stuff-lower-sidebar', array(
        'before' => '<div class="widget-area official-stuff-lower-sidebar">',
        'after'  => '</div>',
    ) );

    genesis_widget_area( 'official-stuff-lower-sidebar-2', array(
        'before' => '<div class="widget-area official-stuff-lower-sidebar-2 ">',
        'after'  => '</div>',
    ) );
}

// Run the Genesis loop.
genesis();

?>
