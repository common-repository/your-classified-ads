<?php


class Yclads_BP_Group_Extension extends BP_Group_Extension {	

	function yclads_bp_group_extension() {

		$this->name = __('Classified Ads','yclads');
		$this->slug = YCLADS_SLUG;

		$this->create_step_position = 21;
		$this->nav_item_position = 31;
		
		$this->enable_nav_item = $this->enable_nav_item();
		
		//TODO TO FIX DO NOT LOAD
		add_action('wp',array(&$this,'dependancies'));

	}
	
	function dependancies() {
		if (( bp_is_group_creation_step( $this->slug ))  || ( bp_is_group_admin_screen( $this->slug ))){
			Yclads_Widget_Search::scripts();
			Yclads_Widget_Search::styles();
		}

	}
	
	function enable_nav_item(){
		global $bp;
		if (!$this->get_group_subscription()) return false;
		return true;
	}
	
	function get_group_subscription($group_id=false){
		if (!$group_id) {
			global $bp;
			$group_id = $bp->groups->current_group->id;
		}
		return groups_get_groupmeta( $group_id, 'yclads_subscription' );
	}
	function set_group_subscription($subscription,$group_id=false){
		if (!$group_id) {
			global $bp;
			$group_id = $bp->groups->current_group->id;
		}
		
		if ($subscription) {		
			return groups_update_groupmeta( $group_id, 'yclads_subscription', $subscription );
		}else{
			return groups_delete_groupmeta( $group_id, 'yclads_subscription' );
		}
	}
	
	function subscription_message(){
		global $yclads_ssubs;
		
		?>
		<div id="message" class="info">
			<p>
			<?php _e('You can subscribe your group to an ads selection.  The ads matching the filters will be displayed in your group.','yclads');?>
			</p>
		</div>
		<?php
		
		$subscription = $this->get_group_subscription();
		$yclads_ssubs->subscription_info($subscription);
		
	}


	function create_screen() {
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
			
		$this->subscription_message();
		yclads_search_form();
			
		?>

		<p>The HTML for my creation step goes here.</p>

		<?php
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}
	
	function create_screen_save() {
		check_admin_referer( 'groups_create_save_' . $this->slug );
		$this->save_group_subscription();
	}

	function save_group_subscription() {
		global $bp;
		global $yclads_ssubs;


		$subscription=$yclads_ssubs->format_before_save($_POST);

		
		/* Save any details submitted here */
		return $this->set_group_subscription($subscription);

	}


	function edit_screen() {
		global $bp;
		global $yclads_ssubs;
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false; ?>

		<h2><?php echo esc_attr( $this->name ) ?></h2>
		
		<?php
		$this->subscription_message();
		yclads_search_form();
		?>

		<p>Edit steps here</p>
		<input type=&quot;submit&quot; name=&quot;save&quot; value=&quot;Save&quot; />

		<?php
		wp_nonce_field( 'groups_edit_save_' . $this->slug );
	}
	
	function edit_screen_save() {
		global $bp;
		if ( !isset( $_POST['save'] ) )	return false;
			
		check_admin_referer( 'groups_edit_save_' . $this->slug );
		$success = $this->save_group_subscription();
		
		if ( !$success )
			bp_core_add_message( __( 'There was an error saving, please try again', 'buddypress' ), 'error' );
		else
			bp_core_add_message( __( 'Settings saved successfully', 'buddypress' ) );

		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
		
	}

	function display() {
		global $yclads_ssubs;
		$subscription = $this->get_group_subscription();
		
		if ($subscription) $this->enable_nav_item=false;
		
		?>
		<div id="message" class="info">
			<p>
			<?php _e('This group displays classified ads matching those filters :','yclads');
			$yclads_ssubs->subscription_info($subscription);
			?>
			</p>
		</div>
		<?php
		
		
		$args = $subscription;
		unset($args['name']);
		$args['post_type']='yclad';

		oqp_loop($args,'yclads');

	}

	function widget_display() { ?>
		<div class=&quot;info-group&quot;>
			<h4><?php echo esc_attr( $this->name ) ?></h4>
			<p>
				You could display a small snippet of information from your group extension here. It will show on the group
				home screen.
			</p>
		</div>
		<?php
	}
}

?>