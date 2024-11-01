<?php

/**
 * Retrieve the permalink for the classified comments feed.
 *
 * @since 2.2.0
 *
 * @param int $post_id Optional. Post ID.
 * @param string $feed Optional. Feed type.
 * @return string
 */



function yclads_dir_link(){
	echo yclads_get_dir_link();
}

	function yclads_get_dir_link(){
		$link = get_post_type_archive_link('yclad');
		$link = untrailingslashit($link);
		return apply_filters( 'yclads_get_dir_link', $link );
	}
	
function yclads_action_link($action_slug) {
	echo yclads_get_action_link($action_slug);
}
	function yclads_get_action_link($action_slug){
		$link = yclads_get_dir_link();
		$link = add_query_arg(array('yclad_action'=>$action_slug),$link);
		return apply_filters( 'yclads_get_action_link', $link, $action_slug );
	}
	

	
function yclads_self_link(){
	echo yclads_get_self_link();
}
	function yclads_get_self_link(){
		if (!is_user_logged_in()) return false;
		return apply_filters( 'yclads_get_self_link', yclads_get_author_link());
	}
	
function yclads_author_link(){
	echo yclads_get_author_link();
}
	
	function yclads_get_author_link($user_id=false){
		if (!$user_id){
			$user_id = get_current_user_id();
		}
		if (!$user_id) return false;

		
		
		$url = yclads_get_dir_link();

		if (!get_option('permalink_structure')){
			$link = add_query_arg(array('author'=>$user_id),$url);
		}else{
			if($user_id==get_current_user_id()){ //SELF
				$link = $url.'/'._x('my-ads','slug','yclads');
			}else{
				global $wp_rewrite;
				$user_info=get_userdata($user_id);
				$link = $url.'/'.$wp_rewrite->author_base.'/'.$user_info->user_nicename;
			}
		}
		
		
		return apply_filters( 'yclads_get_author_link', $link, $user_id);
		
	}
	
function yclads_creation_link(){
	echo yclads_get_creation_link();
}

function yclads_get_creation_link() {

	//CHECK USER CAN CREATE A NEW AD
	
	$user_id=get_current_user_id();
	$cap = 'edit_yclads';

	//GET DUMMY USER IF OQP_FORM IS ENABLED
	if ((!$user_id) && (class_exists('OQP_Guest_User'))) {
		global $oqp_guest;
		$user_id = $oqp_guest->dummy->ID;
	}
	
	if (!$user_id) return false;
	
	$user = new WP_User($user_id);
	if (!$user->has_cap($cap)) return false;


	$link = admin_url('post-new.php?post_type=yclad');
	
	return apply_filters('yclads_get_creation_link',$link);
}
function yclads_get_frontend_creation_link($backend_link){
	global $yclads;
	$oqp_page_id = $yclads->oqp_page_id;
	
	if(!$oqp_page_id) return $backend_link;

	$page_url = get_permalink($oqp_page_id);
	$args['oqp_action']=_x('create','slug','oqp');
	$link = add_query_arg($args,$page_url);

	return apply_filters('yclads_get_frontend_creation_link',$link);

}

add_filter('yclads_get_creation_link','yclads_get_frontend_creation_link');

	


function yclad_taxonomy_list($taxonomy,$separator=false,$type='slug',$post=false) {
	
	if (!$post) {
		global $post;
	}

	$ad_taxonomies = (array)get_the_terms( $post->ID, $taxonomy );
	foreach ($ad_taxonomies as $ad_taxonomy) {
		$ad_taxonomies_arr[]=$ad_taxonomy->$type;
	}

	if ($separator) { //return as string list
		$ad_taxonomies_str = implode($separator,(array)$ad_taxonomies_arr);
		return $ad_taxonomies_str;
	}else{ //return as array
		return $ad_taxonomies_arr;
	}
}


?>