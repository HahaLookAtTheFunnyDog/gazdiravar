<?php

namespace Mesmerize\Customizer\Panels;

class HeaderPanel extends \Mesmerize\Customizer\BasePanel
{

    public function init()
    {
        $this->companion()->customizer()->registerScripts(array($this, 'addScripts'));

        $this->addSections(
            array(
                "header_background_chooser"=>array(
                    "wp_data"=>array(
                        "title" => "Header Background",
                        "priority"=>10,
                        "panel"=> $this->id
                    )
                ),
                "header_layout"=>array(
                    "wp_data"=>array(
                        "priority"=>20,
                        "panel" => $this->id,
                        "title"=> "Header Templates"
                    ),
                )
            )
        );

        $this->addSettings(
            array(
                "header_background_type"=>array(
                    "section"=>"header_background_chooser",
                    "wp_data"=>array(
                         "transport"=> "refresh",
                         "default"=> apply_filters("cloudpress\customizer\header\default_bg_type", "image")
                    ),
                    "control"=>array(
                        "class"=> "Mesmerize\\Customizer\\Controls\\BackroundTypesControl",
                        "choices"=> apply_filters("cloudpress\customizer\header\bg_types", array()),
                        "wp_data"=>array(
                            "priority"=> -1 * PHP_INT_MAX, // put this first
                            "label"=> "Choose Header Background Type",
                        )
                    )
                ),

                "header_presets"=> array(
                    "class"=> false,
                    "section"=> "header_layout",
                    "wp_data"=> array(
                        "transport"=> "postMessage"
                    ),
                    "control"=> array(
                        "class"=> "\\Mesmerize\\Customizer\\Controls\\RowsListControl",
                        "insertText"=> "Apply Preset",
                        "wp_data"=> array(),
                        "type"=> "presets_changer",
                        "dataSource"=> "data:headers"
                    )
                )
            )
        );
    }

    public function addScripts()
    {
        if ($this->isClassic()) {
            return;
        }

        $jsUrl = $this->companion()->assetsRootURL() . "/js/customizer/";
        wp_enqueue_script('cp-customizer-header', $jsUrl . "customizer-header.js", array(), false, true);
    }

    public function render_template()
    {
        if ($this->isClassic()) {
            parent::render_template();
        } else {
            $this->renderEnhanced();
        }
    }

    public function renderEnhanced()
    {
        ?>

        <li id="accordion-panel-{{ data.id }}" data-name="{{{ data.id }}}" class="accordion-section control-section control-panel control-panel-{{ data.type }}">
          <h3 class="accordion-section-title no-chevron" tabindex="0">
             {{ data.title }}
            <span title="<?php _e('Change Header', 'cloudpress-companion'); ?>" data-name="change" class="open-right section-icon"></span>
            <span title="<?php _e('Header Settings', 'cloudpress-companion'); ?>" data-name="edit" class="setting section-icon"></span>
          </h3>
          <ul class="accordion-sub-container control-panel-content"></ul>
        </li>
        <?php

    }
}
