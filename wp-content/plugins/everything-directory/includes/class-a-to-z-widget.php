<?php
/**
 * This widget presents the entire list of all posts in a-z format
 *
 * @package EverythingDirectory
 * @author  Miles Thompson
 */
class EverythingDirectory_A_to_Z_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => '', 'description' => __( 'Display everything as one big a-z list', 'everything-directory' ) );
		$control_ops = array( 'width' => 600, 'height' => 200, 'id_base' => 'a-to-z-widget'  );
		parent::__construct( 'a-to-z-widget', __( 'A_to_Z - Listings', 'everything-directory' ), $widget_ops, $control_ops );
	}

    function sort_title_from_title($title) {
        $tmp = mb_strtolower($title, 'UTF-8');
        $tmp = preg_replace('/^paekakariki /', '', $tmp);
        $tmp = preg_replace('/^paekākāriki /u', '', $tmp);
        $tmp = preg_replace('/^the /', '', $tmp);
        $tmp = preg_replace('/^a /', '', $tmp);
        if (empty($tmp))
            $tmp = "_";
        return $tmp;
    }

    function display_title_from_title($title) {
        // don't even get me started
        $title = preg_replace('/paekakariki/i', 'Paekākāriki', $title);
        return $title;
    }

    function expand_out_listings($listings) {
        $result = array();
        foreach ($listings as $title => $listing) {
            if ($listing->services)
            {
                foreach($listing->services as $service) {
                    $use_this_title = $service->name . " - " . $listing->title;
                    $sort_title = $this->sort_title_from_title($use_this_title);
                    $tmp = clone $listing;
                    $tmp->display_title = $this->display_title_from_title($use_this_title);
                    $result[$sort_title] = $tmp;
                }
            }                    
            else 
            {
                $listing->display_title = $this->display_title_from_title($title);
                $sort_title = $this->sort_title_from_title($title);
                $result[$sort_title] = $listing;
            }

        }
        return $result;            
    }

    function index_by_first_letter($sorted_listings) {
        $result = array();
        foreach ($sorted_listings as $sort_title => $listing) {
            $letter = strtoupper($sort_title)[0];
            if (!array_key_exists($letter, $result))
                $result[$letter] = array();
            $result[$letter][$sort_title] = $listing;
            ksort($result[$letter]);
        }
        ksort($result);
        return $result;            
    }

	function widget( $args, $instance ) {

		extract( $args );

		echo $before_widget;
        $args = array(
            'post_type'   => 'listing',
            'post_status' => 'publish',
            'posts_per_page' => -1
            );
        ?>
        <script>

        jQuery(document).ready(function ($) {
            $('#a_to_z_widget').liveFilter('#livefilter-input', '.a_to_z_section', {
                filterChildSelector: '.directory-item-container',
                filter: function (el, val) {
                    var title = $(el).find(".title").text();
                    var cats = $(el).find(".entry-categories").text();
                    //var description = $(el).find(".short-description").text();
                    return (title + " " + cats).toUpperCase().indexOf(val.toUpperCase()) >= 0;
                },
                after: function (contains, containsNot) {
                    if (containsNot.length) {
                        $(".a_to_z_letter_heading").hide();
                    }
                    else {
                        $(".a_to_z_letter_heading").show();
                    }
                    $('html, body').animate({
                        scrollTop: 0
                    }); 
                }
            });
            $(".a_to_z_jumplinks a").click(function(event) {
                event.preventDefault();
                nowTop = $(this).offset().top;
                targetTop = $($(this).attr("href")).offset().top;

                $('html, body').animate({
                    scrollTop: (targetTop-220)
                });            
            });
          });
        </script>
        <div id="a_to_z_widget" class="directory-widget">
            <div class="a_to_z_searchbar">
                <div class="wrap">
                    <input id="livefilter-input" class="search" type="text" placeholder="Search the A-Z directory" value="">
                    <input type="button" value="search" class="search_button" />
                </div>
            </div>
            <div class="a_to_z_jumplinks">
            <ul>
                <li><a href="#A">A-J</a></li>
                <li><a href="#K">K-N</a></li>
                <li><a href="#O">O-S</a></li>
                <li><a href="#T">T-Z</a></li>
            </ul>
        </div>

        <?php

            $listings = array();

            $listings_query = new WP_Query( $args );
            if( $listings_query->have_posts() ) :
                while( $listings_query->have_posts()) : $listings_query->the_post();
                    $listing = build_listing($listings_query->post);
                    $listings[$listing->title] = $listing;
                endwhile;
            endif;

            $listings = $this->expand_out_listings($listings);
            $listings_by_letter = $this->index_by_first_letter($listings);
            ksort($listings);
          
            foreach($listings_by_letter as $letter => $listings) {  ?>
                <div class="a_to_z_section">
                    <div class="a_to_z_letter_heading"><h3><a id="<?php echo $letter ?>"><?php echo $letter ?></a></h3></div>
                    <div class="a_to_z_listings">
                    <?php
                    foreach ($listings as $sort_title => $listing) 
                    {
                        echo listing_a_z_view($listing->display_title,$listing, false);
                    }
                    ?>
                    </div>
                </div>
                <?php
            }

            wp_reset_postdata();

        ?>
        </div>
        <?php
         echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        return $new_instance;
    }

    function form( $instance ) {
    }
}
