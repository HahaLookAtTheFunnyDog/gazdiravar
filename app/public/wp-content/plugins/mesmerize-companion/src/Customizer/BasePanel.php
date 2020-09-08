<?php

namespace Mesmerize\Customizer;

class BasePanel extends \WP_Customize_Panel
{
    protected $cpData = null;

    public function __construct($manager, $id, $cpData = array())
    {
        $this->cpData = $cpData;

        $args       = (isset($this->cpData['wp_data'])) ? $this->cpData['wp_data'] : array();
        $args = \Mesmerize\Companion::translateArgs($args);
        $this->type = $this->companion()->customizer()->removeNamespace("\\".get_class($this));

        parent::__construct($manager, $id, $args);

        if (!$this->isClassic()) {
            $this->manager->register_panel_type("\\".get_class($this));
        }


        $this->init();
    }

    protected function init()
    {
        return true;
    }

    final protected function companion()
    {
        return \Mesmerize\Companion::instance();
    }

    public function active_callback()
    {
        return !$this->isDisabled();
    }

    public function addSections($data)
    {
        if ($this->isDisabled()) {
            return;
        }
        
        
        $customizerData = $this->companion()->customizer()->cpData['customizer'];

        if (!isset($customizerData['sections'])) {
            $customizerData['sections'] = array();
        }
        
        $customizerData['sections'] =  \Mesmerize\Utils\Utils::mergeArrays($data, $customizerData['sections']);

        $this->companion()->customizer()->cpData['customizer'] = $customizerData;
    }

    public function addSettings($data)
    {
        if ($this->isDisabled()) {
            return;
        }

        $customizerData = $this->companion()->customizer()->cpData['customizer'];

        if (!isset($customizerData['settings'])) {
            $customizerData['settings'] = array();
        }
        
        $customizerData['settings'] = \Mesmerize\Utils\Utils::mergeArrays($data, $customizerData['settings']);

        $this->companion()->customizer()->cpData['customizer'] = $customizerData;
    }

    public function isClassic()
    {
        return (isset($this->cpData['mode']) && $this->cpData['mode'] === "classic");
    }

    public function isDisabled()
    {
        return (isset($this->cpData['disabled']) && $this->cpData['disabled'] === true);
    }
}
