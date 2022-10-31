<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers;

/**
 * Email helper functions used in email templates.
 */
class EmailHelper
{
    /**
     * @return array
     */
    public static function allowed_tags() : array
    {
        return (array) \apply_filters('wpdesk/fr/email/allowed_tags', ['span' => ['class' => []], 'a' => ['href' => [], 'target' => []], 'strong' => ['class' => []], 'h1' => ['class' => []], 'h2' => ['class' => []], 'h3' => ['class' => []], 'table' => ['class' => [], 'id' => []], 'thead' => ['class' => [], 'id' => []], 'tbody' => ['class' => [], 'id' => []], 'tfoot' => ['class' => [], 'id' => []], 'th' => ['class' => [], 'scope' => []], 'tr' => ['class' => [], 'scope' => []], 'td' => ['class' => [], 'scope' => []], 'br' => [], 'p' => []]);
    }
}
