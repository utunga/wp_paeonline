<?php
/**
 * This file contains the EverythingDirectory_Listings class.
 */

/**
 * This class handles the creation of the "Listings" post type, and creates a
 * UI to display the Listing-specific data on the admin screens.
 *
 */
class EverythingDirectory_Listings {

	public $settings_field = 'everythingdir_taxonomies';
	public $menu_page = 'register-taxonomies';

    /**
     * Property details array.
     */
    public $property_details;

	/**
	 * Construct Method.
	 */
	function __construct() {

        // handle this with custom fields
        //$this->property_details = apply_filters( 'everythingdir_property_details', array(
        //    'col1' => array(
        //        __( 'Price:', 'everything-directory' )   => '_listing_price',
        //        __( 'Address:', 'everything-directory' ) => '_listing_address',
        //        __( 'City:', 'everything-directory' )    => '_listing_city',
        //        __( 'State:', 'everything-directory' )   => '_listing_state',
        //        __( 'ZIP:', 'everything-directory' )     => '_listing_zip'
        //    ),
        //    'col2' => array(
        //        __( 'MLS #:', 'everything-directory' )       => '_listing_mls',
        //        __( 'Square Feet:', 'everything-directory' ) => '_listing_sqft',
        //        __( 'Bedrooms:', 'everything-directory' )    => '_listing_bedrooms',
        //        __( 'Bathrooms:', 'everything-directory' )   => '_listing_bathrooms',
        //        __( 'Basement:', 'everything-directory' )    => '_listing_basement'
        //    )
        //) );

		add_action( 'init', array( $this, 'create_post_type' ) );

		add_filter( 'manage_edit-listing_columns', array( $this, 'columns_filter' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'columns_data' ) );

		add_action( 'admin_menu', array( $this, 'register_meta_boxes' ), 5 );
		add_action( 'save_post', array( $this, 'metabox_save' ), 1, 2 );

		add_shortcode( 'property_details', array( $this, 'property_details_shortcode' ) );
		add_shortcode( 'property_map', array( $this, 'property_map_shortcode' ) );
		add_shortcode( 'property_video', array( $this, 'property_video_shortcode' ) );

		#add_action( 'admin_head', array( $this, 'admin_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'all_js' ) );

		add_filter( 'search_template', array( $this, 'search_template' ) );

		add_filter( 'genesis_build_crumbs', array( $this, 'breadcrumbs' ), 10, 2 );


        function filter_listings_orderby(  $q) { 
            if ( $q->is_main_query()  
                 && ( $q->is_search() || $q->is_post_type_archive( 'listing' ) )
            ) {
                $q->set( 'order', 'ASC');
                $q->set( 'orderby', 'slug');
            }
        }; 
        add_action( 'pre_get_posts', 'filter_listings_orderby');
	}

	/**
	 * Creates our "Listing" post type.
	 */
	function create_post_type() {

		$args = apply_filters( 'everythingdir_listings_post_type_args',
			array(
				'labels' => array(
					'name'               => __( 'Everything Directory', 'everything-directory' ),
					'singular_name'      => __( 'Listing', 'everything-directory' ),
					'add_new'            => __( 'Add New', 'everything-directory' ),
					'add_new_item'       => __( 'Add New Listing', 'everything-directory' ),
					'edit'               => __( 'Edit', 'everything-directory' ),
					'edit_item'          => __( 'Edit Listing', 'everything-directory' ),
					'new_item'           => __( 'New Listing', 'everything-directory' ),
					'view'               => __( 'View Listing', 'everything-directory' ),
					'view_item'          => __( 'View Listing', 'everything-directory' ),
					'search_items'       => __( 'Search Listings', 'everything-directory' ),
					'not_found'          => __( 'No listings found', 'everything-directory' ),
					'not_found_in_trash' => __( 'No listings found in Trash', 'everything-directory' )
				),
				'public'        => true,
				'query_var'     => true,
				'menu_position' => 6,
				'menu_icon'     => 'dashicons-admin-home',
				'has_archive'   => true,
				'supports'      => array( 'title', 'editor', 'comments', 'thumbnail', 'genesis-seo', 'genesis-layouts', 'genesis-simple-sidebars' ),
				'rewrite'       => array( 'slug' => 'listings' ),
			)
		);

		register_post_type( 'listing', $args );

	}

	function register_meta_boxes() {
        add_meta_box( 'listing_details_metabox', __( 'Other Details', 'everything-directory' ), array( &$this, 'listing_details_metabox' ), 'listing', 'normal', 'high' );
	}

	function listing_details_metabox() {
		include( dirname( __FILE__ ) . '/views/listing-details-metabox.php' );
	}

