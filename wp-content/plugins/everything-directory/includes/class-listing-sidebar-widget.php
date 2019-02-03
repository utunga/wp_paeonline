<?php
/**
 * This widget presents a search widget which uses listings' taxonomy for search fields.
 *
 * @package EverythingDirectory
 * @author Miles Thompson
 */
class EverythingDirectory_Listing_Sidebar_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'directory', 'description' => __( 'Directory listing vertical summary', 'everything-directory' ) );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'listing-summary' );
		parent::__construct( 'listing-summary', __( 'DoE - Listing Summary', 'everything-directory' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		
        global $post;
        $listing = build_listing(get_post($post->ID));

		$instance = wp_parse_args( (array) $instance, array(
			'title'       => '',
		) );


		extract( $args );

		echo $before_widget;

		if ( $instance['title'] ) echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;

		    echo listing_sidebar_view($listing);

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		
		$instance = wp_parse_args( (array) $instance, array(
			'title'       => '',
		) );

		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id( 'title' ), __( 'Title:', 'everything-directory' ), $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), esc_attr( $instance['title'] ), 'width: 95%;' );		
	}
}