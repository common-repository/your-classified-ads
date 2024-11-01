<?php

///BREADCRUMBS

function yclads_single_ad_breadcrumb() {
	yclad_breadcrumb(false,true);
}


function yclad_breadcrumb($ad=false,$homelink=false,$separator=true) {
	echo yclad_get_breadcrumb($ad,$homelink,$separator);
}
function yclad_get_breadcrumb($post=false,$homelink=false,$separator=true,$taxonomies=array('yclad_action','yclad_category','yclad_tag')) {
	if (!$post) {
		global $post;
	}
	
	if (!$separator) {
		$separator=array('single'=>"",'multi'=>"");
	}else{
		$separator=array('single'=>" &rarr; ",'multi'=>" , ");
	}

	//for breadcrumbs; add actions as param in categories links
	add_filter('term_link', 'yclads_category_link_breadcrumb', 10,3);	
	
	//for breadcrumbs; add categories & actions as params in tags links
	add_filter('term_link', 'yclads_tag_link_breadcrumb', 10,3);
	
	$items=array();

	foreach ($taxonomies as $taxonomy) {
		unset($list);unset($list_items);
		unset($list_items_str);
		$list_items_str = strip_tags(yclad_get_terms_list($taxonomy,'',','));
		$list_items = explode(',',$list_items_str);

		//unset empty values
		foreach ($list_items as $key=>$item) {
			if (!$item) {
				unset($list_items[$key]);
			}
		}
		if (empty($list_items)) continue;

		
		if (count($list_items)==1) {
			
			$parents = yclads_get_ad_term_parents_arr( $list_items[0],'name',true,$taxonomy);
			$items = array_merge($items,$parents);

			
		}else {
			foreach($list_items as $item) {
				$item_obj = get_term_by('slug',$item,$taxonomy);
				$new_items[]= yclads_add_term_link($item_obj);
			}
			$items[]=implode($separator['multi'],$new_items);
		}
		
		
		
	}
	
	//remove the two breadcrumbs links filters
	remove_filter('term_link', 'yclads_category_link_breadcrumb', 10,3);	
	remove_filter('term_link', 'yclads_tag_link_breadcrumb', 10,3);
	
	if ($homelink) {
		$home_str='<a class="yclad-breadcrumb-home" href="'.yclads_get_dir_link().'">'.__('Ads','yclads').'</a>';
		array_unshift($items,$home_str);
	}
	
	$string=implode($separator['single'],(array)$items);

	if ($string) {
		$str = '<span class="yclads-breadcrumb">';
		$str.=$string;
		$str.='</span>';
		return apply_filters('yclad_get_breadcrumb',$str);
	}
}

function yclad_list_terms($taxonomy,$before = '', $sep = '', $after = '' ) {
	echo yclad_get_terms_list($taxonomy,$before,$sep,$after);
}
	function yclad_get_terms_list($taxonomy,$before = '', $sep = '', $after = '' ) {
		global $post;
		$list = get_the_term_list( $post->ID, $taxonomy, $before, $sep, $after );
		return apply_filters('yclad_get_'.$taxonomy.'_list',$list,$before,$sep,$after);
	}

//this function extracts the words existing as tags in the DB; from a string
function yclads_suggest_tags($str, $limit=30){

	$tags_obj = get_categories(array('taxonomy'=>'yclad_tag','style'=>false,'echo'=>false));
	foreach ($tags_obj as $tag_obj) {
		$tags[]=$tag_obj->name;
	}
	
	if (!$tags) return false;
	
	if (is_array($str))
		$str = implode(' ',$str);
		
	$str = strtolower(trim($str)); //trim+lowercase
	$str = preg_replace("#[&].{2,7}[;]#sim", " ", $str);
	$str = preg_replace("#[()�^!\"�\$%&/{(\[)\]=}?�`,;.:\-_\#'~+*]#", " ", $str);
	$str = preg_replace("#\s+#sim", " ", $str);
	$words = explode(" ", $str);
	

	$content_tags=array();

	foreach($words as $word){
		$word = trim($word);
		if(!in_array($word,(array)$tags))continue;
		$content_tags[]=$word;
	}

	$content_tags=array_unique($content_tags);

	$list = array_slice($content_tags, 0, $limit);
	return implode(', ',$list);
}

//adds the /classifieds-ads/ prefix to terms links
function yclad_term_link($termlink, $term, $taxonomy) {
	if (($taxonomy=='yclad_action') || ($taxonomy=='yclad_category') || ($taxonomy=='yclad_tag')) {
		$args[$taxonomy]=$term->slug;
		$termlink = add_query_arg($args,yclads_get_dir_link());
		return apply_filters('yclad_term_link', $termlink, $term, $taxonomy);
	}else {
		return $termlink;
	}
}

function yclads_category_link_breadcrumb($termlink, $term, $taxonomy) {
	if ($taxonomy!='yclad_category') return $termlink;
	$ad_actions = yclad_taxonomy_list('yclad_action',',');
	$params=array("yclad_action"=>$ad_actions);
	$url = add_query_arg($params,$termlink);
	return $url;
}

function yclads_tag_link_breadcrumb($termlink, $term, $taxonomy) {
	if ($taxonomy!='yclad_tag') return $termlink;
	$ad_actions = yclad_taxonomy_list('yclad_action',',');
	$ad_categories = yclad_taxonomy_list('yclad_category',',');
	$params=array("yclad_action"=>$ad_actions,"yclad_category"=>$ad_categories);
	$url = add_query_arg($params,$termlink);
	return $url;
}


/**
 * Retrieve category parents with separator.
 *
 * @since 1.2.0
 *
 * @param int $id Category ID.
 * @param bool $link Optional, default is false. Whether to format with link.
 * @param string $separator Optional, default is '/'. How to separate categories.
 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
 * @param array $visited Optional. Already linked to categories to prevent duplicates.
 * @return string
 */
function yclads_get_ad_term_parents( $value, $getby='id',$taxonomy='yclad_category',$visited = array() ) {

	$parent = get_term_by($getby,$value,$taxonomy);

	if ( is_wp_error( $parent ) )
		return $parent->term_id;


	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= yclads_get_ad_term_parents( $parent->parent,'id',$taxonomy,$visited );

	}
	$chain .= $parent->term_id.',';
	return $chain;
}
function yclads_get_ad_term_parents_arr( $value, $getby='id',$link = true, $taxonomy='yclad_category',$visited = array() ) {
	$items = yclads_get_ad_term_parents( $value, $getby,$taxonomy,$visited);
	$items = explode(',',$items);
	$breadcrumb_items=array();
	foreach ((array)$items as $key=>$item) {
		if (!$item) continue;
		$classes=array();
		$item_obj = get_term_by('id',$item,$taxonomy);

		if ($link) {
			$breadcrumb_items[$item_obj->term_id]=yclads_add_term_link($item_obj);
		}else {
			$breadcrumb_items[$item_obj->term_id]= $name;
		}

	}
	if ($breadcrumb_items) {
		return $breadcrumb_items;
	}
}

function yclads_add_term_link($term) {
	$name = $term->name;

	$link = get_term_link( $term, $term->taxonomy );

	if ( is_wp_error( $link ) )
		unset($link);

	$html='<a class="'.$term->taxonomy.'" href="' .$link. '" title="' . esc_attr( sprintf( __( "View all ads in %s","yclads"), $term->name ) ) . '">'.$name.'</a>';
	
	return $html;
}
	
	
?>