<?php
/**
 * Paekakariki Online.
 *
 * This file adds the generic post template
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */

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

remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'pae_online_post_header' );
function pae_online_post_header() {

    $display_author = get_field('display_author');

    echo "<div class='post_header'>";
    genesis_do_post_title();
    if (!empty( $display_author ) ) {
        echo sprintf( '<p class="attribution">%s</p>', get_the_date() . ' by '. $display_author);
    }
    if (has_excerpt()) {
        echo sprintf( '<p class="excerpt">%s</p>', get_the_excerpt() );
    }
    echo "</div>";
}

add_filter( 'genesis_post_meta', 'sp_post_meta_filter' );
function sp_post_meta_filter($post_meta) {
    if ( !is_page() ) {
        $post_meta = genesis_post_categories_shortcode(array('before' => 'Filed under: '));
    return $post_meta;
}}

// Run Genesis.
genesis();

