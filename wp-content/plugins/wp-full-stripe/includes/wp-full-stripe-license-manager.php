<?php

/*
 * This is a generic license manager for WP Full Stripe
 */

abstract class MM_WPFS_LicenseManager_Root {
	private function __construct() {
	}

	public abstract function initPluginUpdater();

	public abstract function getLicenseOptionDefaults();

	public abstract function setLicenseOptionDefaultsIfEmpty( & $options );

	public abstract function activateLicenseIfNeeded();

}

include( dirname( __FILE__ ) . '/wp-full-stripe-license-manager-implementation.php' );
