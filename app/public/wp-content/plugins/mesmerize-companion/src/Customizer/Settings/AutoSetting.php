<?php

namespace Mesmerize\Customizer\Settings;

class AutoSetting extends \Mesmerize\Customizer\BaseSetting
{
    
    const TYPE = 'cp_auto_setting';
    const SETTING_PATTERN = '/CP_AUTO_SETTING\[(.*)\]/s';
    
    public function init()
    {
        $this->type = self::TYPE;
        $this->transport = 'postMessage';
    }
    
    public function getRealMod()
    {
        $matches = array();
        
        if (preg_match(AutoSetting::SETTING_PATTERN, $this->id, $matches)) {
            $mod = $matches[1];
            
            return $mod;
        }
        
        return '';
    }
    
    public function update($value)
    {
        $mod      = $this->getRealMod();
        $mod      = trim($mod);
        $this->id = $mod;
        $this->type = 'theme_mod';
        return parent::update($value);
    }
    
    
    public function value()
    {
        // $undefined  = new stdClass();
        if ($this->is_previewed) {
            $value = $this->post_value();
        } else {
            $mod   = $this->getRealMod();
            $value = get_theme_mod($mod, false);
        }
        
        return $value;
    }
}
