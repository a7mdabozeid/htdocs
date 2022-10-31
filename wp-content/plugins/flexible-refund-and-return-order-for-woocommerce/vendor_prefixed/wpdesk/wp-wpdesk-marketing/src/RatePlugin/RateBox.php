<?php

namespace FRFreeVendor\WPDesk\Library\Marketing\RatePlugin;

use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use FRFreeVendor\WPDesk\View\Renderer\SimplePhpRenderer;
use FRFreeVendor\WPDesk\View\Resolver\ChainResolver;
use FRFreeVendor\WPDesk\View\Resolver\DirResolver;
/**
 * Displays a rating box for the plugin in the WordPress repository.
 */
class RateBox
{
    /**
     * @var Renderer
     */
    private $renderer;
    public function __construct()
    {
        $this->init_render();
    }
    /**
     * @return void
     */
    private function init_render()
    {
        $resolver = new \FRFreeVendor\WPDesk\View\Resolver\ChainResolver();
        $resolver->appendResolver(new \FRFreeVendor\WPDesk\View\Resolver\DirResolver(\trailingslashit(__DIR__) . 'Views/'));
        $this->renderer = new \FRFreeVendor\WPDesk\View\Renderer\SimplePhpRenderer($resolver);
    }
    /**
     * @param string $url
     * @param string $description
     * @param string $header
     * @param string $footer
     *
     * @return string
     */
    public function render(string $url, string $description = '', string $header = '', string $footer = '') : string
    {
        return $this->renderer->render('rate-plugin', ['url' => $url, 'description' => $description, 'header' => $header, 'footer' => $footer]);
    }
}
