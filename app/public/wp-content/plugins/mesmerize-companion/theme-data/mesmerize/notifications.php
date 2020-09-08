<?php


function extendthemes_remote_notifications_handle($data)
{
    $url  = "http://extendthemes.com/go/mesmerize-offer/";
    
    $args = apply_filters('extendthemes_offer_utm', array(
        'utm_source' => 'install',
        'utm_medium' => 'wp',
    ));
    
    $args['coupon'] = $data['coupon_code'];
    
    if ($data['coupon_code']) {
        $data['buttons']['primary'] = array(
            'text' => 'Get the offer',
            'url'  => add_query_arg($args, $url),
        );
    }
    
    ?>
    <div class="extendthemes-remote-notification extendthemes-remote-notification-<?php echo esc_attr($data['notification_id']); ?>">

        <div class="left-container">
            <div class="message">
                <?php echo wpautop(trim($data['message'])); ?>
            </div>
        </div>

        <div class="right-container">
            <?php foreach ($data['buttons'] as $key => $button_data): ?>
                <?php if ( ! $button_data) {
                    continue;
                } ?>
                <a target="_blank" href="<?php echo $button_data['url']; ?>" class="button button-<?php echo esc_attr($key); ?>"><?php echo esc_html($button_data['text']); ?></a>
            <?php endforeach; ?>
        </div>
        <style>
            [data-cp-notification-name="extendthemes_remote_notification_mesmerize_<?php echo $data['notification_id']; ?>"] {
                background-image: url("<?php echo $data['background_image'];?>");
                background-size: cover;
                background-repeat: no-repeat;
            }

            [data-cp-notification-name="extendthemes_remote_notification_mesmerize_<?php echo $data['notification_id']; ?>"] .message p:first-child {
                font-size: 20px;
            }

            <?php echo str_replace("#ID","[data-cp-notification-name=\"extendthemes_remote_notification_mesmerize_{$data['notification_id']}\"]",$data['custom_css']); ?>
        </style>
    </div>
    <?php
}


// some notifications style

