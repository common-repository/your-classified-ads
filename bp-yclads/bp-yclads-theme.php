<?php
function yclads_bp_dtheme_activity_type_tabs_setup() {
	global $bp;

	//if ( !bp_links_is_default_theme() )
		//return false;

	if ( is_user_logged_in() && yclads_count_posts( array('author'=>get_current_user_id()) ) ) {
		echo sprintf(
			'<li id="activity-links"><a href="%s" title="%s">%s</a></li>',
			bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/' . _x(YCLADS_SLUG,'slug','yclads') . '/',
			__( 'The activity of ads I created.', 'yclads' ),
			sprintf(
				__( 'My Ads (%s)', 'yclads' ),
				yclads_count_posts( array('author'=>get_current_user_id()) )
			)
		);
	}
}
function yclads_bp_dtheme_activity_filter_options_setup() {
	global $bp;

	//if ( !bp_links_is_default_theme() )
		//return false;

	echo sprintf( '<option value="%s">%s</option>', 'ad_approved', __( 'Show Ads Created', 'yclads' ) );
	echo sprintf( '<option value="%s">%s</option>', 'followed_ad', __( 'Show Ads Followed', 'yclads' ) );
}

/**
 * Filter located BP template (code snippet from MrMaz)
 *
 * @see bp_core_load_template()
 * @param string $located_template
 * @param array $template_names
 * @return string
 */

function yclads_bp_subscription_template( $located_template, $template_names ) {
	global $bp;

	// template already located, skip
	
	if ( !empty( $located_template ) )
		return $located_template;

	// only filter for our component
	
	if (yclads_is_component()) {

		$template=yclads_locate_template( $template_names );

		return $template;

	}

	return '';
}

add_action( 'bp_before_activity_type_tab_mentions', 'yclads_bp_dtheme_activity_type_tabs_setup' );
add_action( 'bp_activity_filter_options', 'yclads_bp_dtheme_activity_filter_options_setup' );
add_action( 'yclads_bp_activity_filter_options', 'yclads_bp_dtheme_activity_filter_options_setup' );
//add_action( 'bp_group_activity_filter_options', 'yclads_bp_dtheme_activity_filter_options_setup' );
//add_filter( 'bp_located_template', 'yclads_bp_subscription_template', 10, 2 );
?>