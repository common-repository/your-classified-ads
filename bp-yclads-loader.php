<?php
/**
 * BuddyPress Your Classified Ads Loader
 *
 * an ads component, for users to yclad themselves together. Includes a
 * robust sub-component API that allows Ads to be extended.
 * Comes preconfigured with an activity stream, discussion forums, and settings.

 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;



class BP_Yclads_Component extends BP_Component {

	/**
	 * Start the yclads component creation process
	 *
	 * @since 1.5
	 */
	function __construct() {
		parent::start(
			'yclads',
			__( 'Classified Ads', 'yclads' ),
			YCLADS_PLUGIN_DIR
		);
	}

	/**
	 * Include files
	 */
	function includes() {

		$includes = array(
			'cache',
			'actions',
			'filters',
			'screens',
			'classes',
			'widgets',
			'activity',
			'template',
			'buddybar',
			'adminbar',
			'functions',
			'notifications',
			'theme'
			
		);


		parent::includes( $includes );
	}

	/**
	 * Setup globals
	 *
	 *
	 * @since 1.5
	 * @global obj $bp
	 */
	function setup_globals() {
		global $bp;

		// Global tables for messaging component
		$global_tables = array(
			'table_name'           => $bp->table_prefix . 'bp_yclads',
			'table_name_members'   => $bp->table_prefix . 'bp_yclads_members',
			'table_name_ycladmeta' => $bp->table_prefix . 'bp_yclads_ycladmeta'
		);

		// All globals for messaging component.
		// Note that global_tables is included in this array.
		$globals = array(
			'path'                  => YCLADS_PLUGIN_DIR,
			'slug'                  => YCLADS_SLUG,
			'root_slug'             => $bp->pages->yclads->slug,
			'has_directory'         => false,
			'notification_callback' => 'yclads_format_notifications',
			'search_string'         => __( 'Search Ads...', 'yclads' ),
			'global_tables'         => $global_tables
		);

		parent::setup_globals( $globals );

		/** Single Ad Globals **********************************************/
		
		// Are we viewing a single yclad?

		if ( yclads_is_component() && yclads_is_single() ) {
			global $post;
			print_r("is single:".$post->ID);//DEBUG
			print_r($current_yclad_class);
			

			$bp->is_single_item  = true;
			$this->current_yclad = apply_filters( 'bp_yclads_current_yclad_object', $post );

			// When in a single yclad, the first action is bumped down one because of the
			// yclad name, so we need to adjust this and set the yclad name to current_item.
			$bp->current_item   = bp_current_action();
			$bp->current_action = bp_action_variable( 0 );
			array_shift( $bp->action_variables );

			/*
			// Using "item" not "yclad" for generic support in other components.
			if ( is_super_admin() )
				bp_update_is_item_admin( true, 'yclads' );
			else
				bp_update_is_item_admin( yclads_is_user_admin( $bp->loggedin_user->id, $this->current_yclad->id ), 'yclads' );

			// If the user is not an admin, check if they are a moderator
			if ( !bp_is_item_admin() )
				bp_update_is_item_mod  ( yclads_is_user_mod  ( $bp->loggedin_user->id, $this->current_yclad->id ), 'yclads' );

			// Is the logged in user a member of the yclad?
			
			if ( ( is_user_logged_in() && yclads_is_user_member( $bp->loggedin_user->id, $this->current_yclad->id ) ) )
				$this->current_yclad->is_user_member = true;
			else
				$this->current_yclad->is_user_member = false;
			

			// Should this yclad be visible to the logged in user?
			if ( 'public' == $this->current_yclad->status || $this->current_yclad->is_user_member )
				$this->current_yclad->is_visible = true;
			else
				$this->current_yclad->is_visible = false;

			// If this is a private or hidden yclad, does the user have access?
			if ( 'private' == $this->current_yclad->status || 'hidden' == $this->current_yclad->status ) {
				if ( $this->current_yclad->is_user_member && is_user_logged_in() || is_super_admin() )
					$this->current_yclad->user_has_access = true;
				else
					$this->current_yclad->user_has_access = false;
			} else {
				$this->current_yclad->user_has_access = true;
			}
			*/
		// Set current_yclad to 0 to prevent debug errors
		} else {
			$this->current_yclad = 0;
		}

		// Illegal yclad names/slugs
		$this->forbidden_names = apply_filters( 'yclads_forbidden_names', array(
			_x('my-ads','slug','yclads'),
			'create',
			'invites',
			'send-invites',
			'forum',
			'delete',
			'add',
			'admin',
			'request-membership',
			'members',
			'settings',
			'avatar',
			$this->slug,
			$this->root_slug,
		) );

		// If the user was attempting to access an ad, but no ad by that name was found, 404
		if ( yclads_is_component() && empty( $this->current_yclad ) && !empty( $bp->current_action ) && !in_array( $bp->current_action, $this->forbidden_names ) ) {
			bp_do_404();
			return;
		}
		
		// Ad access control
		if ( yclads_is_component() && !empty( $this->current_yclad ) && !empty( $bp->current_action ) && !$this->current_yclad->user_has_access ) {
			if ( is_user_logged_in() ) {
				// Off-limits to this user. Throw an error and redirect to the
				// yclad's home page			
				bp_core_no_access( array(
					'message'  => __( 'You do not have access to this ad.', 'yclads' ),
					'root'     => bp_get_yclad_permalink( $bp->yclads->current_yclad ),
					'redirect' => false
				) );
			} else {
				// Allow the user to log in
				bp_core_no_access();
			}
		}

		// Preconfigured yclad creation steps
		$this->yclad_creation_steps = apply_filters( 'yclads_create_yclad_steps', array(
			'yclad-details'  => array(
				'name'       => __( 'Details',  'buddypress' ),
				'position'   => 0
			),
			'yclad-settings' => array(
				'name'       => __( 'Settings', 'buddypress' ),
				'position'   => 10
			),
			'yclad-avatar'   => array(
				'name'       => __( 'Avatar',   'buddypress' ),
				'position'   => 20 ),
		) );

		// If friends component is active, add invitations
		if ( bp_is_active( 'friends' ) ) {
			$this->yclad_creation_steps['yclad-invites'] = array(
				'name'     => __( 'Invites', 'buddypress' ),
				'position' => 30
			);
		}

		// Ads statuses
		$this->valid_status = apply_filters( 'yclads_valid_status', array(
			'public',
			'private',
			'hidden'
		) );
	}

	/**
	 * Setup BuddyBar navigation
	 *
	 * @global obj $bp
	 */
	function setup_nav() {
		global $bp;
		global $yclads_ssubs;
		
	if ($yclads_ssubs->user_subscriptions){
		$subscriptions_posts_count = yclads_subscriptions_get_total_post_count( get_current_user_id() );

		if ($subscriptions_posts_count){
			$default_subnav='yclads_bp_screen_my_subscriptions';
			$default_subnav_slug=_x('my-subscriptions','slug','query-subscribe');
		}else{
			$default_subnav='yclads_bp_screen_my_ads';
			$default_subnav_slug=_x('my-ads','slug','yclads');
		}
	}

		// Add 'Ads' to the main navigation
		$main_nav = array(
			'name'                =>  __( 'Classified ads', 'yclads' ),
			'slug'                => $this->slug,
			'position'            => 70,
			'screen_function'     => $default_subnav,
			'default_subnav_slug' => $default_subnav_slug,
			'item_css_id'         => $this->id
		);

		$yclads_link = trailingslashit( $bp->loggedin_user->domain . $this->slug );
		
		// Add the My Subscriptions nav item
		if ($yclads_ssubs->user_subscriptions){
			$sub_nav[] = array(
				'name'            => sprintf( __( 'My Subscriptions <span>%2d</span>', 'yclads' ),$subscriptions_posts_count ),
				'slug'            => _x('my-subscriptions','slug','query-subscribe'),
				'parent_url'      => $yclads_link,
				'parent_slug'     => $this->slug,
				'screen_function' => 'yclads_bp_screen_my_subscriptions',
				'user_has_access' =>  bp_is_my_profile(),
				'position'        => 10
			);
		}

		// Add the My Ads nav item
		$sub_nav[] = array(
			'name'            => sprintf( __( 'My Ads <span>%d</span>', 'yclads' ), yclads_get_total_post_count_for_user() ),
			'slug'            => _x('my-ads','slug','yclads'),
			'parent_url'      => $yclads_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'yclads_bp_screen_my_ads',
			'position'        => 20,
			'item_css_id'     => 'yclads-my-yclads'
		);

		// Add the Create Ads nav item
		$sub_nav[] = array(
			'name'            =>  __( 'Create new Ad', 'yclads' ),
			'slug'            => _x('create','slug','oqp'),
			'parent_url'      => $yclads_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'yclads_bp_screen_create_ad',
			'user_has_access' =>  bp_is_my_profile(),
			'position'        => 30
		);

		parent::setup_nav( $main_nav, $sub_nav );

		if ( yclads_is_component() && bp_is_single_item() ) {

			unset( $main_nav ); unset( $sub_nav );

			// Add 'Ads' to the main navigation
			$main_nav = array(
				'name'                => __( 'Memberships', 'buddypress' ),
				'slug'                => $this->current_yclad->slug,
				'position'            => -1, // Do not show in BuddyBar
				'screen_function'     => 'yclads_screen_yclad_home',
				'default_subnav_slug' => 'home',
				'item_css_id'         => $this->id
			);

			$yclad_link = trailingslashit( bp_get_root_domain() . '/' . $this->root_slug . '/' . $this->current_yclad->slug );

			// Add the "Home" subnav item, as this will always be present
			$sub_nav[] = array(
				'name'            =>  _x( 'Home', 'Ad home navigation title', 'yclads' ),
				'slug'            => 'home',
				'parent_url'      => $yclad_link,
				'parent_slug'     => $this->current_yclad->slug,
				'screen_function' => 'yclads_screen_yclad_home',
				'position'        => 10,
				'item_css_id'     => 'home'
			);

			// If the user is an ad mod or more, then show the yclad admin nav item
			if ( bp_is_item_admin() || bp_is_item_mod() ) {
				$sub_nav[] = array(
					'name'            => __( 'Admin', 'buddypress' ),
					'slug'            => 'admin',
					'parent_url'      => $yclad_link,
					'parent_slug'     => $this->current_yclad->slug,
					'screen_function' => 'yclads_screen_yclad_admin',
					'position'        => 20,
					'user_has_access' => ( $bp->is_item_admin + (int)$bp->is_item_mod ),
					'item_css_id'     => 'admin'
				);
			}

			// If this is a private yclad, and the user is not a member, show a "Request Membership" nav item.
			if ( is_user_logged_in() &&
				 !is_super_admin() &&
				 !$this->current_yclad->is_user_member &&
				 !yclads_check_for_membership_request( $bp->loggedin_user->id, $this->current_yclad->id ) &&
				 $this->current_yclad->status == 'private'
				) {
				$sub_nav[] = array(
					'name'               => __( 'Request Membership', 'buddypress' ),
					'slug'               => 'request-membership',
					'parent_url'         => $yclad_link,
					'parent_slug'        => $this->current_yclad->slug,
					'screen_function'    => 'yclads_screen_yclad_request_membership',
					'position'           => 30
				);
			}

			// Forums are enabled and turned on
			if ( $this->current_yclad->enable_forum && bp_is_active( 'forums' ) ) {
				$sub_nav[] = array(
					'name'            => __( 'Forum', 'buddypress' ),
					'slug'            => 'forum',
					'parent_url'      => $yclad_link,
					'parent_slug'     => $this->current_yclad->slug,
					'screen_function' => 'yclads_screen_yclad_forum',
					'position'        => 40,
					'user_has_access' => $this->current_yclad->user_has_access,
					'item_css_id'     => 'forums'
				);
			}

			$sub_nav[] = array(
				'name'            => sprintf( __( 'Members <span>%s</span>', 'buddypress' ), number_format( $this->current_yclad->total_member_count ) ),
				'slug'            => 'members',
				'parent_url'      => $yclad_link,
				'parent_slug'     => $this->current_yclad->slug,
				'screen_function' => 'yclads_screen_yclad_members',
				'position'        => 60,
				'user_has_access' => $this->current_yclad->user_has_access,
				'item_css_id'     => 'members'
			);

			if ( bp_is_active( 'friends' ) && yclads_user_can_send_invites() ) {
				$sub_nav[] = array(
					'name'            => __( 'Send Invites', 'buddypress' ),
					'slug'            => 'send-invites',
					'parent_url'      => $yclad_link,
					'parent_slug'     => $this->current_yclad->slug,
					'screen_function' => 'yclads_screen_yclad_invite',
					'item_css_id'     => 'invite',
					'position'        => 70,
					'user_has_access' => $this->current_yclad->user_has_access
				);
			}

			parent::setup_nav( $main_nav, $sub_nav );
		}

		if ( isset( $this->current_yclad->user_has_access ) )
			do_action( 'yclads_setup_nav', $this->current_yclad->user_has_access );
		else
			do_action( 'yclads_setup_nav');
	}

	/**
	 * Set up the admin bar
	 *
	 * @global obj $bp
	 */
	function setup_admin_bar() {
		global $bp;

		// Prevent debug notices
		$wp_admin_nav = array();

		// Menus for logged in user
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables
			$user_domain = $bp->loggedin_user->domain;
			$yclads_link = trailingslashit( $user_domain . $this->slug );

			// Pending yclad invites
			$count = yclads_get_invites_for_user( $bp->loggedin_user->id );

			if ( !empty( $count->total ) ) {
				$title   = sprintf( __( 'Ads <span class="count">%s</span>',          'yclads' ), $count->total );
				$pending = sprintf( __( 'Pending Invites <span class="count">%s</span>', 'buddypress' ), $count->total );
			} else {
				$title   = __( 'Ads',             'yclads' );
				$pending = __( 'No Pending Invites', 'buddypress' );
			}

			// Add the "My Account" sub menus
			$wp_admin_nav[] = array(
				'parent' => $bp->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => trailingslashit( $yclads_link )
			);

			// My Ads
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'title'  => __( 'Memberships', 'buddypress' ),
				'href'   => trailingslashit( $yclads_link )
			);

			// Invitations
			$wp_admin_nav[] = array(
				'parent' => 'my-account-' . $this->id,
				'title'  => $pending,
				'href'   => trailingslashit( $yclads_link . 'invites' )
			);
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Sets up the title for pages and <title>
	 *
	 * @global obj $bp
	 */
	function setup_title() {
		global $bp;

		if ( yclads_is_component() ) {

			if ( bp_is_my_profile() && !bp_is_single_item() ) {

				$bp->bp_options_title = __( 'Memberships', 'buddypress' );

			} else if ( !bp_is_my_profile() && !bp_is_single_item() ) {

				$bp->bp_options_avatar = bp_core_fetch_avatar( array(
					'item_id' => $bp->displayed_user->id,
					'type'    => 'thumb'
				) );
				$bp->bp_options_title  = $bp->displayed_user->fullname;

			// We are viewing a single yclad, so set up the
			// yclad navigation menu using the $this->current_yclad global.
			} else if ( bp_is_single_item() ) {
				$bp->bp_options_title  = $this->current_yclad->name;
				$bp->bp_options_avatar = bp_core_fetch_avatar( array(
					'item_id'    => $this->current_yclad->id,
					'object'     => 'yclad',
					'type'       => 'thumb',
					'avatar_dir' => 'yclad-avatars',
					'alt'        => __( 'Ad Avatar', 'yclads' )
				) );
				if ( empty( $bp->bp_options_avatar ) )
					$bp->bp_options_avatar = '<img src="' . esc_attr( $yclad->avatar_full ) . '" class="avatar" alt="' . esc_attr( $yclad->name ) . '" />';
			}
		}

		parent::setup_title();
	}
}

