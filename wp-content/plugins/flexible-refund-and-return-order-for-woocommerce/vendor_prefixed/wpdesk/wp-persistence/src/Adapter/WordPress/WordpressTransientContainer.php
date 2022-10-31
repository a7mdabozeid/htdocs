<?php

namespace FRFreeVendor\WPDesk\Persistence\Adapter\WordPress;

use FRFreeVendor\WPDesk\Persistence\ElementNotExistsException;
use FRFreeVendor\WPDesk\Persistence\FallbackFromGetTrait;
use FRFreeVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Can store data using WordPress transients.
 * Warning: stored false is saved as ''
 *
 * @package WPDesk\Persistence\Wordpress
 */
final class WordpressTransientContainer implements \FRFreeVendor\WPDesk\Persistence\PersistentContainer
{
    use FallbackFromGetTrait;
    /** @var int */
    private $expiration;
    /** @var string */
    private $namespace;
    /**
     * @param string    $namespace  Namespace so transients in different containers would not conflict.
     * @param float|int $expiration Expire transient after xx seconds.
     */
    public function __construct($namespace = '', $expiration = DAY_IN_SECONDS)
    {
        $this->expiration = (int) $expiration;
        $this->namespace = $namespace;
    }
    /**
     * @param int $expiration
     */
    public function set_expiration(int $expiration)
    {
        $this->expiration = $expiration;
    }
    /**
     * @param string $id
     * @param mixed  $value
     */
    public function set(string $id, $value)
    {
        if ($value === null) {
            $this->delete($id);
        } else {
            \set_transient($this->prepare_key_name($id), $value, $this->expiration);
        }
    }
    /**
     * Warning: stored false is converted
     *
     * @param string $id
     *
     * @return bool
     */
    public function has($id) : bool
    {
        return \get_transient($this->prepare_key_name($id)) !== \false;
    }
    public function delete(string $id)
    {
        \delete_transient($this->prepare_key_name($id));
    }
    /**
     * Prepare transient name for key.
     *
     * @param string $key Key.
     *
     * @return string
     */
    private function prepare_key_name($key) : string
    {
        return \sanitize_key($this->namespace . $key);
    }
    public function get($id)
    {
        $value = \get_transient($this->prepare_key_name($id));
        if (\false === $value) {
            throw new \FRFreeVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $id));
        }
        return $value;
    }
}
