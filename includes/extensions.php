<?php

function yclads_init_extensions(){
    //OQP
    if ( class_exists( 'Oqp_Form' ) ) {
            require_once( YCLADS_PLUGIN_DIR . '/includes/extensions/oqp.php' );
    }

}

add_action('init','yclads_init_extensions');


///////////QSUBSCRIBE/////////

function yclads_qsubscribe_available_vars($vars){
    
        global $oqp_form;
        
        if($oqp_form->post_type!='yclad') return $vars;
    
        //ACTIONS
        $vars['yclad_action']=array(
                //function used to check that the post matches the subscription; must return true or false
                'name' => __('Ad actions','query_subscribe'),
                'callback'=>'query_subscribe_post_is_matching_taxonomy',
                'fn_infos'=>'query_subscribe_format_info_text_taxonomy'
        );
        //CATEGORIES
        $vars['yclad_category']=array(
                //function used to check that the post matches the subscription; must return true or false
                'name' => __('Ad categories','query_subscribe'),
                'callback'=>'query_subscribe_post_is_matching_taxonomy',
                'fn_infos'=>'query_subscribe_format_info_text_taxonomy'
        );
        //TAGS
        $vars['yclad_tag']=array(
                'name' => __('Ad tags','query_subscribe'),
                'callback'=>'query_subscribe_post_is_matching_taxonomy',
                'fn_infos'=>'query_subscribe_format_info_text_taxonomy'
        );	

        return $vars;
}
add_filter('query_subscribe_available_vars','yclads_qsubscribe_available_vars');

?>