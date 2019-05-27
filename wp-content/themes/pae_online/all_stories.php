<?php
/**
 * Paekakariki Online.
 *
 * Template Name: All Stories Page
 *
 * This file adds the all stories page 
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */

// Remove default page header.
remove_action( 'genesis_before_content_sidebar_wrap', 'pae_onlinepage_header' );

// Replace default loop with a category loop
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'pae_all_stories_loop' );
function pae_all_stories_loop() {

    echo '<div class="our_stories_wrap our_stories">';
    echo '<h1>Our stories</h1>';
    $story_category = get_category_by_slug('stories'); 
    $sub_category_ids = get_term_children($story_category->term_id, 'category');
    if ( empty( $sub_category_ids ) || is_wp_error( $sub_category_ids ) )
    { 
    	render_category_stories($story_category);
    }
    else
    {
    	foreach ($sub_category_ids as $sub_category_id)
        {
            $cat = get_term($sub_category_id, 'category');
            render_category_stories($cat);
        }
    }
        // Restore original Post Data
    wp_reset_postdata();

}

$displayed_posts = [];
function track_displayed_posts($post_id) {
	global $displayed_posts;
	$displayed_posts[] = $post_id; 
}

function render_category_stories($cat) {
      
    // WP_Query arguments
    global $displayed_posts;
    $args = array (
	    'post_type'              => 'post',
	    'posts_per_page'         => '-1',
        'post_status' => 'publish',
        'post__not_in' => $displayed_posts,
        'tax_query'   => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $cat->slug
            )),
		'order' => 'DESC',
		'date_query' => array(
			'after' => date('Y-m-d', strtotime('-1 years'))
			)
    );

    // The Query
    $query = new WP_Query( $args );

    // The Loop
    $post_count = 0;
    	
	if ( $query->have_posts() ) {
    	?>

    	<div class="stories_category">
  		<h3><?php echo $cat->name ?></h3>
 
        <div class="featured_stories">
		    <?php
	        while ( $query->have_posts() ) {

	            $query->the_post();
		        if (has_post_thumbnail()) {
	            	render_featured_post(); 
	           	}
	           	else {
	           		render_other_post(); 
	           	}
		        track_displayed_posts(get_the_ID());
	            $post_count = $post_count+1;

	            if ($post_count >= 1 )  {
	            	break;
	            }

	        }
	    ?>
        </div>
        <div class="all_stories">
            <?php
            while ( $query->have_posts() ) {

                $query->the_post();
            	track_displayed_posts(get_the_ID());
                render_other_post(); 
                $post_count = $post_count+1; 

            }
            ?>
        </div>
	</div>
<?php	
	}
}


// Run Genesis.
genesis();
