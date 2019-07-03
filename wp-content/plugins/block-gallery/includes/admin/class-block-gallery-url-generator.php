<?php
/**
 * Generates a link to our shop.
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
class Block_Gallery_URL_Generator {

	/**
	 * Returns the URL to upgrade the plugin to the pro version.
	 * Can be overridden by theme developers to use their affiliate
	 * link using the blockgallery_affiliate_id filter.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_affiliate_id() {

		$id = array( 'ref' => apply_filters( 'blockgallery_affiliate_id', null ) );

		return $id;
	}

	/**
	 * Returns a URL that points to the Block Architect store.
	 *
	 * @since 1.0.0
	 * @param string|string $path A URL path to append to the store URL.
	 * @param array|array   $params An array of key/value params to add to the query string.
	 * @return string
	 */
	public function get_store_url( $path = '', $params = array() ) {

		$id = $this->get_affiliate_id();

		$params = array_merge( $params, $id );

		$url = trailingslashit( BLOCKGALLERY_SHOP_URL . $path ) . '?' . http_build_query( $params, '', '&#038;' );

		return $url;
	}
}

return new Block_Gallery_URL_Generator();
