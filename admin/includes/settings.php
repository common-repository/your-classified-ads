<?php

function yclads_admin_page(){
    global $yclads;
//some code here is from the Mike Challis's plugins
    ?>
    <div class="wrap">
        <div id="main">
            <h2>Your Classified Ads</h2>
            <?php yclads_admin_header();?>

            <?php
            ////MESSAGES///
            $notices = get_settings_errors('yclads_options');
            
            print_r($notices);
            if(!$notices){
                ?>
                <div id="message" class="updated">
                    <p><strong><?php _e('Settings updated.','yclads') ?></strong></p>
                </div>
                <?php
            }
            ?>

            <?php settings_errors('yclads_options');?>

            <form method="post" action="options.php">
                <?php settings_fields('yclads_options'); ?>
                <?php do_settings_sections('yclads_options'); ?>

                <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />

            </form>

        </div>

    </div>

    <?php
}

function yclads_admin_header(){
    ?>
    <p>
            <?php
            if ($yclads->oqp_page_id) {
                    $form_options_link = admin_url('options-general.php?page='.OQP_SLUG.'&form=yclads');
                    $form_options_tab =  '<a href="'.$form_options_link.'">'.__('Yclads form options','yclads').'</a> |';
                    echo $form_options_tab;
            }
            ?>
            <a href="<?php echo YCLADS_WORDPRESS_URL;?>changelog/" target="_blank"><?php _e('Changelog', 'yclads'); ?></a> |
            <a href="<?php echo YCLADS_WORDPRESS_URL;?>faq/" target="_blank"><?php _e('FAQ', 'yclads'); ?></a> |
            <a href="<?php echo YCLADS_WORDPRESS_URL;?>" target="_blank"><?php _e('Rate This', 'yclads'); ?></a> |
            <a href="<?php echo YCLADS_SUPPORT_URL;?>" target="_blank"><?php _e('Support', 'yclads'); ?></a> |
            <a href="<?php echo YCLADS_DONATION_URL;?>" target="_blank"><?php _e('Donate', 'yclads'); ?></a>
    </p>
    <p>
    <?php

    if (function_exists('get_transient')) {
        require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

        // First, try to access the data, check the cache.
        if (false === ($api = get_transient('yclads_info'))) {
            // The cache data doesn't exist or it's expired.


            $api = plugins_api('plugin_information', array('slug' => YCLADS_PLUGIN_NAME ));



            if ( !is_wp_error($api) ) {
                // cache isn't up to date, write this fresh information to it now to avoid the query for xx time.
                $myexpire = 60 * 15; // Cache data for 15 minutes
                set_transient('yclads_info', $api, $myexpire);
            }
        }


        if ( !is_wp_error($api) ) {
                $plugins_allowedtags = array('a' => array('href' => array(), 'title' => array(), 'target' => array()),
                                                                    'abbr' => array('title' => array()), 'acronym' => array('title' => array()),
                                                                    'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
                                                                    'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
                                                                    'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
                                                                    'img' => array('src' => array(), 'class' => array(), 'alt' => array()));
                //Sanitize HTML
                foreach ( (array)$api->sections as $section_name => $content )
                    $api->sections[$section_name] = wp_kses($content, $plugins_allowedtags);
                foreach ( array('version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug') as $key )
                    $api->$key = wp_kses($api->$key, $plugins_allowedtags);

                if ( ! empty($api->downloaded) ) {
                    echo sprintf(__('Downloaded %s times', 'yclads'),number_format_i18n($api->downloaded));
                    echo '.';
                }
    ?>
                <?php if ( ! empty($api->rating) ) : ?>
                <div class="star-holder" title="<?php echo esc_attr(sprintf(__('(Average rating based on %s ratings)', 'yclads'),number_format_i18n($api->num_ratings))); ?>">
                <div class="star star-rating" style="width: <?php echo esc_attr($api->rating) ?>px"></div>
                <div class="star star5"><img src="<?php echo YCLADS_PLUGIN_URL;?>/admin/_inc/images/star.png" alt="<?php printf(__('%d stars', 'yclads'),'5'); ?>" /></div>
                <div class="star star4"><img src="<?php echo YCLADS_PLUGIN_URL;?>/admin/_inc/images/star.png" alt="<?php printf(__('%d stars', 'yclads'),'4'); ?>" /></div>
                <div class="star star3"><img src="<?php echo YCLADS_PLUGIN_URL;?>/admin/_inc/images/star.png" alt="<?php printf(__('%d stars', 'yclads'),'3'); ?>" /></div>
                <div class="star star2"><img src="<?php echo YCLADS_PLUGIN_URL;?>/admin/_inc/images/star.png" alt="<?php printf(__('%d stars', 'yclads'),'2'); ?>" /></div>
                <div class="star star1"><img src="<?php echo YCLADS_PLUGIN_URL;?>/admin/_inc/images/star.png" alt="<?php printf(__('%d stars', 'yclads'),'1'); ?>" /></div>
                </div>
                <small><?php echo sprintf(__('(Average rating based on %s ratings)', 'yclads'),number_format_i18n($api->num_ratings)); ?> <a target="_blank" href="http://wordpress.org/extend/plugins/<?php echo $api->slug ?>/"> <?php _e('Rate This', 'ylads') ?></a></small>
                <?php endif;
    }// end if (function_exists('get_transient'
        } // if ( !is_wp_error($api)
}

