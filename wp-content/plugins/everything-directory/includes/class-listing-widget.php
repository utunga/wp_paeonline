<?php
/**
 * This widget presents a single listing in listing widget form 
 *
 * @package EverythingDirectory
 * @author Miles Thompson
 */
class EverythingDirectory_Listing_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'directory single_listing', 'description' => __( 'Directory listing vertical summary', 'everything-directory' ) );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'listing-summary' );
		parent::__construct( 'listing-summary', __( 'DoE - Listing Summary', 'everything-directory' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		
        global $post;
        $listing = build_listing(get_post($post->ID));
				
		$instance = wp_parse_args( (array) $instance, array(
			'title'       => '',
			'show_services' => false
		) );

		extract( $args );
		$title = $instance['title'];
		$show_services = $instance['show_services'];

		echo $before_widget;

		echo listing_a_z_view($title, $listing, $show_services);

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		
		$instance = wp_parse_args( (array) $instance, array(
			'title'       => '',
			'show_services' => false
		) );

		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id( 'title' ), __( 'Title:', 'everything-directory' ), $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), esc_attr( $instance['title'] ), 'width: 95%;' );		
	}
}

function render_single_listing_widget($instance)  {
     the_widget('EverythingDirectory_Listing_Widget', $instance);
}