<?php

if (is_admin()) {
	require_once( YCLADS_PLUGIN_DIR . '/admin/admin.php');
}

//require_once( YCLADS_PLUGIN_DIR . '/includes/rewrite.php');

require_once( YCLADS_PLUGIN_DIR . '/includes/query.php' );

require_once( YCLADS_PLUGIN_DIR . '/includes/custom-post-types.php' );

require_once( YCLADS_PLUGIN_DIR . '/includes/terms-template.php');

require_once( YCLADS_PLUGIN_DIR . '/includes/ad-template.php');
require_once( YCLADS_PLUGIN_DIR . '/includes/link-template.php');
require_once( YCLADS_PLUGIN_DIR . '/includes/theme.php');
require_once( YCLADS_PLUGIN_DIR . '/includes/default-widgets.php');
require_once( YCLADS_PLUGIN_DIR . '/includes/extensions.php');

require_once( YCLADS_PLUGIN_DIR . '/includes/directory-search-menu.php');


function yclads_is_component(){
	if((yclads_is_directory())||yclads_is_single()) $is_comp=true;
	return apply_filters('yclads_is_component',$is_comp);
}
function yclads_is_directory(){
    if ((get_query_var('post_type')=='yclad')&&(!is_single()))$is_dir=true;
    return apply_filters('yclads_is_directory',$is_dir);
}
function yclads_is_single(){
    if ((get_query_var('post_type')=='yclad')&&(is_single()))$is_single=true;
    return apply_filters('yclads_is_single',$is_single);
}


function yclads_admin_get_default_settings($setting=false) {

	$options=apply_filters('yclads_admin_get_default_settings',$options);
	
	if ($setting) {
		return $options[$setting];
	}else{
		return $options;
	}
}

function yclads_get_option($name=false) {
	global $yclads_admin; 
	$options = get_option('yclads_options');

        if($name){ //single option
            if(isset($options[$name])) return $options[$name];
            return yclads_admin_get_default_settings($name);
        }else{
            if($options) return $options;
            return yclads_admin_get_default_settings();
        }
}


function yclads_update_posts_stats($post_ID, $post) {
	if ($post->post_type!='yclad') return false;
	//update posts stats count (donation button)
	$nposts = get_option('yclads_posts_stats');
	update_option( 'yclads_posts_stats', $nposts+1 );
	$is_hundred=$nposts/100;
	//remove donated every 100 posts
	if (is_int($is_hundred)){
		$yclads_options = yclads_get_option();
		unset($yclads_options['donated']);
		update_option( 'yclads_options', $yclads_options );
	}
}
/*
function yclads_notify_check($new_status, $old_status, $post) {

	if ($post->post_type!='yclad') return false;

	$form = new Oqp_Form('yclads');
	$do_author_emails = $form->email_notifications_enabled;
	oqp_notify_for_post($new_status, $old_status, $post, $do_author_emails);
}
*/



function yclads_total_ad_count() {
	echo yclads_get_total_post_count();
}

function yclads_total_ad_count_for_user( $user_id = 0 ) {
	echo yclads_get_total_post_count_for_user( $user_id );
}

function yclads_total_ad_count_for_action( $action_id_or_slug ) {
	echo yclads_get_total_post_count_for_action( $action_id_or_slug );
}


function yclads_get_total_post_count_for_user( $user_id = 0 ) {
	global $bp;

	if ( !$user_id ) $user_id = get_current_user_id();

	if ( !$count = wp_cache_get( 'yclads_total_post_count_for_user_' . $user_id, 'yclads' ) ) {
                $args['author']=$user_id;
                if($user_id==get_current_user_id()) $args['post_status']='any';
		$count = yclads_count_posts($args);
		wp_cache_set( 'yclads_total_post_count_for_user_' . $user_id, $count, 'yclads' );
	}

	return apply_filters( 'yclads_get_total_post_count_for_user', $count, $user_id );
}



function yclads_get_total_post_count_for_action( $action_id_or_slug ) {
	if (is_numeric($action_id_or_slug)){
		$field = 'id';
	}else{
		$field = 'slug';
	}

	$term = get_term_by( $field, $action_id_or_slug, 'yclad_action');

	if ( !$count = wp_cache_get( 'yclads_total_post_count_for_action_' . $term->term_id, 'yclads' ) ) {
		$count = yclads_count_posts(array('yclad_action'=>$term->slug));
		wp_cache_set( 'yclads_total_post_count_for_action_' . $term->term_id, $count, 'yclads' );
	}

	return apply_filters( 'yclads_get_total_post_count_for_action', $count, $term->term_id );
}


function yclads_get_total_post_count() {
	if ( !$count = wp_cache_get( 'yclads_total_post_count', 'yclads' ) ) {
		$count_posts = wp_count_posts('yclad');
		$count = apply_filters( 'yclads_get_total_post_count', $count_posts->publish );
		wp_cache_set( 'yclads_total_post_count', $count, 'yclads' );
	}

	return apply_filters( 'yclads_get_total_post_count', $count );
}

function yclads_user_can_send_invites(){
	$can = is_user_logged_in();
	return apply_filters('yclads_user_can_send_invites',$can);
}


function yclads_init() {
	global $yclads;
        

	$yclads->post_slug = 'yclad';
	$yclads->oqp_page_id = yclads_get_option('oqp_page_id');

	
	//DEBUG
	if(yclads_get_option('enable_debug')) {
		define ( 'YCLADS_DEBUG', true ); //set to one for testing
	}

	//load languages
	load_plugin_textdomain( 'yclads', false, YCLADS_DIRNAME . 'lang' );

	add_action('wp_insert_post','yclads_update_posts_stats',10,2);
	
	if(!is_admin()){
		//allow status (draft,pending...) if viewing my own ads
		add_filter('pre_get_posts','yclads_query_my_ads_statuses');
	
		//adds the /classifieds-ads/ prefix to terms links
		add_filter('term_link', 'yclad_term_link',9,3);

	}
	
}




function yclads_wp() {
	add_filter('body_class','yclads_page_classes');
	add_action('oqp_wp_styles','yclads_wp_styles');
	add_action('wp_print_scripts','yclads_wp_scripts');
}

add_action('wp','yclads_wp');

//TO FIX URGENT
//add_action('init','yclads_init',9);

?>