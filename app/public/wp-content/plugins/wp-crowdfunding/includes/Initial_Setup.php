<?php
namespace WPCF;

defined( 'ABSPATH' ) || exit;

if (! class_exists('Initial_Setup')) {

    class Initial_Setup {

        public function __construct() {
            add_action( 'admin_init', array( $this, 'initial_compatibility_check') );
            add_action('wp_ajax_install_woocommerce_plugin',        array($this, 'install_woocommerce_plugin'));
            add_action('admin_action_activate_woocommerce_free',    array($this, 'activate_woocommerce_free'));
        }

        public function initial_compatibility_check(){
            if (version_compare(WPCF_VERSION, '2.0.5', '>')){
                $option_check = get_option('wpcf_show_description');
                if($option_check != 'true' && $option_check != 'false'){
                    $default_value = array(
                        'wpcf_show_description' => 'true',
                        'wpcf_show_short_description' => 'true',
                        'wpcf_show_category' => 'true',
                        'wpcf_show_tag' => 'true',
                        'wpcf_show_feature' => 'true',
                        'wpcf_show_video' => 'true',
                        'wpcf_show_end_method' => 'true',
                        'wpcf_show_start_date' => 'true',
                        'wpcf_show_end_date' => 'true',
                        'wpcf_show_funding_goal' => 'true',
                        'wpcf_show_predefined_amount' => 'true',
                        'wpcf_show_contributor_table' => 'true',
                        'wpcf_show_contributor_anonymity' => 'true',
                        'wpcf_show_country' => 'true',
                        'wpcf_show_location' => 'true',
                        'wpcf_show_reward_image' => 'true',
                        'wpcf_show_reward' => 'true',
                        'wpcf_show_estimated_delivery_month' => 'true',
                        'wpcf_show_estimated_delivery_year' => 'true',
                        'wpcf_show_quantity' => 'true',
                        'wpcf_show_terms_and_conditions' => 'true'
                    );
                    foreach ($default_value as $key => $value ) {
                        update_option( $key , $value );
                    }
                }
            }
        }
        

        /**
         * Do some task during plugin activation
         */
        public function initial_plugin_activation() {
            if (get_option('wpneo_crowdfunding_is_used')) { // Check is plugin used before or not
                return false;
            }
            self::update_option();
            self::insert_page();
        }

        /**
         * Insert settings option data
         */
        public function update_option() {
            $init_setup_data = array(
                'wpneo_crowdfunding_is_used' => WPCF_VERSION,
                'wpneo_cf_selected_theme' => 'basic',
                'vendor_type' => 'woocommerce',
                'wpneo_default_campaign_status' => 'draft',
                'wpneo_campaign_edit_status' => 'pending',
                'wpneo_enable_color_styling' => 'true',
                'wpneo_show_min_price' => 'true',
                'wpneo_show_max_price' => 'true',
                'wpneo_show_recommended_price' => 'true',
                'wpneo_show_target_goal' => 'true',
                'wpneo_show_target_date' => 'true',
                'wpneo_show_target_goal_and_date' => 'true',
                'wpneo_show_campaign_never_end' => 'true',
                'wpcf_show_description' => 'true',
                'wpcf_show_short_description' => 'true',
                'wpcf_show_category' => 'true',
                'wpcf_show_tag' => 'true',
                'wpcf_show_feature' => 'true',
                'wpcf_show_video' => 'true',
                'wpcf_show_end_method' => 'true',
                'wpcf_show_start_date' => 'true',
                'wpcf_show_end_date' => 'true',
                'wpcf_show_funding_goal' => 'true',
                'wpcf_show_predefined_amount' => 'true',
                'wpcf_show_contributor_table' => 'true',
                'wpcf_show_contributor_anonymity' => 'true',
                'wpcf_show_country' => 'true',
                'wpcf_show_location' => 'true',
                'wpcf_show_reward_image' => 'true',
                'wpcf_show_reward' => 'true',
                'wpcf_show_estimated_delivery_month' => 'true',
                'wpcf_show_estimated_delivery_year' => 'true',
                'wpcf_show_quantity' => 'true',
                'wpcf_show_terms_and_conditions' => 'true',
                'wpneo_enable_paypal_per_campaign_email' => 'true',
                'wpneo_single_page_template' => 'in_wp_crowdfunding',
                'wpneo_single_page_reward_design' => '1',
                'hide_cf_campaign_from_shop_page' => 'false',
                'wpneo_crowdfunding_add_to_cart_redirect' => 'checkout_page',
                'wpneo_single_page_id' => 'true',
                'wpneo_enable_recaptcha' => 'false',
                'wpneo_enable_recaptcha_in_user_registration' => 'false',
                'wpneo_enable_recaptcha_campaign_submit_page' => 'false',
                'wpneo_requirement_agree_title' => 'I agree with the terms and conditions.',
            );

            foreach ($init_setup_data as $key => $value ) {
                update_option( $key , $value );
            }
    
            //Upload Permission
            update_option( 'wpneo_user_role_selector', array('administrator', 'editor', 'author', 'shop_manager') );
            $role_list = get_option( 'wpneo_user_role_selector' );
            if( is_array( $role_list ) ){
                if( !empty( $role_list ) ){
                    foreach( $role_list as $val ){
                        $role = get_role( $val );
                        if ($role){
	                        $role->add_cap( 'campaign_form_submit' );
	                        $role->add_cap( 'upload_files' );
                        }
                    }
                }
            }
        }

        /**
         * Insert menu page
         */
        public function insert_page() {
            // Create page object
            $dashboard = array(
                'post_title'    => 'CF Dashboard',
                'post_content'  => '[wpcf_dashboard]',
                'post_type'     => 'page',
                'post_status'   => 'publish',
            );
            $form = array(
                'post_title'    => 'CF campaign form',
                'post_content'  => '[wpcf_form]',
                'post_type'     => 'page',
                'post_status'   => 'publish',
            );
            $listing = array(
                'post_title'    => 'CF Listing Page',
                'post_content'  => '[wpcf_listing]',
                'post_type'     => 'page',
                'post_status'   => 'publish',
            );
            $registration = array(
                'post_title'    => 'CF User Registration',
                'post_content'  => '[wpcf_registration]',
                'post_type'     => 'page',
                'post_status'   => 'publish',
            );
        
            /**
             * Insert the page into the database
             * @Dashbord, @Form, @Listing and @Registration Pages Object
             */
            $dashboard_page = wp_insert_post( $dashboard );
            if ( !is_wp_error( $dashboard_page ) ) {
                wpcf_function()->update_text( 'wpneo_crowdfunding_dashboard_page_id', $dashboard_page );
            }
            $form_page = wp_insert_post( $form );
            if( !is_wp_error( $form_page ) ){
                wpcf_function()->update_text( 'wpneo_form_page_id', $form_page );
            }
            $listing_page = wp_insert_post( $listing );
            if( !is_wp_error( $listing_page ) ){
                wpcf_function()->update_text( 'wpneo_listing_page_id', $listing_page );
            }
            $registration_page = wp_insert_post( $registration );
            if( !is_wp_error( $registration_page ) ){
                wpcf_function()->update_text( 'wpneo_registration_page_id', $registration_page );
            }
        }


        /**
         * Reset method, the ajax will call that method for Reset Settings
         */
        public function settings_reset() {
            self::update_option();
        }

        /**
         * Deactivation Hook For Crowdfunding
         */
        public function initial_plugin_deactivation(){

        }


        public function activation_css() {
            ?>
            <style type="text/css">
                .wpcf-install-notice{
                    padding: 20px;
                }
                .wpcf-install-notice-inner{
                    display: flex;
                    align-items: center;
                }
                .wpcf-install-notice-inner .button{
                    padding: 5px 30px;
                    height: auto;
                    line-height: 20px;
                    text-transform: capitalize;
                }
                .wpcf-install-notice-content{
                    flex-grow: 1;
                    padding-left: 20px;
                    padding-right: 20px;
                }
                .wpcf-install-notice-icon img{
                    width: 64px;
                    border-radius: 4px;
                    display: block;
                }
                .wpcf-install-notice-content h2{
                    margin-top: 0;
                    margin-bottom: 5px;
                }
                .wpcf-install-notice-content p{
                    margin-top: 0;
                    margin-bottom: 0px;
                    padding: 0;
                }
            </style>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    'use strict';
                    $(document).on('click', '.install-wpcf-button', function(e){
                        e.preventDefault();
                        var $btn = $(this);
                        $.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: {install_plugin: 'woocommerce', action: 'install_woocommerce_plugin'},
                            beforeSend: function(){
                                $btn.addClass('updating-message');
                            },
                            success: function (data) {
                                $('.install-wpcf-button').remove();
                                $('#wpcf_install_msg').html(data);
                            },
                            complete: function () {
                                $btn.removeClass('updating-message');
                            }
                        });
                    });
                });
            </script>
            <?php
        }
        /**
         * Show notice if there is no woocommerce
         */
        public function free_plugin_installed_but_inactive_notice(){
            $this->activation_css();
            ?>
            <div class="notice notice-error wpcf-install-notice">
                <div class="wpcf-install-notice-inner">
                    <div class="wpcf-install-notice-icon">
                        <img src="<?php echo WPCF_DIR_URL.'assets/images/woocommerce-icon.png'; ?>" alt="logo" />
                    </div>
                    <div class="wpcf-install-notice-content">
                        <h2><?php _e('Thanks for using WP Crowdfunding', 'wp-crowdfunding'); ?></h2>
                        <?php 
                            printf(
                                '<p>%1$s <a target="_blank" href="%2$s">%3$s</a> %4$s</p>', 
                                __('You must have','wp-crowdfunding'), 
                                'https://wordpress.org/plugins/woocommerce/', 
                                __('WooCommerce','wp-crowdfunding'), 
                                __('installed and activated on this website in order to use WP Crowdfunding.','wp-crowdfunding')
                            );
                        ?>
                        <a href="https://docs.themeum.com/wp-crowdfunding/" target="_blank"><?php _e('Learn more about WP Crowdfunding', 'wp-crowdfunding'); ?></a>
                    </div>
                    <div class="wpcf-install-notice-button">
                        <a  class="button button-primary" href="<?php echo add_query_arg(array('action' => 'activate_woocommerce_free'), admin_url()); ?>"><?php _e('Activate WooCommerce', 'wp-crowdfunding'); ?></a>
                    </div>
                </div>
            </div>
            <?php
        }
    
        public function free_plugin_not_installed(){
            include( ABSPATH . 'wp-admin/includes/plugin-install.php' );
            $this->activation_css();
            ?>
            <div class="notice notice-error wpcf-install-notice">
                <div class="wpcf-install-notice-inner">
                    <div class="wpcf-install-notice-icon">
                        <img src="<?php echo WPCF_DIR_URL.'assets/images/woocommerce-icon.png'; ?>" alt="logo" />
                    </div>
                    <div class="wpcf-install-notice-content">
                        <h2><?php _e('Thanks for using WP Crowdfunding', 'wp-crowdfunding'); ?></h2>
                        <?php 
                            printf(
                                '<p>%1$s <a target="_blank" href="%2$s">%3$s</a> %4$s</p>', 
                                __('You must have','wp-crowdfunding'), 
                                'https://wordpress.org/plugins/woocommerce/', 
                                __('WooCommerce','wp-crowdfunding'), 
                                __('installed and activated on this website in order to use WP Crowdfunding.','wp-crowdfunding')
                            );
                        ?>
                        <a href="https://docs.themeum.com/wp-crowdfunding/" target="_blank"><?php _e('Learn more about WP Crowdfunding', 'wp-crowdfunding'); ?></a>
                    </div>
                    <div class="wpcf-install-notice-button">
                        <a class="install-wpcf-button button button-primary" data-slug="woocommerce" href="<?php echo add_query_arg(array('action' => 'install_woocommerce_free'), admin_url()); ?>"><?php _e('Install WooCommerce', 'wp-crowdfunding'); ?></a>
                    </div>
                </div>
                <div id="wpcf_install_msg"></div>
            </div>
            <?php
        }

        public function activate_woocommerce_free() {
            activate_plugin('woocommerce/woocommerce.php' );
            wp_redirect(admin_url('admin.php?page=wpcf-crowdfunding'));
		    exit();
        }

        public function install_woocommerce_plugin(){
            include(ABSPATH . 'wp-admin/includes/plugin-install.php');
            include(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
    
            if ( ! class_exists('Plugin_Upgrader')){
                include(ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php');
            }
            if ( ! class_exists('Plugin_Installer_Skin')) {
                include( ABSPATH . 'wp-admin/includes/class-plugin-installer-skin.php' );
            }
    
            $plugin = 'woocommerce';
    
            $api = plugins_api( 'plugin_information', array(
                'slug' => $plugin,
                'fields' => array(
                    'short_description' => false,
                    'sections' => false,
                    'requires' => false,
                    'rating' => false,
                    'ratings' => false,
                    'downloaded' => false,
                    'last_updated' => false,
                    'added' => false,
                    'tags' => false,
                    'compatibility' => false,
                    'homepage' => false,
                    'donate_link' => false,
                ),
            ) );
    
            if ( is_wp_error( $api ) ) {
                wp_die( $api );
            }
    
            $title = sprintf( __('Installing Plugin: %s'), $api->name . ' ' . $api->version );
            $nonce = 'install-plugin_' . $plugin;
            $url = 'update.php?action=install-plugin&plugin=' . urlencode( $plugin );
    
            $upgrader = new \Plugin_Upgrader( new \Plugin_Installer_Skin( compact('title', 'url', 'nonce', 'plugin', 'api') ) );
            $upgrader->install($api->download_link);
            die();
        }
    
        
        public static function wc_low_version(){
            printf(
                '<div class="notice notice-error is-dismissible"><p>%1$s <a target="_blank" href="%2$s">%3$s</a> %4$s</p></div>', 
                __('Your','wp-crowdfunding'), 
                'https://wordpress.org/plugins/woocommerce/', 
                __('WooCommerce','wp-crowdfunding'), 
                __('version is below then 3.0, please update.','wp-crowdfunding') 
            );
        }

    }
}