<?php
namespace WPCF\blocks;

defined( 'ABSPATH' ) || exit;

class ProjectListing{
    
    public function __construct(){
        $this->register_project_listing();
    }
 
    public function register_project_listing(){
        register_block_type(
            'wp-crowdfunding/projectlisting',
            array(
                'attributes' => array(
                    'categories'   => array(
                        'type'      => 'string',
                        'default'   => 'all'
                    ),
                    'order'   => array(
                        'type'      => 'string',
                        'default'   => 'desc'
                    ),
                    'order_by' => array(
                        'type'      => 'string',
                        'default'   => 'date'
                    ),
                    'numbers'   => array(
                        'type'      => 'number',
                        'default'   => 10
                    ),
                    'mjColor'    => array(
                        'type'          => 'string',
                        'default'       => '#000000',
                    ),
                    'progressbarColor'    => array(
                        'type'          => 'string',
                        'default'       => '#1adc68',
                    ),
                    'authorColor'    => array(
                        'type'          => 'string',
                        'default'       => '#737373',
                    ),
                    
                ),
                'render_callback' => array( $this, 'project_listing_block_callback' ),
            )
        );
    }

    public function project_listing_block_callback( $att ){
        $post_limit     = isset( $att['numbers']) ? $att['numbers'] : 10;
        $order          = isset( $att['order']) ? $att['order'] : 'desc';
        $order_by       = isset( $att['order_by']) ? $att['order_by'] : 'date';
        $majorColor         = isset( $att['mjColor']) ? $att['mjColor'] : '#000000';
        $progressbarColor   = isset( $att['progressbarColor']) ? $att['progressbarColor'] : '#1adc68';
        $authorColor        = isset( $att['authorColor']) ? $att['authorColor'] : '#737373';

        if( function_exists('wpcf_function') ){
            $paged = 1;
            if ( get_query_var('paged') ){
                $paged = absint( get_query_var('paged') );
            } elseif (get_query_var('page')) {
                $paged = absint( get_query_var('page') );
            }

            $query_args = array(
                'post_type'     => 'product',
                'post_status'   => 'publish',
                'tax_query'     => array(
                    'relation'  => 'AND',
                    array(
                        'taxonomy'  => 'product_type',
                        'field'     => 'slug',
                        'terms'     => 'crowdfunding',
                    ),
                ),
                'paged'             => $paged,
                'posts_per_page'    => $post_limit,
                'order'             => $order,
                'orderby'           => $order_by,
            );

            query_posts($query_args);
            ob_start();
            wpcf_function()->template('wpneo-listing');
            $html = '';
            $html .= '<style>';
                $html .= '#neo-progressbar > div, ul.wpneo-crowdfunding-update li:hover span.round-circle, .wpneo-links li a:hover, .wpneo-links li.active a, #neo-progressbar > div {
                    background-color: '. $progressbarColor .';
                }';
                $html .= '.wpneo-funding-data span, .wpneo-time-remaining .wpneo-meta-desc, .wpneo-funding-goal .wpneo-meta-name, .wpneo-raised-percent, .wpneo-listing-content p.wpneo-short-description, .wpneo-location .wpneo-meta-desc, .wpneo-listings .wpneo-listing-content h4 a, .wpneo-fund-raised, .wpneo-time-remaining {
                    color: '. $majorColor .';
                }';

                $html .= '.wpneo-listings .wpneo-listing-content .wpneo-author a, .wpneo-listings .wpneo-listing-content p.wpneo-author {
                    color: '. $authorColor .'
                }';
            $html .= '</style>';
            $html .= ob_get_clean();
            wp_reset_query();
            return $html;
        }    
    }
}
