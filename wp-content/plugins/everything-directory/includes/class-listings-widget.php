<?php
/**
 * This widget presents loop content, based on your input, for category listing pages
 *
 * @package EverythingDirectory
 * @author  Miles Thompson
 */
class EverythingDirectory_Listings_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => '', 'description' => __( 'Display an individual DoE listing', 'everything-directory' ) );
		$control_ops = array( 'width' => 600, 'height' => 200, 'id_base' => 'listing-widget'  );
		parent::__construct( 'listing-widget', __( 'DoE - Listings', 'everything-directory' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		/** defaults */
		$instance = wp_parse_args( $instance, array(
			'title'  => ''
		) );

		extract( $args );
      
		echo $before_widget;

            if ( $instance['title'] ) echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;

			$toggle = ''; /** for left/right class */

            $cat = array_key_exists('category', $instance) ? $instance['category'] : get_field('category', 'widget_' . $widget_id);
            $title = array_key_exists('title', $instance) && $instance['title'] ? $instance['title'] : $cat->name;
            $show_title = array_key_exists('show_title', $instance) ?  $instance['show_title'] : true;
            $show_intro = array_key_exists('show_intro', $instance) ?  $instance['show_intro'] : true;
            $foldable = array_key_exists('foldable', $instance) ?  $instance['foldable'] : true;
           
            $intro_text = get_term_meta( $cat->term_id, 'intro_text', true );
            $display_author = get_field("display_author");
            $intro_text = do_shortcode( $intro_text);

            $args = array(
                'post_type'   => 'listing',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby'   => 'slug',
                'tax_query'   => array(
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => $cat->slug
                    )
                )
            );

            $listings = new WP_Query( $args );
            
            ?>
            <div id="cat_<?php echo $cat->slug ?>" <?php if ($foldable) { echo 'class="foldable"'; } ?>>
            <div class="directory-widget" <?php if ($foldable) { echo 'data-foldable-role="group"'; } ?>>
                <?php if ($show_title)  {?>
                    
                    <div class="directory-widget-header" <?php if ($foldable) { echo 'data-foldable-role="trigger"'; } ?>>
                    <h2><?php echo $title ?></h2>
                        <?php if ($show_intro && (trim($intro_text))) { ?>
                            <?php 
                                genesis_markup( array(
                                    'open'    => '<p %s>',
                                    'close'   => '</p>',
                                    'content' =>  $display_author,
                                    'context' => 'entry-meta-before-content',
                                ) );
                            ?>
                            <div class="intro-text">
                                <?php echo $intro_text ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php
                }
                elseif ($show_intro)  {?>
                    
                    <div class="directory-widget-header" <?php if ($foldable) { echo 'data-foldable-role="trigger"'; } ?>>
                        <?php if (trim($intro_text)) { ?>
                            <div class="intro-text">
                                <?php echo $intro_text ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php
                }
                    if( $listings->have_posts() ) :
                        if ($foldable) { 
                            echo '<div data-foldable-role="target">'; 
                        } else {
                            echo '<div>'; 
                        }

                        while( $listings->have_posts()) : $listings->the_post();
                            echo listing_a_z_view(get_the_title(), build_listing($listings->post), false);
                        endwhile;
                        wp_reset_postdata();
                        echo '</div>';
                    //else :
                    //    esc_html_e( 'No listings in this category', 'text-domain' );
                    endif;
                ?>
                </div>
                </div>
                <?php if ($foldable) {  ?>
                <script>
                jQuery(document).ready(function($) {
                    $("#cat_<?php echo $cat->slug ?>").foldable({
                        hash: true,
                        theme: "blue"
                    });
                });
                </script>
                <?php } ?>
                <?php
			$toggle = $toggle == 'left' ? 'right' : 'left';

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args( $instance, array(
			'title'          => ''
		) );

        //printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id('title'), __( 'Title:', 'everything-directory' ), $this->get_field_id('title'), $this->get_field_name('title'), esc_attr( $instance['title'] ), 'width: 95%;' );

	}
}

function render_listing_widget_for_category($instance)  {
     the_widget('EverythingDirectory_Listings_Widget', $instance);
}
