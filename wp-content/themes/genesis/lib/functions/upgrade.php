<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Updates
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://my.studiopress.com/themes/genesis/
 */

/**
 * Calculate or return the first version of Genesis to run on this site.
 *
 * @since 2.1.0
 *
 * @return string First version of Genesis to run on the site.
 */
function genesis_first_version() {

	$first_version = genesis_get_option( 'first_version' );

	if ( ! $first_version ) {
		$first_version = PARENT_THEME_VERSION;
	}

	return $first_version;

}

/**
 * Helper function for comparing the "first install" version to a user specified version.
 *
 * @since 2.1.0
 *
 * @param string $version  Version number to compare first version against.
 * @param string $operator Relationship between versions.
 * @return bool `true` if the relationship is the one specified by the operator, `false` otherwise.
 */
function genesis_first_version_compare( $version, $operator ) {

	return version_compare( genesis_first_version(), $version, $operator );

}

/**
 * Ping https://api.genesistheme.com/ asking if a new version of this theme is available.
 *
 * If not, it returns false.
 *
 * If so, the external server passes serialized data back to this function, which gets unserialized and returned for use.
 *
 * Applies `genesis_update_remote_post_options` filter.
 *
 * Ping occurs at a maximum of once every 24 hours.
 *
 * @since 1.1.0
 *
 * @global string $wp_version WordPress version string.
 *
 * @return array Unserialized data, or empty array if updates are disabled or
 *               theme does not support `genesis-auto-updates`.
 */
function genesis_update_check() {

	// If updates are disabled.
	if ( ! genesis_get_option( 'update' ) || ! current_theme_supports( 'genesis-auto-updates' ) ) {
		return array();
	}

	// Use cache.
	static $genesis_update = null;

	// If cache is empty, pull setting.
	if ( ! $genesis_update ) {
		$genesis_update = genesis_get_expiring_setting( 'update' );
	}

	// If setting has expired, do a fresh update check.
	if ( ! $genesis_update ) {

		$update_config = require GENESIS_CONFIG_DIR . '/update-check.php';

		/**
		 * Filter the request data sent to the update server.
		 *
		 * @since 1.1.0
		 *
		 * @param array The request data sent to the update server.
		 */
		$update_config['post_args'] = apply_filters(
			'genesis_update_remote_post_options',
			$update_config['post_args']
		);

		$update_check = new Genesis_Update_Check( $update_config );

		// If an error occurred, return empty array, store for 1 hour.
		if ( ! $update_check->get_update() ) {
			$genesis_update = array(
				'new_version' => PARENT_THEME_VERSION,
			);
			genesis_set_expiring_setting( 'update', $genesis_update, HOUR_IN_SECONDS );
			return array();
		}

		// Else, unserialize.
		$genesis_update = $update_check->get_update();

		// And store in setting for 24 hours.
		genesis_set_expiring_setting( 'update', $genesis_update, DAY_IN_SECONDS );

	}

	// If we're already using the latest version, return empty array.
	if ( version_compare( PARENT_THEME_VERSION, $genesis_update['new_version'], '>=' ) ) {
		return array();
	}

	return $genesis_update;

}

/**
 * Upgrade the database to latest version.
 *
 * @since 2.6.0
 */
function genesis_upgrade_db_latest() {

	// Update Settings.
	genesis_update_settings(
		array(
			'theme_version' => PARENT_THEME_VERSION,
			'db_version'    => PARENT_DB_VERSION,
			'upgrade'       => 1,
		)
	);

}

/**
 * Upgrade the database to version 2700.
 *
 * @since 2.7.0
 */
function genesis_upgrade_2700() {
	delete_option( 'genesis-scribe-nag-disabled' );
}

/**
 * Upgrade the database to version 2603.
 *
 * @since 2.6.1
 */
function genesis_upgrade_2603() {

	genesis_unslash_post_meta_scripts();

	genesis_update_settings(
		array(
			'header_scripts' => stripslashes( genesis_get_option( 'header_scripts' ) ),
			'footer_scripts' => stripslashes( genesis_get_option( 'footer_scripts' ) ),
			'theme_version'  => '2.6.1',
			'db_version'     => '2603',
			'upgrade'        => 1,
		)
	);

}

