<?php
/*
Plugin Name: Blocksolid Gateway
Plugin URI: https://www.peripatus.uk/blocksolid-gateway
Description: Gated content based upon a Members Only flag for pages, posts and categories with Gutenberg support.
Version: 1.0.6
Author: Peripatus Web Design
Author URI: https://www.peripatus.uk
License: GPLv2 or later
Text Domain: blocksolid-gateway
*/

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'blocksolid_gateway_add_plugin_page_settings_link');
function blocksolid_gateway_add_plugin_page_settings_link( $links ) {
    $links[] = '<a href="' .
        admin_url( 'options-general.php?page=blocksolid-gateway.php' ) .
        '">' . __('Settings') . '</a>';
    return $links;
}

if ((!(is_admin())) && (!(wp_doing_ajax()))){
	add_action( 'wp', 'blocksolid_gateway_check_access' );
}

function blocksolid_gateway_check_access() {

	$members_only = false;

	if (is_category()) {

		$category = get_queried_object();

		// Get all the ancestors of the categories
		$term_ids = get_ancestors( $category->term_id, 'category');

		// Add term id of category itself
		$term_ids[] = $category->term_id;

		foreach ($term_ids AS $term_id){
			if (get_term_meta($term_id, '_blocksolid_gateway_members_only', true)){
				$members_only = true;
			}
		}

	}else{

		global $post;

		if (!($post)){
			return;
		}

		if (is_single()) {

			$cats = wp_get_post_categories( $post->ID, array( 'fields' => 'ids' ) );

			$term_ids = array();

			foreach ($cats AS $cat_term_id){

				// Get all the ancestors of the categories
				$cat_term_ids = get_ancestors( $cat_term_id, 'category');

				// Add term id of category itself
				$cat_term_ids[] = $cat_term_id;

				$term_ids = array_merge($term_ids,$cat_term_ids);

			}

			foreach ($term_ids AS $term_id){
				if (get_term_meta($term_id, '_blocksolid_gateway_members_only', true)){
					$members_only = true;
				}
			}

		}

		$post_ancestors = get_post_ancestors( $post->ID );
		$post_ids_to_check = array($post->ID);
		$post_ids_to_check = array_merge($post_ids_to_check,$post_ancestors);

		foreach ($post_ids_to_check AS $post_id){
			if (get_post_meta($post_id, '_blocksolid_gateway_members_only', true)){
				if ($_SERVER['REQUEST_URI'] != "/events/"){
					$members_only = true;
				}
			}
		}

	}

	if ($members_only){

		$blocksolid_gateway_options = get_option( 'blocksolid_gateway_options' );

		// Check if "Show Page Impression" is set in settings and if not hide the content completely
		$blocksolid_show_page_impression = false;

		if (is_array($blocksolid_gateway_options)){
			if (count($blocksolid_gateway_options)){
				if (isset($blocksolid_gateway_options['blocksolid_gateway_field_impression'])){
					$blocksolid_show_page_impression = true;
				}
				if (isset($blocksolid_gateway_options['blocksolid_gateway_field_show_lost_password_link'])){
					add_action( 'login_form_middle', 'blocksolid_gateway_add_lost_password_link' );
				}
			}
		}

		if (!($blocksolid_show_page_impression)){
			if ( ! is_user_logged_in() ) {
				add_filter('the_content', 'blocksolid_gateway_remove_content', 99);
			}else{
				add_filter('the_content', 'blocksolid_gateway_show_content', 99);
			}
		}else{
			add_filter('the_content', 'blocksolid_gateway_show_content', 99);
		}

		echo blocksolid_gateway_get_gateway_panel();

	}
}

function blocksolid_gateway_show_content ($text){
	return $text;
}

function blocksolid_gateway_remove_content ($text){
    return "";
}

