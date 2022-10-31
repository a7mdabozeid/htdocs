<?php

namespace FRFreeVendor\WPDesk\Composer\Codeception\Commands;

use FRFreeVendor\Symfony\Component\Console\Input\InputArgument;
use FRFreeVendor\Symfony\Component\Console\Input\InputInterface;
use FRFreeVendor\Symfony\Component\Console\Output\OutputInterface;
use FRFreeVendor\Symfony\Component\Yaml\Exception\ParseException;
use FRFreeVendor\Symfony\Component\Yaml\Yaml;
/**
 * Prepare Database for Codeception tests command.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
class PrepareCodeceptionDb extends \FRFreeVendor\WPDesk\Composer\Codeception\Commands\BaseCommand
{
    use LocalCodeceptionTrait;
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('prepare-codeception-db')->setDescription('Prepare codeception database.');
    }
    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(\FRFreeVendor\Symfony\Component\Console\Input\InputInterface $input, \FRFreeVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $configuration = $this->getWpDeskConfiguration();
        $this->installPlugin($configuration->getPluginDir(), $output, $configuration);
        $this->prepareCommonWpWcConfiguration($configuration, $output);
        $this->prepareWpConfig($output, $configuration);
        $this->activatePlugins($output, $configuration);
        $this->executeWpCliAndOutput('db export ' . \getcwd() . '/tests/codeception/tests/_data/db.sql', $output, $configuration->getApacheDocumentRoot());
        return 0;
    }
}
