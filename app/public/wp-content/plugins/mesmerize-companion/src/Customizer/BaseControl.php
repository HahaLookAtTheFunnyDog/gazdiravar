<?php

namespace Mesmerize\Customizer;


class BaseControl extends \WP_Customize_Control
{
    protected $cpData = null;

    public function __construct($manager, $id, $cpData = array())
    {
        $this->cpData = $cpData;
        $args         = (isset($this->cpData['wp_data'])) ? $this->cpData['wp_data'] : array();
        $args         = \Mesmerize\Companion::translateArgs($args);
        $this->type   = isset($args['type']) ? $args['type'] : $this->companion()->customizer()->removeNamespace("\\" . get_class($this));

        parent::__construct($manager, $id, $args);

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


    public function alterSourceData($data)
    {
        return $data;
    }

    public function getSourceData()
    {
        $result = array();

        if (isset($this->cpData['dataSource'])) {

            if (\is_array($this->cpData['dataSource'])) {
                return $this->cpData['dataSource'];
            }

            $result = $this->companion()->getCustomizerData($this->cpData['dataSource']);

            if ( ! $result) {
                $result = array();
            }
        }

        $result = $this->alterSourceData($result);

        return $result;
    }
}