function blocksolid_gateway_add_lost_password_link() {

	$blocksolid_gateway_options = get_option( 'blocksolid_gateway_options' );

	$login_box_get_password_message = "Lost your password?";

	if (is_array($blocksolid_gateway_options)){
		if (count($blocksolid_gateway_options)){
			if (isset($blocksolid_gateway_options['blocksolid_gateway_field_login_box_get_password_message'])){
				if ($blocksolid_gateway_options['blocksolid_gateway_field_login_box_get_password_message'] != ""){
					$login_box_get_password_message = esc_html($blocksolid_gateway_options['blocksolid_gateway_field_login_box_get_password_message']);
				}
			}
		}
	}

	return '<p><a href="/wp-login.php?action=lostpassword">'.$login_box_get_password_message.'</a></p>';
}

function blocksolid_gateway_get_gateway_panel() {

	$blocksolid_gateway_options = get_option( 'blocksolid_gateway_options' );

    $user = wp_get_current_user();
    $allowed_roles = array('editor', 'administrator', 'author', 'member');

	$gateway_panel = "";

    if ((!(array_intersect($allowed_roles, $user->roles )))) {
		$gateway_panel .= '<script type="text/javascript">';
		$gateway_panel .= "
	     window.addEventListener('DOMContentLoaded', function(){
	        document.querySelector('body').classList.add('locked');
	     });
		 window.addEventListener('contextmenu', event => event.preventDefault());
		";
		$gateway_panel .= '</script>';
		$gateway_panel .= '<div id="blocksolid_gateway_login_message_modal"><div id="blocksolid_gateway_login_message">
		<div id="loginform">';

		$login_box_username_label = "User Name";

		if (is_array($blocksolid_gateway_options)){
			if (count($blocksolid_gateway_options)){
				if (isset($blocksolid_gateway_options['blocksolid_gateway_field_login_box_title'])){
					if ($blocksolid_gateway_options['blocksolid_gateway_field_login_box_title'] != ""){
						$gateway_panel .= '<p class="blocksolid_gateway_login_message_title"><strong>'.esc_html($blocksolid_gateway_options['blocksolid_gateway_field_login_box_title']).'</strong></p>';
					}
				}
				if (isset($blocksolid_gateway_options['blocksolid_gateway_field_login_box_username_label'])){
					if ($blocksolid_gateway_options['blocksolid_gateway_field_login_box_username_label'] != ""){
						$login_box_username_label = esc_html($blocksolid_gateway_options['blocksolid_gateway_field_login_box_username_label']);
					}
				}
			}
		}

        if ( ! is_user_logged_in() ) { // Display WordPress login form:
            $args = array(
                'echo' => false,
                'form_id' => 'loginform-custom',
                'label_username' => __( $login_box_username_label ),
                'label_password' => __( 'Password' ),
                'label_remember' => __( 'Remember Me' ),
                'label_log_in' => __( 'Log In' ),
                'remember' => true
            );
            $gateway_panel .= wp_login_form( $args );
        }

		if (is_array($blocksolid_gateway_options)){
			if (count($blocksolid_gateway_options)){

				if (isset($blocksolid_gateway_options['blocksolid_gateway_field_back_button'])){
					$gateway_panel .= '<button onclick="history.back()">< Go Back</button> ';
				}
				if (isset($blocksolid_gateway_options['blocksolid_gateway_field_join_button'])){
					$gateway_panel .= '<button ';
					if (isset($blocksolid_gateway_options['blocksolid_gateway_field_join_link'])){
						if (($blocksolid_gateway_options['blocksolid_gateway_field_join_link'] != "")){
							$gateway_panel .= 'onclick="location.href=\''.esc_js($blocksolid_gateway_options['blocksolid_gateway_field_join_link']).'\'';
						}
					}
					$gateway_panel .= '">';
					if (isset($blocksolid_gateway_options['blocksolid_gateway_field_join_label'])){
						if (($blocksolid_gateway_options['blocksolid_gateway_field_join_label'] != "")){
							$gateway_panel .= esc_html($blocksolid_gateway_options['blocksolid_gateway_field_join_label']);
						}else{
							$gateway_panel .= '&nbsp;Join&nbsp;';
				   		}
					}else{
						$gateway_panel .= '&nbsp;Join&nbsp;';
					}
					$gateway_panel .= '</button>';
				}

			}
		}

		$gateway_panel .= '
		<div style="clear: both;"></div>
		</div>
		</div></div>';

	}

	return $gateway_panel;

}

