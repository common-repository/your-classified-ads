<?php


// Remember to flush_rules() when adding rules
function yclads_rewrite_flush(){
	global $wp_rewrite;
   	$wp_rewrite->flush_rules();
}
//TODO TO FIX : hook on plugin activation, not init
add_action('init','yclads_rewrite_flush');
//add_action('yclads_activation','yclads_rewrite_flush');

//see http://gskinner.com/RegExr/
// http://www.ballyhooblog.com/custom-post-types-wordpress-30-with-template-archives/


function yclads_rewrite_rules($wp_rewrite) {

	$newrules = array();

	//my ads
	$user_id = get_current_user_id();
	if ($user_id){
		$newrules[__(YCLADS_SLUG,'yclads-slugs') . '/'._x('my-ads','slug','yclads')] = 'index.php?post_type=yclad&author='.$user_id;
	}

	
	/*
	//index
	$newrules[__(YCLADS_SLUG,'yclads-slugs') . '/?$'] = 'index.php?post_type=yclad';
	$newrules[__(YCLADS_SLUG,'yclads-slugs') . '/page/?([0-9]{1,})/?$'] = 'index.php?post_type=yclad&paged=' . $wp_rewrite->preg_index(1);
	$newrules[__(YCLADS_SLUG,'yclads-slugs') . '/(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?post_type=yclad&feed=' . $wp_rewrite->preg_index(1);


	//PLACE ad
	$newrules[__(YCLADS_SLUG,'yclads-slugs') . '/'.__('create','oqp-slugs').'/?$'] = 'index.php?post_type=yclad&oqp_action='.__('create','oqp-slugs');
	
	///PLACE ad (steps)
	$forms = oqp_admin_get_forms();
	$form=$forms['yclad'];
	foreach ($form['steps'] as $step) {
		$steps[]=$step['slug'];
	}

	$stepstr=implode('|',$steps);
	$newrules[__(YCLADS_SLUG,'yclads-slugs') . '/'.__('create','oqp-slugs').'/'.__('step','oqp-slugs').'/(' . $stepstr . ')/?$'] = 'index.php?post_type=yclad&oqp_action='.__('create','oqp-slugs').'&oqp_step=' . $wp_rewrite->preg_index(1);
	*/


    $wp_rewrite->rules = $newrules + $wp_rewrite->rules;

}
add_action('generate_rewrite_rules', 'yclads_rewrite_rules');



?>