add_action('admin_head', function () {
    ?>
    <style type="text/css">

        .extendthemes-remote-notification {
            display: -webkit-box;
            display: -webkit-flex;
            display: -moz-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-flex: 0;
            -webkit-flex: 0 1 auto;
            -moz-box-flex: 0;
            -moz-flex: 0 1 auto;
            -ms-flex: 0 1 auto;
            flex: 0 1 auto;
            -webkit-box-direction: normal;
            -webkit-flex-direction: row;
            -moz-flex-direction: row;
            -ms-flex-direction: row;
            flex-direction: row;
            -webkit-flex-wrap: wrap;
            -moz-flex-wrap: wrap;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
            -webkit-flex-basis: auto;
            -moz-flex-basis: auto;
            -ms-flex-preferred-size: auto;
            flex-basis: auto;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            -moz-align-items: center;
            align-items: center;
        }

        .extendthemes-remote-notification .left-container {
            display: flex;
            align-items: center;
            flex-basis: 0;
            flex-grow: 1;
        }

        .extendthemes-remote-notification .right-container {
            display: flex;
            -webkit-flex-shrink: 0;
            -moz-flex-shrink: 0;
            -ms-flex-negative: 0;
            flex-shrink: 0;
            -webkit-flex-basis: auto;
            -moz-flex-basis: auto;
            -ms-flex-preferred-size: auto;
            flex-basis: auto;
            -webkit-box-flex: 0;
            -webkit-flex-grow: 0;
            -moz-flex-grow: 0;
            -ms-flex-positive: 0;
            flex-grow: 0;
            max-width: 100%;
            padding-right: 20px;
        }

        .extendthemes-remote-notification .message {
            padding-left: 20px;
        }

        .notice-cp-announcement {
            border-left-width: 0px;
            padding: 10px 10px 10px 30px;
            background-image: linear-gradient(145deg, #c53a0b 0%, #4e273d 100%);
            background-size: 5px 100%;
            background-repeat: no-repeat;
            background-position: top left;
        }

        .cp-notification.easter,
        .cp-notification.winter {
            position: relative;
            overflow: hidden;
        }

        .cp-notification.easter > div,
        .cp-notification.winter > div {
            z-index: 10;
            position: relative;
        }

        .cp-notification.easter:before,
        .cp-notification.winter:before {

            content: '';
            display: block;
            z-index: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url(https://extendthemes.com/assets/general/flakes.png);
            background-size: 40%;
            background-repeat: repeat;
            opacity: 0.20;
            left: 0;
            top: 0;
        }

        .cp-notification.easter:before {
            background-image: url(https://extendthemes.com/assets/general/easter-bg.jpg);
            opacity: 0.175;

        }

        .extendthemes-remote-notification .button-primary,
        .extendthemes-remote-notification .button-primary:focus,
        .extendthemes-remote-notification .button-primary:hover,
        .notice-cp-announcement .button-primary.main,
        .notice-cp-announcement .button-primary.main:focus,
        .notice-cp-announcement .button-primary.main:hover {
            outline: none;
            border: none;
            background-color: transparent;
            background-image: linear-gradient(145deg, #c53107 0%, #4e273d 100%);
            text-shadow: none;
            transition: all .3s linear;
            box-shadow: 0 1px 0px 0px rgba(0, 0, 0, 0.1);
            height: auto;
            min-width: 7em;
            text-align: center;
            padding: 0.1em 1em;
        }

        .notice-cp-announcement .button-primary.main:hover {
            box-shadow: 0 2px 2px 1px rgba(0, 0, 0, 0.28);
        }

        .notice-cp-announcement .buttons-holder {
            padding-top: 6px;
        }

        .notice-cp-announcement .buttons-holder a {
            margin-right: 4px;
        }

        .notice-cp-announcement .buttons-holder a:last-of-type {
            margin-right: 0px;
        }


    </style>
    <?php
});

// the notifications content display actions

function cp_notification_winter_holiday_2017_active_callback()
{
    $compareTO = "mesmerize";
    $ss        = get_stylesheet();
    
    /** @var WP_Theme $theme */
    $theme = wp_get_theme();
    
    $template = $theme->get('Template');
    if ($template) {
        $ss = $template;
    }
    
    if ($ss === $compareTO) {
        return true;
    } else {
        return false;
    }
}

function cp_notification_discount_offer($data)
{
    $props = isset($data['props']) ? $data['props'] : array();
    $props = array_merge(
        array(
            'message' => 'Discount offer',
            'coupon'  => false,
            'buttons' => array(),
        ), $props
    );
    
    ?>
    <div>
        <div>
            <div style="float: left;">
                <p style="font-size: 20px;"><?php echo $props['message']; ?></p>
            </div>
            <div style="float: right;width: 50%;text-align: right; padding-top: 0.63em;padding-right: 2em;">
                <?php if ($props['coupon']): ?>
                    <p class="coupon-code"><?php echo $props['coupon']; ?></p>
                <?php endif; ?>
                <div class="buttons-holder">
                    <?php foreach ($props['buttons'] as $button): ?>
                        <?php $button = array_merge(
                            array(
                                'class' => 'primary',
                                'link'  => '#',
                                'label' => 'Click Me',
                            ),
                            $button
                        );
                        ?>
                        <a href="<?php echo $button['link']; ?>" target="_blank" class="button button-<?php echo $button['class']; ?>"><?php echo $button['label']; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="wp-clearfix"></div>
        </div>


    </div>
    <?php
}

// the notifications list

return array(
    array(
        "name"            => "winter_holiday_2017",
        "dismissible"     => true,
        "start"           => '20-12-2017',
        "end"             => '10-1-2018',
        "type"            => "cp-announcement winter",
        "handle"          => "cp_notification_discount_offer",
        "active_callback" => "cp_notification_winter_holiday_2017_active_callback",
        "props"           => array(
            "message" => "Winter Holidays Special Offer - <span style='color: red'>20% discount </span> for <strong>Mesmerize PRO</strong>",
            "buttons" => array(
                array(
                    'class' => 'primary main',
                    'link'  => 'https://extendthemes.com/go/mesmerize-upgrade/',
                    'label' => 'Get the offer',
                ),
                array(
                    'class' => 'primary',
                    'label' => 'See PRO Features',
                    'link'  => 'https://extendthemes.com/go/mesmerize-upgrade/#content-1',
                ),
            ),
        ),
    
    ),
    array(
        "name"            => "easter_holiday_2018",
        "dismissible"     => true,
        "start"           => '29-3-2018',
        "end"             => '11-4-2018',
        "type"            => "cp-announcement easter",
        "handle"          => "cp_notification_discount_offer",
        "active_callback" => "cp_notification_winter_holiday_2017_active_callback",
        "props"           => array(
            "message" => "Easter Holidays Special Offer - <span style='color: red'>20% discount </span> for <strong>Mesmerize PRO</strong>",
            "buttons" => array(
                array(
                    'class' => 'primary main',
                    'link'  => 'https://extendthemes.com/go/mesmerize-easter-offer/#pricing',
                    'label' => 'Get the offer',
                ),
                array(
                    'class' => 'primary',
                    'label' => 'See PRO Features',
                    'link'  => 'https://extendthemes.com/go/mesmerize-upgrade/#content-1',
                ),
            ),
        ),
    
    ),
);