function blocksolid_gateway_add_jquery() {
    wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'blocksolid_gateway_add_jquery' );

add_action( 'wp_enqueue_scripts', 'blocksolid_gateway_enqueue_scripts' ); //Blocksolid Gateway Script setup (site only not admin)

function blocksolid_gateway_enqueue_scripts() {

	wp_enqueue_style( 'blocksolid-gateway', plugins_url( '/css/blocksolid-gateway.css', __FILE__ ), array(), filemtime(plugin_dir_path(__FILE__).'/css/blocksolid-gateway.css'));

	wp_enqueue_script( 'blocksolid-gateway', plugins_url( '/js/blocksolid-gateway.js', __FILE__ ), array('jquery'), filemtime(plugin_dir_path(__FILE__).'/js/blocksolid-gateway.js'), true );

}

// ---------------------------------------------------------------------------------------------------------------------------------------------

register_activation_hook( __FILE__, 'blocksolid_gateway_create_custom_user_role_member' );

function blocksolid_gateway_create_custom_user_role_member(){

    add_role(
        'member',          // System name of the role.
        __( 'Member'  ),   // Display name of the role.
        array(
            'read'  => true,
            'delete_posts'  => false,
            'delete_published_posts' => false,
            'edit_posts'   => false,
            'publish_posts' => false,
            'upload_files'  => false,
            'edit_pages'  => false,
            'edit_published_pages'  =>  false,
            'publish_pages'  => false,
            'delete_published_pages' => false,
        )
    );

    $member = get_role('member');
    $member->add_cap('level_1');

}

// ---------------------------------------------------------------------------------------------------------------------------------------------

// Send members somewhere other than dashboard when they log in

add_action('admin_init', 'blocksolid_gateway_redirect_dashboard');

function blocksolid_gateway_redirect_dashboard() {

    $user = wp_get_current_user();
    $allowed_roles = array('editor', 'administrator', 'author');
    if ((!(array_intersect($allowed_roles, $user->roles ))) && is_admin() && !wp_doing_ajax()) {
        if ( wp_get_referer() ) {
            wp_safe_redirect( wp_get_referer() );
        } else {
            wp_safe_redirect( get_home_url() );
        }
        exit;
    }

}

// ---------------------------------------------------------------------------------------------------------------------------------------------

/**
 * Check if Block Editor is active.
 * Must only be used after plugins_loaded action is fired.
 *
 * @return bool
 */
function blocksolid_gateway_is_gutenberg_active() {
    // Gutenberg plugin is installed and activated.
    $gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );

    // Block editor since 5.0.
    $block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );

    if ( ! $gutenberg && ! $block_editor ) {
        return false;
    }

    if ( blocksolid_gateway_is_classic_editor_plugin_active() ) {
        $editor_option       = get_option( 'classic-editor-replace' );
        $block_editor_active = array( 'no-replace', 'block' );

        return in_array( $editor_option, $block_editor_active, true );
    }

    return true;
}

/**
 * Check if Classic Editor plugin is active.
 *
 * @return bool
 */
function blocksolid_gateway_is_classic_editor_plugin_active() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
        return true;
    }

    return false;
}

// ---------------------------------------------------------------------------------------------------------------------------------------------

