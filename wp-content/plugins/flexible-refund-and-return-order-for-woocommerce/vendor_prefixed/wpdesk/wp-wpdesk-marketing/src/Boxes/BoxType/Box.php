<?php

/**
 * Main class of box.
 *
 * @package WPDesk\Library\Marketing\Abstracts
 */
namespace FRFreeVendor\WPDesk\Library\Marketing\Boxes\BoxType;

use FRFreeVendor\WPDesk\Library\Marketing\Boxes\Abstracts\BoxInterface;
use FRFreeVendor\WPDesk\View\Renderer\Renderer;
/**
 * Abstraction for defining boxes.
 */
class Box implements \FRFreeVendor\WPDesk\Library\Marketing\Boxes\Abstracts\BoxInterface
{
    const TYPE = 'simple';
    /**
     * @var array
     */
    public $box = ['title' => '', 'slug' => '', 'type' => '', 'description' => '', 'links' => [], 'className' => [], 'open_row' => [], 'close_row' => [], 'button' => []];
    /**
     * @var Renderer
     */
    public $renderer;
    /**
     * @param array    $box
     * @param Renderer $renderer
     */
    public function __construct(array $box, \FRFreeVendor\WPDesk\View\Renderer\Renderer $renderer)
    {
        $this->box = $box;
        $this->renderer = $renderer;
    }
    /**
     * @return string
     */
    public function get_title() : string
    {
        return \is_string($this->box['title']) ? $this->box['title'] : '';
    }
    /**
     * @return string
     */
    public function get_slug() : string
    {
        return \is_string($this->box['slug']) ? $this->box['slug'] : '';
    }
    /**
     * @return string
     */
    public function get_type() : string
    {
        return static::TYPE;
    }
    /**
     * @return string
     */
    public function get_description() : string
    {
        return \is_string($this->box['description']) ? $this->box['description'] : '';
    }
    /**
     * @return array
     */
    public function get_links() : array
    {
        return \is_array($this->box['links']) ? $this->box['links'] : array();
    }
    /**
     * @return string
     */
    public function get_class() : string
    {
        return \is_string($this->box['className']) ? $this->box['className'] : '';
    }
    /**
     * @param string $slug
     *
     * @return mixed
     */
    public function get_field(string $slug)
    {
        return $this->box[$slug] ?? '';
    }
    /**
     * @return bool
     */
    public function get_row_open() : bool
    {
        return isset($this->box['open_row'][0]) && 'yes' === $this->box['open_row'][0];
    }
    /**
     * @return bool
     */
    public function get_row_close() : bool
    {
        return isset($this->box['close_row'][0]) && 'yes' === $this->box['close_row'][0];
    }
    /**
     * @return array
     */
    public function get_button() : array
    {
        return \is_array($this->box['button']) ? $this->box['button'] : array();
    }
    /**
     * @param array $args
     *
     * @return string
     */
    public function render(array $args = []) : string
    {
        return $this->renderer->render(static::TYPE, \array_merge(['box' => $this], $args));
    }
}
