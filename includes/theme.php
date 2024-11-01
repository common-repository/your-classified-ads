<?php

/**
 * Define the active theme and stylesheet paths
 */


function yclads_get_theme_file_url($filename,$filepath=false,$is_url=true) {

	if ($filepath)
		$filepath.='/';

	$url =  YCLADS_PLUGIN_URL . '/theme/'.$filepath.$filename;
	$path =  YCLADS_PLUGIN_DIR . '/theme/'.$filepath.$filename;


	if ( file_exists( $path ) ) {
		if ($is_url) {
			$located = $url;
		}else{
			$located = $path;
		}
	}

	return apply_filters('yclads_get_theme_file',$located,$filename,$filepath,$is_url);
} 



/**
 * Check if template exists in style path, then check custom plugin location
 *
 * @param array $template_names
 * @param boolean $load Auto load template if set to true
 * @return string
 */

function yclads_locate_theme_template( $template_names, $load = false ) {

	if ( !is_array( $template_names ) )
		return '';
	
	$located = '';
	foreach($template_names as $template_name) {

		// split template name at the slashes

		$shared_path = YCLADS_PLUGIN_DIR.'/theme/'. $template_name;
		

		if ( file_exists( $shared_path ) ) {
			$located = $shared_path;
			break;
		}
	
	}

	if ($load && '' != $located)
		load_template($located);

	return $located;
}

/**
 * Auto-prepend theme name and call standard locate template function
 *
 * @param array $template_names
 * @param boolean $load Auto load template if set to true
 * @return string
 */
function yclads_locate_template( $template_names, $load = false ) {

	if ( !is_array( $template_names ) )
		return '';

	$ret_arr = array();

	foreach( $template_names as $template_name ) {
		$ret_arr[] = $template_name;
	}

	return yclads_locate_theme_template( $ret_arr, $load );
}

/**
 * Use this only inside of screen functions, etc
 *
 * @param string $template
 */
function yclads_load_template( $template ) {
	yclads_locate_template( (array)$template,true );
}

/**
 * Load a template part into a template
 *
 * Makes it easy for a theme to reuse sections of code in a easy to overload way
 * for child themes.
 *
 * Includes the named template part for a theme or if a name is specified then a
 * specialised part will be included. If the theme contains no {slug}.php file
 * then no template will be included.
 *
 * For the parameter, if the file is called "{slug}-special.php" then specify
 * "special".
 */
function yclads_get_template_part( $slug, $name = null,$load=true) {
	do_action( "get_template_part{$slug}", $name );

	$templates = array();
	if ( isset($name) )
		$templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

	return yclads_locate_template($templates, $load);
}

function yclads_wp_styles() {

	if (yclads_is_component()) {
		//TO FIX use style.php instead
		//wp_enqueue_style( 'yclads', yclads_get_theme_file_url('style.php') );
		//
		wp_enqueue_style( 'yclads', yclads_get_theme_file_url('style.css') );
	}

	if (yclads_is_directory()) {
		wp_enqueue_style('jquery.collapsibleCheckboxTree', yclads_get_theme_file_url('/_inc/css/jquery.collapsibleCheckboxTree.css'));
	}

}

function yclads_page_classes($classes){
	$classes[]='yclads';
	if (defined('YCLADS_BP')) {
		$classes[]='yclads-bp';
	}else{
		$classes[]='yclads-wp';
	}
	return $classes;
}



function yclads_wp_scripts() {

    
	if 	(yclads_is_component()) {
		wp_enqueue_script( 'yclads', yclads_get_theme_file_url('yclads.js','_inc/js'),array('jquery'), YCLADS_VERSION );
	}
	if (yclads_is_directory()) {

            wp_enqueue_script('jquery.collapsibleCheckboxTree', yclads_get_theme_file_url('jquery.collapsibleCheckboxTree.js','_inc/js'),array('jquery'), '1.0.1' );
            wp_enqueue_script('yclads.collapsibleCheckboxTree', yclads_get_theme_file_url('yclads.collapsibleCheckboxTree.js','_inc/js'),array('jquery','jquery.collapsibleCheckboxTree'), YCLADS_VERSION );
            
                
	}
}


function yclads_without_bp_feed_links( $args ) {
	$defaults = array(
		/* translators: Separator between blog name and feed type in feed links */
		'separator'    => _x('&raquo;', 'feed link'),
		/* translators: 1: blog title, 2: separator (raquo) */
		'feedtitle'    => __('%1$s %2$s Classified Ads Feed'),
		/* translators: %s: blog title, 2: separator (raquo) */
		'comstitle'    => __('%1$s %2$s Comments Feed'),
	);
	
	$rss_link_args['feed']=true;

	$args = wp_parse_args( $args, $defaults );

	echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr(sprintf( $args['feedtitle'], get_bloginfo('name'), $args['separator'] )) . '" href="' . add_query_arg($rss_link_args,yclads_get_dir_link()) . "\" />\n";
//	echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr(sprintf( $args['comstitle'], get_bloginfo('name'), $args['separator'] )) . '" href="' . get_feed_link( 'comments_' . get_default_feed() ) . "\" />\n";
}

/*
//returns the content of a template file.
//needed by the custom query widget because load_template can't be run several times ?
*/

function yclads_get_template_html($file) {

	$template_names[]=$file;

	$filename = yclads_locate_template($template_names,false);

    if (is_file($filename)) {
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}

require_once( YCLADS_PLUGIN_DIR . '/theme/functions.php' );



?>