// Admin setup - https://developer.wordpress.org/plugins/settings/custom-settings-page/

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function blocksolid_gateway_settings_init() {

	$show_blocksolid_gateway_settings = true;

	if (is_multisite()){
		if ( !(current_user_can( 'setup_network' ) )) {
			$show_blocksolid_gateway_settings = false;
		}
	}

	if ($show_blocksolid_gateway_settings){

		 // register a new setting for "blocksolid_gateway_" page
		 register_setting( 'blocksolid_gateway_', 'blocksolid_gateway_options' );

		 // register a new section in the "blocksolid_gateway_" page
		 add_settings_section(
		 'blocksolid_gateway_section_developers',
		 __( '', 'blocksolid_gateway_' ),
		 'blocksolid_gateway_section_developers_cb',
		 'blocksolid_gateway_'
		 );

		$settings_field_array = array('reset_id' => 'blocksolid_gateway_field_impression', 'label_for' => 'blocksolid_gateway_field_impression', 'class' => 'blocksolid_gateway_row first', 'blocksolid_gateway_custom_data' => 'custom_impression',); //Mod to below code to allow older PHP
		 // register a new field in the "blocksolid_gateway_section_developers" section, inside the "blocksolid_gateway_" page
		 add_settings_field(
		 'blocksolid_gateway_field_impression', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Show an impression of page under the login box', 'blocksolid_gateway_' ),'blocksolid_gateway_field_impression_cb','blocksolid_gateway_','blocksolid_gateway_section_developers',$settings_field_array);

		$settings_field_array = array('reset_id' => 'blocksolid_gateway_field_login_box_title', 'label_for' => 'blocksolid_gateway_field_login_box_title', 'class' => 'blocksolid_gateway_row first', 'blocksolid_gateway_custom_data' => 'custom_login_box_title',); //Mod to below code to allow older PHP
		 // register a new field in the "blocksolid_gateway_section_developers" section, inside the "blocksolid_gateway_" page
		 add_settings_field(
		 'blocksolid_gateway_field_login_box_title', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Login box title', 'blocksolid_gateway_' ),'blocksolid_gateway_field_login_box_title_cb','blocksolid_gateway_','blocksolid_gateway_section_developers',$settings_field_array);

		$settings_field_array = array('reset_id' => 'blocksolid_gateway_field_login_box_username_label', 'label_for' => 'blocksolid_gateway_field_login_box_username_label', 'class' => 'blocksolid_gateway_row first', 'blocksolid_gateway_custom_data' => 'custom_login_box_username_label',); //Mod to below code to allow older PHP
		 // register a new field in the "blocksolid_gateway_section_developers" section, inside the "blocksolid_gateway_" page
		 add_settings_field(
		 'blocksolid_gateway_field_login_box_username_label', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Login box username label', 'blocksolid_gateway_' ),'blocksolid_gateway_field_login_box_username_label_cb','blocksolid_gateway_','blocksolid_gateway_section_developers',$settings_field_array);

		$settings_field_array = array('reset_id' => 'blocksolid_gateway_field_back_button', 'label_for' => 'blocksolid_gateway_field_back_button', 'class' => 'blocksolid_gateway_row first', 'blocksolid_gateway_custom_data' => 'custom_back_button',); //Mod to below code to allow older PHP
		 // register a new field in the "blocksolid_gateway_section_developers" section, inside the "blocksolid_gateway_" page
		 add_settings_field(
		 'blocksolid_gateway_field_back_button', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Show Back button', 'blocksolid_gateway_' ),'blocksolid_gateway_field_back_button_cb','blocksolid_gateway_','blocksolid_gateway_section_developers',$settings_field_array);

		$settings_field_array = array('reset_id' => 'blocksolid_gateway_field_join_button', 'label_for' => 'blocksolid_gateway_field_join_button', 'class' => 'blocksolid_gateway_row first', 'blocksolid_gateway_custom_data' => 'custom_join_button',); //Mod to below code to allow older PHP
		 // register a new field in the "blocksolid_gateway_section_developers" section, inside the "blocksolid_gateway_" page
		 add_settings_field(
		 'blocksolid_gateway_field_join_button', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Show Join button', 'blocksolid_gateway_' ),'blocksolid_gateway_field_join_button_cb','blocksolid_gateway_','blocksolid_gateway_section_developers',$settings_field_array);

		$settings_field_array = array('reset_id' => 'blocksolid_gateway_field_join_label', 'label_for' => 'blocksolid_gateway_field_join_label', 'class' => 'blocksolid_gateway_row first', 'blocksolid_gateway_custom_data' => 'custom_join_label',); //Mod to below code to allow older PHP
		 // register a new field in the "blocksolid_gateway_section_developers" section, inside the "blocksolid_gateway_" page
		 add_settings_field(
		 'blocksolid_gateway_field_join_label', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Join button label', 'blocksolid_gateway_' ),'blocksolid_gateway_field_join_label_cb','blocksolid_gateway_','blocksolid_gateway_section_developers',$settings_field_array);

		$settings_field_array = array('reset_id' => 'blocksolid_gateway_field_join_link', 'label_for' => 'blocksolid_gateway_field_join_link', 'class' => 'blocksolid_gateway_row first', 'blocksolid_gateway_custom_data' => 'custom_join_link',); //Mod to below code to allow older PHP
		 // register a new field in the "blocksolid_gateway_section_developers" section, inside the "blocksolid_gateway_" page
		 add_settings_field(
		 'blocksolid_gateway_field_join_link', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Join button link', 'blocksolid_gateway_' ),'blocksolid_gateway_field_join_link_cb','blocksolid_gateway_','blocksolid_gateway_section_developers',$settings_field_array);

		$settings_field_array = array('reset_id' => 'blocksolid_gateway_field_show_lost_password_link', 'label_for' => 'blocksolid_gateway_field_show_lost_password_link', 'class' => 'blocksolid_gateway_row first', 'blocksolid_gateway_custom_data' => 'custom_show_lost_password_link',); //Mod to below code to allow older PHP
		 // register a new field in the "blocksolid_gateway_section_developers" section, inside the "blocksolid_gateway_" page
		 add_settings_field(
		 'blocksolid_gateway_field_show_lost_password_link', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Show lost password link', 'blocksolid_gateway_' ),'blocksolid_gateway_field_show_lost_password_link_cb','blocksolid_gateway_','blocksolid_gateway_section_developers',$settings_field_array);

		$settings_field_array = array('reset_id' => 'blocksolid_gateway_field_login_box_get_password_message', 'label_for' => 'blocksolid_gateway_field_login_box_get_password_message', 'class' => 'blocksolid_gateway_row first', 'blocksolid_gateway_custom_data' => 'custom_login_box_get_password_message',); //Mod to below code to allow older PHP
		 // register a new field in the "blocksolid_gateway_section_developers" section, inside the "blocksolid_gateway_" page
		 add_settings_field(
		 'blocksolid_gateway_field_login_box_get_password_message', // as of WP 4.6 this value is used only internally
		 // use $args' label_for to populate the id inside the callback
		 __( 'Lost password link text', 'blocksolid_gateway_' ),'blocksolid_gateway_field_login_box_get_password_message_cb','blocksolid_gateway_','blocksolid_gateway_section_developers',$settings_field_array);

	////

	}

}

/**
 * register our blocksolid_gateway_settings_init to the admin_init action hook
 */
add_action( 'admin_init', 'blocksolid_gateway_settings_init' );

/**
 * custom option and settings:
 * callback functions
 */

// developers section cb

// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function blocksolid_gateway_section_developers_cb( $args ) {
 ?>
 <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php _e( '<p>Choose how the login box is displayed. Make your choices and click "Save settings".</p>', 'blocksolid_gateway_' ); ?></p>
<?php
}

// impression field cb

// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.

function blocksolid_gateway_get_default_option() {
	return "";
}

function blocksolid_gateway_field_impression_cb( $args ) {$blocksolid_gateway_options = get_option( 'blocksolid_gateway_options' ); ?>
	<input type='checkbox' class='blocksolid_option' name='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]' id='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]' <?php checked( isset( $blocksolid_gateway_options[ $args['label_for'] ] ) ?  $blocksolid_gateway_options[ $args['label_for']] : "", 1 ); ?> value='1'> <label for='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]'>Hide content completely <strong>including source code</strong> when a page is blocked or show an impression of the page beneath the login box.</label>
	<br><br>
<?php }

function blocksolid_gateway_field_login_box_title_cb( $args ) {$options = get_option( 'blocksolid_gateway_options' ); ?>
  <input type="text" placeholder="Example: Member Login Required" maxlength="30" size="35" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['blocksolid_gateway_custom_data'] ); ?>" name="blocksolid_gateway_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset( $options[ $args['label_for'] ] ) ?  esc_html(sanitize_text_field(wp_unslash($options[ $args['label_for']]))) : ( blocksolid_gateway_get_default_option( $args['label_for']) ); ?>" />
 <?php }

function blocksolid_gateway_field_login_box_username_label_cb( $args ) {$options = get_option( 'blocksolid_gateway_options' ); ?>
  <input type="text" placeholder="Example: User Name" maxlength="30" size="35" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['blocksolid_gateway_custom_data'] ); ?>" name="blocksolid_gateway_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset( $options[ $args['label_for'] ] ) ?  esc_html(sanitize_text_field(wp_unslash($options[ $args['label_for']]))) : ( blocksolid_gateway_get_default_option( $args['label_for']) ); ?>" />
 <?php }

function blocksolid_gateway_field_join_button_cb( $args ) {$blocksolid_gateway_options = get_option( 'blocksolid_gateway_options' ); ?>
    <input type='checkbox' class='blocksolid_option' name='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]' id='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]' <?php checked( isset( $blocksolid_gateway_options[ $args['label_for'] ] ) ?  $blocksolid_gateway_options[ $args['label_for']] : "", 1 ); ?> value='1'> <label for='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]'></label><br><br>
<?php }

function blocksolid_gateway_field_back_button_cb( $args ) {$blocksolid_gateway_options = get_option( 'blocksolid_gateway_options' ); ?>
    <input type='checkbox' class='blocksolid_option' name='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]' id='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]' <?php checked( isset( $blocksolid_gateway_options[ $args['label_for'] ] ) ?  $blocksolid_gateway_options[ $args['label_for']] : "", 1 ); ?> value='1'> <label for='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]'></label><br><br>
<?php }

function blocksolid_gateway_field_join_label_cb( $args ) {$options = get_option( 'blocksolid_gateway_options' ); ?>
  <input type="text" placeholder="Example: Join" maxlength="20" size="10" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['blocksolid_gateway_custom_data'] ); ?>" name="blocksolid_gateway_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset( $options[ $args['label_for'] ] ) ?  esc_html(sanitize_text_field(wp_unslash($options[ $args['label_for']]))) : ( blocksolid_gateway_get_default_option( $args['label_for']) ); ?>" />
 <?php }

function blocksolid_gateway_field_join_link_cb( $args ) {$options = get_option( 'blocksolid_gateway_options' ); ?>
  <input type="text" placeholder="Example: /join" maxlength="250" size="55" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['blocksolid_gateway_custom_data'] ); ?>" name="blocksolid_gateway_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset( $options[ $args['label_for'] ] ) ?  esc_html(sanitize_text_field(wp_unslash($options[ $args['label_for']]))) : ( blocksolid_gateway_get_default_option( $args['label_for']) ); ?>" />
 <?php }

function blocksolid_gateway_field_show_lost_password_link_cb( $args ) {$blocksolid_gateway_options = get_option( 'blocksolid_gateway_options' ); ?>
    <input type='checkbox' class='blocksolid_option' name='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]' id='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]' <?php checked( isset( $blocksolid_gateway_options[ $args['label_for'] ] ) ?  $blocksolid_gateway_options[ $args['label_for']] : "", 1 ); ?> value='1'> <label for='blocksolid_gateway_options[<?php echo esc_attr( $args['reset_id'] ); ?>]'></label><br><br>
<?php }

function blocksolid_gateway_field_login_box_get_password_message_cb( $args ) {$options = get_option( 'blocksolid_gateway_options' ); ?>
  <input type="text" placeholder="Example: Lost your password?" maxlength="30" size="35" id="<?php echo esc_attr( $args['label_for'] ); ?>" data-custom="<?php echo esc_attr( $args['blocksolid_gateway_custom_data'] ); ?>" name="blocksolid_gateway_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset( $options[ $args['label_for'] ] ) ?  esc_html(sanitize_text_field(wp_unslash($options[ $args['label_for']]))) : ( blocksolid_gateway_get_default_option( $args['label_for']) ); ?>" />
 <?php }

/**
 * top level menu
 */
function blocksolid_gateway_options_page() {

	$show_blocksolid_gateway_settings = true;

	if (is_multisite()){
		if ( !(current_user_can( 'setup_network' ) )) {
			$show_blocksolid_gateway_settings = false;
		}
	}

	if ($show_blocksolid_gateway_settings){

	 // add menu page under Settings
	    add_options_page(
	        'Blocksolid Gateway',
	        'Blocksolid Gateway',
	        'manage_options',
	        'blocksolid-gateway.php',
	        'blocksolid_gateway_options_page_html'
	    );

	}

}

/**
 * register our blocksolid_gateway_options_page to the admin_menu action hook
 */
add_action( 'admin_menu', 'blocksolid_gateway_options_page' );

/**
 * top level menu:
 * callback functions
 */
function blocksolid_gateway_options_page_html() {
 // check user capabilities
 if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }

 // add error/update messages

 // check if the user have submitted the settings
 // wordpress will add the "settings-updated" $_GET parameter to the url
 if ( isset( $_GET['settings-updated'] ) ) {
 // add settings saved message with the class of "updated"
 //add_settings_error( 'blocksolid_gateway_messages', 'blocksolid_gateway_message', __( 'Settings Saved', 'blocksolid_gateway_' ), 'updated' );
 }

 // show error/update messages
 settings_errors( 'blocksolid_gateway_messages' );
 ?>
 <div class="wrap">
 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 <form action="options.php" method="post" id="blocksolid_gateway_options">
 <?php
 // output security fields for the registered setting "blocksolid_gateway_"
 settings_fields( 'blocksolid_gateway_' );
 // output setting sections and their fields
 // (sections are registered for "blocksolid_gateway_", each field is registered to a specific section)
 do_settings_sections( 'blocksolid_gateway_' );
 // output save settings button
 submit_button( 'Save Settings' );
 ?>
 </form>

 </div>
 <?php
}

// ---------------------------------------------------------------------------------------------------------------------------------------------

// Add Members Only custom field to posts
function blocksolid_gateway_createCustomField(){
    $post_id = get_the_ID();

    if (!(blocksolid_gateway_is_gutenberg_active())) {
        if (!((get_post_type($post_id) == 'post') || (get_post_type($post_id) == 'page'))) {
            return;
        }
    }else{
        if (!((get_post_type($post_id) == 'post') || (get_post_type($post_id) == 'tribe_events'))) {
            return;
        }
    }

    $value = get_post_meta($post_id, '_blocksolid_gateway_members_only', true);
    wp_nonce_field('blocksolid_gateway_members_only_nonce_'.$post_id, 'blocksolid_gateway_members_only_nonce');
    ?>
    <div class="misc-pub-section misc-pub-section-last">
        <label><input type="checkbox" value="1" <?php checked($value, true, true); ?> name="_blocksolid_gateway_members_only" /><?php _e('Members Only', 'pmg'); ?></label>
    </div>
    <?php
}

function blocksolid_gateway_saveCustomField($post_id){
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (
        !isset($_POST['blocksolid_gateway_members_only_nonce']) ||
        !wp_verify_nonce($_POST['blocksolid_gateway_members_only_nonce'], 'blocksolid_gateway_members_only_nonce_'.$post_id)
    ) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['_blocksolid_gateway_members_only'])) {

        $posted_members_only_field_value = sanitize_text_field(wp_unslash($_POST['_blocksolid_gateway_members_only']));

        update_post_meta($post_id, '_blocksolid_gateway_members_only', $posted_members_only_field_value);
    } else {
        delete_post_meta($post_id, '_blocksolid_gateway_members_only');
    }
}

