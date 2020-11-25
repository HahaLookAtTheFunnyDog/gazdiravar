<?php
namespace WPCF\blocks;

defined( 'ABSPATH' ) || exit;

class Dashboard{
    
    public function __construct(){
        $this->register_dashboard();
    }

    public function register_dashboard(){
        register_block_type(
            'wp-crowdfunding/dashboard',
            array(
                'attributes' => array(
                    'bgColor'    => array(
                        'type'          => 'string',
                        'default'       => '#1adc68',
                    ),
                    'titleColor'    => array(
                        'type'          => 'string',
                        'default'       => '#ffffff',
                    ),
                ),
                'render_callback' => array( $this, 'dashboard_block_callback' ),
            )
        );
    }

    public function dashboard_block_callback( $att ){
        $majorColor     = isset( $att['bgColor']) ? $att['bgColor'] : '';
        $textcolor      = isset( $att['titleColor']) ? $att['titleColor'] : '';
    
        $html = $get_id = '';

        if( isset($_GET['page_type']) ){ $get_id = $_GET['page_type']; }
            if ( is_user_logged_in() ) {
                $pagelink = get_permalink( get_the_ID() );
                $dashboard_menus = apply_filters('wpcf_frontend_dashboard_menus', array(
                    'dashboard' => array(
                        'tab'             => 'dashboard',
                        'tab_name'        => __('Dashboard','wp-crowdfunding'),
                        'load_form_file'  => WPCF_DIR_PATH.'includes/woocommerce/dashboard/dashboard.php'
                    ),
                    'profile' => array(
                        'tab'             => 'account',
                        'tab_name'        => __('Profile','wp-crowdfunding'),
                        'load_form_file'  => WPCF_DIR_PATH.'includes/woocommerce/dashboard/profile.php'
                    ),
                    'contact' => array(
                        'tab'             => 'account',
                        'tab_name'        => __('Contact','wp-crowdfunding'),
                        'load_form_file'  => WPCF_DIR_PATH.'includes/woocommerce/dashboard/contact.php'
                    ),
                    'campaign' => array(
                        'tab'             => 'campaign',
                        'tab_name'        => __('My Campaigns','wp-crowdfunding'),
                        'load_form_file'  => WPCF_DIR_PATH.'includes/woocommerce/dashboard/campaign.php'
                    ),
                    'backed_campaigns' => array(
                        'tab'             => 'campaign',
                        'tab_name'        => __('My Invested Campaigns','wp-crowdfunding'),
                        'load_form_file'  => WPCF_DIR_PATH.'includes/woocommerce/dashboard/investment.php'
                    ),
                    'pledges_received' => array(
                        'tab'            => 'campaign',
                        'tab_name'       => __('Pledges Received','wp-crowdfunding'),
                        'load_form_file' => WPCF_DIR_PATH.'includes/woocommerce/dashboard/order.php'
                    ),
                    'bookmark' => array(
                        'tab'            => 'campaign',
                        'tab_name'       => __('Bookmarks','wp-crowdfunding'),
                        'load_form_file' => WPCF_DIR_PATH.'includes/woocommerce/dashboard/bookmark.php'
                    ),
                    'password' => array(
                        'tab'            => 'account',
                        'tab_name'       => __('Password','wp-crowdfunding'),
                        'load_form_file' => WPCF_DIR_PATH.'includes/woocommerce/dashboard/password.php'
                    ),
                    'rewards' => array(
                        'tab'            => 'account',
                        'tab_name'       => __('Rewards','wp-crowdfunding'),
                        'load_form_file' => WPCF_DIR_PATH.'includes/woocommerce/dashboard/rewards.php'
                    ),
                ));
                

                /**
                 * Print menu with active link marking...
                 */
                
                $html .= '<div class="wpcf-dashboard">';
                $html .= '<div class="wpneo-wrapper">';
                $html .= '<div class="wpneo-head wpneo-shadow">';
                    $html .= '<div class="wpneo-links clearfix">';

                        $dashboard = $account = $campaign = $extra = '';
                        foreach ($dashboard_menus as $menu_name => $menu_value){

                            if ( empty($get_id) && $menu_name == 'dashboard'){ $active = 'active';
                            } else { $active = ($get_id == $menu_name) ? 'active' : ''; }

                            $pagelink = add_query_arg( 'page_type', $menu_name , $pagelink );

                            if( $menu_value['tab'] == 'dashboard' ){
                                $dashboard .= '<div class="wpneo-links-list '.$active.'"><a href="'.$pagelink.'">'.$menu_value['tab_name'].'</a></div>';
                            }elseif( $menu_value['tab'] == 'account' ){
                                $account .= '<div class="wpneo-links-lists '.$active.'"><a href="'.$pagelink.'">'.$menu_value['tab_name'].'</a></div>';
                            }elseif( $menu_value['tab'] == 'campaign' ){
                                $campaign .= '<div class="wpneo-links-lists '.$active.'"><a href="'.$pagelink.'">'.$menu_value['tab_name'].'</a></div>';
                            }else{
                                $extra .= '<div class="wpneo-links-list '.$active.'"><a href="'.$pagelink.'">'.$menu_value['tab_name'].'</a></div>';
                            }
                        }
                        
                        $html .= $dashboard;
                        $html .= '<div class="wpneo-links-list wp-crowd-parent"><a href="#">'.__("My Account","wp-crowdfunding").'<span class="wpcrowd-arrow-down"></span></a>';
                            $html .= '<div class="wp-crowd-submenu wpneo-shadow">';
                                $html .= $account;
                                $html .= '<div class="wpneo-links-lists"><a href="'.wp_logout_url( home_url() ).'">'.__('Logout','wp-crowdfunding').'</a></div>';
                            $html .= '</div>';
                        $html .= '</div>';
                        $html .= '<div class="wpneo-links-list wp-crowd-parent"><a href="#">'.__("Campaigns","wp-crowdfunding").'<span class="wpcrowd-arrow-down"></span></a>';
                            $html .= '<div class="wp-crowd-submenu wpneo-shadow">';
                                $html .= $campaign;
                            $html .= '</div>';
                        $html .= '</div>';
                        $html .= $extra;
                        $html .= '<div class="wp-crowd-new-campaign"><a class="wp-crowd-btn wp-crowd-btn-primary" href="'.get_permalink(get_option('wpneo_form_page_id')).'">'.__("Add New Campaign","wp-crowdfunding").'</a></div>';

                    $html .= '</div>';
                $html .= '</div>';


                $var = '';
                if( isset($_GET['page_type']) ){
                    $var = $_GET['page_type'];
                }
        
                ob_start();
                if( $var == 'update' ){
                    require_once WPCF_DIR_PATH.'includes/woocommerce/dashboard/update.php';
                }else{
                    if ( ! empty($dashboard_menus[$get_id]['load_form_file']) ) {
                        if (file_exists($dashboard_menus[$get_id]['load_form_file'])) {
                            include $dashboard_menus[$get_id]['load_form_file'];
                        }
                    }else{
                        include $dashboard_menus['dashboard']['load_form_file'];
                    }
                }
                $html .= ob_get_clean();
                
            $html .= '</div>'; //wpneo-wrapper
            $html .= '</div>';

            $html .= '<style>';
                $html .= '.wpcf-dashboard .wp-crowd-btn-primary, .wpcf-dashboard .wpneo-dashboard-summary ul li.active,
                .wpcf-dashboard .wpneo-edit-btn, .wpcf-dashboard .wpneo-pagination ul li span.current, .wpneo-pagination ul li a:hover, .wpneo-pagination ul li span.current {
                    background-color: '. $majorColor .';
                }';
                $html .= '.wpneo-links div.active a, .wpneo-links div a:hover, .wpcf-dashboard .wpneo-name > p, .wpcf-dashboard .wpcrowd-listing-content .wpcrowd-admin-title h3 a{
                    color: '. $majorColor .';
                }';

                $html .= '.wpneo-links div a.wp-crowd-btn.wp-crowd-btn-primary, .wpneo-links div a.wp-crowd-btn.wp-crowd-btn-primary:hover, .wpcf-dashboard .wp-crowd-btn-primary, .wpcf-dashboard .wpneo-pagination ul li span.current, .wpneo-pagination ul li a:hover, .wpneo-pagination ul li span.current, .wpcf-dashboard .wpneo-edit-btn, .wpneo-dashboard-summary ul li.active .wpneo-value, .wpneo-dashboard-summary ul li.active .wpneo-value-info {
                    color: '. $textcolor .'
                }';

                $html .= '.wpneo-dashboard-summary ul li.active:after {
                    border-color: '.$majorColor.' rgba(0, 128, 0, 0) rgba(255, 255, 0, 0) rgba(0, 0, 0, 0);
                }';
                $html .= '.wpneo-pagination ul li a:hover, .wpneo-pagination ul li span.current {
                    border: 2px solid '.$majorColor.';
                }';
            $html .= '</style>';

        } else {
            $html .= '<div class="woocommerce">';
            $html .= '<div class="woocommerce-info">' . __("Please log in first?", "wp-crowdfunding") . ' <a class="wpneoShowLogin" href="#">' . __("Click here to login", "wp-crowdfunding") . '</a></div>';
            $html .= wpcf_function()->login_form();
            $html .= '</div>';
        }

        return $html;
        
    }
}
