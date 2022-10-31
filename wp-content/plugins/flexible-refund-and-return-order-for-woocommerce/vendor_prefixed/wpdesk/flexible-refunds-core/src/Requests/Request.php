<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Requests;

interface Request
{
    public function do_action(\WC_Order $order, array $post_data) : bool;
}