add_action('post_submitbox_misc_actions', 'blocksolid_gateway_createCustomField');
add_action('save_post', 'blocksolid_gateway_saveCustomField');

// ---------------------------------------------------------------------------------------------------------------------------------------------

// Register members only field for pages
function blocksolid_gateway_register_members_only_page_meta() {
    register_post_meta( 'page', '_blocksolid_gateway_members_only', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'boolean',
            'auth_callback' => function() {
                    return current_user_can( 'edit_posts' );
            }
    ) );
}
add_action( 'init', 'blocksolid_gateway_register_members_only_page_meta' );

// -------------------------------------------------------------------------------------------------------------------------------

/*  Custom Field for Categories. */

// Add new term page
function blocksolid_gateway_taxonomy_add_meta_fields( $taxonomy ) { ?>
    <div class="form-field term-group">
        <label for="_blocksolid_gateway_members_only">
            <?php _e( 'Members Only', '_blocksolid_gateway_members_only' ); ?> &nbsp;<input type="checkbox" id="_blocksolid_gateway_members_only" name="_blocksolid_gateway_members_only" value="1" />
        </label>
    </div><?php
}
add_action( 'category_add_form_fields', 'blocksolid_gateway_taxonomy_add_meta_fields', 10, 2 );

// Edit term page
function blocksolid_gateway_taxonomy_edit_meta_fields( $term, $taxonomy ) {
    $blocksolid_gateway_members_only = get_term_meta( $term->term_id, '_blocksolid_gateway_members_only', true ); ?>

    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="_blocksolid_gateway_members_only"><?php _e( 'Members Only', '_blocksolid_gateway_members_only' ); ?></label>
        </th>
        <td>
            <input type="checkbox" id="_blocksolid_gateway_members_only" name="_blocksolid_gateway_members_only" value="1" <?php echo ( $blocksolid_gateway_members_only ) ? checked( $blocksolid_gateway_members_only, '1' ) : ''; ?>/>
        </td>
    </tr><?php
}
add_action( 'category_edit_form_fields', 'blocksolid_gateway_taxonomy_edit_meta_fields', 10, 2 );

