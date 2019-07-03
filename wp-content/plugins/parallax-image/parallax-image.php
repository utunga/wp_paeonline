<?php
/*
  	Plugin Name: Parallax Image
  	Plugin URI: https://www.duckdiverllc.com/parallax-image-plugin/
  	Version: 1.7
  	Contributors: thehowarde
	Author: Howard Ehrenberg
	Author URI: https://www.howardehrenberg.com
	Donate link: https://www.duckdiverllc.com/parallax-image-plugin/
	Tags: Parallax, Full Screen Parallax, Parallax Window, Parallax Image
	Requires at least: 4.5
	Tested up to: 5.0
	Requires PHP: 5.6
  	Description: A Simple plugin to employ the parallax.js script by pixelcog.  Use the shortcode [dd-parallax] to use.  See readme.txt for complete instructions. 
	License:  GNU General Public License v3
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) )
exit; 
 
define( 'DD_PARALLAX_FILE', __FILE__ );
$plugin_url = WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__));

require_once "$plugin_url/assets/shortcode.php";
require_once "$plugin_url/assets/admin-scripts.php";

register_activation_hook( __FILE__, 'dd_set_up_options' );

function dd_set_up_options(){
  add_option('dd_parallax_mce_button', 'checked');
}
?>