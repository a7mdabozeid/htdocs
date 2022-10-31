<?php

declare (strict_types=1);
namespace FRFreeVendor\WPDesk\Logger;

use FRFreeVendor\Monolog\Handler\HandlerInterface;
use FRFreeVendor\Monolog\Handler\NullHandler;
use FRFreeVendor\Monolog\Logger;
use FRFreeVendor\Monolog\Handler\ErrorLogHandler;
use FRFreeVendor\WPDesk\Logger\WC\WooCommerceHandler;
final class SimpleLoggerFactory implements \FRFreeVendor\WPDesk\Logger\LoggerFactory
{
    /** @var Settings */
    private $options;
    /** @var string */
    private $channel;
    /** @var Logger */
    private $logger;
    public function __construct(string $channel, \FRFreeVendor\WPDesk\Logger\Settings $options = null)
    {
        $this->channel = $channel;
        $this->options = $options ?? new \FRFreeVendor\WPDesk\Logger\Settings();
    }
    public function getLogger($name = null) : \FRFreeVendor\Monolog\Logger
    {
        if ($this->logger) {
            return $this->logger;
        }
        $logger = new \FRFreeVendor\Monolog\Logger($this->channel);
        if ($this->options->use_wc_log && \function_exists('wc_get_logger')) {
            $logger->pushHandler(new \FRFreeVendor\WPDesk\Logger\WC\WooCommerceHandler(\wc_get_logger(), $this->channel));
        }
        // Adding WooCommerce logger may have failed, if so add WP by default.
        if ($this->options->use_wp_log || empty($logger->getHandlers())) {
            $logger->pushHandler($this->get_wp_handler());
        }
        return $this->logger = $logger;
    }
    private function get_wp_handler() : \FRFreeVendor\Monolog\Handler\HandlerInterface
    {
        if (\defined('FRFreeVendor\\WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            return new \FRFreeVendor\Monolog\Handler\ErrorLogHandler(\FRFreeVendor\Monolog\Handler\ErrorLogHandler::OPERATING_SYSTEM, $this->options->level);
        }
        return new \FRFreeVendor\Monolog\Handler\NullHandler();
    }
}
