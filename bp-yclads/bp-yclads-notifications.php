<?php
/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */
 
 
 /***EMAIL NOTIFICATIONS OPTIONS***/

function yclads_bp_creation_screen_notification_settings() {
	
	?>
	<table class="notification-settings zebra" id="yclads-notification-settings">
		<thead>
			<tr>
				<th class="icon"></th>
				<th class="title"><?php _e( 'Classified Ads', 'yclads' ) ?></th>
				<th class="yes"><?php _e( 'Yes', 'buddypress' ) ?></th>
				<th class="no"><?php _e( 'No', 'buddypress' )?></th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td></td>
				<td><?php _e( 'One of your ads is awaiting moderation', 'yclads' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_yclads_pending_post]" value="yes" <?php if ( !get_user_meta( get_current_user_id(), 'notification_yclads_pending_post', true ) || 'yes' == get_user_meta( get_current_user_id(), 'notification_yclads_pending_post', true ) ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_yclads_pending_post]" value="no" <?php if ( 'no' == get_user_meta( get_current_user_id(), 'notification_yclads_pending_post', true ) ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
			<tr>
				<td></td>
				<td><?php _e( 'One of your ads has been published', 'yclads' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_yclads_approved_post]" value="yes" <?php if ( !get_user_meta( get_current_user_id(), 'notification_yclads_approved_post', true ) || 'yes' == get_user_meta( get_current_user_id(), 'notification_yclads_approved_post', true ) ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_yclads_approved_post]" value="no" <?php if ( 'no' == get_user_meta( get_current_user_id(), 'notification_yclads_approved_post', true ) ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
			<tr>
				<td></td>
				<td><?php _e( 'One of your ads has been deleted', 'yclads' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_yclads_deleted_post]" value="yes" <?php if ( !get_user_meta( get_current_user_id(), 'notification_yclads_deleted_post', true ) || 'yes' == get_user_meta( get_current_user_id(), 'notification_yclads_deleted_post', true ) ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_yclads_deleted_post]" value="no" <?php if ( 'no' == get_user_meta( get_current_user_id(), 'notification_yclads_deleted_post', true ) ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
			<tr>
				<td></td>
				<td><?php _e( 'One of the ads you follow has been updated', 'yclads' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_yclads_follow_ad]" value="yes" <?php if ( !get_user_meta( get_current_user_id(), 'notification_yclads_follow_ad', true ) || 'yes' == get_user_meta( get_current_user_id(), 'notification_yclads_follow_ad', true ) ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_yclads_follow_ad]" value="no" <?php if ( 'no' == get_user_meta( get_current_user_id(), 'notification_yclads_follow_ad', true ) ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
			<tr>
				<td></td>
				<td><?php _e( 'One of the ads authors you follow has published a new ad', 'yclads' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_yclads_follow_author]" value="yes" <?php if ( !get_user_meta( get_current_user_id(), 'notification_yclads_follow_author', true ) || 'yes' == get_user_meta( get_current_user_id(), 'notification_yclads_follow_author', true ) ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_yclads_follow_author]" value="no" <?php if ( 'no' == get_user_meta( get_current_user_id(), 'notification_yclads_follow_author', true ) ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
			<tr>
				<td></td>
				<td><?php _e( 'A new ad matching one of your subscription has been published', 'yclads' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[yclads_email_notifications]" value="yes" <?php if ( !get_user_meta( get_current_user_id(), 'yclads_email_notifications', true ) || 'yes' == get_user_meta( get_current_user_id(), 'yclads_email_notifications', true ) ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[yclads_email_notifications]" value="no" <?php if ( 'no' == get_user_meta( get_current_user_id(), 'yclads_email_notifications', true ) ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
			<?php do_action( 'yclads_bp_creation_screen_notification_settings' ) ?>
			
		</tbody>
	</table>
<?php
}


function yclads_bp_email_notifications_settings_message($message,$ad,$user_id){
	$settings_link = bp_core_get_user_domain( $user_id ) .  BP_SETTINGS_SLUG . '/notifications/';
	$message[]= sprintf( __( 'To disable these notifications please log in and go to: %s', 'buddypress' ), $settings_link );
	return $message;
}


/***ACTIVITY***/

function yclads_bp_register_activity_actions() {
	global $bp;

	if ( !function_exists( 'bp_activity_set_action' ) )
		return false;

	bp_activity_set_action( $bp->yclads->id, 'new_ad', __( 'New ad published', 'yclads' ) );
	bp_activity_set_action( $bp->yclads->id, 'new_ad_comment', __( 'New ad comment posted', 'yclads' ) );

	do_action( 'yclads_bp_register_activity_actions' );
}

function yclads_bp_record_activity( $args = '' ) {
	global $bp;

	if ( !function_exists( 'bp_activity_add' ) )
		return false;

	/* Because blog, comment, and blog post code execution happens before anything else
	   we may need to manually instantiate the activity component globals */
	if ( !$bp->activity && function_exists('bp_activity_setup_globals') )
		bp_activity_setup_globals();

	$defaults = array(
		'user_id' => get_current_user_id(),
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => $bp->yclads->id,
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => bp_core_current_time(),
		'hide_sitewide' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	/* Remove large images and replace them with just one image thumbnail */
 	if ( function_exists( 'bp_activity_thumbnail_content_images' ) && !empty( $content ) )
		$content = bp_activity_thumbnail_content_images( $content );

	if ( !empty( $action ) )
		$action = apply_filters( 'yclads_bp_record_activity_action', $action );

	if ( !empty( $content ) )
		$content = apply_filters( 'yclads_bp_record_activity_content', bp_create_excerpt( $content ), $content );

	/* Check for an existing entry and update if one exists. */
	$id = bp_activity_get_activity_id( array(
		'user_id' => $user_id,
		'component' => $component,
		'type' => $type,
		'item_id' => $item_id,
		'secondary_item_id' => $secondary_item_id
	) );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}

function yclads_bp_delete_activity( $args = true ) {
	global $bp;

	if ( function_exists('bp_activity_delete_by_item_id') ) {
		$defaults = array(
			'item_id' => false,
			'component' => $bp->yclads->id,
			'type' => false,
			'user_id' => false,
			'secondary_item_id' => false
		);

		$params = wp_parse_args( $args, $defaults );
		extract( $params, EXTR_SKIP );

		bp_activity_delete_by_item_id( array(
			'item_id' => $item_id,
			'component' => $component,
			'type' => $type,
			'user_id' => $user_id,
			'secondary_item_id' => $secondary_item_id
		) );
	}
}

function yclads_record_ad_approved_activity($post) {
	$activity_action = sprintf( __( '%s published a new ad: %s', 'yclads' ), bp_core_get_userlink( (int)$post->post_author ), '<a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a>' );
	$activity_content = $post->post_content;

	yclads_bp_record_activity( array(
		'user_id' => (int)$post->post_author,
		'action' => apply_filters( 'yclads_bp_activity_ad_approved_action', $activity_action, &$post, get_permalink($post->ID) ),
		'content' => apply_filters( 'yclads_bp_activity_ad_approved_content', $activity_content, &$post, get_permalink($post->ID) ),
		'primary_link' => apply_filters( 'yclads_bp_activity_ad_approved_primary_link', $post->guid, get_permalink($post->ID) ),
		'type' => 'new_ad',
		'item_id' => $post->ID,
		'recorded_time' => bp_core_current_time()
	));
}


function yclads_record_ad_updated_activity($post) {
	$activity_action = sprintf( __( '%s updated the ad: %s', 'yclads' ), bp_core_get_userlink( (int)$post->post_author ), '<a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a>' );
	$activity_content = $post->post_content;

	yclads_bp_record_activity( array(
		'user_id' => (int)$post->post_author,
		'action' => apply_filters( 'yclads_bp_activity_ad_updated_action', $activity_action, &$post, get_permalink($post->ID) ),
		'content' => apply_filters( 'yclads_bp_activity_ad_updated_content', $activity_content, &$post, get_permalink($post->ID) ),
		'primary_link' => apply_filters( 'yclads_bp_activity_ad_updated_primary_link', get_permalink($post->ID), $post->ID ),
		'type' => 'new_ad',
		'item_id' => $post->ID,
		'recorded_time' => bp_core_current_time()
	));
}




/***NOTIFICATIONS***/

function yclads_bp_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;

	switch ( $action ) {
		
	}

	do_action( 'yclads_bp_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}



?>