<?php
namespace WPCF\blocks;

defined( 'ABSPATH' ) || exit;

class Submit_Form{
    
    public function __construct(){
        $this->register_submit_form();
    }

    public function register_submit_form(){
        register_block_type(
            'wp-crowdfunding/submitform',
            array(
                'attributes' => array(
                    'textColor' => array(
                        'type'          => 'string',
                        'default'       => '#ffffff',
                    ),
                    'bgColor'   => array(
                        'type'          => 'string',
                        'default'       => '#1adc68',
                    ),
                    'cancelBtnColor'   => array(
                        'type'          => 'string',
                        'default'       => '#cf0000',
                    ),
                ),
                'render_callback' => array( $this, 'submit_form_block_callback' ),
            )
        );
    }

    public function submit_form_block_callback( $att ){
        $textColor          = isset( $att['textColor']) ? $att['textColor'] : '';
        $bgColor            = isset( $att['bgColor']) ? $att['bgColor'] : '';
        $cancelBtnColor     = isset( $att['cancelBtnColor']) ? $att['cancelBtnColor'] : '';
    
        $html = '';
        $html .= wpcf_get_submit_form_campaign();

        $html .= '<style>';
            $html .= 'input[type="button"].wpneo-image-upload, .wpneo-image-upload.float-right, .wpneo-image-upload-btn, #addreward, #wpneofrontenddata .wpneo-form-action input[type="submit"].wpneo-submit-campaign, .wpneo-single .wpneo-image-upload-btn {
                background-color: '. $bgColor .';
            }';
            $html .= 'input[type="button"].wpneo-image-upload, .wpneo-image-upload.float-right, .wpneo-image-upload-btn, #addreward, #wpneofrontenddata .wpneo-form-action input[type="submit"].wpneo-submit-campaign, a.wpneo-cancel-campaign, .editor-styles-wrapper a.wpneo-cancel-campaign {
                color: '.$textColor.'
            }';
            $html .= 'a.wpneo-cancel-campaign {
                background-color: '.$cancelBtnColor.'
            }';
        $html .= '</style>';

        return $html;
    }
}
