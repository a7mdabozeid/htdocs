<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions;

interface Condition
{
    /**
     * @return bool
     */
    public function should_show() : bool;
}
