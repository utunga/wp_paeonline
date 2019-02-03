<?php
/**
 * Holds miscellaneous functions for use in the Everything Directory plugin
 *
 */


// UTIL functions 
add_image_size('sponsor-thumb-size', 240, 220, false); // width, height, crop
add_image_size('sponsor-thumb-mobile-size', 120, 110, false); // width, height, crop

/**
 * Returns true if the given predicate is true for all elements.
 */
function array_every(callable $callback, array $arr) {
  foreach ($arr as $element) {
    if (!$callback($element)) {
      return FALSE;
    }
  }
  return TRUE;
}
/**
 * Returns true if the given predicate is true for at least one element.
 */
function array_any(callable $callback, array $arr) {
  foreach ($arr as $element) {
    if ($callback($element)) {
      return TRUE;
    }
  }
  return FALSE;
}

/**
 * Returns true if item content not empty
 */
function not_empty($item) {
    return (trim($item));
}

/**
 * This function redirects the user to an admin page, and adds query args
 * to the URL string for alerts, etc.
 *
 * This is just a temporary function, until WordPress fixes add_query_arg(),
 * or Genesis 1.8 is released, whichever comes first.
 *
 */
function apl_admin_redirect( $page, $query_args = array() ) {

	if ( ! $page )
		return;

	$url = html_entity_decode( menu_page_url( $page, 0 ) );

	foreach ( (array) $query_args as $key => $value ) {
		if ( isset( $key ) && isset( $value ) ) {
			$url = add_query_arg( $key, $value, $url );
		}
	}

	wp_redirect( esc_url_raw( $url ) );

}

genesis_register_sidebar( array(
	'id'		=> 'sidebar-everythingdir-listing',
	'name'		=> __( 'Listing Sidebar', 'nabm' ),
	'description'	=> __( 'This is the sidebar for Everything Directory listings.', 'nabm' ),
) );

// Swap sidebars on listing type pages
add_action('get_header','child_change_genesis_sidebar');
function child_change_genesis_sidebar() {
    if ( is_singular('listing')) { 
        remove_action( 'genesis_sidebar', 'genesis_do_sidebar' ); //remove the default genesis sidebar
        add_action( 'genesis_sidebar', 'everythingdir_listing_sidebar' ); //add an action hook to call the function for my custom sidebar
    }
}
  
// Output sidebar with the id 'sidebar-everythingdir-listing'
function everythingdir_listing_sidebar() {
    genesis_widget_area( 'sidebar-everythingdir-listing' );
}


function listing_a_z_view($title, $listing) {
    include( dirname( __FILE__ ) . '/views/listing-a-z-view.php' );
}

function listing_sidebar_view($listing) {
    include( dirname( __FILE__ ) . '/views/listing-sidebar-view.php' );
}


function empty_content($str) {
    return trim(str_replace('&nbsp;','',strip_tags($str, '<img>'))) == '';
}

// only works within the_loop
function build_listing($page) {
    
    $services = array_filter(array(get_field('service_0'),  get_field('service_1'),  get_field('service_2')));
    $featured_checkboxes = get_field("featured_checkboxes");
    $listing = (object) [
        "ID" => $page->ID,
        "slug" => $page->post_name,
	    "contact_name" => get_field("contact_name"),
	    "address" => get_field("address"),
	    "phone_number" => get_field("phone_number"),
	    "email_address" => get_field("email_address"),
	    "opening_hours" => get_field("opening_hours"),
	    "duration" => get_field("duration"),
	    "map" => get_field("map"),
	    "website" => get_field("website"),
	    "logo" => get_field('logo'), 
	    "short_description" => get_field("short_description"),
        "long_description" => get_field("long_description"),
        "services" => $services,
        "supporter" => ( $featured_checkboxes && in_array('supporter', $featured_checkboxes) ),
        "commercial" => ( $featured_checkboxes && in_array('commercial', $featured_checkboxes) ),
        "title" => get_the_title(),
        "has_content" => !empty_content($page->post_content),
        "page_link" => get_page_link($page->ID),
        "is_featured" => false,
        "is_redirect_only" => false,
        "disable_page_link" => false
    ];

    // if no content to speak of but there is a website link
    if (!empty_content($listing->website) and
	    !$listing->has_content and
	    empty_content($listing->long_description) and
	    empty_content($listing->address) and 
	    empty_content($listing->phone_number) and 
	    empty_content($listing->email_address) and 
	    empty_content($listing->opening_hours) and 
	    empty_content($listing->short_description))
    {
        $listing->is_redirect_only = true;
    }

    if ($listing->commercial && !$listing->supporter) {
        $listing->disable_page_link = true;
        $listing->has_content=false;
        $listing->website = "";
        $listing->is_featured = false;
        $listing->logo = null;
    }
    else if ($listing->supporter) {
        $listing->is_featured = true;
    }
    return $listing;
}