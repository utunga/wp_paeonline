<?php
/**
 * This widget presents the list of categories specifically for the home page
 *
 * @package EverythingDirectory
 * @author  Miles Thompson
 */
class EverythingDirectory_Category_List_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => '', 'description' => __( 'Display a list of DoE categories with their key images', 'everything-directory' ) );
		$control_ops = array( 'width' => 600, 'height' => 200, 'id_base' => 'category-listing-widget'  );
		parent::__construct( 'category-listing-widget', __( 'DoE - Category List', 'everything-directory' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

        $instance = wp_parse_args( (array) $instance, array() );

        $dir_of_everything_cat = get_category_by_slug('directory-of-everything'); 
        $a_z_cat = get_category_by_slug('a_z'); 
        $args = [
            'taxonomy'  => 'category',
            'parent'    => $dir_of_everything_cat->term_id,
            'orderby'   => 'slug',
            'hide_empty'    => false, 
        ];
        $categories = get_terms($args);
        array_unshift($categories,$a_z_cat);
           
        ?>

        <ul class="category_list_container">
            <?php
                
            foreach($categories as $cat) {
                    $cat_title =  $cat->name;
                    $key_image_id = get_field('banner_image', 'category'.'_'.$cat->term_id);
                ?>
                    <li class="category_item <?php echo $cat->slug ?>">
                        <a href="<?php echo get_category_link($cat->term_id) ?>" />
                            <?php echo wp_get_attachment_image( $key_image_id, 'full' ); ?>
                            <div class="category_title">
                                    <?php echo $cat_title ?>
                            </div>
                        </a>
                    </li>
                <?php
            }
            ?>
        </ul>

        <?php
           

	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		
		$instance = wp_parse_args( (array) $instance, array(
			'title'       => '',
			'intro_text' => __( 'Intro Text', 'everything-directory' )
		) );

		$new_widget = empty( $instance );

		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id( 'title' ), __( 'Title:', 'everything-directory' ), $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), esc_attr( $instance['title'] ), 'width: 95%;' );
		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id( 'intro_text' ), __( 'Intro Text:', 'everything-directory' ), $this->get_field_id( 'intro_text' ), $this->get_field_name( 'intro_text' ), esc_attr( $instance['intro_text'] ), 'width: 95%;' );
	}
}

function render_category_list_widget($show_title=true, $show_intro=true)  {
     the_widget('EverythingDirectory_Category_List_Widget');
}
