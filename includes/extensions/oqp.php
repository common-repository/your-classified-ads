<?php

function yclads_oqp_form_settings($settings,$post_type) {
    
        if($post_type!='yclad') return $settings;
    
	$options['name']=__('Classified Ads','yclads');
	$options['post_type']='yclad';
        $options['default']=true;
        
        $options['templates']=array('archives'=>false,'singular'=>true);

	$options['steps'][0]['name']=__('Ad Details','yclads');
	$options['steps'][0]['fields'][0]['model']='title';
	$options['steps'][0]['fields'][0]['required']=true;

	$options['steps'][0]['fields'][1]['model']='taxonomy';
	$options['steps'][0]['fields'][1]['required']=true;
	$options['steps'][0]['fields'][1]['taxonomy']='yclad_action';
	$options['steps'][0]['fields'][1]['taxonomy_args']='type=radio';

	
	$options['steps'][0]['fields'][2]['model']='taxonomy';
	$options['steps'][0]['fields'][2]['required']=true;
	$options['steps'][0]['fields'][2]['taxonomy']='yclad_category';
	$options['steps'][0]['fields'][2]['taxonomy_args']='type=radio';


	$options['steps'][1]['name']=__('Ad Settings','yclads');
	
	$options['steps'][1]['fields'][0]['model']='section';
        $options['steps'][1]['fields'][0]['name']=__('Description','yclads');
	$options['steps'][1]['fields'][0]['required']=true;
	$options['steps'][1]['fields'][1]['model']='excerpt';
	
	$options['steps'][1]['fields'][2]['model']='taxonomy';
	$options['steps'][1]['fields'][2]['taxonomy']='yclad_tag';
	
	$options['steps'][1]['fields'][3]['model']='custom';
        $options['steps'][1]['fields'][3]['name']=__('Price','yclads');
	$options['steps'][1]['fields'][3]['meta_key']='price';
	
	
	$options['steps'][2]['name']=__('Ad Pictures','yclads');
	$options['steps'][2]['fields'][0]['model']='upload';
	
	$options['steps'][3]['name']=__('Ad location','yclads');
	$options['steps'][3]['fields'][0]['model']='location';
        
	return $options;
}

///GUESS TAGS

//add a 'guess tags' links for the form creation
function yclads_oqp_render_field_tags_description($description,$field) {
	if ($field->value!='yclad_tag') return $description;
	if (Oqp_Form_Step::is_first_step()) return $description; //only if post has been saved before

	$suggest_link=oqp_get_form_action().'/?'._x('suggest-tags','slug','yclads');
	
	$guess_text='<span class="yclads_suggest_tags"><a title="'.__('Use your ad description and title to suggest tags','yclads').'" href="'.$suggest_link.'">'.__('Suggest tags','yclads').'</a></span>';

	$description.=$guess_text;
	
	return $description;
}

//gets guessed tags
function yclads_guessed_tags_list($terms,$taxonomy_slug) {
	global $oqp_form;
	if (!isset($_REQUEST[_x('suggest-tags','slug','yclads')])) return $terms;
	if ($taxonomy_slug!='yclad_tag') return $terms;
	
	$post_id = $oqp_form->current_post->ID;
	if (!$post_id) return $terms;

	$matching_tags = yclads_suggest_tags(array($oqp_form->current_post->post_title,$oqp_form->current_post->post_content));

	return $matching_tags;
}

function yclads_oqp_single_steps($steps){

	if (!yclads_is_single()) return $steps;

	foreach($steps as $stepkey=>$step){
		//DELETE AD DETAILS
		if($step->slug==_x('ad-details','slug','yclad')){
			unset($steps[$stepkey]);
		}
		//AD SETTINGS
		if($step->slug==_x('ad-settings','slug','yclad')){
			//rename
			$step->slug=_x('home','slug','yclad');
			$step->name=__('Home','yclad');
			//delete tags field
			foreach($step->fields as $field_key=>$field){
				if($field->taxonomy=='yclad_tag'){
					unset($steps[$stepkey]->fields[$field_key]);
				}
			}
			
			$steps[$stepkey]=$step;
		}
	}

	return $steps;
}

//SUBSCRIPTIONS
/*
function yclads_is_oqp_subscription_item($oqp_slug,$result,$slug=false){
	global $oqp_form;
	global $wp_query;
	
	if(!$oqp_form->author_subscription)return $result;
	$subscription_slug=$wp_query->get('yclads_subscription');

	if((!$slug)&&(($subscription_slug==$oqp_slug))){
		return true;
	}elseif(($slug==$oqp_slug)&&($subscription_slug==$oqp_slug)){
		return true;
	}
	return $result;

}
function yclads_is_oqp_subscription($result,$slug=false){
	global $wp_query;
	global $oqp_subscriptions;
	global $oqp_form;
	
	//AUTHOR
	if((!$result)&&($oqp_form->author_subscription)){
		$result = yclads_is_oqp_subscription_item($oqp_subscriptions->slug_authors,$result,$slug);
	}
	
	//FAVORITES
	if((!$result)&&($oqp_form->post_subscription)){
		$result = yclads_is_oqp_subscription_item($oqp_subscriptions->slug_fav,$result,$slug);
	}
	
	return $result;
}
function oqp_add_subscriptions_posts_count_to_yclads($count,$user_id){
	global $oqp_form;
	
	//AUTHOR
	if($oqp_form->author_subscription)
		$count+=oqp_subscriptions_authors_get_total_post_count($user_id,array('post_type'=>'yclad'));
	
	//FAVORITES
	if($oqp_form->post_subscription)
		$count+=oqp_subscriptions_favorites_get_total_post_count($user_id,array('post_type'=>'yclad'));
		
	return $count;
}
function oqp_add_subscribed_posts_to_oqp($posts_ids,$args){
	global $oqp_form;
	global $oqp_subscriptions;
	
	//AUTHOR
	if($oqp_form->author_subscription){
	
		$author_defaults['oqp_subscription']=$oqp_subscriptions->slug_authors;
		$author_args = wp_parse_args( $args, $author_defaults );
		
		$author_query = new WP_Query($author_args);
		foreach($author_query->posts as $post){
			$posts_ids[]=$post->ID;
		}
	}
	
	//FAVORITES
	if($oqp_form->post_subscription){
	
		$fav_defaults['oqp_subscription']=$oqp_subscriptions->slug_fav;
		$fav_args = wp_parse_args( $args, $fav_defaults );
		
		$fav_query = new WP_Query($fav_args);
		foreach($fav_query->posts as $post){
			$posts_ids[]=$post->ID;
		}
		
	}

	return $posts_ids;
}


*/



