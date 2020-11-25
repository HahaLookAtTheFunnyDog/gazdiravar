<?php
namespace WPCF\blocks;

defined( 'ABSPATH' ) || exit;

class Single_Campaign{
    
    public function __construct(){
        $this->register_single_campaign();
    }

    public function register_single_campaign(){
        register_block_type(
            'wp-crowdfunding/singlecampaign',
            array(
                'attributes' => array(
                    'campaignID'    => array(
                        'type'          => 'string',
                        'default'       => '',
                    ),
                    'textColor' => array(
                        'type'          => 'string',
                        'default'       => '#94c94a',
                    ),
                    'bgColor'   => array(
                        'type'          => 'string',
                        'default'       => '#94c94a',
                    ),
                ),
                'render_callback' => array( $this, 'single_campaign_block_callback' ),
            )
        );
    }

    public function single_campaign_block_callback( $att ){
        $campaignID     = isset( $att['campaignID']) ? $att['campaignID'] : '';
        $bgColor        = isset( $att['bgColor']) ? $att['bgColor'] : '';
        $textcolor      = isset( $att['textColor']) ? $att['textColor'] : '';
    
    
        $atts = array(
            'campaign_id' => $campaignID,
        );

        $args = array(
            'posts_per_page'      => 1,
            'post_type'           => 'product',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'no_found_rows'       => 1,
        );

        if ( isset( $atts['campaign_id'] ) ) {
            $args['p'] = absint( $atts['campaign_id'] );
        }

        $single_product = new \WP_Query( $args );

        // For "is_single" to always make load comments_template() for reviews.
        $single_product->is_single = true;

        ob_start();
        
        global $wp_query;

        // Backup query object so following loops think this is a product page.
        $previous_wp_query = $wp_query;
        $wp_query          = $single_product;

        wp_enqueue_script( 'wc-single-product' );

        while ( $single_product->have_posts() ) {
            $single_product->the_post();
            wpcf_function()->template('single-crowdfunding-content-only');
        }

        // restore $previous_wp_query and reset post data.
        $wp_query = $previous_wp_query;
        wp_reset_postdata();

        $html = '';
        $html .= '<style>';
            $html .= '.wpneo-list-details .wpneo_donate_button, #wpneo-tab-reviews .submit, #neo-progressbar > div, ul.wpneo-crowdfunding-update li:hover span.round-circle, .wpneo-links li a:hover, .wpneo-links li.active a, #neo-progressbar > div {
                background-color: '. $bgColor .';
                color: #ffffff;
            }';
            $html .= '.tab-rewards-wrapper .overlay {
                background: '. $bgColor .';
            }';

            $html .= 'a.wpneo-fund-modal-btn.wpneo-link-style1, .wpneo-tabs-menu li.wpneo-current a, ul.wpneo-crowdfunding-update li .wpneo-crowdfunding-update-title {
                color: '. $textcolor .';
            }';

            $html .= '.wpneo-tabs-menu li.wpneo-current {
                border-bottom: 3px solid '. $textcolor .';
            }';
        $html .= '</style>';

        $final_content = ob_get_clean();

        return '<div class="woocommerce">' . $final_content . ' '. $html .'</div>';
    }
}
