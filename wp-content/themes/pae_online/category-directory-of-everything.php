<?php
/**
 * Paekakariki Online
 *
 * Template Name: A-Z Page
 *
 * This file adds the A-Z page to the Paekakariki Online theme
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */

add_filter( 'body_class', 'pae_online_a_to_z_body_class' );
/**
 * Add landing page body class to the head.
 *
 * @param  array $classes Array of body classes.
 * @return array $classes Array of body classes.
 */
function pae_online_a_to_z_body_class( $classes ) {
	$classes[] = 'a-to-z-page';
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


// switch out header for custom header
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );


/** Replace the standard loop with our custom loop */
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pae_online_everything_dir' );
function pae_online_everything_dir() {
    $category = get_queried_object(); 
	$intro_text = get_term_meta( $category->term_id, 'intro_text', true );
	$intro_text = do_shortcode( $intro_text);
	$title = $category->name; 
   

    ?>
    <div class="generic_wrap our_stories">
    	<div class="stories_category">
	    	<h3><?php echo $title ?></h3>
	        <div class="directory_category_list">
	            
                <div class="intro_area">
                    <?php echo $intro_text ?>
                </div>
                <?php the_widget('EverythingDirectory_Category_List_Widget');  ?>
       
	        </div>
	    </div>
    </div>

    <?php
}



// function force_not_is_archive( $query ) {
//  	$query->is_archive = false;
// }
// add_action( 'pre_get_posts', 'force_not_is_archive' );


// Run the Genesis loop.
genesis();

?>
