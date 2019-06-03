<?php
/*
Plugin Name: Everything Directory
Description: Everything Directory is a plugin for keeping track of all the things in the directory of everything
Author: @utunga
Author URI: http://github.com/utunga
Version: 0.5

Text Domain: everything-directory
Domain Path: /languages/

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

register_activation_hook( __FILE__, 'everythingdir_listings_activation' );
/**
 * This function runs on plugin activation. It checks to make sure the required
 * minimum Genesis version is installed. If not, it deactivates itself.
 *
 * @since 0.1.0
 */
function everythingdir_listings_activation() {

		$latest = '2.0.2';

		if ( 'genesis' != get_option( 'template' ) ) {

			//* Deactivate ourself
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( sprintf( __( 'Sorry, you can\'t activate unless you have installed <a href="%s">Genesis</a>', 'everything-directory' ), 'http://my.studiopress.com/themes/genesis/' ) );

		}

		if ( version_compare( wp_get_theme( 'genesis' )->get( 'Version' ), $latest, '<' ) ) {

			//* Deactivate ourself
			deactivate_plugins( plugin_basename( __FILE__ ) ); /** Deactivate ourself */
			wp_die( sprintf( __( 'Sorry, you cannot activate without <a href="%s">Genesis %s</a> or greater', 'everything-directory' ), 'http://www.studiopress.com/support/showthread.php?t=19576', $latest ) );

		}

		/** Flush rewrite rules */
		if ( ! post_type_exists( 'listing' ) ) {

			everythingdir_listings_init();
			global $_everythingdir_listings, $_everythingdir_taxonomies;
			$_everythingdir_listings->create_post_type();
			$_everythingdir_taxonomies->register_taxonomies();

		}

		flush_rewrite_rules();

}

add_action( 'after_setup_theme', 'everythingdir_listings_init' );
/**
 * Initialize Everything Directory.
 *
 * Include the libraries, define global variables, instantiate the classes.
 *
 * @since 0.1.0
 */
function everythingdir_listings_init() {

	/** Do nothing if a Genesis child theme isn't active */
	if ( ! function_exists( 'genesis_get_option' ) )
		return;

	global $_everythingdir_listings, $_everythingdir_taxonomies;

	define( 'APL_URL', plugin_dir_url( __FILE__ ) );
	define( 'APL_VERSION', '1.2.6' );

	/** Load textdomain for translation */
	load_plugin_textdomain( 'everything-directory', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	/** Includes */
	require_once( dirname( __FILE__ ) . '/includes/functions.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-listings.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-taxonomies.php' );
    require_once( dirname( __FILE__ ) . '/includes/class-listings-widget.php' );
    require_once( dirname( __FILE__ ) . '/includes/class-category-links-widget.php' );
    require_once( dirname( __FILE__ ) . '/includes/class-category-list-widget.php' );
    require_once( dirname( __FILE__ ) . '/includes/class-a-to-z-widget.php' );
    require_once( dirname( __FILE__ ) . '/includes/class-listing-sidebar-widget.php' );
    require_once( dirname( __FILE__ ) . '/includes/class-listing-widget.php' );

	/** Instantiate */
	$_everythingdir_listings = new EverythingDirectory_Listings;
	$_everythingdir_taxonomies = new EverythingDirectory_Taxonomies;

	add_action( 'widgets_init', 'everythingdir_register_widgets' );

}

/**
 * Register Widgets that will be used in the Everything Directory plugin
 *
 * @since 0.1.0
 */
function everythingdir_register_widgets() {

	$widgets = array(
        //'EverythingDirectory_Featured_Listings_Widget',
        //'EverythingDirectory_Listings_Search_Widget',
		'EverythingDirectory_Listing_Widget',
        'EverythingDirectory_Listing_Sidebar_Widget',
        'EverythingDirectory_Category_Links_Widget',
        'EverythingDirectory_Listings_Widget',
        'EverythingDirectory_A_to_Z_Widget',
        'EverythingDirectory_Category_List_Widget');

	foreach ( (array) $widgets as $widget ) {
		register_widget( $widget );
	}

}
