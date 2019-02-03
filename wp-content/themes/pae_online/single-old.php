<?php
/**
 * Paekakariki Online
 *
 * Template Name: Listing/Post/General Page
 *
 * This file adds the landing page template to the Paekakariki Online Theme.
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */

add_filter( 'body_class', 'pae_online_category_body_class' );
/**
 * Add landing page body class to the head.
 *
 * @param  array $classes Array of body classes.
 * @return array $classes Array of body classes.
 */
function pae_online_category_body_class( $classes ) {

	$classes[] = 'category-page';

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


////move post info to end and only include date
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );

//// switch out header for custom header
remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );
add_action( 'genesis_before_content_sidebar_wrap', 'pae_online_general_banner_header' );
function pae_online_general_banner_header() {
    $image = get_field('banner_image');
    $title = get_the_title();
    pae_online_banner_header($image, $title, $sub_text);
}

//add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

add_filter( 'genesis_post_info', 'pae_online_post_info' );
function pae_online_post_info($post_info) {
	return "";
}

add_action( 'genesis_entry_header', 'pae_online_entry_header', 5 );
function pae_online_entry_header() {

    $short_description  = get_field("short_description");
    $long_description  = get_field("long_description");
    $teaser = trim($long_description) ? $long_description : $short_description;
?>

    <div class="teaser">
        <?php echo $teaser ?>
    </div>

<?php
    
}

// Run the Genesis loop.
genesis();


