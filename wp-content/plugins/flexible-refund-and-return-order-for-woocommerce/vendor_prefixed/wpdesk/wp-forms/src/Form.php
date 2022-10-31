<?php

namespace FRFreeVendor\WPDesk\Forms;

use FRFreeVendor\WPDesk\View\Renderer\Renderer;
/**
 * Abstraction layer for forms.
 *
 * @package WPDesk\Forms
 */
interface Form
{
    /**
     * For some reason you may want to disable a form. Returns false when disabled.
     */
    public function is_active() : bool;
    /**
     * Whether form handle_request method was successfully executed.
     */
    public function is_submitted() : bool;
    /**
     * After handle_request or set_data the data in form can be invalid according to field validators.
     * Returns false when one of them says the data is invalid.
     */
    public function is_valid() : bool;
    /**
     * Add array to update data.
     *
     * @param array $request New data to update.
     *
     * @return void
     */
    public function handle_request(array $request = []);
    /**
     * Use to render the form to string.
     *
     * @param Renderer $renderer Renderer to render form fields and form-templates.
     */
    public function render_form(\FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer) : string;
    /**
     * Get data from form. Use after handle_request or set_data.
     *
     * @return array<int|string>
     */
    public function get_data() : array;
    /**
     * Get data from form. Use after handle_request or set_data.
     * The difference get_data is that here you will not get any objects and complex data types besides arrays.
     *
     * @return array
     */
    public function get_normalized_data() : array;
    /**
     * Form if you ever need to have more than one form at once.
     */
    public function get_form_id() : string;
}