/**
 * Upgrade the database to version 2501.
 *
 * @since 2.5.0
 */
function genesis_upgrade_2501() {

	if ( genesis_get_seo_option( 'semantic_headings', false ) ) {
		genesis_update_settings(
			array(
				'semantic_headings' => 'unset',
			),
			GENESIS_SEO_SETTINGS_FIELD
		);
	}

	// Update Settings.
	genesis_update_settings(
		array(
			'theme_version' => '2.5.0',
			'db_version'    => '2501',
			'upgrade'       => 1,
		)
	);

}

/**
 * Upgrade the database to version 2403.
 *
 * @since 2.4.2
 */
function genesis_upgrade_2403() {

	// Update Settings.
	genesis_update_settings(
		array(
			'theme_version' => '2.4.2',
			'db_version'    => '2403',
			'upgrade'       => 1,
		)
	);

}

/**
 * Upgrade the database to version 2209.
 *
 * @since 2.2.6
 */
function genesis_upgrade_2209() {

	$term_meta = get_option( 'genesis-term-meta' );

	foreach ( (array) $term_meta as $term_id => $meta ) {
		foreach ( (array) $meta as $key => $value ) {
			add_term_meta( $term_id, $key, $value, true );
		}
	}

	// Update Settings.
	genesis_update_settings(
		array(
			'theme_version' => '2.2.6',
			'db_version'    => '2209',
			'upgrade'       => 1,
		)
	);

}

/**
 * Upgrade the database to version 2207.
 *
 * @since 2.2.4
 */
function genesis_upgrade_2207() {

	$update_email_address = genesis_get_option( 'update_email' ) ? genesis_get_option( 'update_email_address' ) : '';

	// Update Settings.
	genesis_update_settings(
		array(
			'theme_version'        => '2.2.4',
			'db_version'           => '2207',
			'update_email'         => 'unset',
			'update_email_address' => $update_email_address,
			'upgrade'              => 1,
		)
	);

}

/**
 * Upgrade the database to version 2201.
 *
 * @since 2.2.0
 */
function genesis_upgrade_2201() {

	// Update Settings.
	genesis_update_settings(
		array(
			'theme_version' => '2.2.0-beta2',
			'db_version'    => '2201',
			'upgrade'       => 1,
		)
	);

	// Update SEO Settings.
	genesis_update_settings(
		array(
			'canonical_archives' => 'unset',
		),
		GENESIS_SEO_SETTINGS_FIELD
	);

}

/**
 * Upgrade the database to version 2100.
 *
 * @since 2.1.0
 */
function genesis_upgrade_2100() {

	// Update Settings.
	genesis_update_settings(
		array(
			'db_version'      => '2100',
			'image_alignment' => 'alignleft',
			'first_version'   => '2.0.2',
			'upgrade'         => 1,
		)
	);

}

/**
 * Upgrade the database to version 2003.
 *
 * @since 2.0.0
 */
function genesis_upgrade_2003() {

	// Update Settings.
	genesis_update_settings(
		array(
			'superfish'  => genesis_get_option( 'nav_superfish', null, 0 ) || genesis_get_option( 'subnav_superfish', null, 0 ) ? 1 : 0,
			'db_version' => '2003',
			'upgrade'    => 1,
		)
	);

}

/**
 * Upgrade the database to version 2001.
 *
 * @since 2.0.0
 */
function genesis_upgrade_2001() {

	// Update Settings.
	genesis_update_settings(
		array(
			'nav_extras' => genesis_get_option( 'nav_extras_enable', null, 0 ) ? genesis_get_option( 'nav_extras', null, 0 ) : '',
			'db_version' => '2001',
			'upgrade'    => 1,
		)
	);

}

/**
 * Upgrade the database to version 1901.
 *
 * @since 1.9.0
 */
