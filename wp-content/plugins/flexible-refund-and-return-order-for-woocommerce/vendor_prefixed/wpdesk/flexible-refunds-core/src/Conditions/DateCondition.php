<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions;

class DateCondition extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions\AbstractCondition
{
    /**
     * @return bool
     */
    public function should_show() : bool
    {
        try {
            $conditions = $this->get_conditions();
            if (empty($conditions)) {
                return \true;
            }
            $time_value = $conditions['time_value'] ?? 1;
            $time_period = $conditions['time_period'] ?? 'year';
            $order_date = $this->get_order()->get_date_created();
            $order_date->modify($time_value . ' ' . $time_period);
            return $order_date->getTimestamp() > \current_time('timestamp');
        } catch (\Exception $e) {
            return \true;
        }
    }
}
