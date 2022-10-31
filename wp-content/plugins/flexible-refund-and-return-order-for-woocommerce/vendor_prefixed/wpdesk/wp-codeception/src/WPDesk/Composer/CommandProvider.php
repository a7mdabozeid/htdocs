<?php

namespace FRFreeVendor\WPDesk\Composer\Codeception;

use FRFreeVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests;
use FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareCodeceptionDb;
use FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTests;
use FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTestsWithCoverage;
use FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareParallelCodeceptionTests;
use FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareWordpressForCodeception;
use FRFreeVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests;
use FRFreeVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTests;
use FRFreeVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTestsWithCoverage;
/**
 * Links plugin commands handlers to composer.
 */
class CommandProvider implements \FRFreeVendor\Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return [new \FRFreeVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests(), new \FRFreeVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests(), new \FRFreeVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTests(), new \FRFreeVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTestsWithCoverage(), new \FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareCodeceptionDb(), new \FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareWordpressForCodeception(), new \FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTests(), new \FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTestsWithCoverage(), new \FRFreeVendor\WPDesk\Composer\Codeception\Commands\PrepareParallelCodeceptionTests()];
    }
}
