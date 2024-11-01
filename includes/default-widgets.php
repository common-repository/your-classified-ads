<?php
/*

function yclads_search_form() {
	global $wp_query;
        ?>

	<?php


}

function yclads_get_search_form_content(){
	?>
	<?php
}

 
class Yclads_Widget_Search extends WP_Widget {

    function yclads_widget_search() {
		$widget_ops = array('description' => __('Search form for classified ads', 'yclads'));
        parent::WP_Widget('yclads_widget_search', $name = __('Classified Ads Search','yclads'),$widget_ops);	
		
		if ( is_active_widget( false, false, $this->id_base ) ) {
			//LOAD SCRIPT & STYLES
			//check not loaded in admin
			//must be the sames args than in OQP plugin (not load them twice)
			$this->scripts;
			$this->styles;
		}
		
    }

    function widget($args, $instance) {	
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);   
		
		global $wpdb;
		global $classifieds_cats_arr;

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		self::output();
		echo $after_widget;
    }

} 


class Yclads_Widget_Terms_Cloud extends WP_Widget {

	function yclads_widget_terms_cloud() {
		$widget_ops = array( 'description' => __( "Your most used terms in cloud format") );
		$this->WP_Widget('yclads_terms_cloud', __('Classified Ads Terms Cloud','yclads'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$current_taxonomy = $this->_get_current_taxonomy($instance);
		if ( !empty($instance['title']) ) {
			$title = $instance['title'];
		} else {
			if ( 'yclad_tag' == $current_taxonomy ) {
				$title = __('Classified Tags','yclads');
			} else {
				$tax = get_taxonomy($current_taxonomy);
				$title = $tax->label;
			}
		}
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		self::output(array('taxonomy' => $current_taxonomy));

		echo $after_widget;
	}
	
	function output($args) {
		echo '<div class="'.$args['taxonomy'].'">';
		//TODO TO FIX : exclude terms from not published posts ?
		wp_tag_cloud( apply_filters('widget_tag_cloud_args', $args ) );
		echo "</div>\n";
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['taxonomy'] = stripslashes($new_instance['taxonomy']);
		return $instance;
	}

	function form( $instance ) {
		$current_taxonomy = $this->_get_current_taxonomy($instance);
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
	<p><label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Taxonomy:') ?></label>
	<select class="widefat" id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>">
	<?php foreach ( get_object_taxonomies('yclad') as $taxonomy ) :
				$tax = get_taxonomy($taxonomy);
				if ( !$tax->show_tagcloud || empty($tax->label) )
					continue;
	?>
		<option value="<?php echo esc_attr($taxonomy) ?>" <?php selected($taxonomy, $current_taxonomy) ?>><?php echo $tax->label ?></option>
	<?php endforeach; ?>
	</select></p><?php
	}

	function _get_current_taxonomy($instance) {
		if ( !empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy']) )
			return $instance['taxonomy'];

		return 'post_tag';
	}
}

add_action('widgets_init', create_function('', 'return register_widget("Yclads_Widget_Terms_Cloud");'));


class Yclads_Widget_Custom_Query extends WP_Widget {

	var $opts=array(
		'query_args'=>'post_type=yclad&posts_per_page=5',
		'template_suffix'=>'index'
	);

	function yclads_widget_custom_query() {
		$widget_ops = array( 'description' => __( "Fetch classifieds ads with custom query args","yclads") );
		$this->WP_Widget('yclads_custom_query', __('Classifieds Ads Custom Query','yclads'), $widget_ops);
	}
	
	function output($query_args=false,$template_suffix=false) {
		global $widgetPosts;
		if (!$query_args)
			$query_args=$this->opts['query_args'];
			

		parse_str($query_args, $arr_args);

		//if -1, get single post author
		if ($arr_args['author']==-1) {
			global $post;
			$arr_args['author']=$post->post_author;
		}
		
		
		$query_args=http_build_query($arr_args);

			
		$query_args=apply_filters('yclads_widget_custom_query_args',$query_args);
			
		$template='loop-widget';
			
		if ($template_suffix)
			$template.='-'.$template_suffix;

		$widgetPosts = new WP_Query();
		$widgetPosts->query($query_args);
		
		echo yclads_get_template_html($template.'.php');
		
		wp_reset_query();
	}
	
	function WigetPosts(){
		global $widgetPosts;
		return $widgetPosts;
	}

	function widget( $args, $instance ) {
		extract($args);

		if ( !empty($instance['title']) )
			$title = $instance['title'];
			
		if ( !empty($instance['template_suffix']) )
			$template_suffix = $instance['template_suffix'];
			

			
			
		if ( !empty($instance['query_args']) )
			$query_args = $instance['query_args'];
			
		if (!$title) {
			$title = __('Classified Ads','yclads');
		}

		$title = apply_filters('widget_title', $title, $instance, $this->id_base);


		echo $before_widget;
		
		if ( $title )
			echo $before_title . $title . $after_title;
			
			self::output($query_args,$template_suffix);

		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['query_args'] = strip_tags(stripslashes($new_instance['query_args']));
		$instance['template_suffix'] = strip_tags(stripslashes($new_instance['template_suffix']));

		return $instance;
	}
	
	function form( $instance ) {
	
		if (!$instance['query_args']) 
			$instance['query_args']=$this->opts['query_args'];

	
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('query_args'); ?>"><?php _e('Query args','yclads') ?>:</label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('query_args'); ?>" name="<?php echo $this->get_field_name('query_args'); ?>" value="<?php if (isset ( $instance['query_args'])) {echo esc_attr( $instance['query_args'] );} ?>" /></p>
		<small><?php _e('Custom taxonomies you can use:','yclads');?></small>
		<ul>
		<li><code>yclad_action=<em>slug</em></code></li>
		<li><code>yclad_category=<em>slug</em> <strike><strong>category</strong></strike></code></li>
		<li><code>yclad_tag=<em>slug</em> <strike><strong>post_tag</strong></strike></code></li>
		<li><small><?php printf(__('Other args you can use : %s','yclads'),'<a href="http://codex.wordpress.org/Template_Tags/query_posts#Parameters" target="_blank">query_posts parameters</a>');?></strike></small></li>
		</ul>

		
		<p><label for="<?php echo $this->get_field_id('template_suffix'); ?>"><?php _e('Template Suffix') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('template_suffix'); ?>" name="<?php echo $this->get_field_name('template_suffix'); ?>" value="<?php if (isset ( $instance['template_suffix'])) {echo esc_attr( $instance['template_suffix'] );} ?>" />
		<br><small><?php printf(__('If you want a custom template for this widget, create a new template file - eg. %s, then put the suffix (eg. %s) here.','yclads'),'<strong>loop-widget-<em>custom</em>.php</strong>','<em>custom</em>');?></small>
		</p>
		<?php
	}


}

add_action('widgets_init', create_function('', 'return register_widget("Yclads_Widget_Custom_Query");'));
*/
?>