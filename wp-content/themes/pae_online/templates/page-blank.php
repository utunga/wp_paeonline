<?php
/**
 * Paekakariki Online
 *
 * Template Name: Blank Page
 *
 * This file adds a completely blank page template to the Paekakariki Online
 * theme. It removes everything except for the page content, leaving a
 * completely blank template with no site header or site footer.
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {

	die;

}

// Remove default page header.
remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );

// Custom loop, remove all hooks except entry content.
if ( have_posts() ) :

	the_post();

	do_action( 'genesis_entry_content' );

endif;
