<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 19.05.15
 * Time: 08:32
 */

namespace whm\CacheWatch\Cli;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use whm\CacheWatch\Cli\Command\ScanCommand;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct('CacheWatch', "1.0");
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $output) {
            $styles['failure'] = new OutputFormatterStyle('red');
            $formatter = new OutputFormatter(null, $styles);
            $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
        }
        return parent::run($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->registerCommands();
        return parent::doRun($input, $output);
    }

    /**
     * Initializes all the yuml-php commands
     */
    private function registerCommands()
    {
        $this->add(new ScanCommand());
    }
}