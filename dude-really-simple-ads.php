<?php

/**
 * Plugin Name: Really simple ads
 * Plugin URL: https://www.dude.fi
 * Description: A simple way to manage, track and show ads
 * Version: 0.1.0
 * Author: Digitoimisto Dude Oy, Timi Wahalahti
 * Author URL: https://www.dude.fi
 * Requires at least: 4.6
 * Tested up to: 4.6
 *
 * Text Domain: dude-really-simple-ads
 * Domain Path: /languages
 */

if( !defined( 'ABSPATH' )  )
	exit();

// We use composer to loading and keeping CMB2 up-to-date
require __DIR__.'/vendor/autoload.php';

// Check that plugin isn't active already for some odd reason
if( !function_exists( 'run_drsa' ) ) {
	require plugin_dir_path( __FILE__ ).'includes/class-dude-really-simple-ads.php';

	function run_drsa() {
		$plugin = new Dude_Really_Simple_Ads();
		$plugin->run();
	} // end run_drsa

	run_drsa();
}
