<?php
namespace WPCF\blocks;

defined( 'ABSPATH' ) || exit;

class PopularCampaigns{
    
    public function __construct(){
        $this->register_popular_campaigns();
    }
 
    public function register_popular_campaigns(){
        register_block_type(
            'wp-crowdfunding/popularcampaigns',
            array(
                'attributes' => array(

                    'order'   => array(
                        'type'      => 'string',
                        'default'   => 'desc'
                    ),
                    'numbers'   => array(
                        'type'      => 'number',
                        'default'   => 10
                    ),
                    
                ),
                'render_callback' => array( $this, 'popular_campaigns_block_callback' ),
            )
        );
    }

    public function popular_campaigns_block_callback( $att ){
         
        $post_limit     = isset( $att['numbers']) ? $att['numbers'] : 6;
        $order          = isset( $att['order']) ? $att['order'] : 'desc';
        $orderby        = isset( $att['orderby']) ? $att['orderby'] : 'date';

        $paged = 1;
        if ( get_query_var('paged') ) {
            $paged = absint( get_query_var('paged') );
        } elseif (get_query_var('page')) {
            $paged = absint( get_query_var('page') );
        }

        $query_args = array(
            'post_type'             => 'product',
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 1,
            'meta_key'              => 'total_sales',
            'posts_per_page'        => $post_limit,
            'paged'                 => $paged,
            'orderby'               => 'meta_value_num',
            'order'                 => $order,
            'meta_query' => array(
                array(
                    'key'       => 'total_sales',
                    'value'     => 0,
                    'compare'   => '>',
                )
            ),

            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'crowdfunding',
                ),
            )
        );

        query_posts($query_args);
        
        ob_start();
        wpcf_function()->template('wpneo-listing');
        $html = ob_get_clean();
        wp_reset_query();
        return $html;     
    }
}