function yclads_bp_is_member_component($wp_result){
	global $bp;
	if ( bp_is_current_component( 'yclads' ) ){
		return true;
	}else{
		return $wp_result;
	}
}



function yclads_bp2_init(){
	global $bp;
	$bp->yclads = new BP_Yclads_Component();
	$bp->yclads->includes(); //TODO TO FIX should be done automatically ?
	add_action( 'bp_setup_root_components', 'yclads_bp_setup_root_component' );

	add_action('bp_member_plugin_options_nav','yclads_my_ads_options_nav');

	//set IS component if we are in member's section
	add_filter('yclads_is_component','yclads_bp_is_member_component');

	
	//GROUPS extension
	bp_register_group_extension( 'Yclads_BP_Group_Extension' );
	
	//add settings link to email
	add_filter('yclads_notify_subscriber_post_publish_message','oqp_bp_email_notifications_settings_message',10,3);
	
	//
	add_action( 'bp_register_activity_actions', 'yclads_bp_register_activity_actions' );

	//user settings for notifications
	add_action( 'bp_notification_settings', 'yclads_bp_creation_screen_notification_settings');
	
	//email settings link to disable notifications
	add_filter('oqp_notification_author_follower_email_message','yclads_bp_email_notifications_settings_message',10,3);
	add_filter('notify_subscriber_post_publish_message','yclads_bp_email_notifications_settings_message',10,3);
	add_filter('yclads_email_notification_single_post_follower_message','yclads_bp_email_notifications_settings_message',10,3);
	
	//(1.5)BP admin active components
	add_filter('bp_admin_optional_components','yclads_bp_admin_optional_component');
	//(1.5)BP admin component directory page creation
	//should be moved on another hook (not in the directories) but the hook do not exists yet

	//TO CHECK
	//add_action('oqp_transition_approved','yclads_record_ad_approved_activity');
	//add_action('oqp_transition_updated','yclads_record_ad_updated_activity');
	
	//registers post type to enable comments in activity
	add_filter( 'bp_blogs_record_comment_post_types', 'yclads_bp_register_post_type_for_activity_comments' );
	
}
yclads_bp2_init();

function yclads_bp_register_post_type_for_activity_comments( $post_types ) {
      $post_types[] = 'yclad';
      return $post_types;
  }


function yclads_bp_setup_root_component() {
	bp_core_add_root_component( 'yclads' );
}


function yclads_bp_admin_optional_component($components){
	$components['yclads'] = array(
		'title'       => __( 'Classified Ads', 'yclads' ),
		'description' => __( 'Classified Ads component', 'yclads' )
	);
	return $components;
}


function yclads_bp_init_oqp_form_member(){
	echo "yclads_bp_init_oqp_form_member";

	yclads_populate_oqp_form();
}



?>