function genesis_upgrade_1901() {

	// Get menu locations.
	$menu_locations = get_theme_mod( 'nav_menu_locations' );

	// Clear assigned nav if nav disabled.
	if ( $menu_locations['primary'] && ! genesis_get_option( 'nav' ) ) {
		$menu_locations['primary'] = 0;
		set_theme_mod( 'nav_menu_locations', $menu_locations );
	}

	// Clear assigned subnav if subnav disabled.
	if ( $menu_locations['secondary'] && ! genesis_get_option( 'subnav' ) ) {
		$menu_locations['secondary'] = 0;
		set_theme_mod( 'nav_menu_locations', $menu_locations );
	}

	// Update Settings.
	genesis_update_settings(
		array(
			'db_version' => '1901',
			'upgrade'    => 1,
		)
	);

}

/**
 * Upgrade the database to version 1800.
 *
 * @since 1.8.0
 */
function genesis_upgrade_1800() {

	// Convert term meta for new title/description options.
	genesis_convert_term_meta();

	// Update Settings.
	genesis_update_settings(
		array(
			'db_version' => '1800',
			'upgrade'    => 1,
		)
	);

}

/**
 * Upgrade the database to version 1700.
 *
 * Also removes old user meta box options, as the UI changed.
 *
 * @since 1.7.0
 *
 * @global wpdb $wpdb WordPress database object.
 */
function genesis_upgrade_1700() {

	global $wpdb;

	// Changing the UI. Remove old user options.
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->usermeta WHERE meta_key = %s OR meta_key = %s", 'meta-box-order_toplevel_page_genesis', 'meta-box-order_genesis_page_seosettings' ) );
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->usermeta SET meta_value = %s WHERE meta_key = %s OR meta_key = %s", '1', 'screen_layout_toplevel_page_genesis', 'screen_layout_genesis_page_seosettings' ) );

	// Update Settings.
	genesis_update_settings(
		array(
			'db_version' => '1700',
			'upgrade'    => 1,
		)
	);

}

/**
 * Convert term meta for new title/description options.
 *
 * Called in `genesis_upgrade_1800()`.
 *
 * @since 2.6.0
 */
function genesis_convert_term_meta() {
	// Convert term meta for new title/description options.
	$terms     = get_terms(
		get_taxonomies(),
		array(
			'hide_empty' => false,
		)
	);
	$term_meta = get_option( 'genesis-term-meta' );

	foreach ( (array) $terms as $term ) {
		if ( isset( $term_meta[ $term->term_id ]['display_title'] ) && $term_meta[ $term->term_id ]['display_title'] ) {
			$term_meta[ $term->term_id ]['headline'] = $term->name;
		}

		if ( isset( $term_meta[ $term->term_id ]['display_description'] ) && $term_meta[ $term->term_id ]['display_description'] ) {
			$term_meta[ $term->term_id ]['intro_text'] = $term->description;
		}
	}

	update_option( 'genesis-term-meta', $term_meta );
}

/**
 * Strip slashes from header and body scripts saved as post meta.
 *
 * Called in `genesis_upgrade_2603()`.
 *
 * @since 2.6.1
 */
