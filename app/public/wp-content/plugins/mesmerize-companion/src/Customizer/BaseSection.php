<?php

namespace Mesmerize\Customizer;

class BaseSection extends \WP_Customize_Section
{
    protected $cpData = null;

    public function __construct($manager, $id, $cpData = array())
    {
        $this->cpData = $cpData;

        $args = (isset($this->cpData['wp_data'])) ? $this->cpData['wp_data'] : array();
        $args = \Mesmerize\Companion::translateArgs($args);
         
        parent::__construct($manager, $id, $args);
        $this->init();
    }

    

    final protected function companion()
    {
        return \Mesmerize\Companion::instance();
    }

    protected function init()
    {
        return true;
    }
}
