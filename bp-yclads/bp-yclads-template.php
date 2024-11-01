<?php

function yclads_my_ads_options_nav() {
	global $bp;
	global $wp_query;
	if ($bp->current_action!=_x('my-ads','slug','yclads')) return false;
	if (!bp_is_my_profile()) return false;
	
	//TODO TO FIX count ads for each and display only if any
	
?>
	<li style="float:right">
		<form action="" method="post" id="my-ads-subscription-status">
			<select name="post_status" id="ads-sort-by" onchange="this.form.submit()">
				<option value="publish"<?php if($_REQUEST['post_status']=='publish')echo" SELECTED";?>><?php echo _x('published','slug','yclads');?></option>
				<option value="pending"<?php if($_REQUEST['post_status']=='pending')echo" SELECTED";?>><?php echo _x('pending','slug','yclads');?></option>
				<option value="draft"<?php if($_REQUEST['post_status']=='draft')echo" SELECTED";?>><?php echo _x('draft','slug','yclads');?></option>
			</select>
		</form>
	</li>
<?php
}

function yclads_bp_get_self_link($link){
	global $bp;
	$link = $bp->loggedin_user->domain . yclads_get_root_slug() . '/' . _x('my-ads','slug','yclads');
	return apply_filters('yclads_bp_get_self_link',$link);
}

function yclads_bp_get_subscriptions_link($link){
	global $bp;
	$link = $bp->loggedin_user->domain . yclads_get_root_slug() . '/' . _x('my-subscriptions','slug','query-subscribe');
	return apply_filters('yclads_bp_get_subscriptions_link',$link);
}

function yclads_bp_get_subscription_link($link,$subscription_id){
	global $bp;
	$link = $bp->loggedin_user->domain . yclads_get_root_slug() . '/' . _x('my-subscriptions','slug','query-subscribe') . '/' . $subscription_id;
	return apply_filters('yclads_bp_get_subscription_link',$link);
}


add_filter( 'yclads_get_self_link', 'yclads_bp_get_self_link' );
add_filter( 'yclads_get_subscriptions_link', 'yclads_bp_get_subscriptions_link' );
add_filter( 'yclads_get_subscription_link', 'yclads_bp_get_subscription_link',10,2);



/**
 * Output the yclads component root slug
 *
 * @uses get_yclads_root_slug()
 */
function yclads_root_slug() {
	echo get_yclads_root_slug();
}
	/**
	 * Return the yclads component root slug
	 */
	function yclads_get_root_slug() {
		//global $bp;
		//return apply_filters( 'yclads_get_root_slug', $bp->yclads->root_slug );
		global $yclads;
		return apply_filters( 'yclads_get_root_slug', YCLADS_SLUG );
	}
	
	


function bp_yclads_directory_ads_search_form() {
	global $bp;

	$default_search_value = bp_get_search_default_text( 'yclads' );
	$search_value         = !empty( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : $default_search_value; ?>

	<form action="" method="get" id="search-ads-form">
		<label><input type="text" name="s" id="ads_search" value="<?php echo esc_attr( $search_value ) ?>"  onfocus="if (this.value == '<?php echo $default_search_value ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo $default_search_value ?>';}" /></label>
		<input type="submit" id="ads_search_submit" name="ads_search_submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
	</form>

<?php
}

	
?>