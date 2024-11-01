<?php
//TO DO remove edit cap

class YCLADS_Model_Yclad_Post {

	function yclads_model_yclad_post() {

		register_post_type( 'yclad', $this->register_type_args() );

		add_filter( 'map_meta_cap', Array( &$this, 'map_meta_cap' ), 11, 4 );

		// Insert post hook
		//add_action("wp_insert_post", array(&$this, "wp_insert_post"), 10, 2);
		

		
		
		//add_filter('pub_priv_sql_capability', array(&$this, "view_cap"));

	}
	//http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
	function map_meta_cap( $caps, $cap, $user_id, $args ) {
		/* If editing, deleting, or reading a yclad, get the post and post type object. */
		if ( 'edit_yclad' == $cap || 'delete_yclad' == $cap || 'read_yclad' == $cap ) {
			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );

			/* Set an empty array for the caps. */
			$caps = array();
		}

		/* If editing a yclad, assign the required capability. */
		if ( 'edit_yclad' == $cap ) {
			
			if ( $user_id == $post->post_author )
				$caps[] = $post_type->cap->edit_posts;
			else {
				$caps[] = $post_type->cap->edit_others_posts;
			}
		}

		/* If deleting a yclad, assign the required capability. */
		elseif ( 'delete_yclad' == $cap ) {
			if ( $user_id == $post->post_author )
				$caps[] = $post_type->cap->delete_posts;
			else
				$caps[] = $post_type->cap->delete_others_posts;
		}

		/* If reading a private yclad, assign the required capability. */
		elseif ( 'read_yclad' == $cap ) {

			if ( 'private' != $post->post_status )
				$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
				$caps[] = 'read';
			else
				$caps[] = $post_type->cap->read_private_posts;
		}

		/* Return the capabilities required by the user. */
		return apply_filters('yclads_meta_caps',$caps);
	}
	

	
	/**
	 * Returns arguments for registering the post type.
	 *
	 * @since 0.1
	 *
	 * @return Array arguments
	 */
	function register_type_args() {
		$labels = Array(
			'name' 			=> __( 'Ads', 'yclad' ),
			'singular_name' 	=> __( 'Ad' , 'yclad' ),
			'add_new' 		=> _x( 'Create New', 'Ad' , 'yclad' ),
			'add_new_item' 		=> __( 'Create New Ad' , 'yclad' ),
			'edit_item' 		=> __( 'Edit Ad' , 'yclad' ),
			'edit' 			=> _x( 'Edit', 'Ad' , 'yclad' ),
			'new_item' 		=> __( 'New Ad' , 'yclad' ),
			'view_item' 		=> __( 'View Ads' , 'yclad' ),
			'search_items' 		=> __( 'Search Ads' , 'yclad' ),
			'not_found' 		=> __( 'No ads found' , 'yclad' ),
			'not_found_in_trash' 	=> __( 'No ads found in trash' , 'yclad' )
		);

		$supports = Array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions', 'comments', 'trackbacks', 'custom-fields' );

		$taxonomies = Array( 'yclad_action', 'yclad_tag', 'yclad_category' );
		
		$capabilities = array(
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
			
		);


		$args = Array(
			'labels'		=> $labels,
			//'description' 		=> '__( 'Classified Ads','yclads'),
			'public' 		=> true,
			'show_ui'		=> true,
			'capabilities'		=> $capabilities,
			'capability_type'	=> 'yclad',
			'supports'		=> $supports,
			'menu_position' 	=> 30,
			'taxonomies'		=> $taxonomies,
			'rewrite'		=> Array( 'slug' => YCLADS_SLUG, 'with_front' => false ),
			'has_archive' 		=> true
		);

		return $args;
	}

	// When a post is inserted or updated
	/*
	function wp_insert_post($post_id, $post = null)
	{
		if ($post->post_type == "yclad")
		{
			//TO CHECK
			$yclad_actions=$_POST['yclad-actions'];

			if ($yclad_actions)
				$_POST['tax_input']['yclad_action']=implode(',',$yclad_actions);
		}
	}
	*/
	



}

class YCLADS_Model_Yclad_Action {
	function yclads_model_yclad_action() {
		register_taxonomy( 'yclad_action', 'yclad', $this->register_type_args());
	}
	