function genesis_unslash_post_meta_scripts() {

	global $wpdb;

	$header_scripts_posts = $wpdb->get_results( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_genesis_scripts'" );
	$body_scripts_posts   = $wpdb->get_results( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_genesis_scripts_body'" );

	foreach ( $header_scripts_posts as $post ) {
		if ( ! empty( $post->post_id ) ) {
			update_post_meta( $post->post_id, '_genesis_scripts', get_post_meta( $post->post_id, '_genesis_scripts', 1 ) );
		}
	}

	foreach ( $body_scripts_posts as $post ) {
		if ( ! empty( $post->post_id ) ) {
			update_post_meta( $post->post_id, '_genesis_scripts_body', get_post_meta( $post->post_id, '_genesis_scripts_body', 1 ) );
		}
	}

}

add_action( 'admin_init', 'genesis_upgrade', 20 );
/**
 * Update Genesis to the latest version.
 *
 * This iterative update function will take a Genesis installation, no matter
 * how old, and update its options to the latest version.
 *
 * It used to iterate over theme version, but now uses a database version
 * system, which allows for changes within pre-releases, too.
 *
 * @since 1.0.1
 *
 * @return void Return early if we're already on the latest version.
 */
function genesis_upgrade() {

	// Don't do anything if we're on the latest version.
	if ( genesis_get_option( 'db_version', null, false ) >= PARENT_DB_VERSION ) {
		return;
	}

	global $wp_db_version;

	// If the WP db hasn't been upgraded, make them upgrade first.
	if ( get_option( 'db_version' ) != $wp_db_version ) {
		wp_redirect( admin_url( 'upgrade.php?_wp_http_referer=' . urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
		exit;
	}

	// UPDATE TO VERSION 1.0.1.
	if ( version_compare( genesis_get_option( 'theme_version', null, false ), '1.0.1', '<' ) ) {
		$theme_settings = get_option( GENESIS_SETTINGS_FIELD );
		$new_settings   = array(
			'nav_home'         => 1,
			'nav_twitter_text' => 'Follow me on Twitter',
			'subnav_home'      => 1,
			'theme_version'    => '1.0.1',
			'upgrade'          => 1,
		);

		$settings = wp_parse_args( $new_settings, $theme_settings );
		update_option( GENESIS_SETTINGS_FIELD, $settings );
	}

	// UPDATE TO VERSION 1.1.
	if ( version_compare( genesis_get_option( 'theme_version', null, false ), '1.1', '<' ) ) {
		$theme_settings = get_option( GENESIS_SETTINGS_FIELD );
		$new_settings   = array(
			'content_archive_thumbnail' => genesis_get_option( 'thumbnail' ),
			'theme_version'             => '1.1',
			'upgrade'                   => 1,
		);

		$settings = wp_parse_args( $new_settings, $theme_settings );
		update_option( GENESIS_SETTINGS_FIELD, $settings );
	}

	// UPDATE TO VERSION 1.1.2.
	if ( version_compare( genesis_get_option( 'theme_version', null, false ), '1.1.2', '<' ) ) {
		$theme_settings = get_option( GENESIS_SETTINGS_FIELD );
		$new_settings   = array(
			'header_right'            => genesis_get_option( 'header_full' ) ? 0 : 1,
			'nav_superfish'           => 1,
			'subnav_superfish'        => 1,
			'nav_extras_enable'       => genesis_get_option( 'nav_right' ) ? 1 : 0,
			'nav_extras'              => genesis_get_option( 'nav_right' ),
			'nav_extras_twitter_id'   => genesis_get_option( 'twitter_id' ),
			'nav_extras_twitter_text' => genesis_get_option( 'nav_twitter_text' ),
			'theme_version'           => '1.1.2',
			'upgrade'                 => 1,
		);

		$settings = wp_parse_args( $new_settings, $theme_settings );
		update_option( GENESIS_SETTINGS_FIELD, $settings );
	}

	// UPDATE TO VERSION 1.2.
	if ( version_compare( genesis_get_option( 'theme_version', null, false ), '1.2', '<' ) ) {
		$theme_settings = get_option( GENESIS_SETTINGS_FIELD );
		$new_settings   = array(
			'update'        => 1,
			'theme_version' => '1.2',
			'upgrade'       => 1,
		);

		$settings = wp_parse_args( $new_settings, $theme_settings );
		update_option( GENESIS_SETTINGS_FIELD, $settings );
	}

	// UPDATE TO VERSION 1.3.
	if ( version_compare( genesis_get_option( 'theme_version', null, false ), '1.3', '<' ) ) {
		// Update theme settings.
		$theme_settings = get_option( GENESIS_SETTINGS_FIELD );
		$new_settings   = array(
			'author_box_single' => genesis_get_option( 'author_box' ),
			'theme_version'     => '1.3',
			'upgrade'           => 1,
		);

		$settings = wp_parse_args( $new_settings, $theme_settings );
		update_option( GENESIS_SETTINGS_FIELD, $settings );

		// Update SEO settings.
		$seo_settings = get_option( GENESIS_SEO_SETTINGS_FIELD );
		$new_settings = array(
			'noindex_cat_archive'    => genesis_get_seo_option( 'index_cat_archive' ) ? 0 : 1,
			'noindex_tag_archive'    => genesis_get_seo_option( 'index_tag_archive' ) ? 0 : 1,
			'noindex_author_archive' => genesis_get_seo_option( 'index_author_archive' ) ? 0 : 1,
			'noindex_date_archive'   => genesis_get_seo_option( 'index_date_archive' ) ? 0 : 1,
			'noindex_search_archive' => genesis_get_seo_option( 'index_search_archive' ) ? 0 : 1,
			'noodp'                  => 1,
			'noydir'                 => 1,
			'canonical_archives'     => 1,
		);

		$settings = wp_parse_args( $new_settings, $seo_settings );
		update_option( GENESIS_SEO_SETTINGS_FIELD, $settings );

		// Delete the store transient, force refresh.
		delete_transient( 'genesis-remote-store' );
	}

	// UPDATE TO VERSION 1.6.
	if ( version_compare( genesis_get_option( 'theme_version', null, false ), '1.6', '<' ) ) {
		// Vestige nav settings, for backward compatibility.
		if ( 'nav-menu' !== genesis_get_option( 'nav_type' ) ) {
			_genesis_vestige( array( 'nav_type', 'nav_superfish', 'nav_home', 'nav_pages_sort', 'nav_categories_sort', 'nav_depth', 'nav_exclude', 'nav_include' ) );
		}

		// Vestige subnav settings, for backward compatibility.
		if ( 'nav-menu' !== genesis_get_option( 'subnav_type' ) ) {
			_genesis_vestige( array( 'subnav_type', 'subnav_superfish', 'subnav_home', 'subnav_pages_sort', 'subnav_categories_sort', 'subnav_depth', 'subnav_exclude', 'subnav_include' ) );
		}

		$theme_settings = get_option( GENESIS_SETTINGS_FIELD );
		$new_settings   = array(
			'theme_version' => '1.6',
			'upgrade'       => 1,
		);

		$settings = wp_parse_args( $new_settings, $theme_settings );
		update_option( GENESIS_SETTINGS_FIELD, $settings );
	}

	// UPDATE DB TO VERSION 1700.
	if ( genesis_get_option( 'db_version', null, false ) < '1700' ) {
		genesis_upgrade_1700();
	}

	// UPDATE DB TO VERSION 1800.
	if ( genesis_get_option( 'db_version', null, false ) < '1800' ) {
		genesis_upgrade_1800();
	}

	// UPDATE DB TO VERSION 1901.
	if ( genesis_get_option( 'db_version', null, false ) < '1901' ) {
		genesis_upgrade_1901();
	}

	// UPDATE DB TO VERSION 2001.
	if ( genesis_get_option( 'db_version', null, false ) < '2001' ) {
		genesis_upgrade_2001();
	}

	// UPDATE DB TO VERSION 2003.
	if ( genesis_get_option( 'db_version', null, false ) < '2003' ) {
		genesis_upgrade_2003();
	}

	// UPDATE DB TO VERSION 2100.
	if ( genesis_get_option( 'db_version', null, false ) < '2100' ) {
		genesis_upgrade_2100();
	}

	// UPDATE DB TO VERSION 2201.
	if ( genesis_get_option( 'db_version', null, false ) < '2201' ) {
		genesis_upgrade_2201();
	}

	// UPDATE DB TO VERSION 2207.
	if ( genesis_get_option( 'db_version', null, false ) < '2207' ) {
		genesis_upgrade_2207();
	}

	// UPDATE DB TO VERSION 2209.
	if ( genesis_get_option( 'db_version', null, false ) < '2209' ) {
		genesis_upgrade_2209();
	}

	// UPDATE DB TO VERSION 2403.
	if ( genesis_get_option( 'db_version', null, false ) < '2403' ) {
		genesis_upgrade_2403();
	}

	// UPDATE DB TO VERSION 2501.
	if ( genesis_get_option( 'db_version', null, false ) < '2501' ) {
		genesis_upgrade_2501();
	}

	// UPDATE DB TO VERSION 2603.
	if ( genesis_get_option( 'db_version', null, false ) < '2603' ) {
		genesis_upgrade_2603();
	}

	// UPDATE DB TO VERSION 2700.
	if ( genesis_get_option( 'db_version', null, false ) < '2700' ) {
		genesis_upgrade_2700();
	}

	// UPDATE DB TO LATEST VERSION.
	if ( genesis_get_option( 'db_version', null, false ) < PARENT_DB_VERSION ) {
		genesis_upgrade_db_latest();
	}

	// Clear the cache to prevent a redirect loop in some object caching environments.
	wp_cache_flush();
	wp_cache_delete( 'alloptions', 'options' );

	/**
	 * Fires after upgrade processes have completed.
	 *
	 * @since 1.0.1
	 */
	do_action( 'genesis_upgrade' );

}

add_action( 'wpmu_upgrade_site', 'genesis_network_upgrade_site' );
/**
 * Run silent upgrade on each site in the network during a network upgrade.
 *
 * Update Genesis database settings for all sites in a network during network upgrade process.
 *
 * @since 2.0.0
 *
 * @param int $blog_id Blog ID.
 */
function genesis_network_upgrade_site( $blog_id ) {

	switch_to_blog( $blog_id );
	$upgrade_url = add_query_arg(
		array(
			'action' => 'genesis-silent-upgrade',
		),
		admin_url( 'admin-ajax.php' )
	);
	restore_current_blog();

	wp_remote_get( esc_url_raw( $upgrade_url ) );

}

add_action( 'wp_ajax_no_priv_genesis-silent-upgrade', 'genesis_silent_upgrade' );
/**
 * Genesis settings upgrade. Silent upgrade (no redirect).
 *
 * Meant to be called via ajax request during network upgrade process.
 *
 * @since 2.0.0
 */
function genesis_silent_upgrade() {

	remove_action( 'genesis_upgrade', 'genesis_upgrade_redirect' );
	genesis_upgrade();
	exit( 0 );

}

add_action( 'genesis_upgrade', 'genesis_upgrade_redirect' );
/**
 * Redirect the user back to the "What's New" page, refreshing the data and notifying the user that they have
 * successfully updated.
 *
 * @since 1.6.0
 *
 * @return null Return early if not an admin page.
 */
function genesis_upgrade_redirect() {

	if ( ! is_admin() || ! current_user_can( 'edit_theme_options' ) || is_customize_preview() ) {
		return;
	}

	genesis_admin_redirect( 'genesis-upgraded' ); // What's New page.

}

add_action( 'admin_notices', 'genesis_upgraded_notice' );
/**
 * Displays the notice that the theme settings were successfully updated to the latest version.
 *
 * Currently only used for pre-release update notices.
 *
 * @since 1.2.0
 *
 * @return void Return early if not on the Theme Settings page.
 */
function genesis_upgraded_notice() {

	if ( ! genesis_is_menu_page( 'genesis' ) ) {
		return;
	}
	if ( isset( $_REQUEST['upgraded'] ) && 'true' === $_REQUEST['upgraded'] ) {
		echo '<div id="message" class="updated highlight"><p><strong>';
		printf(
			/* translators: 1: Genesis version, 2: URL for What's New admin page. */
			esc_html__( 'Congratulations, you are now rocking Genesis %1$s! %2$s', 'genesis' ),
			esc_html( genesis_get_option( 'theme_version' ) ),
			sprintf(
				'<a href="%s">%s</a> %s.',
				esc_url( menu_page_url( 'genesis-upgraded', 0 ) ),
				esc_html__( 'See what\'s new in', 'genesis' ),
				esc_html( PARENT_THEME_BRANCH )
			)
		);
		echo '</strong></p></div>';
	}

}

add_filter( 'update_theme_complete_actions', 'genesis_update_action_links', 10, 2 );
/**
 * Filter the action links at the end of an update.
 *
 * This function filters the action links that are presented to the user at the end of a theme update. If the theme
 * being updated is not Genesis, the filter returns the default values. Otherwise, it will provide a link to the
 * Genesis Theme Settings page, which will trigger the database upgrade.
 *
 * @since 1.1.3
 *
 * @param array  $actions Existing array of action links.
 * @param string $theme   Theme name.
 * @return array Removes all existing action links in favour of a single link, if Genesis is
 *               the theme being updated. Otherwise, return existing action links.
 */
function genesis_update_action_links( array $actions, $theme ) {

	if ( 'genesis' !== $theme ) {
		return $actions;
	}

	return array(
		sprintf(
			'<a href="%s">%s</a>',
			menu_page_url( 'genesis', 0 ),
			esc_html__( 'Click here to complete the upgrade', 'genesis' )
		),
	);

}

add_action( 'admin_notices', 'genesis_update_nag' );
/**
 * Display the update nag at the top of the dashboard if there is a Genesis update available.
 *
 * @since 1.1.0
 *
 * @return void Return early if there is no available update, or user is not a site administrator,
 *              or file modifications have been disabled.
 */
function genesis_update_nag() {

	if ( defined( 'DISALLOW_FILE_MODS' ) && true === DISALLOW_FILE_MODS ) {
		return;
	}

	$genesis_update = genesis_update_check();

	if ( ! $genesis_update || ! is_super_admin() ) {
		return;
	}

	echo '<div id="update-nag">';
	printf(
		/* translators: 1: Genesis version, 2: URL for change log, 3: URL for updating Genesis. */
		esc_html__( 'Genesis %1$s is available. %2$s or %3$s.', 'genesis' ),
		esc_html( $genesis_update['new_version'] ),
		// translators: 1: URL for change log, 2: class attribute for anchor, 3: call to action.
		sprintf(
			'<a href="%1$s" class="%2$s">%3$s</a>',
			esc_url( $genesis_update['changelog_url'] ),
			esc_attr( 'thickbox thickbox-preview' ),
			esc_html__( 'Check out what\'s new', 'genesis' )
		),
		// translators: 1: URL for updating Genesis, 2: class attribute for anchor, 3: call to action.
		sprintf(
			'<a href="%1$s" class="%2$s">%3$s</a>',
			esc_url( wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=genesis', 'upgrade-theme_genesis' ) ),
			esc_attr( 'genesis-js-confirm-upgrade' ),
			esc_html__( 'update now', 'genesis' )
		)
	);
	echo '</div>';

}

add_action( 'init', 'genesis_update_email' );
/**
 * Sends out update notification email.
 *
 * Does several checks before finally sending out a notification email to the
 * specified email address, alerting it to a Genesis update available for that install.
 *
 * @since 1.1.0
 *
 * @return void Return early if email should not be sent.
 */
function genesis_update_email() {

	// Pull email options from DB.
	$email_on = genesis_get_option( 'update_email' );
	$email    = genesis_get_option( 'update_email_address' );

	// If we're not supposed to send an email, or email is blank / invalid, stop.
	if ( ! $email_on || ! is_email( $email ) ) {
		return;
	}

	// Check for updates.
	$update_check = genesis_update_check();

	// If no new version is available, stop.
	if ( ! $update_check ) {
		return;
	}

	// If we've already sent an email for this version, stop.
	if ( get_option( 'genesis-update-email' ) === $update_check['new_version'] ) {
		return;
	}

	// Let's send an email.
	/* translators: 1: Genesis version, 2: URL for current website. */
	$subject = sprintf( __( 'Genesis %1$s is available for %2$s', 'genesis' ), esc_html( $update_check['new_version'] ), home_url() );

	/* translators: %s: Genesis version. */
	$message  = sprintf( __( 'Genesis %s is now available. We have provided 1-click updates for this theme, so please log into your dashboard and update at your earliest convenience.', 'genesis' ), esc_html( $update_check['new_version'] ) );
	$message .= "\n\n" . wp_login_url();

	// Update the option so we don't send emails on every pageload.
	update_option( 'genesis-update-email', $update_check['new_version'], true );

	// Send that puppy!
	wp_mail( sanitize_email( $email ), $subject, $message );

}

add_filter( 'pre_set_site_transient_update_themes', 'genesis_disable_wporg_updates' );
add_filter( 'pre_set_transient_update_themes', 'genesis_disable_wporg_updates' );
/**
 * Disable WordPress from giving update notifications on Genesis or Genesis child themes.
 *
 * This function filters the value that is saved after WordPress tries to pull theme update transient data from WordPress.org
 *
 * Its purpose is to disable update notifications for Genesis and Genesis child themes.
 * This prevents WordPress.org repo themes from being installed over one of our themes.
 *
 * @since 2.0.2
 *
 * @param object $value Update check object.
 * @return object Update check object.
 */
function genesis_disable_wporg_updates( $value ) {

	foreach ( wp_get_themes() as $theme ) {

		if ( 'genesis' === $theme->get( 'Template' ) ) {

			unset( $value->response[ $theme->get_stylesheet() ] );

		}
	}

	return $value;

}

add_filter( 'site_transient_update_themes', 'genesis_update_push' );
add_filter( 'transient_update_themes', 'genesis_update_push' );
/**
 * Integrate the Genesis update check into the WordPress update checks.
 *
 * This function filters the value that is returned when WordPress tries to pull theme update transient data.
 *
 * It uses `genesis_update_check()` to check to see if we need to do an update, and if so, adds the proper array to the
 * `$value->response` object. WordPress handles the rest.
 *
 * @since 1.1.0
 *
 * @param object $value Update check object.
 * @return object Modified update check object.
 */
function genesis_update_push( $value ) {

	if ( defined( 'DISALLOW_FILE_MODS' ) && true === DISALLOW_FILE_MODS ) {
		return $value;
	}

	if ( isset( $value->response['genesis'] ) ) {
		unset( $value->response['genesis'] );
	}

	$genesis_update = genesis_update_check();

	if ( $genesis_update ) {
		$value->response['genesis'] = $genesis_update;
	}

	return $value;

}

add_action( 'load-update-core.php', 'genesis_clear_update_transient' );
add_action( 'load-themes.php', 'genesis_clear_update_transient' );
/**
 * Delete Genesis update transient after updates or when viewing the themes page.
 *
 * The server will then do a fresh version check.
 *
 * It also disables the update nag on those pages as well.
 *
 * @since 1.1.0
 *
 * @see genesis_update_nag()
 */
function genesis_clear_update_transient() {

	genesis_delete_expiring_setting( 'update' );
	remove_action( 'admin_notices', 'genesis_update_nag' );

}

/**
 * Converts array of keys from Genesis options to vestigial options.
 *
 * This is done for backwards compatibility.
 *
 * @since 1.6.0
 *
 * @access private
 *
 * @param array  $keys    Array of keys to convert. Default is an empty array.
 * @param string $setting Optional. The settings field the original keys are found under. Default is GENESIS_SETTINGS_FIELD.
 * @return void Return early if no `$keys` were provided, or no new vestigial options are needed.
 */
function _genesis_vestige( array $keys = array(), $setting = GENESIS_SETTINGS_FIELD ) {

	// If no $keys passed, do nothing.
	if ( ! $keys ) {
		return;
	}

	// Pull options.
	$options = get_option( $setting );
	$vestige = get_option( 'genesis-vestige' );

	// Cycle through $keys, creating new vestige array.
	$new_vestige = array();
	foreach ( (array) $keys as $key ) {
		if ( isset( $options[ $key ] ) ) {
			$new_vestige[ $key ] = $options[ $key ];
			unset( $options[ $key ] );
		}
	}

	// If no new vestigial options being pushed, do nothing.
	if ( ! $new_vestige ) {
		return;
	}

	// Merge the arrays, if necessary.
	$vestige = $vestige ? wp_parse_args( $new_vestige, $vestige ) : $new_vestige;

	// Insert into options table.
	update_option( 'genesis-vestige', $vestige );
	update_option( $setting, $options );

}
