<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions;

use WC_Order;
abstract class AbstractCondition implements \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions\Condition
{
    /**
     * @var array
     */
    private $conditions;
    /**
     * @var WC_Order
     */
    private $order;
    /**
     * @param array    $conditions
     * @param WC_Order $order
     */
    public function __construct(array $conditions, \WC_Order $order)
    {
        $this->conditions = $conditions;
        $this->order = $order;
    }
    /**
     * @return array
     */
    protected function get_conditions() : array
    {
        return $this->conditions;
    }
    /**
     * @return WC_Order
     */
    protected function get_order() : \WC_Order
    {
        return $this->order;
    }
    /**
     * @return bool
     */
    public abstract function should_show() : bool;
}
