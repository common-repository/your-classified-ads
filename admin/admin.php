<?php

require_once( YCLADS_PLUGIN_DIR . '/admin/includes/settings.php');
require_once( YCLADS_PLUGIN_DIR . '/admin/includes/custom-post-types.php');



function yclads_activation() {
	global $wp_user_roles;

	unset( $wp_user_roles );

	remove_role( 'yclad_creator' );
	//Create the Yclad Role
	add_role( 'yclad_creator', _x( 'Ad Creator', 'Role title', 'yclad' ), YCLADS_Admin_Post::create_role() );  

	//Extend the capabilities
	$capabilities = YCLADS_Admin_Post::get_extended_capabilities();
	YCLADS_Admin_Post::extend_caps($capabilities);
	
	yclads_set_default_settings();

	//Add hook to flush permalinks
	add_action( 'wp', '_flush_permalinks' );
	
	
	do_action('yclads_activation');
}

function yclads_deactivation() {
	remove_role( 'yclad_creator' );
	do_action('yclads_deactivation');
}

function yclads_plugin_settings_action( $links, $file ) {
	//Static so we don't call plugin_basename on every plugin row.
	static $this_plugin;


	if ( ! $this_plugin ) $this_plugin = YCLADS_DIRNAME . 'loader.php';

	if ( $file == $this_plugin ){
		$settings_link = '<a href="edit.php?post_type=yclad&page='.YCLADS_SLUG.'">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
} 


function yclads_admin_init() {

	//keep in loader
	add_filter( 'plugin_action_links', 'yclads_plugin_settings_action',10,2);
	
	add_action('right_now_content_table_end', 'yclads_admin_right_now_count');

	yclads_post_types_admin_init();

	add_action('admin_init', 'yclads_settings_init');
	add_action('admin_menu', 'yclads_admin_menu');
	add_action('admin_enqueue_scripts', 'yclads_enqueue_admin_scripts',1);

}
	
// adds "Settings" link to the plugin action page

add_action("init", "yclads_admin_init",9);

?>