<?php
namespace WPCF;

defined( 'ABSPATH' ) || exit;

class Gutenberg{

    public function __construct(){
        add_action( 'init', array( $this, 'blocks_init' ));
        add_action( 'enqueue_block_editor_assets', array( $this, 'post_editor_assets' ) );
        add_filter( 'block_categories', array( $this, 'block_categorie_callback'), 1 , 2 );
    }
    
    /** 
     * Blocks Init
     */
    public function blocks_init(){
        require_once WPCF_DIR_PATH . 'includes/blocks/Search.php';
        require_once WPCF_DIR_PATH . 'includes/blocks/Donate.php';
        require_once WPCF_DIR_PATH . 'includes/blocks/Project_Listing.php';
        require_once WPCF_DIR_PATH . 'includes/blocks/Popular_Campaigns.php';
        require_once WPCF_DIR_PATH . 'includes/blocks/Campaign_Box.php';
        require_once WPCF_DIR_PATH . 'includes/blocks/Registration.php';
        require_once WPCF_DIR_PATH . 'includes/blocks/Dashboard.php';
        require_once WPCF_DIR_PATH . 'includes/blocks/Single_Campaign.php';
        require_once WPCF_DIR_PATH . 'includes/blocks/Submit_Form.php';

        new \WPCF\blocks\Search();
        new \WPCF\blocks\Donate();
        new \WPCF\blocks\ProjectListing();
        new \WPCF\blocks\PopularCampaigns();
        new \WPCF\blocks\CampaignBox();
        new \WPCF\blocks\Registration();
        new \WPCF\blocks\Dashboard();
        new \WPCF\blocks\Single_Campaign();
        new \WPCF\blocks\Submit_Form();
    }
    
    /**
     * Only for the Gutenberg Editor(Backend Only)
     */
    public function post_editor_assets(){
        
        // Scripts
        wp_enqueue_script(
            'wpcf-block-script-js',
            WPCF_DIR_URL . 'assets/js/blocks.min.js', 
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
            false,
            true
        );

        // Localize Scripts
        wp_localize_script( 'wpcf-block-script-js', 'plugin_option', array(
            'plugin' => WPCF_DIR_URL,
            'name' => 'crowdfunding'
        ) );
        
    }

    /**
     * Block Category Add
     */
    public function block_categorie_callback( $categories, $post ){
        return array_merge(
            $categories,
            array(
                array(
                    'slug' 	=> 'wp-crowdfunding',
                    'title' => __( 'WP Crowdfunding', 'wp-crowdfunding' ),
                )
            )
        );
    }
}