function yclads_before_content_hook(){
    do_action("yclads_before_content");
}
function yclads_after_content_hook(){
    do_action("yclads_after_content");
}

function oqp_yclads_is_component($is_comp){
    global $oqp_form;
    if(!is_oqp()) return $is_comp;
    if($oqp_form->post_type=='yclad')return true;
}
function oqp_yclads_is_directory($is_dir){
    global $oqp_form;
    if(!oqp_is_directory()) return $is_dir;
    if($oqp_form->post_type=='yclad')return true;
}

function yclads_oqp_directory_tabs_selected($tabs_selected){
    global $wp_query;
    
    if(!$tabs_selected['search'])return $tabs_selected;

    //ACTION
    $actions_searched = explode(',',$wp_query->get('yclad_action'));
    $actions_searched = array_filter($actions_searched);//remove null values

    if((!get_query_var('s'))&&(count($actions_searched)==1)){ //SINGLE ACTION
            $tabs_selected['yclad_action']=$actions_searched[0];
            $tabs_selected['search']=false;
    }
    return $tabs_selected;
}

function yclads_directory_tabs_action($tabs_selected){
    $actions = get_terms( 'yclad_action', $args );
    if(!$actions) return false;

    foreach ($actions as $action){
        unset($class_str,$classes);
        
        if (!yclads_get_total_post_count_for_action( $action->term_id ))continue;
            
        if ($tabs_selected['yclad_action']==$action->slug)$classes[]='selected';
        if($classes)$class_str=' class="'.implode(' ',$classes).'"';
        
        $link = oqp_get_base_link(array('yclad_action'=>$action->slug));
        $count = yclads_get_total_post_count_for_action( $action->term_id );

            
        ?>
        <li<?php echo $class_str;?> id="ads-action-<?php echo $action->slug;?>">
            <a href="<?php echo $link; ?>"><?php echo $action->name; ?><span class="count"><?php echo $count;?></span></a></li>
        <?php
        
    }
}
            
function yclads_oqp_init(){
    if (get_query_var('post_type')!='yclad')return false;
    //BREADCRUMBS
    //add breadcrumb for single AD
    add_filter('oqp_item_header_meta','yclads_single_ad_breadcrumb');
    add_filter('oqp_after_loop_item_footer','yclads_single_ad_breadcrumb');
    //link to OQP hooks
    add_action('oqp_before_content','yclads_before_content_hook');
    add_action('oqp_before_content','yclads_after_content_hook');

    //actions tabs
    add_action('oqp_directory_tabs','yclads_directory_tabs_action');
    add_filter('oqp_directory_tabs_selected','yclads_oqp_directory_tabs_selected');

    
add_filter('yclads_is_component','oqp_yclads_is_component');
add_filter('yclads_is_directory','oqp_yclads_is_directory');
    
}

function yclads_directory_search_taxonomies($wp){
    $args = array('yclad_category','yclad_tag','yclad_action');
    
    foreach($args as $arg){
       $tax_val = $wp->query_vars[$arg];

       if(!is_array($tax_val))continue;

       $wp->query_vars[$arg]=implode(',',$tax_val);

        oqp_debug($wp->query_vars[$arg],'yclads_handle_taxonomies');

    }
    
    return $wp;
    
}
          
add_action('wp','yclads_oqp_init');

//yclads default form settings
add_filter('oqp_get_default_form_settings','yclads_oqp_form_settings',10,2);


//handle taxonomies checkboxes
add_filter('parse_request','yclads_directory_search_taxonomies');

add_filter('oqp_after_loop_item_footer','yclads_single_ad_breadcrumb');


//TO FIX TO CHECK
function yclads_oqp_theme_init(){
	//CHANGE THE STEPS WHEN DISPLAYING THE SINGLE OQP ITEM !!!
        ////TO FIX
	//add_filter('oqp_form_populated_steps','yclads_oqp_single_steps');
	
	//add breadcrumbf for OQP loop

	
	//TO FIX CHECK
	add_filter('oqp_render_field_description','yclads_oqp_render_field_tags_description',9,2); 
	add_filter('oqp_get_the_terms_list', 'yclads_guessed_tags_list',9,2);
	
}

?>