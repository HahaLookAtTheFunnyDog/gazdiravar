<?php

namespace Mesmerize\Customizer\Panels;

class FooterPanel extends \Mesmerize\Customizer\BasePanel
{

    public function init()
    {
        $this->companion()->customizer()->registerScripts(array($this, 'addScripts'));

        $this->addSections(
            array(
               "footer_layout"=>array(
                    "wp_data"=>array(
                        "priority"=>20,
                        "panel" => $this->id,
                        "title"=> "Header Presets"
                    ),
                )
            )
        );

        $this->addSettings(
            array(
                "footer_presets"=> array(
                    "class"=> false,
                    "section"=> "footer_layout",
                    "wp_data"=> array(
                        "transport"=> "postMessage"
                    ),
                    "control"=> array(
                        "class"=> "\\Mesmerize\\Customizer\\Controls\\RowsListControl",
                        "insertText"=> "Apply Preset",
                        "wp_data"=> array(),
                        "type"=> "presets_changer",
                        "dataSource"=> "data:footers"
                    )
                )
            )
        );
    }

    public function addScripts()
    {
        $jsUrl = $this->companion()->assetsRootURL() . "/js/customizer/";
        wp_enqueue_script('cp-customizer-footer', $jsUrl . "customizer-footer.js", array(), false, true);
    }

    public function render_template()
    {
        ?>
        <li id="accordion-panel-{{ data.id }}" data-name="{{{ data.id }}}" class="accordion-section control-section control-panel control-panel-{{ data.type }}">
          <h3 class="accordion-section-title no-chevron" tabindex="0">
             {{ data.title }}
            <span title="<?php _e('Change Footer', 'cloudpress-companion'); ?>" data-name="change" class="open-right section-icon"></span>
          </h3>
          <ul class="accordion-sub-container control-panel-content"></ul>
        </li>
        <?php

    }
}
