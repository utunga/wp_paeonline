<?php
/**
 * Paekakariki Online.
 *
 * This file adds a generic attachment page - full width images
 *
 * @package   PaekakarikiOnline
 * @license   GPL-2.0+
 */
// Force full width content layout.
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

/** Replace the standard loop with our custom loop */
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'attachment_display' );
function attachment_display() {

    $attachment_id =  get_the_ID();
    $attachment = get_post($attachment_id);
    $description = $attachment->post_content;
    $caption = $attachment->post_excerpt;
    $metadata = wp_get_attachment_metadata();
    $img_tag = wp_get_attachment_image($attachment_id, 'full' );

    ?>

    <div class="entry-content attachment">
        <!-- FIXME there's probably a proper 'wordpress way' to do this but for now just want to get it to work -->
        <figure id="<?php echo $attachment_id?>"
                aria-describedby="caption-attachment-<?php echo $attachment_id?>"
                style="width:<?php echo $metadata['width']?>px;">
            <?php echo $img_tag ?>
            <figcaption id="caption-attachment-<?php echo $attachment_id?>" class="wp-caption-text">
                <?php echo $caption ?>
            </figcaption>
        </figure>

        <div class="attachment_description"><?php echo wpautop($description, true); ?></div>

        <div class="attachment_link">
        <?php printf(
          __( 'Full size image: %s pixels', 'pae_online' ),
	        sprintf(
		        '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
		        esc_url( wp_get_attachment_url() ),
		        esc_attr( __( 'Link to full-size image', 'pae_online' ) ),
		        $metadata['width'],
		        $metadata['height']
	        )
        ); ?>
        </div>

    </div>
    <?php 
}

// Run the Genesis loop.
genesis();


