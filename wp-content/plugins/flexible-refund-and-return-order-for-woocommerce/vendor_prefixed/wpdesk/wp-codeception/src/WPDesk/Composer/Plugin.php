<?php

namespace FRFreeVendor\WPDesk\Composer\Codeception;

use FRFreeVendor\Composer\Composer;
use FRFreeVendor\Composer\IO\IOInterface;
use FRFreeVendor\Composer\Plugin\Capable;
use FRFreeVendor\Composer\Plugin\PluginInterface;
/**
 * Composer plugin.
 *
 * @package WPDesk\Composer\Codeception
 */
class Plugin implements \FRFreeVendor\Composer\Plugin\PluginInterface, \FRFreeVendor\Composer\Plugin\Capable
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;
    public function activate(\FRFreeVendor\Composer\Composer $composer, \FRFreeVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    /**
     * @inheritDoc
     */
    public function deactivate(\FRFreeVendor\Composer\Composer $composer, \FRFreeVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    /**
     * @inheritDoc
     */
    public function uninstall(\FRFreeVendor\Composer\Composer $composer, \FRFreeVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    public function getCapabilities()
    {
        return [\FRFreeVendor\Composer\Plugin\Capability\CommandProvider::class => \FRFreeVendor\WPDesk\Composer\Codeception\CommandProvider::class];
    }
}
