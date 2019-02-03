<?php

add_image_size('major-sponsor-size',  435, 290, false); // width, height, crop

if(!class_exists('PaeOnline_SponsorWidget')) {

  class PaeOnline_SponsorWidget extends WP_Widget {

    /**
    * Sets up the widgets name etc
    */
    public function __construct() {
      $widget_ops = array(
        'classname' => 'pae_sponsor_widget',
        'description' => 'Sponsor Widget, custom for Pae Online',
      );
      parent::__construct( 'pae_sponsor_widget', 'Pae - Sponsor Widget', $widget_ops );
    }

    /**
    * Outputs the content of the widget
    *
    * @param array $args
    * @param array $instance
    */
    public function widget( $args, $instance ) {

        // outputs the content of the widget
        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        // widget ID with prefix for use in ACF API functions
        $widget_id = 'widget_' . $args['widget_id'];

        $sponsor_title = get_field( 'sponsor_title', $widget_id ) ? get_field( 'sponsor_title', $widget_id ) : '';
        $sponsor_link = get_field( 'sponsor_link', $widget_id );

        $image = wp_get_attachment_image_src(get_field('sponsor_image', $widget_id ), 'major-sponsor-size');
        $has_image = ($image);

        echo $args['before_widget'];

        if ($sponsor_link):
            echo '<a href="' . esc_url($sponsor_link) . '">';
        endif;

        if ($has_image):
            echo '<img src="'. $image[0].'"  class="sponsor_image" alt="'. get_the_title(get_field('sponsor_image')) .'" />';
        endif;

        echo '<div class="content">';

            if ($sponsor_title):
                echo '<h4>'.esc_html($sponsor_title).'</h4>';
            endif;

            the_field('sponsor_text', $widget_id );

        echo '</div>';

        if ($sponsor_link):
            echo '</a>';
        endif;

        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
    	// outputs the options form on admin
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
    	// processes widget options to be saved
    }

  }

}

/**
 * Register our Sponsor Widget
 */
function register_pae_sponsor_widget()
{
  register_widget( 'PaeOnline_SponsorWidget' );
}
add_action( 'widgets_init', 'register_pae_sponsor_widget' );
