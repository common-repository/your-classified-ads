<?php
//TODO TO FIX
//UNINSTALL ROUTINES
//remove caps
//remove ptions
//remove role


class YCLADS_Admin_Post {
	function yclads_admin_posts() {

		// Admin interface init
		add_filter("manage_edit-yclad_columns", array(&$this, "edit_columns"));
		
		add_action("manage_posts_custom_column", array(&$this, "custom_columns"));

		add_filter('add_menu_classes', array(&$this, "show_pending_number"), 8);
		
		add_action('admin_head',array(&$this, 'toggle_media_buttons'));
		
		// Remove Default Meta Box (actions) for edit screen
		remove_meta_box('tagsdiv-yclad_action','yclad','normal');
		//Add Custom Meta Box (actions) for edit screen
		$actions = get_terms('yclad_action',array('hide_empty'=>false));
		if ($actions)
			add_meta_box("yclad_actions", "Action", array(&$this, "meta_box_actions"), "yclad", "side", "low");
			
	}
	/*
	Adds the pending classifieds count to the menu
	Based upon the plugin "Pending Posts Indicator" (http://www.gudlyf.com/2009/01/05/wordpress-plugin-pending-posts-indicator/);
	*/
	function show_pending_number($menu) {
	
		foreach ($menu as $key=>$menu_item) {
			if ($menu_item[0]!=__('Classified Ads','yclads')) continue;

			$num_posts = wp_count_posts( 'yclad', 'readable' );
			$status = "pending";
			$pending_count = 0;
			if ( !empty($num_posts->$status) ) {
				$pending_count = $num_posts->$status;
				// Use 'plugins' classes for now. May add specific ones to this later.
				$menu[$key][0] = sprintf(__('Classified Ads %s','yclads'), "<span class='update-plugins count-$pending_count'><span class='plugin-count'>" . number_format_i18n($pending_count) . "</span></span>" );
			}
			
		}
		return $menu;
	}
	function edit_columns($default_columns)
	{
		$my_columns = array(
			"cl_description" => "Description",
			"cl_action" => __('Type','yclads'),
			"cl_categories" => __('Categories'),
			"cl_tags" => __('Tags')
		);	
		
		$columns = wp_parse_args( $my_columns, $default_columns );
		return $columns;
	}
	
	function custom_columns($column)
	{
		global $post;
		switch ($column)
		{
			case "cl_description":
				the_excerpt();
				break;
			case "cl_action":
				 echo get_the_term_list( $post->ID, 'yclad_action','',',');
				break;
			case "cl_categories":
				echo get_the_term_list( $post->ID, 'yclad_category','',',');
				break;
			case "cl_tags":
				echo get_the_term_list( $post->ID, 'yclad_tag','',',');
				break;
		}
	}
	
	// Admin post meta contents
	function meta_box_actions()
	{
	
		global $wp_meta_boxes;
		global $post;
		
		$actions = get_terms('yclad_action',array('hide_empty'=>false));
		$post_actions_ids = yclad_taxonomy_list('yclad_action',false,$type='term_id');

		//TODO TO FIX TO CHECK
		foreach ($actions as $action) {
			
			unset($checked);
			if (in_array($action->term_id,$post_actions_ids)) $checked=' checked';
			
			?>
			<input type="radio" id="yclad_action_<?php echo $action->term_id;?>" name="tax_input[yclad_action][]" value="<?php echo $action->name;?>"<?php echo $checked;?>/><?php echo $action->name;?><br/>
			<?php
		}
	}
	
	function toggle_media_buttons() {
		global $current_screen;
		$post_type = $current_screen->post_type;
		
		//run only for YCLAD post type
		//?should be if (!is_post_type($post_type)) return;
		if ($post_type!='yclad') return;
		
		//if (!yclads_pictures_is_enabled())	
			//remove_action( "media_buttons", "media_buttons" );
	}
	
	/**
	 * Returns capabilities to be granted to an event creator.
	 *
	 * @since 0.1
	 *
	 * @return Array of capabilities for event creator.
	 */


	/**
	 * Add capabilities to arrays specified as: 
	 * Array(
	 *	'type' => Array( 'cap1', 'cap2', ... )
	 * )
	 */
	function extend_caps( $extend ) {
		global $wp_roles;

		foreach ( $extend as $type => $caps ) {
			foreach ( $caps as $cap ) {
				$wp_roles->add_cap( $type, $cap );
			}
		}
	}

	function remove_caps( $extend ) {
		global $wp_roles;

		foreach ( $extend as $type => $caps ) {
			foreach ( $caps as $cap ) {
				$wp_roles->remove_cap( $type, $cap );
			}
		}
	}
	
