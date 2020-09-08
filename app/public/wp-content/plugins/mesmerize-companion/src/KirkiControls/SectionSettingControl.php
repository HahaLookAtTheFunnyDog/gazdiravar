<?php

namespace Mesmerize\KirkiControls;

class SectionSettingControl extends \Kirki_Customize_Control
{
    public $type = "sectionsetting";

    protected function content_template()
    {
        ?>
            <# if ( data.label ) { #>
                <span class="customize-control-title">{{ data.label }}</span>
            <# } #>
			<div class="setting-control-container">
            </div>
			<?php
    }
}
