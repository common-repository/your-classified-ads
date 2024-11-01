<?php
/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function yclads_bp_screen_my_ads() {
	global $bp;

	if ( isset($_GET['n']) ) {
		// Delete group request notifications for the user
		BP_Core_Notification::delete_for_user_by_type( get_current_user_id(), $bp->groups->id, 'membership_request_accepted' );
		BP_Core_Notification::delete_for_user_by_type( get_current_user_id(), $bp->groups->id, 'membership_request_rejected' );
		BP_Core_Notification::delete_for_user_by_type( get_current_user_id(), $bp->groups->id, 'member_promoted_to_mod' );
		BP_Core_Notification::delete_for_user_by_type( get_current_user_id(), $bp->groups->id, 'member_promoted_to_admin' );
	}

	add_action( 'bp_template_content', 'yclads_bp_screen_my_ads_content' );

	do_action( 'yclads_bp_screen_my_ads' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}


function yclads_bp_screen_my_ads_content() {
	global $bp;

	switch ( $_REQUEST['post_status'] ) {
		case 'pending' :
			$post_status='pending';
		break;
		case 'draft' :
			$post_status='draft';
		break;
		default:
			$post_status='publish';
	}
	
	yclads_bp_init_oqp_form_member();
	$args=array(
		'post_type'=>'yclad',
		'author'=>$bp->displayed_user->id,
		'post_status'=>$post_status
	);

	oqp_loop($args);

}

//MY SUBSCRIPTIONS
function yclads_bp_screen_my_subscriptions() {
	global $bp;
	
	/*
	if ( isset($_GET['n']) ) {
		// Delete group request notifications for the user
		BP_Core_Notification::delete_for_user_by_type( get_current_user_id(), $bp->groups->id, 'membership_request_accepted' );
		BP_Core_Notification::delete_for_user_by_type( get_current_user_id(), $bp->groups->id, 'membership_request_rejected' );
		BP_Core_Notification::delete_for_user_by_type( get_current_user_id(), $bp->groups->id, 'member_promoted_to_mod' );
		BP_Core_Notification::delete_for_user_by_type( get_current_user_id(), $bp->groups->id, 'member_promoted_to_admin' );
	}
	*/
	
	

	add_action( 'bp_template_content', 'yclads_bp_screen_my_subscriptions_content' );

	do_action( 'yclads_bp_screen_my_subscriptions' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function yclads_bp_screen_my_subscriptions_content() {
	global $bp;
	global $wp_query;
	
	$subscription_id=$bp->action_variables[0];
	
	$wp_query->set('yclads_subscriptions',true);//to be able to see the subscription tabs

	if (!$subscription_id){	
		$args['yclads_subscriptions']=true;
	}else{
		$wp_query->set('yclads_subscription',$subscription_id);//to be able to see the subscription tabs
		$args['yclads_subscription']=$subscription_id;
	}
	yclads_bp_init_oqp_form_member();
	
	$args['post_type']='yclad';

	oqp_loop($args);

}

function yclads_bp_screen_create_ad(){
	$creation_link = yclads_get_creation_link();
	wp_redirect( $creation_link );die("yclads_bp_screen_create_ad");
}

?>