	function register_type_args() {
		$labels = Array(
			'name'			=> _x( 'Ads Actions', 'Taxonomy General Name' ),
			'singular_name'		=> _x( 'Ads Actions', 'Taxonomy Singular Name' ),
			'search_items'		=> __( 'Search Ads Actions' ),
			'popular_items'		=> __( 'Popular Ads Actions' ),
			'all_items'		=> __( 'All Ads Actions' ),
			'edit_item'		=> __( 'Edit Ad Action' ),
			'update_item'		=> __( 'Update Ad Action' ),
			'add_new_item'		=> __( 'Add New Ad Action' ),
			'new_item_name'		=> __( 'New Ad Action Name' )
		);
		
		$capabilities = array(
			'manage_terms' => 'manage_yclad_actions',
			'edit_terms' => 'edit_yclad_actions',
			'delete_terms' => 'delete_yclad_actions',
			'assign_terms' => 'assign_yclad_actions'
		);
		
		$args = Array(
			'labels'		=> $labels,
			'show_ui'		=> true,
			'public'		=> true,
			'show_tagcloud'		=> false,
			'hierarchical'		=> true,
			'capabilities'		=> $capabilities,
			'rewrite' => false
		);

		$args = apply_filters( 'yclads_yclad_action_type_args', $args );
		return $args;
	}
	
}

class YCLADS_Model_Yclad_Category {
	function yclads_model_yclad_category() {
		register_taxonomy( 'yclad_category', 'yclad', $this->register_type_args());
	}
	
	function register_type_args() {
		$labels = Array(
			'name'			=> _x( 'Ads Categories', 'Taxonomy General Name' ),
			'singular_name'		=> _x( 'Ads Categories', 'Taxonomy Singular Name' ),
			'search_items'		=> __( 'Search Ads Categories' ),
			'popular_items'		=> __( 'Popular Ads Categories' ),
			'all_items'		=> __( 'All Ads Categories' ),
			'edit_item'		=> __( 'Edit Ad Category' ),
			'update_item'		=> __( 'Update Ad Category' ),
			'add_new_item'		=> __( 'Add New Ad Category' ),
			'new_item_name'		=> __( 'New Ad Category Name' )
		);
		
		$capabilities = array(
			'manage_terms' => 'manage_yclad_categories',
			'edit_terms' => 'edit_yclad_categories',
			'delete_terms' => 'delete_yclad_categories',
			'assign_terms' => 'assign_yclad_categories'
		);
		
		$args = Array(
			'labels'		=> $labels,
			'show_ui'		=> true,
			'public'		=> true,
			'show_tagcloud'		=> false,
			'hierarchical'		=> true,
			'capabilities'		=> $capabilities,
			'rewrite' => false
		);

		$args = apply_filters( 'yclads_yclad_category_type_args', $args );
		return $args;
	}
	
}

class YCLADS_Model_Yclad_Tag {
	function yclads_model_yclad_tag() {
		register_taxonomy( 'yclad_tag', 'yclad', $this->register_type_args());
	}
	
	function register_type_args() {
		$labels = Array(
			'name'			=> _x( 'Ads Tags', 'Taxonomy General Name' ),
			'singular_name'		=> _x( 'Ads Tags', 'Taxonomy Singular Name' ),
			'search_items'		=> __( 'Search Ads Tags' ),
			'popular_items'		=> __( 'Popular Ads Tags' ),
			'all_items'		=> __( 'All Ads Tags' ),
			'edit_item'		=> __( 'Edit Ad Tag' ),
			'update_item'		=> __( 'Update Ad Tag' ),
			'add_new_item'		=> __( 'Add New Ad Tag' ),
			'new_item_name'		=> __( 'New Ad Tag Name' )
		);
		$args = Array(
			'labels'		=> $labels,
			'show_ui'		=> true,
			'public'		=> true,
			'show_tagcloud'		=> false,
			'hierarchical'		=> false,
			'rewrite' => false
		);

		$args = apply_filters( 'yclads_yclad_tag_type_args', $args );
		return $args;
	}
	
}

function yclads_post_types_init() {
	global $yclads; 
	
	$yclads->post_type = new YCLADS_Model_Yclad_Post();
	$yclads->taxonomy['yclad_action'] = new YCLADS_Model_Yclad_Action();
	$yclads->taxonomy['yclad_category'] = new YCLADS_Model_Yclad_Category();
	$yclads->taxonomy['yclad_tag'] = new YCLADS_Model_Yclad_Tag();
}
// Initiate the class
add_action("init", "yclads_post_types_init",8);

?>