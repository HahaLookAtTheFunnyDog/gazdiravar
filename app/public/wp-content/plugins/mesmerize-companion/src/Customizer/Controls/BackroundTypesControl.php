<?php

namespace Mesmerize\Customizer\Controls;

class BackroundTypesControl extends \Mesmerize\Customizer\BaseControl
{
    public function init()
    {
        $this->type = 'select';
        foreach ($this->cpData['choices'] as $key => $value) {
            $this->choices[$key] = $value['label'];
        }
    }


    public function render_content()
    {
        parent::render_content(); ?>
            <script> 
                jQuery(document).ready(function($){
                    $('[<?php $this->link(); ?>]').data('controlBinds',<?php echo json_encode($this->cpData['choices']) ?>);
                    function updateControlBinds(){
                        var controlBinds =$('[<?php $this->link(); ?>]').data('controlBinds');
                        var currentType = $('[<?php $this->link(); ?>]').val();

                        for(var type in controlBinds){
                            var controls = controlBinds[type].control;
                            if(!_.isArray(controls)){
                                controls = [controls];
                            }
                            
                            for(var i=0;i<controls.length;i++){
                                var control = wp.customize.control(controls[i]);

                                if(control){
                                    var container = control.container.eq(0);
                                    if(type === currentType){
                                        container.show();
                                    } else {
                                        container.hide();
                                    }
                                }

                            }
                        }
                    }
                    wp.customize('<?php echo $this->settings['default']->id ?>').bind(updateControlBinds);
                    $('[<?php $this->link(); ?>]').change(updateControlBinds);
                    updateControlBinds();
                });
            </script>
        <?php

    }
}
