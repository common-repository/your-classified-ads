<?php

function yclads_query_my_ads_statuses(&$query) {

	if ($query->get('post_type')!='yclad') return $query;
	if (!$query->is_author) return $query;
	
	$wp_author = $query->get('author_name'); //QUERY IS FILTERED BY AUTHOR NAME
	if ($query->get('post_status')) return $query; //QUERY HAS A PARTICULAR POST_STATUS

	
	if ($wp_author) {
		$id = get_user_id_from_string( $wp_author );
		$query->set('author_name',false);
		$query->set('author',$id);
	}
	
	$wp_author_ID = $query->get('author'); //QUERY IS FILTERED BY AUTHOUR ID
	
	//NOT "MY POSTS" QUERY
	if ( $wp_author_ID != get_current_user_id()) return $query;
	
	$allowed_statuses = array(
		'publish',
		'pending',
		'draft');
		
	$query->set('post_status',$allowed_statuses);

	return $query;
}



function yclads_count_posts($args=false) {
	$defaults['post_type']='yclad';
	$defaults['suppress_filters']=true;
	$r = wp_parse_args( $args, $defaults );
	$count_query = new WP_Query($r);
	
	return (int)$count_query->found_posts;
}