<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions;

use WC_Order_Item_Product;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\RegisterOrderStatus;
class RefundCondition extends \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Conditions\AbstractCondition
{
    const EXCLUDED_CONDITIONS = ['wc-cancelled', 'wc-refunded', 'wc-failed'];
    /**
     * @return bool
     */
    public function should_show() : bool
    {
        if (\in_array('wc-' . $this->get_order()->get_status(), self::EXCLUDED_CONDITIONS, \true)) {
            return \false;
        }
        if ('wc-' . $this->get_order()->get_status() === \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration\RegisterOrderStatus::REQUEST_REFUND_STATUS) {
            return \true;
        }
        $conditions = $this->get_conditions();
        if (!isset($conditions['condition_type'])) {
            return \true;
        }
        foreach ($conditions['condition_type'] as $condition_type_key => $condition_type) {
            $operator = $conditions['condition_operator'][$condition_type_key] ?? [];
            $values = $conditions['condition_values'][$condition_type_key][$condition_type] ?? [];
            if (empty($values)) {
                continue;
            }
            if (!$this->condition_factory($condition_type, $operator, $values)) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * @param string       $type
     * @param string       $operator
     * @param string|array $values
     *
     * @return bool
     */
    private function condition_factory(string $type, string $operator, $values)
    {
        switch ($type) {
            case 'order_statuses':
                return $this->order_statuses_condition($operator, $values);
            case 'user_roles':
                return $this->user_roles_condition($operator, $values);
            case 'product_cats':
                return $this->product_cats_condition($operator, $values);
            case 'products':
                return $this->products_condition($operator, $values);
            case 'payment_methods':
                return $this->payment_methods_condition($operator, $values);
        }
        return \false;
    }
    /**
     * @param string $operator
     * @param array  $values
     *
     * @return bool
     */
    private function order_statuses_condition(string $operator, array $values) : bool
    {
        $status = \in_array('wc-' . $this->get_order()->get_status(), $values, \true);
        if ($operator === 'is_not') {
            return !$status;
        }
        return $status;
    }
    /**
     * @param string $operator
     * @param array  $values
     *
     * @return bool
     */
    private function user_roles_condition(string $operator, array $values) : bool
    {
        global $current_user;
        $user_role = \false;
        if (\is_user_logged_in() && !empty($current_user->roles[0])) {
            $user_role = \in_array($current_user->roles[0], $values, \true);
            if ($operator === 'is_not') {
                return !$user_role;
            }
        }
        return $user_role;
    }
    /**
     * @param string $operator
     * @param array  $values
     *
     * @return bool
     */
    private function product_cats_condition(string $operator, array $values) : bool
    {
        $items = $this->get_order()->get_items();
        $order_product_cats = [];
        foreach ($items as $item) {
            if ($item instanceof \WC_Order_Item_Product) {
                $product = $item->get_product();
                $order_product_cats = $product->get_category_ids();
            }
        }
        foreach ($values as $value) {
            $product_cats = \in_array((string) $value, $order_product_cats);
            if ($operator === 'is_not') {
                return !$product_cats;
            }
            return $product_cats;
        }
        return \false;
    }
    /**
     * @param string $operator
     * @param array  $values
     *
     * @return bool
     */
    private function products_condition(string $operator, array $values) : bool
    {
        $items = $this->get_order()->get_items();
        $order_products = [];
        foreach ($items as $item) {
            if ($item instanceof \WC_Order_Item_Product) {
                $order_products[] = (string) $item->get_product_id();
            }
        }
        foreach ($values as $value) {
            $payment_method = \in_array((string) $value, $order_products);
            if ($operator === 'is_not') {
                return !$payment_method;
            }
            return $payment_method;
        }
        return \false;
    }
    /**
     * @param string $operator
     * @param array  $values
     *
     * @return bool
     */
    private function payment_methods_condition(string $operator, array $values) : bool
    {
        $payment_method = \in_array($this->get_order()->get_payment_method(), $values, \true);
        if ($operator === 'is_not') {
            return !$payment_method;
        }
        return $payment_method;
    }
}
