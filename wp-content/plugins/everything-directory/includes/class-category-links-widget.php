<?php
/**
 * This widget presents a simple list of links to all listings within the specified category (user can choose the category)
 *
 * @package EverythingDirectory
 * @author  Miles Thompson
 */
class EverythingDirectory_Category_Links_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => '', 'description' => __( 'Simple list of links to listings within a category', 'everything-directory' ) );
		$control_ops = array( 'width' => 600, 'height' => 200, 'id_base' => 'category-links-widget'  );
		parent::__construct( 'category-links-widget', __( 'DoE - Category Links Widget', 'everything-directory' ), $widget_ops, $control_ops );
	}



    function output_item($listing_post) {
        echo "HI";
    }

	function widget( $args, $instance ) {

        extract($args);
        if (isset($instance['cat_slug']) && strlen($instance['cat_slug'])) {
            $cat_slug = $instance['cat_slug'];
        } else {
            return;
        }

        if (isset($instance['title']) && strlen($instance['title'])) {
            $title = esc_html($instance['title']);
        } else {
            $title = esc_html(get_category_by_slug($cat_slug)->name);
        }

        echo $before_widget;

        $args = array(
            'post_type'   => 'listing',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby'   => 'slug',
            'tax_query'   => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $cat_slug
                )
            )
        );

        echo "<h3>".$title."</h3>";
        $listings = new WP_Query( $args );
        if( $listings->have_posts() ) :
            while( $listings->have_posts()) : $listings->the_post();
                ?>
                    <li class="directory-link">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </li>
                <?php

            endwhile;
            wp_reset_postdata();
        endif;
        wp_reset_query();

		echo $after_widget;

	}


	public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['cat_slug'] = strip_tags($new_instance['cat_slug']);
        return $instance;
    }

	public function form($instance) {
        if (isset($instance['cat_slug']) && strlen($instance['cat_slug'])) {
            $cat_slug = $instance['cat_slug'];
        } else {
            $cat_slug = '';
        }
        if (isset($instance['title']) && strlen($instance['title'])) {
            $title_value = esc_attr($instance['title']);
        } else {
            $title_value = '';
        }
        echo '<p>';
        echo __("Title:","pae_online");
        echo "<br />";
        $tfield = $this->get_field_id('title');
        $tname = $this->get_field_name('title');
        printf(
            '<input type="text" name="%s" id="%s" value="%s" style="width:100%%" />',
            $tname,
            $tfield,
            $title_value
        );
        echo '</p>';

        echo '<p>';
        echo __("Category Slug:","pae_online");
        echo "<br />";
        $cat_field_id = $this->get_field_id('cat_slug');
        $cat_field_name = $this->get_field_name('cat_slug');
        printf(
            '<input type="text" name="%s" id="%s" value="%s" style="width:100%%" />',
            $cat_field_name,
            $cat_field_id,
            $cat_slug
        );
        echo '</p>';
	}
}
