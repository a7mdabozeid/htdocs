<?php

namespace FRFreeVendor\WPDesk\Composer\Codeception\Commands;

use FRFreeVendor\Composer\Downloader\FilesystemException;
use FRFreeVendor\Symfony\Component\Console\Input\InputArgument;
use FRFreeVendor\Symfony\Component\Console\Input\InputInterface;
use FRFreeVendor\Symfony\Component\Console\Output\OutputInterface;
use FRFreeVendor\Symfony\Component\Yaml\Exception\ParseException;
use FRFreeVendor\Symfony\Component\Yaml\Yaml;
/**
 * Codeception tests run command.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
class PrepareLocalCodeceptionTests extends \FRFreeVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests
{
    use LocalCodeceptionTrait;
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('prepare-local-codeception-tests')->setDescription('Prepare local codeception tests.');
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
        $this->prepareLocalCodeceptionTests($input, $output, \false);
        return 0;
    }
    /**
     * @param array $theme_files
     * @param $theme_folder
     *
     * @throws FilesystemException
     */
    private function copyThemeFiles(array $theme_files, $theme_folder)
    {
        foreach ($theme_files as $theme_file) {
            if (!\copy($theme_file, $this->trailingslashit($theme_folder) . \basename($theme_file))) {
                throw new \FRFreeVendor\Composer\Downloader\FilesystemException('Error copying theme file: ' . $theme_file);
            }
        }
    }
}