function yclads_is_plugin_page() {
	if($_REQUEST['page']==YCLADS_SLUG) return true;
	if($_REQUEST['option_page']=='yclads_options') return true;
}


function yclads_settings_init(){
	global $yclads;
	//if (!yclads_is_plugin_page()) return false;
	
	$options = yclads_get_option();

	register_setting( 'yclads_options', 'yclads_options', 'yclads_options_validate' );
	
	if (!$options['donated']) {
		add_settings_section('yclads_options_donate', __('Donate','yclads'), 'yclads_section_donate_text', 'yclads_options');
		add_settings_field('donate',false, 'yclads_option_donate_text', 'yclads_options', 'yclads_options_donate');
	}

	add_settings_section('yclads_options_main', __('Main Options','yclads'), 'yclads_section_main_text', 'yclads_options');
	
	add_settings_field('oqp_page_id', __('Enable frontend posting','yclads'), 'yclads_options_oqp_text', 'yclads_options', 'yclads_options_main');

	///
	do_action('yclads_settings_init');
	///

	add_settings_section('yclads_options_system', __('System','yclads'), 'yclads_section_system_text', 'yclads_options');
	add_settings_field('reset_options', __('Reset plugin options','yclads'), 'yclads_options_reset_text', 'yclads_options', 'yclads_options_system');
	
	if ($yclads->oqp_page_id) {
		$form_options_link = admin_url('options-general.php?page='.OQP_SLUG.'&form=yclads');
		add_settings_field('reset_default_form',sprintf(__('Reset %s','oqp'),'<a href="'.$form_options_link.'">'.__('form options','yclads')).'</a>', 'yclads_options_reset_default_form_text', 'yclads_options', 'yclads_options_system');
	}
	
	add_settings_field('enable_debug', __('Enable Debug','yclads'), 'yclads_options_enable_debug_text', 'yclads_options', 'yclads_options_system');


}

function yclads_admin_menu(){
	add_submenu_page('edit.php?post_type=yclad',__('Settings'),__('Settings'), 'manage_options', YCLADS_SLUG, 'yclads_admin_page' );
}

function yclads_enqueue_admin_scripts($hook_suffix) {
	if ($hook_suffix!='yclad_page_'.YCLADS_SLUG) return false;
	
	wp_enqueue_script( 'yclads-admin', YCLADS_PLUGIN_URL . '/admin/_inc/js/yclads.js',array('jquery'), YCLADS_VERSION );
	//wp_enqueue_script('jquery-ui-tabs');
	//wp_enqueue_style( 'your-classifieds-admin-tabs', YCLADS_PLUGIN_URL . '/admin/_inc/css/jquery.ui.tabs.css' );
	wp_enqueue_style( 'yclads-admin', YCLADS_PLUGIN_URL . '/admin/_inc/css/style.css' );
}



function yclads_set_default_settings($force=false){

	$default = yclads_admin_get_default_settings();
	
	$options = yclads_get_option();
	
	if ((!$options) || ($force))
		update_option('yclads_options', $default);

	
}

