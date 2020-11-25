<?php
namespace WPCF\woocommerce;

defined( 'ABSPATH' ) || exit;

class Submit_Form {

    public function __construct() {
        add_action( 'wp_ajax_addfrontenddata', array($this, 'frontend_data_save')); // Save data for frontend campaign submit form
    }

    /**
     * @param int $user_id
     * @return array
     *
     * Get logged user all campaign id;
     */
    public function logged_in_user_campaign_ids($user_id = 0) {
        global $wpdb;
        if ($user_id == 0)
            $user_id = get_current_user_id();

        //Removed AND post_status = 'publish'
        $wp_query_users_product_id = $wpdb->get_col("select ID from {$wpdb->posts} WHERE post_author = {$user_id} AND post_type = 'product' ");
        return $wp_query_users_product_id;
    }

    /**
     * @frontend_data_save()
     *
     * Save
     */

    function frontend_data_save(){
        if ( ! isset( $_POST['wpcf_form_action_field'] ) || ! wp_verify_nonce( $_POST['wpcf_form_action_field'], 'wpcf_form_action' ) ) {
            die(json_encode(array('success'=> 0, 'message' => __('Sorry, your data did not verify.', 'wp-crowdfunding'))));
            exit;
        }

        global $wpdb;

        $show_description = get_option('wpcf_show_description');
        $show_short_description = get_option('wpcf_show_short_description');
        $show_category = get_option('wpcf_show_category');
        $show_tag = get_option('wpcf_show_tag');
        $show_feature = get_option('wpcf_show_feature');
        $show_video = get_option('wpcf_show_video');
        $show_end_method = get_option('wpcf_show_end_method');
        $show_start_date = get_option('wpcf_show_start_date');
        $show_end_date = get_option('wpcf_show_end_date');
        $show_min_price = get_option('wpneo_show_min_price');
        $show_max_price = get_option('wpneo_show_max_price');
        $show_recommended_price = get_option('wpneo_show_recommended_price');
        $predefined_amount = get_option('wpcf_show_predefined_amount');
        $show_funding_goal = get_option('wpcf_show_funding_goal');
        $show_contributor_table = get_option('wpcf_show_contributor_table');
        $show_contributor_anonymity = get_option('wpcf_show_contributor_anonymity');
        $show_country = get_option('wpcf_show_country');
        $show_location = get_option('wpcf_show_location');
        // Repetable
        $show_reward_image = get_option('wpcf_show_reward_image');
        $show_reward = get_option('wpcf_show_reward');
        $show_estimated_delivery_month = get_option('wpcf_show_estimated_delivery_month');
        $show_estimated_delivery_year = get_option('wpcf_show_estimated_delivery_year');
        $show_quantity = get_option('wpcf_show_quantity');

        $show_terms_and_conditions = get_option('wpcf_show_terms_and_conditions');
        
        if ( empty($_POST['wpneo-form-title'])){
            die(json_encode(array('success'=> 0, 'message' => __('Title required', 'wp-crowdfunding'))));
        }
        if ( empty($_POST['wpneo-form-description']) && $show_description == 'true'){
            die(json_encode(array('success'=> 0, 'message' => __('Description required', 'wp-crowdfunding'))));
        }
        if ( empty($_POST['wpneo-form-short-description']) && $show_short_description == 'true' ){
            die(json_encode(array('success'=> 0, 'message' => __('Short Description required', 'wp-crowdfunding'))));
        }
        if ( empty($_POST['wpneo-form-funding-goal']) && $show_funding_goal == 'true' ){
            die(json_encode(array('success'=> 0, 'message' => __('Funding goal required', 'wp-crowdfunding'))));
        }
        if ( empty($_POST['wpneo_terms_agree']) && $show_terms_and_conditions == 'true' ){
            die(json_encode(array('success'=> 0, 'message' => __('Please check terms condition', 'wp-crowdfunding'))));
        }

        $title = isset($_POST['wpneo-form-title']) ? sanitize_text_field($_POST['wpneo-form-title']) : '';
        $description = (isset($_POST['wpneo-form-description']) && $show_description == 'true' ) ? $_POST['wpneo-form-description'] : '';
        $short_description = (isset($_POST['wpneo-form-short-description']) && $show_short_description == 'true') ? $_POST['wpneo-form-short-description'] : '';
        $category = (isset($_POST['wpneo-form-category']) && $show_category == 'true') ? sanitize_text_field($_POST['wpneo-form-category']) : '';
        $tag = (isset($_POST['wpneo-form-image-id']) && $show_tag == 'true') ? sanitize_text_field($_POST['wpneo-form-tag']) : '';
        $image_id = (isset($_POST['wpneo-form-image-id']) && $show_feature == 'true') ? sanitize_text_field($_POST['wpneo-form-image-id']) : '';
        $video = (isset($_POST['wpneo-form-video']) && $show_video == 'true') ? sanitize_text_field($_POST['wpneo-form-video']) : '';
        $start_date = (isset($_POST['wpneo-form-start-date']) && $show_start_date == 'true') ? sanitize_text_field($_POST['wpneo-form-start-date']) : '';
        $end_date = (isset($_POST['wpneo-form-end-date']) && $show_end_date == 'true') ? sanitize_text_field($_POST['wpneo-form-end-date']) : '';
        $min_price = (isset($_POST['wpneo-form-min-price']) && $show_min_price == 'true') ? sanitize_text_field($_POST['wpneo-form-min-price']) : '';
        $max_price = (isset($_POST['wpneo-form-max-price']) && $show_max_price == 'true') ? sanitize_text_field($_POST['wpneo-form-max-price']) : ''; 
        $recommended_price = (isset($_POST['wpneo-form-recommended-price']) && $show_recommended_price == 'true') ? sanitize_text_field($_POST['wpneo-form-recommended-price']) : '';
        $predefined_amount = (isset($_POST['wpcf_predefined_pledge_amount']) && $predefined_amount == 'true') ? sanitize_text_field($_POST['wpcf_predefined_pledge_amount']) : '';
        $funding_goal = (isset($_POST['wpneo-form-funding-goal']) && $show_funding_goal == 'true') ? sanitize_text_field($_POST['wpneo-form-funding-goal']) : '';
        $type = (isset($_POST['wpneo-form-type']) && $show_end_method == 'true') ? sanitize_text_field($_POST['wpneo-form-type']) : ''; 
        $contributor_table = (isset($_POST['wpneo-form-contributor-table']) && $show_contributor_table == 'true') ? sanitize_text_field($_POST['wpneo-form-contributor-table']) : '';
        $contributor_show = (isset($_POST['wpneo-form-contributor-show']) && $show_contributor_anonymity == 'true') ? sanitize_text_field($_POST['wpneo-form-contributor-show']) : '';
        $paypal = isset($_POST['wpneo-form-paypal']) ? sanitize_text_field($_POST['wpneo-form-paypal']) : ''; 
        $country = (isset($_POST['wpneo-form-country']) && $show_country == 'true') ? sanitize_text_field($_POST['wpneo-form-country']) : '';
        $location = (isset($_POST['wpneo-form-location']) && $show_location == 'true') ? sanitize_text_field($_POST['wpneo-form-location']) : '';

        $user_id = get_current_user_id();
        $my_post = array(
            'post_type'		=>'product',
            'post_title'    => $title,
            'post_content'  => $description,
            'post_excerpt'  => $short_description,
            'post_author'   => $user_id,
        );

        do_action('wpcf_before_campaign_submit_action');

        if(isset($_POST['edit_form'])){
            //Prevent if unauthorised access
            $wp_query_users_product_id = $this->logged_in_user_campaign_ids();
            $my_post['ID'] = $_POST['edit_post_id'];

            $campaign_status = get_option('wpneo_campaign_edit_status', 'pending');
            $my_post['post_status'] = $campaign_status;

            if ( ! in_array($my_post['ID'], $wp_query_users_product_id)) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => 0, 'msg' => 'Unauthorized action'));
                exit;
            }
            $post_id = wp_update_post( $my_post );

        }else{
            $my_post['post_status'] = get_option( 'wpneo_default_campaign_status' );
            $post_id = wp_insert_post( $my_post );
            if ($post_id) {
                WC()->mailer(); // load email classes
                do_action('wpcf_after_campaign_email',$post_id);
            }
        }        

        if ($post_id) {
            if( $category != '' ){
                $cat = explode(' ',$category );
                wp_set_object_terms( $post_id , $cat, 'product_cat',true );
            }
            if( $tag != '' ){
                $tag = explode( ',',$tag );
                wp_set_object_terms( $post_id , $tag, 'product_tag',true );
            }
            wp_set_object_terms( $post_id , 'crowdfunding', 'product_type',true );

            wpcf_function()->update_meta($post_id, '_thumbnail_id', esc_attr($image_id));
            wpcf_function()->update_meta($post_id, 'wpneo_funding_video', esc_url($video));
            wpcf_function()->update_meta($post_id, '_nf_duration_start', esc_attr($start_date));
            wpcf_function()->update_meta($post_id, '_nf_duration_end', esc_attr($end_date));
            wpcf_function()->update_meta($post_id, 'wpneo_funding_minimum_price', esc_attr($min_price));
            wpcf_function()->update_meta($post_id, 'wpneo_funding_maximum_price', esc_attr($max_price));
            wpcf_function()->update_meta($post_id, 'wpneo_funding_recommended_price', esc_attr($recommended_price));
            wpcf_function()->update_meta($post_id, 'wpcf_predefined_pledge_amount', esc_attr($wpcf_predefined_pledge_amount));
            wpcf_function()->update_meta($post_id, '_nf_funding_goal', esc_attr($funding_goal));
            wpcf_function()->update_meta($post_id, 'wpneo_campaign_end_method', esc_attr($type));
            wpcf_function()->update_meta($post_id, 'wpneo_show_contributor_table', esc_attr($contributor_table));
            wpcf_function()->update_meta($post_id, 'wpneo_mark_contributors_as_anonymous', esc_attr($contributor_show));
            wpcf_function()->update_meta($post_id, 'wpneo_campaigner_paypal_id', esc_attr($paypal));
            wpcf_function()->update_meta($post_id, 'wpneo_country', esc_attr($country));
            wpcf_function()->update_meta($post_id, '_nf_location', esc_html($location));

            //Saved repeatable rewards
            if (!empty($_POST['wpneo_rewards_pladge_amount'])) {
                $data             = array();
                
                $pladge_amount    = $_POST['wpneo_rewards_pladge_amount'];
                $description      = $_POST['wpneo_rewards_description'];
                $endmonth         = $_POST['wpneo_rewards_endmonth'];
                $endyear          = $_POST['wpneo_rewards_endyear'];
                $item_limit       = $_POST['wpneo_rewards_item_limit'];
                $image_field      = $_POST['wpneo_rewards_image_field'];

                $field_number     = count($pladge_amount);
                for ($i = 0; $i < $field_number; $i++) {
                    if (!empty($pladge_amount[$i])) {
                        $data[] = array(
                            'wpneo_rewards_pladge_amount'   => intval($pladge_amount[$i]),
                            'wpneo_rewards_description'     => (($show_reward == 'true' && isset($description[$i])) ? esc_textarea($description[$i]) : ''),
                            'wpneo_rewards_endmonth'        => (($show_estimated_delivery_month == 'true' && isset($endmonth[$i])) ? esc_html($endmonth[$i]) : ''),
                            'wpneo_rewards_endyear'         => (($show_estimated_delivery_year == 'true' && isset($endyear[$i])) ? esc_html($endyear[$i]) : ''),
                            'wpneo_rewards_item_limit'      => (($show_quantity == 'true' && isset($item_limit[$i])) ? esc_html($item_limit[$i]) : ''),
                            'wpneo_rewards_image_field'     => (($show_reward_image == 'true' && isset($image_field[$i])) ? esc_html($image_field[$i]) : ''),
                        );
                    }
                }
                $data_json = json_encode($data,JSON_UNESCAPED_UNICODE);
                wpcf_function()->update_meta($post_id, 'wpneo_reward', wp_slash($data_json));
            }
        }
        $redirect = get_permalink(get_option('wpneo_crowdfunding_dashboard_page_id')).'?page_type=campaign';
        
        die(json_encode(array('success'=> 1, 'message' => __('Campaign successfully submitted', 'wp-crowdfunding'), 'redirect' => $redirect)));
    }

}