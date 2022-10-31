<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs;

use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\ConditionSettingFactory;
abstract class AbstractSettingsTab
{
    /**
     * @var Renderer
     */
    protected $renderer;
    /**
     * @param Renderer $renderer
     */
    public function __construct(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        $this->renderer = $renderer;
    }
    /**
     * @return Renderer
     */
    protected function get_renderer() : \FRFreeVendor\WPDesk\View\Renderer\Renderer
    {
        return $this->renderer;
    }
    protected function get_condition_fields() : \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\ConditionSettingFactory
    {
        return new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\ConditionSettingFactory(self::get_renderer());
    }
    /**
     * @return array
     */
    public abstract function get_fields() : array;
    /**
     * @return string
     */
    public static abstract function get_tab_slug() : string;
    /**
     * @return string
     */
    public static abstract function get_tab_name() : string;
}
