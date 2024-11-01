<?php
/**
 * The Menu of the classifieds index
 *
 * @since 1.0.0
 */

function yclads_search_menu_actions(){
    global $wp_query;
    
    if (get_query_var('post_type')!='yclad')return false;
    
     if (!get_terms('yclad_action')) return false;
    
    //COUNT ROOT CATS
    $actions_args = array(
        'hide_empty'=>0
    );

    $actions = get_terms('yclad_action',$actions_args);
     
    do_action('yclads_search_menu_before_actions');
    
    $current_action = $wp_query->get('yclad_action');
    if($current_action)$classes[]="selected";
    if($classes)$classes_str=' class="'.implode(' ',$classes).'"';

    ?>

    <div id="yclads_actions">
        <select<?php echo $classes_str;?> id="yclad_action_select" name="yclad_action">
                <?php echo '<option value="">---</option>';
                foreach($actions as $action){
                    $selected='';
                    if($action->slug==$current_action)$selected=' selected="selected"';
                    echo '<option value="'.$action->slug.'"'.$selected.'>';
                    echo $action->name;
                    echo "</option>";
                }
               ?> 
        </select>   
    </div>
    <?php
    do_action('yclads_search_menu_after_actions');
}





function yclads_search_menu_categories_column($id,$top_level,$args=''){
    global $wp_query;
    
    if (get_query_var('post_type')!='yclad')return false;
    
    foreach($top_level as $key=>$exclude_id){
        if($exclude_id==$id) continue;
        $brothers[]=$exclude_id;
    }

    //GET CATEGORIES
    $default_args = array(
        'hide_empty'=>true,
        'show_count'=>true,
        'taxonomy'=>'yclad_category',
        'input_field_value'=>'slug',
        'echo'=>false,
        'title_li'=>false,
        'walker'=>new Oqp_Walker_Category(),
        'input_type'=>'checkbox',
        'link'=>false,
        'exclude'=>$brothers,
        'selected'=>array_filter(explode(',',$wp_query->get('yclad_category')))
    );
    

    
    $args = wp_parse_args( $args,$default_args);

    
    return apply_filters('yclads_search_menu_categories_column',wp_list_categories( $args ),$id,$top_level,$args);
    
    
}

function yclads_search_menu_categories(){
    global $wp_query;
    if (!get_terms('yclad_category')) return false;
    
    //COUNT ROOT CATS
    $categories_count_args = array(
        'hide_empty'=>0,
        'fields'=>'ids',
        'parent'=>0 //top-level
    );

    $top_level_ids = get_terms( 'yclad_category',$categories_count_args);

    $column_count=count($top_level_ids);
    
    
    ?><div id="yclads_categories"><?php
        do_action('yclads_search_menu_before_categories');


        
        $cat_args = array(
            'hide_empty'=>$categories_count_args['hide_empty']
        );

        foreach($top_level_ids as $key=>$cat_id){
            $cat_html[] = '<ul class="cat-column expandable">'.yclads_search_menu_categories_column($cat_id,$top_level_ids,$cat_args).'</ul>';
        }
            
        echo implode('',$cat_html);
        
        do_action('yclads_search_menu_after_categories');
     ?></div><?php
}


function yclads_search_menu_tags_list(){
     global $wp_query;
     
    if (get_query_var('post_type')!='yclad')return false; 
    
     
    if (!get_terms('yclad_tag')) return false;
    do_action('yclads_search_menu_before_tags_list');
    
    //GET TAGS
    $default_args = array(
        'hide_empty'=>false,
        'show_count'=>true,
        'taxonomy'=>'yclad_tag',
        'echo'=>false,
        'title_li'=>false,
        'hierarchical'=>false,
        'orderby'=>'count',
        'order'=>'DESC',
        'style'=>false,
        'input_type'=>'checkbox',
        'input_field_value'=>'slug',
        'walker'=>new Oqp_Walker_Category(),
        'link'=>false,
        'number'=>50,
        'selected'=>array_filter(explode(',',$wp_query->get('yclad_tag')))
    );



    $args = wp_parse_args( $args,$default_args);
    $list = apply_filters('yclads_search_menu_tags_list',wp_list_categories( $args ),$args);

    ?>
    <div id="yclads_tags_list">
            <?php echo $list;?>
    </div>
    <?php

    do_action('yclads_search_menu_after_tags_list');
}




function oqp_search_menu_init(){
    
    add_action('oqp_search_menu_simple','yclads_search_menu_actions',5);
    add_action('oqp_search_menu_advanced','yclads_search_menu_categories',3);
    add_action('oqp_search_menu_advanced','yclads_search_menu_tags_list',5); 

    
}




add_action('wp','oqp_search_menu_init',9);

?>
