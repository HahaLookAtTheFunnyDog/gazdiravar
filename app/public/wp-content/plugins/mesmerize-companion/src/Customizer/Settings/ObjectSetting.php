<?php

namespace Mesmerize\Customizer\Settings;

class ObjectSetting extends \Mesmerize\Customizer\BaseSetting
{
    private $settingPattern = "";

    public function init()
    {
        $this->settingPattern = '/'.$this->id.'\[(.*)\]/s';

        $controls = isset($this->cpData['controlsMap']) ? $this->cpData['controlsMap'] : array();

        if (isset($this->cpData['title'])) {
            $titleControl =array(
               'cp__element__title' => array(
                    "class" => "\\Mesmerize\\Customizer\\Controls\\LabelControl",
                    "wp_data" => array(
                        "label" => $this->cpData['title']
                    )
                 )
            );

            $controls = array_merge($titleControl, $controls);
        }
       
        foreach ($controls as $key => $value) {
            $this->companion()->customizer()->registerSettings(
                $this->manager,
                array(
                    "{$this->id}[{$key}]"=> array(
                       "section"=>$this->cpData['section'],
                       "wp_data"=>array(
                           'default'=> $this->companion()->getCustomizerData("customizer:settings:{$this->id}:wp_data:default:{$key}"),
                           'tranport'=> $this->transport
                       ),
                       "control"=>$value
                    )
                )
            );
        }
        

        add_filter('cloudpress\customizer\temp_mod_exists', array($this, 'tempKeyExists'), 10, 2);
        add_filter('cloudpress\customizer\temp_mod_content', array($this, '_tempContent'), 10, 2);
    }

    public function tempKeyExists($value, $mod)
    {   
        return ($value || ($mod === $this->id));
    }

    
    public function _tempContent($value, $mod)
    {
        if($mod === $this->id){
            return $this->tempContent();
        }

        return $value;
    }

    public function tempContent(){
        $settings = $this->manager->unsanitized_post_values();


        $result = array();
        foreach ($settings as $setting => $value) {
            if (strpos($setting, $this->id . "[") ===0) {
                $matches = array();
                preg_match($this->settingPattern, $setting, $matches);
               
                $key = $matches[1];
                $result[$key] = $value;
            }
        }

        $savedData = get_theme_mod($this->id);
        $result = array_merge($savedData,$result);

        return $result;
    }


    public function setControl()
    {
    }


    public function update($value)
    {
        $value = $this->tempContent();
        $value = array_merge($value, $currentValue);
        

        if (isset($value['cp__element__title'])) {
            unset($value['cp__element__title']);
        }

        if (isset($value["{$this->id}[cp__element__title]"])) {
            unset($value["{$this->id}[cp__element__title]"]);
        }
        
        set_theme_mod($this->id, $value);
    }


    public function value()
    {
        if ($this->is_previewed) {
            $value = $this->tempContent();
        } else {
            $mod = $this->id;
            $value = get_theme_mod($mod, false);
        }

        if (isset($value['cp__element__title'])) {
            unset($value['cp__element__title']);
        }

        if (isset($value["{$this->id}[cp__element__title]"])) {
            unset($value["{$this->id}[cp__element__title]"]);
        }

        return $value;
    }
}
