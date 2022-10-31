<?php
namespace ACFWP\Interfaces;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Abstraction that provides contract relating to deactivation.
 * Any model that needs some sort of deactivation must implement this interface.
 *
 * @since 2.0
 */
interface Deactivatable_Interface {

    /**
     * Contract for deactivation.
     *
     * @since 2.0
     * @access public
     */
    public function deactivate();

}