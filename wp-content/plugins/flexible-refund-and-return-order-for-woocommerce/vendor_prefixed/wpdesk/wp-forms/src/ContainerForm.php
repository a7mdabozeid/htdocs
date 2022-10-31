<?php

namespace FRFreeVendor\WPDesk\Forms;

use Psr\Container\ContainerInterface;
use FRFreeVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Persistent container support for forms.
 *
 * @package WPDesk\Forms
 */
interface ContainerForm
{
    /**
     * @param ContainerInterface $data
     *
     * @return void
     */
    public function set_data(\Psr\Container\ContainerInterface $data);
    /**
     * Put data from form into a container.
     *
     * @return void
     */
    public function put_data(\FRFreeVendor\WPDesk\Persistence\PersistentContainer $container);
}
