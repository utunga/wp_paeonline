<?php
/**
 * Paekakariki Online.
 *
 * This file adds a template for 'listing' pages. To be used when clubs and groups want to customize content extensively. 
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */

//// move default page header into main entry
remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );

//// Remove content-sidebar-wrap.
//add_filter( 'genesis_markup_content-sidebar-wrap', '__return_null' );

////move post info to end and only include date
// remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
// add_action( 'genesis_entry_header', 'pae_online_post_header' );

// // add_filter( 'genesis_post_info', 'pae_online_post_info' );
// // function pae_online_post_info($post_info) {
// // }

//// switch out header for custom header
remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );
add_action( 'genesis_before_content_sidebar_wrap', 'pae_online_general_banner_header' );
function pae_online_general_banner_header() {
    $image = get_field('banner_image');
    $title = get_the_title();
    pae_online_banner_header($image, $title);
}

// function pae_online_post_header() {

//     $display_author = get_field('display_author');

//     echo "<div class='post_header'>";
//     genesis_do_post_title();
//     echo sprintf( '<p class="excerpt">%s</p>', get_the_excerpt() );
//     echo "</div>";
// }




// Run Genesis.
genesis();