// Save custom meta
function blocksolid_gateway_taxonomy_save_taxonomy_meta( $term_id, $tag_id ) {
	if ( isset( $_POST[ '_blocksolid_gateway_members_only' ] ) ) {
	    update_term_meta( $term_id, '_blocksolid_gateway_members_only', '1' );
	} else {
	    update_term_meta( $term_id, '_blocksolid_gateway_members_only', '' );
	}
}
add_action( 'created_category', 'blocksolid_gateway_taxonomy_save_taxonomy_meta', 10, 2 );
add_action( 'edited_category', 'blocksolid_gateway_taxonomy_save_taxonomy_meta', 10, 2 );

// -------------------------------------------------------------------------------------------------------------------------------

function blocksolid_gateway_admin_post_type () {
    global $post, $parent_file, $typenow, $current_screen, $pagenow;

    $post_type = NULL;

    if($post && (property_exists($post, 'post_type') || method_exists($post, 'post_type')))
        $post_type = $post->post_type;

    if(empty($post_type) && !empty($current_screen) && (property_exists($current_screen, 'post_type') || method_exists($current_screen, 'post_type')) && !empty($current_screen->post_type))
        $post_type = $current_screen->post_type;

    if(empty($post_type) && !empty($typenow))
        $post_type = $typenow;

    if(empty($post_type) && function_exists('get_current_screen'))
        $post_type = get_current_screen();

    if(empty($post_type) && isset($_REQUEST['post']) && !empty($_REQUEST['post']) && function_exists('get_post_type') && $get_post_type = get_post_type((int)$_REQUEST['post']))
        $post_type = $get_post_type;

    if(empty($post_type) && isset($_REQUEST['post_type']) && !empty($_REQUEST['post_type']))
        $post_type = sanitize_key($_REQUEST['post_type']);

    if(empty($post_type) && 'edit.php' == $pagenow)
        $post_type = 'post';

    return $post_type;
}

// -------------------------------------------------------------------------------------------------------------------------------

// Gutenberg Settings

add_action('init', function() {

    $current_screen_type = blocksolid_gateway_admin_post_type();

    if (!($current_screen_type == 'page')) {
        return;
    }

	$js_data = array();

    wp_register_script( 'block-blocksolid-gateway-js', plugins_url( '/gutenberg/block-blocksolid-gateway.js', __FILE__ ), array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ), '1.0', false );
    register_block_type('pwd/blocksolid-gateway', [
        'editor_script' => 'block-blocksolid-gateway-js'
    ]);

	wp_localize_script(
	  'block-blocksolid-gateway-js',
	  'jsData',
	  $js_data
	);

});

// ---------------------------------------------------------------------------------------------------------------------------------------------

?>