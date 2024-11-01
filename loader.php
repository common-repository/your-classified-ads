<?php
/*
Plugin Name: Your Classified Ads
Plugin URI:  http://dev.pellicule.org/?page_id=16
Description: This component adds classified ads to your Wordpress installation.
Version: 0.9.6.1-beta
Revision Date: April 25, 2012
Requires at least: WP 3.1
Tested up to: WP 3.3.2
License: (Classifieds: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
Author: G.Breant
Author URI: http://dev.pellicule.org
Site Wide Only: true
*/

if ( !defined( 'YCLADS_SLUG' ) ) {
	define ( 'YCLADS_SLUG', 'classified-ads' );
	define ( 'YCLADS_PLUGIN_NAME', 'your-classified-ads');
	define ( 'YCLADS_IS_INSTALLED', 1 );
	define ( 'YCLADS_VERSION', '0.9.6.1-beta' );
	//Core Path
	
	define ( 'YCLADS_DIRNAME', str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) );

	
	define ( 'YCLADS_PLUGIN_DIR',  WP_PLUGIN_DIR . '/' . YCLADS_DIRNAME );
	define ( 'YCLADS_PLUGIN_URL', WP_PLUGIN_URL . '/' . YCLADS_DIRNAME );

	define ( 'YCLADS_WORDPRESS_URL', 'http://wordpress.org/extend/plugins/your-classified-ads/' );
	define ( 'YCLADS_SUPPORT_URL', 'http://dev.pellicule.org/bbpress/forum/your-classified-ads/' );
	define ( 'YCLADS_DONATION_URL', 'http://dev.pellicule.org/your-classified-ads-plugin/#donations' );
}

//////////////
// Make sure OQP is loaded before we do anything. 

if ( !class_exists( 'Oqp_Form' ) ) {
	$file='one-quick-post/loader.php';
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	
	//if ( !is_plugin_active( $file ) ) {
		//require_once ( WP_PLUGIN_DIR .'/'. $file );
	//} else {
		add_action( 'admin_notices', 'yclads_install_oqp_notice' );
		return false;
	//}
}
/* The notice we show when OQP is missing. */
function yclads_install_oqp_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	printf(__('%s requires the %s plugin to work.  Please <a target="_blank" href="http://wordpress.org/extend/plugins/one-quick-post">install it</a> first, or <a href="plugins.php">deactivate the plugin</a>.','yclads'),'<strong>'.__('Your Classified Ads','yclads').'</strong>',__('One Quick Post','oqp'));
	echo '</p></div>';
}

//////////////

require_once( YCLADS_PLUGIN_DIR . '/includes/yclads-core.php'); 




//BUDDYPRESS
function yclads_bp_init() {
	define ( 'YCLADS_BP', true );
	define ( 'YCLADS_BP_PLUGIN_DIR', YCLADS_PLUGIN_DIR.'/buddypress' );
	require_once( YCLADS_PLUGIN_DIR . '/bp-yclads-loader.php');
}

add_action( 'bp_include', 'yclads_bp_init' );


register_activation_hook(__FILE__,'yclads_activation');
register_deactivation_hook(__FILE__, 'yclads_deactivation');


?>