<?php
/**
 * Add links to Block Gallery on the plugins admin page.
 *
 * @package Block Gallery
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generates a link.
 */
class Block_Gallery_Action_Links {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( BLOCKGALLERY_PLUGIN_DIR . 'class-block-gallery.php' ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Add links to the settings page to the plugin.
	 *
	 * @param       array|array $links The plugin's links.
	 * @return      array
	 */
	public function plugin_action_links( $links ) {

		// Return early if we do not have a pro version or Pro is activated.
		if ( ! Block_Gallery()->has_pro() || Block_Gallery()->is_pro() ) {
			return $links;
		}

		// Generator the upgrade link.
		$url_generator = new Block_Gallery_URL_Generator();

		$url = $url_generator->get_store_url(
			'pricing',
			array(
				'utm_medium'   => 'block-gallery-lite',
				'utm_source'   => 'plugins-page',
				'utm_campaign' => 'plugins-row',
				'utm_content'  => 'go-pro',
			)
		);

		$links['go_pro'] = sprintf( '<a href="%1$s" target="_blank" class="block-gallery-plugins-gopro" style="color: #39b54a;font-weight: 700;">%2$s</a>', esc_url( $url ), esc_html__( 'Go Pro', 'block-gallery' ) );

		return $links;
	}

	/**
	 * Plugin row meta links
	 *
	 * @param array  $plugin_meta An array of the plugin's metadata.
	 * @param string $plugin_file Path to the plugin file.
	 * @return array $input
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file ) {

		// Check if this is defined.
		if ( ! defined( 'BLOCKGALLERYPRO_PLUGIN_BASE' ) ) {
			define( 'BLOCKGALLERYPRO_PLUGIN_BASE', null );
		}

		$url_generator = new Block_Gallery_URL_Generator();

		$support_url = $url_generator->get_store_url(
			'/',
			array(
				'utm_medium'   => 'block-gallery-lite',
				'utm_source'   => 'plugins-page',
				'utm_campaign' => 'plugins-row',
				'utm_content'  => 'help-and-faqs',
			)
		);

		if ( BLOCKGALLERY_PLUGIN_BASE === $plugin_file ) {
			$row_meta = array(
				'docs'   => '<a href="' . esc_url( $support_url ) . '" aria-label="' . esc_attr( __( 'View Block Gallery Documentation', 'block-gallery' ) ) . '" target="_blank">' . __( 'Help & FAQs', 'block-gallery' ) . '</a>',
				'review' => '<a href="' . esc_url( BLOCKGALLERY_REVIEW_URL ) . '" aria-label="' . esc_attr( __( 'Review Block Gallery on WordPress.org', 'block-gallery' ) ) . '" target="_blank">' . __( 'Leave a Review', 'block-gallery' ) . '</a>',
			);

			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}

		return $plugin_meta;
	}
}

new Block_Gallery_Action_Links();