function yclads_admin_right_now_count() {
        if (!post_type_exists('yclad')) {
             return;
        }
		
		$published_ads = yclads_count_posts();
		$pending_ads = yclads_count_posts(array('post_status'=>'pending'));
		
		$published_text = _n( 'Ad', 'Ads', intval($published_ads) );

        if ( current_user_can( 'edit_yclads' ) ) {
            $num = "<a href='edit.php?post_type=yclad'>$published_ads</a>";
            $text = "<a href='edit.php?post_type=yclad'>$published_text</a>";
        }
        echo '<td class="first b b-yclad">' . $num . '</td>';
        echo '<td class="t yclad">' . $text;
		
		 if ($pending_ads > 0) {
            $pending_text = _n( 'Ad Pending', 'Ads Pending', intval($pending_ads) );
            if ( current_user_can( 'edit_yclads' ) ) {
                $num = "<a href='edit.php?post_status=pending&post_type=yclad'>$pending_ads</a>";
                $text = "<a href='edit.php?post_status=pending&post_type=yclad'>$pending_text</a>";
            }
            echo ' - <small><span class="first b b-yclad">' . $num . '</span> ';
            echo '<span class="t yclad">'.$text.'</span></small>';

           
        }
		
		 echo '</td></tr>';
}

//OPTIONS | VALIDATION
function yclads_options_validate($options) {
	if ($options['reset_options']) {
		yclads_set_default_settings(true);
		$options = yclads_get_option();
		return $options;
	}

	if ($options['oqp_page_id']) {
		if (is_numeric($options['oqp_page_id'])) {
			$page_exists = get_post($options['oqp_page_id']);
		}
		if (!$page_exists) {
			$message = __('This page does not exists','yclads');
			add_settings_error('yclads_options','oqp_page_id',$message,'error');	
			unset($options['oqp_page_id']);
		}
	}
	
	
	//allow to extend options
	$options = apply_filters('yclads_validated_options',$options);

	return $options;
}

//DONATE
function yclads_section_donate_text() {
	yclads_admin_paypal_form();
}
function yclads_option_donate_text(){
	?>
	<input name="yclads_options[donated]" type="checkbox"/>
	<label for="yclads_options[donated]"><?php _e('I have donated to help contribute for the development of this plugin.', 'yclads'); ?></label>
	<?php
}

function yclads_admin_paypal_form() {
?>
<table style="background-color:#FFE991; border:none; margin: -5px 0;" width="600">
	<tr>
		<td>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
			">
			<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
			<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
			</form>
		</td>
			<td>
			<?php _e('I spend a lot of time working on this plugin.  If you use it, please make a donation !', 'yclads'); ?>
			</td>
	</tr>
</table>
<?php
}

//OPTIONS | GENERAL
function yclads_section_main_text() {
	?>
	<?php
}


//fields
function yclads_options_oqp_text() {
	global $yclads;
	echo "<input id='oqp_page_id' name='yclads_options[oqp_page_id]' type='input' size='4' value='".yclads_get_option('oqp_page_id')."'/> ".__('Frontend page ID','yclads');
	if (!class_exists( 'Oqp_Form' ) ) {
		$oqp_link=admin_url('options-general.php?page='.OQP_SLUG.'&form=0&form_template=yclads');
		$msg = sprintf(__('To enable frontend posting, you need to create %s.','yclads'),'<a href="'.$oqp_link.'">'.__('a new OQP form','yclads').'</a>');
	}else{
		$msg = sprintf(__('%s requires the %s plugin to work.  Please <a target="_blank" href="http://wordpress.org/extend/plugins/one-quick-post">install it</a> first, or <a href="plugins.php">deactivate the plugin</a>.','yclads'),'<strong>'.__('Your Classified Ads','yclads').'</strong>',__('One Quick Post','oqp'));
	}
	
	oqp_form_balloon_info($msg,'error');
	
}


//OPTIONS | SYSTEM
function yclads_section_system_text() {
	?>
	<?php
}
function yclads_options_reset_text() {
	echo "<input id='reset_options' name='yclads_options[reset_options]' type='checkbox' value='1'/>";
}
function yclads_options_reset_default_form_text() {
	echo "<input id='reset_default_form' name='yclads_options[reset_default_form]' type='checkbox' value='1'/>";
}
function yclads_options_enable_debug_text() {
	$option = yclads_get_option('enable_debug');
	if ($option) $checked=" CHECKED";

	echo "<input id='enable_debug' name='yclads_options[enable_debug]' type='checkbox' value='1'".$checked."/>";
}


?>