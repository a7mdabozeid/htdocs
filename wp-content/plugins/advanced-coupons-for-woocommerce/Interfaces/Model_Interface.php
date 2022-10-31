<?php
namespace ACFWP\Interfaces;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Abstraction that provides contract relating to plugin models.
 * All "regular models" should implement this interface.
 *
 * @since 2.0
 */
interface Model_Interface {

    /**
     * Contract for running the model.
     *
     * @since 2.0
     * @access public
     */
    public function run();

}