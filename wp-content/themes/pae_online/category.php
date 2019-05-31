<?php
/**
 * Paekakariki Online
 *
 * Template Name: Category Page
 *
 * This file adds the category page template to the Paekakariki Online Theme.
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */


add_filter( 'body_class', 'pae_online_category_body_class' );
function pae_online_category_body_class( $classes ) {

    $classes[] = 'category-page';

    return $classes;

}

add_action( 'wp_enqueue_scripts', 'pae_onlinedequeue_skip_links' );
function pae_onlinedequeue_skip_links() {
    wp_dequeue_script( 'skip-links' );
}

function is_directory_of_everything() {
    $cat = get_queried_object();
    $parent_cats_str = get_category_parents($cat);
    return preg_match("/directory of everything/i",$parent_cats_str);
}

if (is_directory_of_everything()) {

    ////move post info to end and only include date
    remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
    remove_action( 'genesis_loop_else', 'genesis_do_noposts' );

    //// switch out header for custom header
    remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );
    add_action( 'genesis_before_content_sidebar_wrap', 'pae_online_category_banner_header' );
    function pae_online_category_banner_header() {
        $category = get_queried_object();
        $image = get_field('banner_image', 'category'.'_'.$category->term_id);
        $title = $category->name; 
        pae_online_banner_header($image, $title);
    }


    add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

    /** Replace the standard loop with our custom loop */
    remove_action( 'genesis_loop', 'genesis_do_loop' );
    add_action( 'genesis_loop', 'pae_online_category_custom_loop' );
    function pae_online_category_custom_loop() {

        $category = get_queried_object();
        $sub_category_ids = get_term_children($category->term_id, 'category');
        if ( empty( $sub_category_ids ) || is_wp_error( $sub_category_ids ) )
        {
            render_listing_widget_for_category(array(
                'category' => $category,
                'show_title' => true,
                'show_intro' => true,
                'foldable' => false));
        }
        else
        {
          
            foreach ($sub_category_ids as $sub_category_id)
            {
                render_listing_widget_for_category(array(
                    'category' => get_term($sub_category_id, 'category'),
                    'show_title' => true,
                    'show_intro' => false,
                    'foldable' => true
                ));
            }
        }
        
    }

}

   
// Run the Genesis loop.
genesis();


