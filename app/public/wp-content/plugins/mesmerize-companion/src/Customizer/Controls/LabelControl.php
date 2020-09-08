<?php

namespace Mesmerize\Customizer\Controls;

class LabelControl extends \Mesmerize\Customizer\BaseControl
{
    public function init()
    {
    
    }


    public function render()
    {
        $id    = 'customize-control-' . str_replace(array( '[', ']' ), array( '-', '' ), $this->id);
        $class = 'customize-control customize-control-' . $this->type; ?>

        <li id="<?php echo esc_attr($id); ?>" class="cp-label-control <?php echo esc_attr($class); ?>">
			<?php $this->render_content(); ?>
		</li>
        <?php

    }


    public function render_content()
    {
       ?>
        	<label>
                <?php if ( ! empty( $this->label ) ) : ?>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php endif;
                if ( ! empty( $this->description ) ) : ?>
                    <span class="description customize-control-description"><?php echo $this->description; ?></span>
                <?php endif; ?>
            
            </label>
       <?php
    }
}
