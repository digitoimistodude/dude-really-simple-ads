<?php
/**
 * Plugin Name: Really simple ads
 * Plugin URL: https://www.dude.fi
 * Description: A simple way to manage, track and show ads
 * Version: 1.1.5
 * Author: Digitoimisto Dude Oy, Timi Wahalahti
 * Author URL: https://www.dude.fi
 * Requires at least: 4.6
 * Tested up to: 5.6
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: dude-really-simple-ads
 * Domain Path: /languages
 *
 * @package dude-really-simple-ads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 *  Include CMB2 metabox library which is used for ad meta.
 *  See https://github.com/CMB2/CMB2
 */
require plugin_dir_path( __FILE__ ) . 'includes/cmb2/init.php';

// Check that plugin isn't active already for some odd reason
if ( ! function_exists( 'run_drsa' ) ) {
	require plugin_dir_path( __FILE__ ) . 'includes/class-dude-really-simple-ads.php';

	function run_drsa() {
		$plugin = new Dude_Really_Simple_Ads();
		$plugin->run();
	} // end run_drsa

	run_drsa();
}