	function metabox_save( $post_id, $post ) {

		if ( ! isset( $_POST['everythingdir_details_metabox_nonce'] ) || ! isset( $_POST['ap'] ) )
			return;

		/** Verify the nonce */
	    if ( ! wp_verify_nonce( $_POST['everythingdir_details_metabox_nonce'], 'everythingdir_details_metabox_save' ) )
	        return;

		/** Run only on listings post type save */
		if ( 'listing' != $post->post_type )
			return;

	    /** Don't try to save the data under autosave, ajax, or future post */
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
	    if ( defined( 'DOING_CRON' ) && DOING_CRON ) return;

	    /** Check permissions */
	    if ( ! current_user_can( 'edit_post', $post_id ) )
	        return;


        $property_details = $_POST['ap'];

        /** Store the custom fields */
        foreach ( (array) $property_details as $key => $value ) {

            /** Save/Update/Delete */
            if ( $value ) {
                update_post_meta($post->ID, $key, $value);
            } else {
                delete_post_meta($post->ID, $key);
            }

        }

	}

	/**
	 * Filter the columns in the "Listings" screen, define our own.
	 */
	function columns_filter ( $columns ) {

		$columns = array(
			'cb'                 => '<input type="checkbox" />',
			'title'              => __( 'Listing Title', 'everything-directory' ),
			'listing_details'    => __( 'Description', 'everything-directory' ),
			'listing_categories' => __( 'Taxonomy', 'everything-directory' ),
            'listing_slug'  => __( 'Slug', 'everything-directory' ),	
            'listing_thumbnail'  => __( 'Featured Logo', 'everything-directory' ),			
		);

		return $columns;

	}

	/**
	 * Filter the data that shows up in the columns in the "Listings" screen, define our own.
	 */
	function columns_data( $column ) {

		global $post, $wp_taxonomies;

		switch( $column ) {
			case "listing_details":
				printf( '%s', get_field("short_description"));
				break;

			case "listing_categories":
				foreach ( (array) get_option( $this->settings_field ) as $key => $data ) {
                    if ($key=='service') 
                        continue;
					printf( '<b>%s:</b> %s<br />', esc_html( $data['labels']['singular_name'] ), get_the_term_list( $post->ID, $key, '', ', ', '' ) );
				}
                $services = array_filter(array(get_field('service_0'),  get_field('service_1'),  get_field('service_2')));
                printf( '<b>Services: </b>');
                $prefix = '';
                foreach ( $services as $service ) {
                    printf( '%s%s', $prefix, $service->name );
                    $prefix=', ';
				}
				break;

			case "listing_slug":
				printf( '%s', get_post_field( 'post_name', get_post() ));
				break;

			case "listing_thumbnail":
				$image = get_field("logo");
				if( !empty($image) ): 
					$url = $image['url'];
					printf( '<p><img src="%s" width="120" height="120"></p>', $url);
				endif;
				break;
		}

	}

	function property_details_shortcode( $atts ) {

		global $post;

		$output = '';

		$output .= '<div class="property-details">';

		$output .= '<div class="property-details-col1 one-half first">';
			foreach ( (array) $this->property_details['col1'] as $label => $key ) {
				$output .= sprintf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );
			}
		$output .= '</div><div class="property-details-col2 one-half">';
			foreach ( (array) $this->property_details['col2'] as $label => $key ) {
				$output .= sprintf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );
			}
        //$output .= '</div><div class="clear">';
        //    $output .= sprintf( '<p><b>%s</b><br /> %s</p></div>', __( 'Additional Features:', 'everything-directory' ), get_the_term_list( $post->ID, 'features', '', ', ', '' ) );

		$output .= '</div>';

		return $output;

	}

	function property_map_shortcode( $atts ) {

		return genesis_get_custom_field( '_listing_map' );

	}

	function property_video_shortcode( $atts ) {
		return genesis_get_custom_field( '_listing_video' );
	}

	function admin_js() {
		wp_enqueue_script( 'accesspress-admin-js', APL_URL . 'includes/js/admin.js', array(), APL_VERSION, true );
	}

    function all_js() {
        wp_enqueue_script( 'jquery.liveFilter.js', APL_URL . 'includes/js/jquery.liveFilter.js', array(), APL_VERSION, true );
    }

	function search_template( $template ) {

		$post_type = get_query_var( 'post_type' );

		if ( is_array( $post_type ) || 'listing' != $post_type ) {
			return $template;
		}

		$listing_template = locate_template( array( 'archive-listing.php' ), false );

		return $listing_template ? $listing_template : $template;

	}

	function breadcrumbs( $crumbs, $args ) {

		$post_type = get_query_var( 'post_type' );

		if ( is_array( $post_type ) || 'listing' != $post_type ) {
			return $crumbs;
		}

		array_pop( $crumbs );

		$crumbs[] = __( 'Listing Search Results', 'everything-directory' );

		return $crumbs;

	}

}