	/**
	 * Returns capabilities to be granted to an yclad creator.
	 *
	 * @since 0.1
	 *
	 * @return Array of capabilities for yclad creator.
	 */
	 
/*	 
'edit_post' => 'edit_yclad',
'read_post' => 'read_yclad',
'delete_post' => 'delete_yclad',
'edit_posts' => 'edit_yclads',
'edit_others_posts' => 'edit_others_yclads',
'publish_posts' => 'publish_yclads',
'read_private_posts' => 'read_private_yclads',

'delete_posts'	=> 'delete_yclads',
'delete_private_posts'	=> 'delete_private_yclads',
'delete_published_posts'	=> 'delete_published_yclads',
'delete_others_posts'	=> 'delete_others_yclads',
'edit_private_posts'	=> 'edit_private_yclads',
'edit_published_posts'	=> 'edit_published_yclads'
*/	 
	function create_role() {
		$role_caps = Array(
				'edit_yclad' => true,
				'read_yclad' => true,
				'delete_yclad' => true,
				'edit_yclads' => true,
				'edit_others_yclads' => false,
				'publish_yclads' => false,
				'read_private_yclads' => false,
				'delete_yclads' => true,
				'delete_private_yclads' => true,
				'delete_published_yclads' => false,
				'delete_others_yclads' => false,
				'edit_private_yclads' => false,
				'edit_published_yclads' => false,
				
				'manage_yclad_actions' => true,
				'edit_yclad_actions' => false,
				'delete_yclad_actions' => false,
				'assign_yclad_actions' => true,
				
				'manage_yclad_categories' => true,
				'edit_yclad_categories' => false,
				'delete_yclad_categories' => false,
				'assign_yclad_categories' => true
				
				
		); 
		return apply_filters( 'yclads_role_caps', $role_caps );
	}

	/**
	 * Returns arguments to extend user capabilities to support yclad editing.
	 *
	 * Grants capabilities according to the post capabilities,
	 * to the administrator, author, etc. However, also granting
	 * the core capabilities edit_yclad, read_yclad, delete_yclad,
	 * etc. as for some reason wp-admin tries to check against these
	 * too.
	 *
	 * @uses $wp_roles
	 *
	 * @return Array Arguments
	 */
	function get_extended_capabilities() {
		global $wp_roles;

		$extend_caps = Array(
			'administrator' => Array( 
				'edit_yclad',
				'read_yclad',
				'delete_yclad',
				'edit_yclads',
				'edit_others_yclads',
				'publish_yclads',
				'read_private_yclads',
				'delete_yclads',
				'delete_private_yclads',
				'delete_published_yclads',
				'delete_others_yclads',
				'edit_private_yclads',
				'edit_published_yclads',
				
				'manage_yclad_actions',
				'edit_yclad_actions',
				'delete_yclad_actions',
				'assign_yclad_actions',
				
				'manage_yclad_categories',
				'edit_yclad_categories',
				'delete_yclad_categories',
				'assign_yclad_categories'
			),
			'editor' => Array( 
				'edit_yclad',
				'read_yclad',
				'delete_yclad',
				'edit_yclads',
				'edit_others_yclads',
				'edit_published_yclads',
				'publish_yclads',
				'delete_yclads',
				'delete_others_yclads',
				'delete_published_yclads',
				'delete_private_yclads',
				'edit_private_yclads',
				'read_yclads',
				'read_private_yclads',
				'assign_yclad_actions',
				'assign_yclad_categories'
			),
			'author' => Array(
				'edit_yclad',
				'read_yclad',
				'delete_yclad',
				'edit_yclads',
				'edit_published_yclads',
				'publish_yclads',
				'delete_yclads',
				'delete_published_yclads',
				'delete_private_yclads',
				'edit_private_yclads',
				'read_yclads',
				'read_private_yclads',
				'assign_yclad_actions',
				'assign_yclad_categories'
			),
			'contributor' => Array(
				'edit_yclad',
				'read_yclad',
				'edit_yclads',
				'read_yclads',
				'assign_yclad_actions',
				'assign_yclad_categories'
			),
			'subscriber' => Array(
				'read_yclad',
				'read_yclads'
			)
		);
		return apply_filters( 'yclads_extend_caps', $extend_caps );
	}
	

}



class YCLADS_Admin_Action {
	function yclads_admin_action() {
		//text before the action edition form
		add_action('yclad_action_pre_add_form',array(&$this, 'pre_add_form'),10,1);
	}
	function pre_add_form($taxonomy) {
		?>
		<span style="color:red">
			<?php _e('Don\'t use nested Actions.  You don\'t need that !','yclads');?>
		</span>
		<?php
	}
}
class YCLADS_Admin_Category {
	function yclads_admin_category() {
	}
}
class YCLADS_Admin_Tag {
	function yclads_admin_tag() {
	}
}
function yclads_post_types_admin_init() {
	global $yclads; 
	
	$yclads->post_type = new YCLADS_Admin_Post();
	$yclads->taxonomy['yclad_action'] = new YCLADS_Admin_Action();
	$yclads->taxonomy['yclad_category'] = new YCLADS_Admin_Category();
	$yclads->taxonomy['yclad_tag'] = new YCLADS_Admin_Tag();
}


?>